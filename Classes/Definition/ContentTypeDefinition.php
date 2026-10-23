<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Definition;

enum ContentTypeDefinition {
  case DEFAULT;

  case FILE_PLAIN;

  case IMAGE_JPEG;

  case JSON;

  case TEXT;

  case XML;
}
