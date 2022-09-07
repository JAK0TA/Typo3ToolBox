<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Domain\Model\Api;

class Response {
  /**
   * @var string
   */
  public $call;

  /**
   * @var mixed
   */
  public $data;

  /**
   * @var int
   */
  public $errorCode = 0;

  /**
   * @var string
   */
  public $errorMessage = '';

  /**
   * @var bool
   */
  public $success = false;

  public function __construct(string $call) {
    $this->call = $call;
  }
}
