<?php

namespace Utils;

use Aws\Credentials\Credentials;
use Aws\Signature\SignatureV4;
use GuzzleHttp\Psr7\Request;
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
        $accessKey = $this->config['esAccess'];
        $secretKey = $this->config['esSecret'];

        AppLogger::debug($this->config);

        $endpoint = $this->endpoint;
        $url = "{$endpoint}/{$index}/_search";

        $request = new Request('POST', $url, [
            'auth' => [$accessKey, $secretKey],
            'Content-Type' => 'application/json'
        ], json_encode($searchRequest));

        $response = $this->httpClient->send($request);

        $result = json_decode($response->getBody()->getContents(), true);

        $totalItemCount = $result['hits']['total']['value'] ?? 0;
        $hits = $result['hits']['hits'] ?? [];
        $records = array_map(fn($item) => $item["_source"], $hits);
        $startFrom = $searchRequest['from'] ?? 0;
        $endLimit = $startFrom + count($records);

        AppLogger::debug($result);

        return [
            'records' => $records,
            'totalItemCount' => $totalItemCount,
            'endLimit' => $endLimit
        ];
    }

    /**
     * Add or update a document in an OpenSearch index.
     * @param string $index The name of the index
     * @param array $document The document data to be indexed
     * @param string|null $id Optional document ID (if null, OpenSearch will auto-generate one)
     */
    public function putDocument(string $index, array $document, ?string $id = null): array
    {
        $url = empty($id)
            ? "{$this->endpoint}/{$index}/_doc" // Post to auto-generate ID
            : "{$this->endpoint}/{$index}/_doc/{$id}"; // Put with specific ID

        $accessKey = $this->config['esAccess'];
        $secretKey = $this->config['esSecret'];

        $method = $id ? 'PUT' : 'POST';

        $request = new Request($method, $url, [
            'auth' => [$accessKey, $secretKey],
            'Content-Type' => 'application/json'
        ], json_encode($document));
        AppLogger::debug([
            'url' => $url,
            'method' => $method,
            'document' => $document,
            'id' => $id,
        ]);

        $response = $this->httpClient->send($request);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function deleteByQuery(string $index, array $query): array
    {
        $url = "{$this->endpoint}/{$index}/_delete_by_query";

        $accessKey = $this->config['esAccess'];
        $secretKey = $this->config['esSecret'];

        $request = new Request('POST', $url, [
            'auth' => [$accessKey, $secretKey],
            'Content-Type' => 'application/json'
        ], json_encode(['query' => $query]));

        AppLogger::debug([
            'url' => $url,
            'method' => 'POST',
            'query' => $query,
        ]);

        try {
            $response = $this->httpClient->send($request);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            AppLogger::error("Elasticsearch deleteByQuery error: " . $e->getMessage());
            throw $e;
        }
    }
}
