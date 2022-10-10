<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

class HashUtility {
  /**
   * @param string ...$parts
   */
  public static function compareHash(string $hash, string $secret, ...$parts): bool {
    return hash_equals($hash, self::getHash($secret, ...$parts));
  }

  /**
   * @param string ...$parts
   */
  public static function getHash(string $secret, ...$parts): string {
    $hashing = $secret;
    foreach ($parts as $part) {
      $hashing .= $part;
    }
    $hashing .= $secret;

    return sha1($hashing);
  }
}
