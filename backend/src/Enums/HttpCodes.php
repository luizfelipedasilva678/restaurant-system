<?php

namespace App\Enums;

enum HttpCodes: int
{
  case HTTP_OK = 200;
  case HTTP_CREATED = 201;
  case HTTP_BAD_REQUEST = 400;
  case HTTP_UNAUTHORIZED = 401;
  case HTTP_FORBIDDEN = 403;
  case HTTP_NOT_FOUND = 404;
  case HTTP_METHOD_NOT_ALLOWED = 405;
  case HTTP_SERVER_ERROR = 500;
}
