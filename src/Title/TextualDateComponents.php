<?php

declare(strict_types=1);

namespace Ternaryop\MediaExtractor\Title;

/**
 * MatchedPosition contains the string and its position inside a more large string
 * @phpstan-type MatchedPosition array{string, int}
 */
class TextualDateComponents {
    /**
     * handle dates in the form Jan 10, 2010 or January 10 2010 or Jan 15
     * @param string $text the string to parse
     * @return ?DateComponents date components on success, null otherwise
     */
    static function extract(string $text): ?DateComponents {
      $pattern = "/\s?(?:[-|,]|\bon\b)?\s?(\d+)*(?:st|ns|rd|th)?\s?\(?(jan\w*|feb\w*|mar\w*|apr\w*|may\w*|jun\w*|jul\w*|aug\w*|sep\w*|oct\w*|nov\w*|dec\w*)(?!.*(?=jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec))[^0-9]*([0-9]*)[^0-9]*([0-9]*)\)?.*/i";
      $result = preg_match($pattern, $text, $m, PREG_OFFSET_CAPTURE);
      if ($result == 0) {
        return null;
      }

      $dayIndex = 3;
      $monthIndex = 2;
      $yearIndex = 4;
      $expectedGroupCount = 5;

      if (self::containsDateDDMonthYear($m)) {
          $dayIndex = 1;
          $monthIndex = 2;
          $yearIndex = 3;
          $expectedGroupCount = 5;
      } else if (!DateComponents::containsDateMatch($m)) {
          return null;
      }

      $dc = new DateComponents();
      $dc->format = DateComponents::TEXTUAL_FORMAT;

      $dc->month = DateComponents::indexOfMonthFromShort(strtolower($m[$monthIndex][0]));
      $dc->day = empty($m[$dayIndex][0]) ? 0 : intval($m[$dayIndex][0]);
      // The date could have the form February 2011 so the day contains the year
      if ($dc->day > DateComponents::YEAR_2000) {
        $dc->year = $dc->day;
        $dc->day = 0;
      } else {
        if (count($m) == $expectedGroupCount && !empty($m[$yearIndex][0])) {
          $dc->year = intval($m[$yearIndex][0]);
        }
      }
      $dc->startDatePosition = intval($m[0][1]);
      $endPosition = self::endPositionComponent(array($m[$monthIndex], $m[$dayIndex], $m[$yearIndex]));
      $dc->endDatePosition = intval($m[0][1]) + $endPosition[1] + strlen($endPosition[0]);
      return $dc;
    }

  /**
   * @param array<MatchedPosition> $arr
   * @return MatchedPosition the end position
   */
    private static function endPositionComponent(array $arr): array {
      $component = $arr[0];

      foreach ($arr as $v) {
        if ($v[1] > $component[1]) {
          $component = $v;
        }
      }
      return $component;
    }

    /**
     * Check if contains date in the form 12th November 2011
     * @param array<MatchedPosition> $m the m
     * @return bool if contains date component, false otherwise
     */
    private static function containsDateDDMonthYear(array $m): bool {
      return count($m) == 5
          && !empty($m[1][0])
          && DateComponents::isMonthName($m[2][0])
          && !empty($m[3][0])
          && empty($m[4][0]);
    }
}

