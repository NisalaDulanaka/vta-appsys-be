<?php

use Aws\DynamoDb\Exception\DynamoDbException;
use Ramsey\Uuid\Uuid;

use Utils\ServiceRegistry;
use Utils\UserSession;
use Utils\AppLogger;

trait ApplicationModel
{
    function addNewApplication(string $userId, AddApplicationRequest $data)
    {
        $client = ServiceRegistry::getDbClient(UserSession::$credentials);

        try {
            $applicationId = Uuid::uuid4()->toString();
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
                'applicationType' => $data->applicationType,
                'courses' => array_map(fn($course) => [
                    'courseId' => $course->courseId,
                    'courseName' => $course->courseName,
                    'centerId' => $course->centerId,
                    'centerName' => $course->centerName
                ], $data->courses),
                'status' => ApplicationStatus::ADDED->value,
                'createdAt' => $timestamp,
                'updatedAt' => $timestamp,
            ];

            $client->putItem('vta_appsys_applications', $item);
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }

    function updateExistingApplication(UpdateApplicationRequest $request): void
    {
        $client = ServiceRegistry::getDbClient(UserSession::$credentials);

        try {
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');

            $updates = [
                'name' => $request->updates->name,
                'telNo' => $request->updates->telNo,
                'address' => $request->updates->address,
                'applicationType' => $request->updates->applicationType,
                'courses' => array_map(fn($course) => [
                    'courseId' => $course->courseId,
                    'courseName' => $course->courseName,
                    'centerId' => $course->centerId,
                    'centerName' => $course->centerName
                ], $request->updates->courses),
                'updatedAt' => $timestamp
            ];

            $updateRequest = $client->buildUpdateItemRequest($updates);

            if (empty($updateRequest)) {
                return; // Nothing to update
            }

            $key = [
                'pk' => "APPLICATION#{$request->userId}",
                'sk' =>"{$request->nic}#{$request->applicationId}",
            ];

            $client->updateItem([
                'TableName' => 'vta_appsys_applications',
                'Key' => $key,
                ...$updateRequest
            ]);
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }


    function getApplications(string $userId, SearchApplicationsRequest $data)
    {
        try {
            $mustClauses = [];

            if (!empty($data->term)) {
                $mustClauses[] = [
                    'match_phrase' => [
                        $data->searchField => $data->term,
                    ]
                ];
            }

            // If role is student, restrict by userId
            if (UserSession::$userRole === 'vta-student-role') {
                $mustClauses[] = [
                    'match' => [
                        'userId' => $userId,
                    ]
                ];
            }

            // Apply filters
            if (!empty($data->filters)) {
                foreach ($data->filters as $key => $value) {
                    if (!empty($value)) {
                        $mustClauses[] = [
                            'match' => [
                                $key => $value,
                            ]
                        ];
                    }
                }
            }

            $boolQuery = [];
            if (!empty($mustClauses)) {
                $boolQuery['must'] = $mustClauses;
            }

            // If no must/filter provided, use match_all
            $query = [
                'query' => !empty($boolQuery) ? ['bool' => $boolQuery] : ['match_all' => new stdClass()]
            ];


            $client = ServiceRegistry::getOpenSearchClient();
            $applications = $client->search('appsys_applications', $query);

            return $applications;
        } catch (\Exception $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }
}
