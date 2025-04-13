<?php

use Aws\DynamoDb\Exception\DynamoDbException;
use Ramsey\Uuid\Uuid;

use App\Utils\ServiceRegistry;
use App\Utils\UserSession;
use App\Utils\AppLogger;

trait ApplicationModel
{
    function addNewApplication(string $userId, AddApplicationRequest $data)
    {
        $client = ServiceRegistry::getDbClient(UserSession::$credentials);

        try {
            $applicationId = Uuid::uuid4();
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');

            $item = [
                'pk' => "APPLICATION#{$userId}",
                'sk' => "{$data->nic}#{$applicationId}",
                'applicationId' => $applicationId,
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
                'status' => ApplicationStatus::added,
                'createdAt' => $timestamp,
                'updatedAt' => $timestamp,
            ];

            $client->putItem('vta_appsys_applications', $item);
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

            $applications = $client->search('appsys_applications', $query);

            return $applications;
        } catch (\Exception $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }
}
