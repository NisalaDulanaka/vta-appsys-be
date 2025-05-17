<?php

namespace Utils;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

class DbHelper
{
    private ?DynamoDbClient $dynamoClient = null;
    private Marshaler $marshaler;

    public function __construct(?array $credentials = null)
    {
        $this->dynamoClient = new DynamoDbClient([
            'region'  => 'us-east-2',
            'version' => 'latest',
            'credentials' => $credentials,
        ]);

        $this->marshaler = new Marshaler();
    }

    public function putItem(string $tableName, array $item): void
    {
        AppLogger::debug(['item' => $item,]);
        $params = [
            'TableName' => $tableName,
            'Item' =>  $this->marshaler->marshalItem($item),
        ];

        try {
            $this->dynamoClient->putItem($params);
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }

    public function getItem(string $tableName, array $key): ?array
    {
        AppLogger::debug(['key' => $key]);

        $params = [
            'TableName' => $tableName,
            'Key' => $this->marshaler->marshalItem($key),
        ];

        try {
            $result = $this->dynamoClient->getItem($params);

            if (isset($result['Item'])) {
                return $this->marshaler->unmarshalItem($result['Item']);
            }

            return null;
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }


    public function updateItem(array $request): void
    {
        AppLogger::debug(['updateItem' => $request,]);
        $params = [
            'TableName' => $request['TableName'],
            'Key' => $this->marshaler->marshalItem($request['Key']),
            'UpdateExpression' => $request['UpdateExpression'],
            'ExpressionAttributeNames' => $request['ExpressionAttributeNames'],
            'ExpressionAttributeValues' => $this->marshaler->marshalItem($request['ExpressionAttributeValues']),
        ];

        try {
            $this->dynamoClient->updateItem($params);
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }

    public function deleteItem(string $tableName, array $key): void
    {
        AppLogger::debug(['Deleting item with key' => $key]);

        $params = [
            'TableName' => $tableName,
            'Key' => $this->marshaler->marshalItem($key),
        ];

        try {
            $this->dynamoClient->deleteItem($params);
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }


    public function unMarshallRecords(array $event): array
    {
        $records = [];

        foreach ($event['Records'] as $record) {
            if (!isset($record['dynamodb'])) {
                continue;
            }

            $keys = $record['dynamodb']['Keys'] ?? null;
            $newImage = $record['dynamodb']['NewImage'] ?? null;
            $oldImage = $record['dynamodb']['OldImage'] ?? null;

            $unmarshalledRecord = [
                'eventName' => $record['eventName']
            ];

            if ($newImage) {
                $unmarshalledRecord['newImage'] = array_merge(
                    $this->marshaler->unmarshalItem($keys),
                    $this->marshaler->unmarshalItem($newImage)
                );
            }

            if ($oldImage) {
                $unmarshalledRecord['oldImage'] = array_merge(
                    $this->marshaler->unmarshalItem($keys),
                    $this->marshaler->unmarshalItem($oldImage)
                );
            }

            $records[] = $unmarshalledRecord;
        }

        return $records;
    }

    public function buildUpdateItemRequest(array $fields): array
    {
        $updateExpression = [];
        $expressionAttributeNames = [];
        $expressionAttributeValues = [];

        foreach ($fields as $key => $value) {
            if ($value === '' || $value === null || (is_array($value) && empty($value))) {
                continue;
            }

            $updateExpression[] = "#{$key} = :{$key}";
            $expressionAttributeNames["#{$key}"] = $key;
            $expressionAttributeValues[":{$key}"] = $value;
        }

        if (empty($updateExpression)) {
            return [];
        }

        $params = [
            'UpdateExpression' => 'SET ' . implode(', ', $updateExpression),
            'ExpressionAttributeNames' => $expressionAttributeNames,
            'ExpressionAttributeValues' => $expressionAttributeValues,
        ];
        AppLogger::debug(['updateParams' => $params,]);

        return $params;
    }
}
