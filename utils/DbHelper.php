<?php

namespace App\Utils;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

class DbHelper
{
    private static ?DynamoDbClient $dynamoClient = null;
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
}
