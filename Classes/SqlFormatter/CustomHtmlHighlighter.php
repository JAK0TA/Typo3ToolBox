<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\DebuggerUtility\SqlFormatter;

use Doctrine\SqlFormatter\Token;
use const ENT_COMPAT;
use const ENT_IGNORE;
use function htmlentities;
use const PHP_EOL;
use function sprintf;
use function trim;

final class CustomHtmlHighlighter implements \Doctrine\SqlFormatter\Highlighter {
  public const HIGHLIGHT_PRE = 'pre';

  /** @var array<string, string> */
  private $htmlAttributes;

  /**
   * @param array<string, string> $htmlAttributes
   */
  public function __construct(array $htmlAttributes = []) {
    $this->htmlAttributes = $htmlAttributes + [
      self::HIGHLIGHT_QUOTE => 'style="color: #ce9178;"',
      self::HIGHLIGHT_BACKTICK_QUOTE => 'style="color: #87f3ff;"',
      self::HIGHLIGHT_RESERVED => 'style="font-weight: normal;"',
      self::HIGHLIGHT_BOUNDARY => '',
      self::HIGHLIGHT_NUMBER => 'style="color: #ce9178;"',
      self::HIGHLIGHT_WORD => 'style="color: #ebebeb;;"',
      self::HIGHLIGHT_ERROR => 'style="background-color: red;"',
      self::HIGHLIGHT_COMMENT => 'style="color: #aaa;"',
      self::HIGHLIGHT_VARIABLE => 'style="color: orange;"',
      self::HIGHLIGHT_PRE => 'style="background-color: #2a2a2a;;"',
    ];
  }

  public function attributes(int $type): ?string {
    if (!isset(self::TOKEN_TYPE_TO_HIGHLIGHT[$type])) {
      return null;
    }

    return $this->htmlAttributes[self::TOKEN_TYPE_TO_HIGHLIGHT[$type]];
  }

  public function highlightError(string $value): string {
    return sprintf(
      '%s<span %s>%s</span>',
      PHP_EOL,
      $this->htmlAttributes[self::HIGHLIGHT_ERROR],
      $value
    );
  }

  public function highlightErrorMessage(string $value): string {
    return $this->highlightError($value);
  }

  public function highlightToken(int $type, string $value): string {
    $value = htmlentities($value, ENT_COMPAT | ENT_IGNORE, 'UTF-8');

    if (Token::TOKEN_TYPE_BOUNDARY === $type && ('(' === $value || ')' === $value)) {
      return $value;
    }

    $attributes = $this->attributes($type);
    if (null === $attributes) {
      return $value;
    }

    return '<span '.$attributes.'>'.$value.'</span>';
  }

  public function output(string $string): string {
    $string = trim($string);

    return '<pre '.$this->htmlAttributes[self::HIGHLIGHT_PRE].'>'.$string.'</pre>';
  }
}
