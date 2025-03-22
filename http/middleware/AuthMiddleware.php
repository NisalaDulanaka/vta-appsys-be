<?php

use Aws\CognitoIdentity\CognitoIdentityClient;
use Aws\Exception\AwsException;

class AuthMiddleware extends Middleware
{
    public static ?string $token;
    public static ?array $credentials;

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
            return [
                "message" => "Access denied",
                "code" => 403,
            ];
        }

        // initialize cognito client
        $this->identityClient = new CognitoIdentityClient([
            'region'  => $this->config['region'],
            'version' => 'latest',
        ]);

        // extract token and set credentials
        AuthMiddleware::$token = str_replace('Bearer ', '', $idToken);

        try {
            $errors = $this->generateCredentials();
            if ($errors) {
                return [
                    "message" => "Unauthorized",
                    "code" => 403,
                ];
            }
        } catch (Exception $e) {
            return [
                "message" => $e->getMessage(),
                "code" => 500,
            ];
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
                    'cognito-idp.{$this->config["region"]}.amazonaws.com/{$this->config["userPoolId"]}' => $token
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
                    'cognito-idp.{$this->config["region"]}.amazonaws.com/{$this->config["userPoolId"]}' => $token,
                ]
            ]);

            return $credentialsResponse['Credentials'];
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
            $id = $this->getIdentityId(AuthMiddleware::$token);
            if ($id === "Unauthorized") {
                return $id;
            }

            AuthMiddleware::$credentials = $this->getCredentials($id, AuthMiddleware::$token);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
