<?php

namespace Utils;

enum HttpStatusCode: int
{
    case OK = 200;
    case CREATED = 201;
    case NO_CONTENT = 204;
    case BAD_REQUEST = 403;
    case UNAUTHORIZED = 401;
    case NOT_FOUND = 404;
    case INTERNAL_SERVER_ERROR = 500;
}
