<?php

namespace App\Utils;

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
        $accessKey = $this->config['esUsername'];
        $secretKey = $this->config['esPassword'];
        $region = $this->config['region'];
        $service = 'es';

        $endpoint = $this->endpoint;
        $url = "{$endpoint}/{$index}/_search";

        $credentials = new Credentials($accessKey, $secretKey);
        $signer = new SignatureV4($service, $region);

        $request = new Request('POST', $url, [
            'Content-Type' => 'application/json'
        ], json_encode($searchRequest));

        $signedRequest = $signer->signRequest($request, $credentials);

        $response = $this->httpClient->send($signedRequest);

        $result = json_decode($response->getBody()->getContents(), true);

        $totalItemCount = $result['hits']['total']['value'] ?? 0;
        $hits = $result['hits']['hits'] ?? [];
        $records = array_map(fn($item) => $item["_source"], $hits);
        $startFrom = $searchRequest['from'] ?? 0;
        $endLimit = $startFrom + count($records);

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
    public function putDocument(string $index, array $document, ?string $id): array
    {
        $url = $id
            ? "{$this->endpoint}/{$index}/_doc/{$id}" // Put with specific ID
            : "{$this->endpoint}/{$index}/_doc";      // Post to auto-generate ID

        $accessKey = $this->config['esAccess'];
        $secretKey = $this->config['esSecret'];
        $region = $this->config['region'];
        $service = 'es';

        $credentials = new Credentials($accessKey, $secretKey);
        $signer = new SignatureV4($service, $region);

        $method = $id ? 'PUT' : 'POST';

        $request = new Request($method, $url, [
            'Content-Type' => 'application/json'
        ], json_encode($$document));

        $signedRequest = $signer->signRequest($request, $credentials);

        $response = $this->httpClient->send($signedRequest);

        return json_decode($response->getBody()->getContents(), true);
    }
}
