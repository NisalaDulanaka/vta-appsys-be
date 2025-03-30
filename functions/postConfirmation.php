<?php

require 'vendor/autoload.php';

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

require_once('./utils/ServiceRegistry.php');

return function ($event) {

    $userData = $event['request']['userAttributes'];

    $client = ServiceRegistry::getDbClient();
    $marshaler = new Marshaler();

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
        
        $params = [
            'TableName' => 'vta_appsys_user',
            'Item' => $marshaler->marshalItem($item),
        ];

        $client->putItem($params);
    } catch (DynamoDbException $e) {
        AppLogger::error($e->__toString());
        throw $e;
    }

    return $event;
};
