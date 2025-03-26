<?php
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

trait AuthModel {
    function createUser(string $userId, RegisterRequestDto $data) {
        $client = new DynamoDbClient([
            'region'  => 'us-east-2',
            'version' => 'latest',
            'credentials' => UserSession::$credentials,
        ]);
        $marshaler = new Marshaler();

        try {
            $item = [
                'pk' => 'USER',
                'sk' => $userId,
                'userId' => $userId,
                'name' => $data->name,
                'userRole' => $data->userRole,
                'dob' => $data->dob,
                'nic' => $data->nic,
            ];
            
            $params = [
                'TableName' => 'vta_appsys_applications',
                'Item' => $marshaler->marshalItem($item),
            ];

            $client->putItem($params);
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }
}
