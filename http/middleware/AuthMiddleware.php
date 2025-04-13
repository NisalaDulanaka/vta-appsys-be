<?php

use App\Utils\AppResponse;
use Aws\CognitoIdentity\CognitoIdentityClient;
use Aws\Exception\AwsException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;

use App\Utils\UserSession;

class AuthMiddleware extends Middleware
{
    private array $config;
    private CognitoIdentityClient $identityClient;

    public function __construct()
    {
        $this->config = require './config/config.php';
    }

    public function handleIncoming(Request $request)
    {
        // check for auth header
        $idToken = $request->header('Authorization');

        if ($idToken === null || empty($idToken)) {
            return AppResponse::error(["message" => "Access denied"], 403);
        }

        // initialize cognito client
        $this->identityClient = new CognitoIdentityClient([
            'region'  => $this->config['region'],
            'version' => 'latest',
        ]);

        // get id token and set credentials
        UserSession::$token = str_replace('Bearer ', '', $idToken);

        try {
            // retrieve credentials
            $errors = $this->generateCredentials();
            if ($errors) {
                return AppResponse::error(["message" => "Unauthorized"], 403);
            }

            // decode token and set user data
            $this->setTokenData();
        } catch (Exception $e) {
            return AppResponse::error(["message" => $e->getMessage()], 403);
        }

        return NEXT_ROUTE;
    }

    public function handleOutgoing($response)
    {
        // Put your outgoing logic here
    }

    /**
     * Retrieves the unique identity id for the user
     * @param string $token The id token
     */
    private function getIdentityId(string $token): string
    {
        try {
            $response = $this->identityClient->getId([
                'IdentityPoolId' => $this->config['identityPoolId'],
                'Logins' => [
                    "cognito-idp.{$this->config['region']}.amazonaws.com/{$this->config['userPoolId']}" => $token,
                ]
            ]);

            return $response['IdentityId'];
        } catch (AwsException $e) {
            return "Unauthorized";
        }
    }

    /**
     * Retrieves temporary credentials for the user
     * @param string $identityId The identity id retrieved from identity pool
     * @param string $token The id token
     */
    private function getCredentials(string $identityId, string $token): array
    {
        try {
            $credentialsResponse = $this->identityClient->getCredentialsForIdentity([
                'IdentityId' => $identityId,
                'Logins' => [
                    "cognito-idp.{$this->config['region']}.amazonaws.com/{$this->config['userPoolId']}" => $token,
                ]
            ]);

            $credentials = $credentialsResponse['Credentials'];
            return [
                'key' => $credentials['AccessKeyId'],
                'secret' => $credentials['SecretKey'],
                'token' => $credentials['SessionToken'],
            ];
        } catch (AwsException $e) {
            throw $e;
        }
    }

    /**
     * Retrieves aws credentials for the user using the token
     */
    private function generateCredentials()
    {
        try {
            $id = $this->getIdentityId(UserSession::$token);
            if ($id === "Unauthorized") {
                return $id;
            }

            UserSession::$credentials = $this->getCredentials($id, UserSession::$token);
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function decodeIdToken($idToken): array | string
    {
        // Fetch Cognito public keys
        $keysUrl = "https://cognito-idp.{$this->config['region']}.amazonaws.com/{$this->config['userPoolId']}/.well-known/jwks.json";
        $jsonKeys = file_get_contents($keysUrl);
        $jwks = json_decode($jsonKeys, true);

        if (!isset($jwks['keys'])) {
            return "Failed to retrieve Cognito public keys.";
        }

        // Decode the token header to get 'kid'
        $tokenParts = explode('.', $idToken);
        if (count($tokenParts) !== 3) {
            return "Invalid ID Token structure.";
        }
        $header = json_decode(base64_decode($tokenParts[0]), true);
        if (!isset($header['kid'])) {
            return "Invalid token header.";
        }

        // Find matching key
        $publicKeys = JWK::parseKeySet(['keys' => $jwks['keys']]);
        if (!isset($publicKeys[$header['kid']])) {
            return "No matching key found for token.";
        }

        // Decode and verify token
        $decodedToken = JWT::decode($idToken, $publicKeys[$header['kid']]);

        return (array) $decodedToken;
    }

    /**
     * Calls the token decode method and sets the decoded data
     */
    private function setTokenData()
    {
        $result = $this->decodeIdToken(UserSession::$token);
        if (gettype($result) === "string") {
            throw new Exception($result);
        }

        UserSession::$userData = $result;
        UserSession::$userId = $result['sub'];
        UserSession::$userRole = $result['custom:userRole'];
    }
}
