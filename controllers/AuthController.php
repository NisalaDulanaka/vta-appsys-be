<?php

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Exception\AwsException;

use App\Utils\AppLogger;
use App\Utils\AppResponse;

require('./traits/dto/AuthDto.php');

class AuthController extends Controller
{
    private array $config;
    private CognitoIdentityProviderClient $client;

    public function __construct()
    {
        $this->config = require './config/config.php';
        $this->client = new CognitoIdentityProviderClient([
            'region'  => $this->config['region'],
            'version' => 'latest',
        ]);
    }

    public function login(Request $request)
    {
        $body = $request->getRequestBody();
        AppLogger::debug($body);

        $errors = $this->validate($body, 'AuthSchema.login');
        if ($errors !== null) {
            return AppResponse::error($errors);
        }

        $body = LoginRequestDto::fromArray($body);

        try {
            $result = $this->client->adminInitiateAuth([
                'UserPoolId' => $this->config['userPoolId'],
                'ClientId'   => $this->config['clientId'],
                'AuthFlow'   => 'ADMIN_USER_PASSWORD_AUTH',
                'AuthParameters' => [
                    'USERNAME' => $body->email,
                    'PASSWORD' => $body->password
                ]
            ]);

            return AppResponse::success([
                "accessToken" => $result['AuthenticationResult']['AccessToken'],
                "idToken" => $result['AuthenticationResult']['IdToken'],
                "refreshToken" => $result['AuthenticationResult']['RefreshToken'],
            ]);
        } catch (AwsException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }

    public function register(Request $request)
    {
        $body = $request->getRequestBody();
        $errors = $this->validate($body, 'AuthSchema.register');
        if ($errors !== null) {
            return AppResponse::error($errors);
        }
        $body = RegisterRequestDto::fromArray($body);

        try {
            $response = $this->client->signUp([
                'ClientId' => $this->config['clientId'],
                'Username' => $body->nic,
                'Password' => $body->password,
                'UserAttributes' => [
                    ['Name' => 'email', 'Value' => $body->email],
                    ['Name' => 'custom:userRole', 'Value' => $body->userRole],
                    ['Name' => 'birthdate', 'Value' => $body->dob],
                    ['Name' => 'name', 'Value' => $body->name],
                ],
            ]);

            if (!isset($response['CodeDeliveryDetails'])) {
                return AppResponse::error([
                    "message" => "Registration failed",
                ]);
            }

            return AppResponse::success([
                "message" => "User successfully registered"
            ]);
        } catch (AwsException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }

    public function confirmUser(Request $request)
    {
        $body = $request->getRequestBody();
        $errors = $this->validate($body, 'AuthSchema.confirm');
        if ($errors !== null) {
            return AppResponse::error($errors);
        }
        $body = ConfirmUserRequestDto::fromArray($body);

        try {
            $response = $this->client->confirmSignUp([
                'ClientId' => $this->config['clientId'],
                'Username' => $body->userName,
                'ConfirmationCode' => $body->code,
            ]);

            if ($response['@metadata']['statusCode'] !== 200) {
                return AppResponse::error([
                    "message" => "Confirmation failed",
                ]);
            }

            return AppResponse::success([
                "message" => "User successfully confirmed"
            ]);
        } catch (AwsException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }
}
