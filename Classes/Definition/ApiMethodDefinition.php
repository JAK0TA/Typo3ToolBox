<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Definition;

enum ApiMethodDefinition {
  case CONNECT;

  case COPY;

  case DELETE;

  case GET;

  case HEAD;

  case LOCK;

  case MKCOL;

  case MOVE;

  case OPTIONS;

  case PATCH;

  case POST;

  case PROPFIND;

  case PROPPATCH;

  case PUT;

  case QUERY;

  case REPORT;

  case TRACE;

  case UNLOCK;
}
