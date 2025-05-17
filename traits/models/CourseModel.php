<?php

use Aws\DynamoDb\Exception\DynamoDbException;
use Ramsey\Uuid\Uuid;

use Utils\ServiceRegistry;
use Utils\UserSession;
use Utils\AppLogger;

trait CourseModel
{
    function addNewCenter(AddCenterRequest $data)
    {
        $client = ServiceRegistry::getDbClient(UserSession::$credentials);

        try {
            $centerId = Uuid::uuid4();
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');

            $item = [
                'pk' => "CENTER",
                'sk' => $centerId,
                'centerId' => $centerId,
                'centerName' => $data->centerName,
                'address' => $data->address,
                'telNo' => $data->telNo,
                'email' => $data->email,
                'createdAt' => $timestamp,
                'updatedAt' => $timestamp,
            ];

            $client->putItem('vta_appsys_referencedata', $item);
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }

    function addNewCourse(AddCourseRequest $data)
    {
        $client = ServiceRegistry::getDbClient(UserSession::$credentials);

        try {
            $courseId = Uuid::uuid4()->toString();
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');

            $item = [
                'pk' => 'COURSE',
                'sk' => $courseId,
                'courseId' => $courseId,
                'courseName' => $data->courseName,
                'nvqLevel' => $data->nvqLevel,
                'description' => $data->description,
                'courseType' => $data->courseType->value,
                'centers' => $data->centers,
                'createdAt' => $timestamp,
                'updatedAt' => $timestamp,
            ];

            AppLogger::debug($item);
            $client->putItem('vta_appsys_referencedata', $item);
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }

    function updateExistingCourse(UpdateCourseRequest $data): void
    {
        $client = ServiceRegistry::getDbClient(UserSession::$credentials);

        try {
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');

            $updates = [
                'courseName' => $data->updates->courseName,
                'nvqLevel' => $data->updates->nvqLevel,
                'description' => $data->updates->description,
                'courseType' => $data->updates->courseType->value,
                'centers' => $data->updates->centers,
                'updatedAt' => $timestamp
            ];

            $updateRequest = $client->buildUpdateItemRequest($updates);

            if (empty($updateRequest)) {
                return; // Nothing to update
            }

            $key = [
                'pk' => "COURSE",
                'sk' => $data->courseId,
            ];

            $client->updateItem([
                'TableName' => 'vta_appsys_referencedata',
                'Key' => $key,
                ...$updateRequest
            ]);
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }

    function deleteExistingCourse(string $courseId): void
    {
        $client = ServiceRegistry::getDbClient(UserSession::$credentials);

        try {
            $key = [
                'pk' => 'COURSE',
                'sk' => $courseId,
            ];

            AppLogger::debug(['Deleting course with ID' => $courseId]);
            $client->deleteItem('vta_appsys_referencedata', $key);
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }


    function getASingleCourse(string $courseId): ?array
    {
        $client = ServiceRegistry::getDbClient(UserSession::$credentials);

        try {
            $key = [
                'pk' => 'COURSE',
                'sk' => $courseId,
            ];

            $item = $client->getItem('vta_appsys_referencedata', $key);
            AppLogger::debug($item);

            return $item;
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }


    function getCourses(GetCourseRequest $data)
    {
        $client = ServiceRegistry::getOpenSearchClient();

        try {
            $query = [
                'query' => [],
                'from' => $data->startLimit ?? 0,
                'size' => $data->itemCount ?? 10,
            ];

            $termCondition = [];
            if (!empty($data->term)) {
                $termCondition['match_phrase'] = [
                    'courseName' => $data->term,
                ];
            } else {
                $termCondition['match_all'] = new stdClass();
            }

            if (isset($data->filters) && $data->filters !== null) {
                $query['query']['bool'] = [
                    'must' => [
                        $termCondition
                    ],
                ];

                foreach ($data->filters as $key => $value) {
                    if (!empty($value)) {
                        $query['query']['bool']['filter'][] = [
                            'term' => [
                                $key => $value,
                            ],
                        ];
                    }
                }
            } else {
                $query['query'][] = $termCondition;
            }


            AppLogger::debug($query);

            $result = $client->search("appsys_courses", $query);
            return $result;
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }

    function getCenters(GetCenterRequest $data)
    {
        $client = ServiceRegistry::getOpenSearchClient();

        try {
            $query = [
                'query' => [],
            ];

            $termCondition = [];
            if (!empty($data->term)) {
                $termCondition['match_phrase'] = [
                    'centerName' => $data->term,
                ];
            } else {
                $termCondition['match_all'] = new stdClass();
            }

            if (isset($data->filters) && $data->filters !== null) {
                $query['query']['bool'] = [
                    'must' => [
                        $termCondition
                    ],
                ];

                foreach ($data->filters as $key => $value) {
                    if (!empty($value)) {
                        $query['query']['bool']['filter'][] = [
                            'term' => [
                                $key => $value,
                            ],
                        ];
                    }
                }
            } else {
                $query['query'][] = $termCondition;
            }
            AppLogger::debug($query);

            $result = $client->search("appsys_centers", $query);
            return $result;
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }
}
