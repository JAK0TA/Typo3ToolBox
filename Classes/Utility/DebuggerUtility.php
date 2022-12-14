<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace JAKOTA\Typo3ToolBox\Utility;

use Doctrine\SqlFormatter\SqlFormatter;
use JAKOTA\Typo3ToolBox\SqlFormatter\CustomHtmlHighlighter;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;

class DebuggerUtility extends \TYPO3\CMS\Extbase\Utility\DebuggerUtility {
  /**
   * @param Query|QueryBuilder $query
   */
  public static function debugQuery($query, string $title = 'SQL Debug'): void {
    if ($query instanceof \TYPO3\CMS\Extbase\Persistence\Generic\Query) {
      $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
      if (version_compare($typo3Version->getVersion(), '10.4.0') >= 0) {
        $queryParser = GeneralUtility::makeInstance(Typo3DbQueryParser::class);
      } else {
        $ObjectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $queryParser = $ObjectManager->get(Typo3DbQueryParser::class);
      }

      $query = $queryParser->convertQueryToDoctrineQueryBuilder($query);
    }

    $sql = $query->getSQL();
    $params = $query->getParameters();

    self::renderDebug($sql, $params, $title);
  }

  /**
   * @param string[] $params
   */
  private static function renderDebug(string $sql, array $params, string $title): void {
    $sql = self::replaceParameters($sql, $params);

    $highlighter = new CustomHtmlHighlighter();
    $formatedSql = (new SqlFormatter($highlighter))->format($sql);

    $css = '
      <style>
        .extbase-debugger{display:block;text-align:left;background:#2a2a2a;border:1px solid #2a2a2a;box-shadow:0 3px 0 rgba(0,0,0,.5);color:#000;margin:20px;overflow:hidden;border-radius:4px}
        .extbase-debugger-floating{position:relative;z-index:99990}
        .extbase-debugger-top{background:#444;font-size:12px;font-family:monospace;color:#f1f1f1;padding:6px 15px}
        .extbase-debugger-center{padding:0 15px;margin:15px 0;}
        .extbase-debugger-center.bg-black{background: #2a2a2a !important}
        .extbase-debugger-center,.extbase-debugger-center .extbase-debug-string,.extbase-debugger-center a,.extbase-debugger-center p,.extbase-debugger-center pre,.extbase-debugger-center strong{font-size:12px;font-weight:400;font-family:monospace;line-height:20px;color:#f1f1f1}
        .extbase-debugger-center pre{background-color:transparent;margin:0;padding:0;border:0;word-wrap:break-word;color:#999}
      </style>
    ';

    $output = '
      <div class="extbase-debugger extbase-debugger-floating">
        <div class="extbase-debugger-top">'.htmlspecialchars($title, ENT_COMPAT).'</div>
        <div class="extbase-debugger-center bg-black">'.$formatedSql.'</div>
      </div>
    ';

    echo $css.$output;
  }

  /**
   * @param array<string, array<float|int|string>|int|string> $dcValues
   */
  private static function replaceParameters(string $sql, array $dcValues): string {
    $dcValuesFull = [];
    foreach ($dcValues as $k => $v) {
      if (is_array($v)) {
        foreach ($v as &$n) {
          if (!is_numeric($n)) {
            $n = "'".addslashes($n)."'";
          }
        }
        $v = implode(',', $v);
      } elseif (!is_numeric($v)) {
        $v = "'".addslashes($v)."'";
      }
      $dcValuesFull[":{$k}"] = $v;
    }

    uksort($dcValuesFull, function ($a, $b) {
      return strlen($a) > strlen($b) ? -1 : 1;
    });

    return str_replace(array_keys($dcValuesFull), array_values($dcValuesFull), $sql);
  }
}
