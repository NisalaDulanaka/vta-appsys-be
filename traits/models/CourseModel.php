<?php

use Aws\DynamoDb\Exception\DynamoDbException;
use Ramsey\Uuid\Uuid;

use App\Utils\ServiceRegistry;
use App\Utils\UserSession;
use App\Utils\AppLogger;

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
            $courseId = Uuid::uuid4();
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');

            $item = [
                'pk' => 'COURSE',
                'sk' => $courseId,
                'courseId' => $courseId,
                'courseName' => $data->courseName,
                'nvqLevel' => $data->nvqLevel,
                'courseType' => $data->courseType,
                'centers' => $data->centers,
                'createdAt' => $timestamp,
                'updatedAt' => $timestamp,
            ];

            $client->putItem('vta_appsys_referencedata', $item);
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
            ];

            if (!empty($data->term))
            {
                $query['query']['match_phrase'] = [
                    'courseName' => $data->term,
                ];
            } else {
                $query['query']['match_all'] = new stdClass();
            }

            $result = $client->search("appsys_courses", $query);
            return $result;
        } catch (DynamoDbException $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }
}
