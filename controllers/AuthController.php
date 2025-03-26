<?php

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Exception\AwsException;

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
        $errors = $this->validate($body, 'AuthSchema.login');
        if ($errors !== null) {
            return ["error" => $errors];
        }

        $body = $this->mapToDto(LoginRequestDto::class, $body);

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

            return [
                "accessToken" => $result['AuthenticationResult']['AccessToken'],
                "idToken" => $result['AuthenticationResult']['IdToken'],
                "refreshToken" => $result['AuthenticationResult']['RefreshToken'],
            ];
        } catch (AwsException $e) {
            AppLogger::error($e->__toString());
            return ["error" => $e->getAwsErrorMessage()];
        }
    }

    public function register(Request $request)
    {
        $body = $request->getRequestBody();
        $errors = $this->validate($body, 'AuthSchema.register');
        if ($errors !== null) {
            return ["error" => $errors];
        }
        $body = $this->mapToDto(RegisterRequestDto::class, $body);

        try {
            $response = $this->client->signUp([
                'ClientId' => $this->config['clientId'],
                'Username' => $body->nic,
                'Password' => $body->password,
                'UserAttributes' => [
                    ['Name' => 'email', 'Value' => $body->email],
                    ['Name' => 'custom:role', 'Value' => $body->userRole],
                    ['Name' => 'birthdate', 'Value' => $body->dob],
                ],
            ]);

            if (!isset($response['CodeDeliveryDetails'])) {
                return [
                    "error" => "Registration unsuccessful",
                ];
            }

            return [
                "message" => "Registered successfully",
            ];
        } catch (AwsException $e) {
            AppLogger::error($e->__toString());
            return ["error" => $e->getAwsErrorMessage()];
        }
    }

    public function confirmUser(Request $request)
    {
        $body = $request->getRequestBody();
        $errors = $this->validate($body, 'AuthSchema.confirm');
        if ($errors !== null) {
            return ["error" => $errors];
        }
        $body = $this->mapToDto(ConfirmUserRequestDto::class, $body);

        try {
            $response = $this->client->confirmSignUp([
                'ClientId' => $this->config['clientId'],
                'Username' => $body->userName,
                'ConfirmationCode' => $body->code,
            ]);

            if ($response['@metadata']['statusCode'] !== 200) {
                return [
                    "error" => "Confirmation failed",
                ];
            }

            return [
                "message" => "User successfully confirmed",
            ];
        } catch (AwsException $e) {
            AppLogger::error($e->__toString());
            return ["error" => $e->getAwsErrorMessage()];
        }
    }
}
