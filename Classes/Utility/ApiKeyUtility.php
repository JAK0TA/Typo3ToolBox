<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

class ApiKeyUtility {
  public static function generateApiKey(): string {
    $salt = 'J7ZNg1KT29wijJDzCKpf8q9wunRgZ2M2ExKydc6pMnmpKNG4nTksjaTficheP5CE';
    $date = date('D M d, Y G:i');

    $dateParts = str_split($date, 4);
    $saltParts = str_split($salt, 2);

    $merged = array_merge($dateParts, $saltParts);

    shuffle($merged);

    return md5(implode($merged));
  }
}
