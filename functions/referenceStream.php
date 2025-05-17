<?php

require 'vendor/autoload.php';

use Utils\AppLogger;
use Utils\ServiceRegistry;

return function (array $event) {
    $dbClient = ServiceRegistry::getDbClient();
    $records = $dbClient->unMarshallRecords($event);

    foreach ($records as $record) {
        try {
            AppLogger::debug($record);
            $client = ServiceRegistry::getOpenSearchClient();
            
            if ($record['eventName'] === 'REMOVE' && !empty($record['oldImage']['courseId'])) {
                AppLogger::debug([
                    "message" => "deleting item from elastic search"
                ]);
                $client->deleteByQuery('appsys_courses', [
                    'term' => ['courseId' => $record['oldImage']['courseId']]
                ]);
                return;
            }

            $shouldUpdate = empty($record['oldImage']) || $record['eventName'] === 'MODIFY';
            if (!$shouldUpdate) {
                continue;
            }

            $indexName = 'appsys_courses';
            $id = $record['newImage']['courseId'];
            if ($record['newImage']['pk'] === 'CENTER') {
                $indexName = 'appsys_centers';
                $id = $record['newImage']['centerId'];
            }

            $client->putDocument($indexName, $record['newImage'], $id);
        } catch (Exception $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }
};
