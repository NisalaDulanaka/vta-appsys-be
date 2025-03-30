<?php

use Aws\DynamoDb\DynamoDbClient;

/**
 * This class is responsible for generating and maintaining
 * resource heavy instances of external libraries
 */
class ServiceRegistry
{
    private static ?DynamoDbClient $dynamoClient = null;
    private static ?ElasticClient $elasticClient = null;

    public static function getDbClient(?array $credentials = null): DynamoDbClient
    {
        if (ServiceRegistry::$dynamoClient === null) {
            ServiceRegistry::$dynamoClient = new DynamoDbClient([
                'region'  => 'us-east-2',
                'version' => 'latest',
                'credentials' => $credentials,
            ]);
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
