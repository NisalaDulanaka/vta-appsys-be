<?php

use GuzzleHttp\Client;

/**
 * AWS OpenSearch client wrapper for making authenticated search requests
 * Uses AWS Signature V4 for request signing when credentials are provided
 */
class ElasticClient 
{
    private array $config;
    private Client $httpClient;
    private string $endpoint;

    public function __construct() 
    {
        $this->config = require './config/config.php';
        $this->endpoint = $this->config['elasticEndpoint'];
        $this->httpClient = new Client();
    }

    /**
     * Execute a search query against an OpenSearch index
     * @param string $index The name of the index to search
     * @param array $searchRequest The Elasticsearch query body
     * @param CredentialsInterface|null $credentials Optional AWS credentials for request signing
     */
    public function search(string $index, array $searchRequest)
    {
        $url = "{$this->endpoint}/{$index}/_search";
        $username = $this->config['esUsername'];
        $password = $this->config['esPassword'];
        
        $response = $this->httpClient->request('POST', $url, [
            'auth' => [$username, $password],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($searchRequest),
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        return $result['hits']['hits'];
    }
}
