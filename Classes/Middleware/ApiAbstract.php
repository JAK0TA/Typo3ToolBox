<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Middleware;

use JAKOTA\Typo3ToolBox\Domain\Model\Api\Response;

abstract class ApiAbstract {
  protected function notAuthorized(Response $response, string $message = 'Not authorized'): Response {
    header('HTTP/1.1 401 Unauthorized');
    $response->errorCode = 401;
    $response->errorMessage = $message;

    return $response;
  }

  protected function serverError(Response $response, string $message): Response {
    header('HTTP/1.1 500 Internal Server Error');
    $response->errorCode = 500;
    $response->errorMessage = $message;

    return $response;
  }
}
