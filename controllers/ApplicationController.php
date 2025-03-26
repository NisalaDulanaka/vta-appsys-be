<?php
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

require_once("./utils/UserSession.php");

class ApplicationController extends Controller
{
    public function credTest()
    {
        $client = new DynamoDbClient([
            'region'  => 'us-east-2',
            'version' => 'latest',
            'credentials' => UserSession::$credentials,
        ]);
        $marshaler = new Marshaler();
        
        try {
            $item = [
                'pk' => 'APPLICATION',
                'sk' => '74d330ae-f1ce-4480-a117-05b21b152d14',
                'email' => 'john@example.com'
            ];
            
            $params = [
                'TableName' => 'vta_appsys_applications',
                'Item' => $marshaler->marshalItem($item),
            ];

            $client->putItem($params);

            return [
                "message" => "Application saved successfully",
            ];
        } catch (DynamoDbException $e) {
            throw $e;
        }
    }
}
