<?php

require 'vendor/autoload.php';

use App\Utils\AppLogger;
use App\Utils\ServiceRegistry;

return function (array $event) {
    $dbClient = ServiceRegistry::getDbClient();
    $records = $dbClient->unMarshallRecords($event);

    foreach ($records as $record) {
        try {
            $shouldUpdate = empty($record['oldImage']) || $record['eventName'] === 'MODIFY';

            if (!$shouldUpdate) {
                continue;
            }

            $indexName = 'appsys_applications';
            $client = ServiceRegistry::getOpenSearchClient();

            $client->putDocument($indexName, $record['newImage'], $record['newImage']['applicationId']);
        } catch (Exception $e) {
            AppLogger::error($e->__toString());
            throw $e;
        }
    }
};

