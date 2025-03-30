<?php

return [
    'userPoolId' => getenv('USER_POOL_ID'),
    'identityPoolId' => getenv('IDENTITY_POOL_ID'),
    'clientId' => getenv('APP_CLIENT_ID'),
    'region' => getenv('REGION'),
    'elasticEndpoint' => getenv('ES_ENDPOINT'),
    'esUsername' => getenv('ES_USER'),
    'esPassword' => getenv('ES_PASS'),
];
