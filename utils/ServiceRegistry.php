<?php

namespace App\Utils;

use App\Utils\ElasticClient;

/**
 * This class is responsible for generating and maintaining
 * resource heavy instances of external libraries
 */
class ServiceRegistry
{
    private static ?DbHelper $dynamoClient = null;
    private static ?ElasticClient $elasticClient = null;

    public static function getDbClient(?array $credentials = null): DbHelper
    {
        if (ServiceRegistry::$dynamoClient === null) {
            ServiceRegistry::$dynamoClient = new DbHelper($credentials);
        }

        return ServiceRegistry::$dynamoClient;
    }

    public static function getOpenSearchClient(): ElasticClient
    {
        if (ServiceRegistry::$elasticClient === null)
        {
            ServiceRegistry::$elasticClient = new ElasticClient();
        }

        return ServiceRegistry::$elasticClient;
    }
}
