<?php

return [
    'userPoolId' => getenv('USER_POOL_ID'),
    'identityPoolId' => getenv('IDENTITY_POOL_ID'),
    'clientId' => getenv('APP_CLIENT_ID'),
    'region' => getenv('REGION'),
    'elasticEndpoint' => getenv('ES_ENDPOINT'),
    'esAccess' => getenv('ES_USER'),
    'esSecret' => getenv('ES_PASS'),
];
