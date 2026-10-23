<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Middleware;

use JAKOTA\Typo3ToolBox\Domain\Model\Api\ApiResponse;

/**
 * @deprecated this class will be removed, use MiddlewareApiAbstract::class for APIs instead
 */
abstract class ApiAbstract {
  protected function notAuthorized(ApiResponse $response, string $message = 'Not authorized'): ApiResponse {
    header('HTTP/1.1 401 Unauthorized');
    $response->errorCode = 401;
    $response->errorMessage = $message;

    return $response;
  }

  protected function serverError(ApiResponse $response, string $message): ApiResponse {
    header('HTTP/1.1 500 Internal Server Error');
    $response->errorCode = 500;
    $response->errorMessage = $message;

    return $response;
  }
}
