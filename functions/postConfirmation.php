<?php

require 'vendor/autoload.php';

use Aws\DynamoDb\Exception\DynamoDbException;

use App\Utils\ServiceRegistry;
use App\Utils\AppLogger;

require_once('./utils/ServiceRegistry.php');

return function ($event) {

    $userData = $event['request']['userAttributes'];
    $client = ServiceRegistry::getDbClient();

    try {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');

        $item = [
            'pk' => 'USER',
            'sk' => $userData['sub'],
            'userId' => $userData['sub'],
            'name' => $userData['name'],
            'userRole' => $userData['custom:userRole'],
            'dob' => $userData['birthdate'] ?? null,
            'nic' => $event['userName'],
            'createdAt' => $timestamp,
            'updatedAt' => $timestamp,
        ];
        
        $client->putItem('vta_appsys_user', $item);
    } catch (DynamoDbException $e) {
        AppLogger::error($e->__toString());
        throw $e;
    }

    return $event;
};
