<?php

return [
    'userPoolId' => getenv('USER_POOL_ID'),
    'identityPoolId' => getenv('IDENTITY_POOL_ID'),
    'clientId' => getenv('APP_CLIENT_ID'),
    'region' => getenv('REGION'),
];
