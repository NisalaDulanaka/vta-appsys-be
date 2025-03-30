<?php

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

trait ApplicationModel
{
    function addNewApplication(?string $userId, AddApplicationRequest $data)
    {
        $client = ServiceRegistry::getDbClient(UserSession::$credentials);
        $marshaler = new Marshaler();

        try {
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');

            $item = [
                'pk' => "APPLICATION#{$userId}",
                'sk' => $data->nic,
                'userId' => $userId,
                'name' => $data->name,
                'nic' => $data->nic,
                'telNo' => $data->telNo,
                'address' => $data->address,
                'courses' => array_map(fn($course) => [
                    'courseId' => $course->courseId,
                    'courseName' => $course->courseName,
                    'centerId' => $course->centerId,
                    'centerName' => $course->centerName
                ], $data->courses),
                'createdAt' => $timestamp,
                'updatedAt' => $timestamp,
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

    function getApplications(string $userId)
    {
        try {
            $client = ServiceRegistry::getOpenSearchClient();

            $query = [
                'query' => [
                    'match' => [
                        'userId' => $userId,
                    ]
                ]
            ];

            $applications = $client->search('index_name', $query);

            return $applications;
        } catch (\Exception $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }
}
