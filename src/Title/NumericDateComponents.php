<?php

declare(strict_types=1);

namespace Ternaryop\MediaExtractor\Title;

class NumericDateComponents {
  /**
   * @param string $text the string to parse
   * @return ?DateComponents date components on success, null otherwise
   */
  static function extract(string $text): ?DateComponents {
    $dc = self::extractMostCompleteDate($text);
    if ($dc != null) {
        return $dc;
    }
    $dc = self::extractYear($text);
    if ($dc != null) {
        return $dc;
    }
    return self::extractIso8601Date($text);
  }

  /**
   * Extract dates in the form dd/dd/dd?? or (dd/dd/??) or (dddd)
   * @return ?DateComponents date components on success, null otherwise
   */
  private static function extractMostCompleteDate(string $text): ?DateComponents {
    $result = preg_match("/.*\D(\d{1,2})\s*\D\s*(\d{1,2})\s*\D\s*(\d{2}|\d{4})\b/", $text, $m, PREG_OFFSET_CAPTURE);
    if ($result !== false && count($m) > 1) {
        return new DateComponents(
          intval($m[1][0]),
          intval($m[2][0]),
          intval($m[3][0]),
          intval($m[1][1]),
          DateComponents::NUMERIC_FORMAT);
    }
    return null;
  }

  /**
   * Extract year starting with 2 (i.e. 2ddd)
   * @return ?DateComponents date components on success, null otherwise
   */
  private static function extractYear(string $text): ?DateComponents {
      $result = preg_match("/\(\s*(2\d{3})\s*\)/", $text, $m, PREG_OFFSET_CAPTURE);
      if ($result == 0) {
        return null;
      }
      return new DateComponents(
        -1,
        -1,
        intval($m[1][0]),
        intval($m[0][1]),
        DateComponents::NUMERIC_FORMAT);
  }

  private static function extractIso8601Date(string $text): ?DateComponents {
      $result = preg_match("/.*\D(\d{4})\s*\D\s*(\d{1,2})\s*\D\s*(\d{2})\b/", $text, $m, PREG_OFFSET_CAPTURE);
      if ($result == 0) {
        return null;
      }
      return new DateComponents(
        intval($m[3][0]),
        intval($m[2][0]),
        intval($m[1][0]),
        intval($m[1][1]),
        DateComponents::NUMERIC_FORMAT);
  }
}


