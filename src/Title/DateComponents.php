<?php
namespace Ternaryop\MediaExtractor\Title;

/**
 * Hold the date components day, month, year
 */
class DateComponents {
  const MONTH_COUNT = 12;
  const YEAR_2000 = 2000;
  const UNKNOWN_FORMAT = 0;
  const NUMERIC_FORMAT = 1;
  const TEXTUAL_FORMAT = 2;

  // At index 0 the full name (used also for formatting, capitalization is important)
  // From index 1 all possible short names
  const monthsNames = [
    [""],
    ["January", "Jan"],
    ["February", "Feb"],
    ["March", "Mar"],
    ["April", "Apr"],
    ["May", "May"],
    ["June", "Jun"],
    ["July", "Jul"],
    ["August", "Aug"],
    ["September", "Sep", "Sept"],
    ["October", "Oct"],
    ["November", "Nov"],
    ["December", "Dec"]
  ];

  public int $day;
  public int $month;
  public int $year;
  public int $startDatePosition;
  public int $endDatePosition;
  public int $format;

  public function __construct(
    int $day = 0,
    int $month = 0,
    int $year = -1,
    int $startDatePosition = -1,
    int $format = DateComponents::UNKNOWN_FORMAT) {
    $this->day = $day;
    $this->month = $month;
    $this->year = $year;
    $this->startDatePosition = $startDatePosition;
    $this->endDatePosition = -1;
    $this->format = $format;
  }

  function isDateInTheFuture(): bool {
    if ($this->month > self::MONTH_COUNT) {
        return false;
    }
    if ($this->day < 0 && $this->month < 0) {
        return false;
    }
    $strDate =sprintf('%04d%02d%02d', $this->year, $this->month, $this->day);
    $strNow = date('Ymd');
    return $strDate > $strNow;
  }

  function format(): string {
    $sb = '';
    // day could be not present for example "New York City, January 11"
    if ($this->day > 0) {
        $sb .= $this->day . " ";
    }
    if (0 < $this->month && $this->month <= count(self::monthsNames)) {
        $sb .= self::monthsNames[$this->month][0] . ", ";
    }
    $sb .= $this->year;
    return $sb;
  }

  function fixYear(int $yearToUse): void {
    // year not found on parsed text
    if ($this->year < 0) {
      $this->year = $yearToUse;
    } else {
      // we have a two-digits year
      if ($this->year < 100) {
          $this->year += self::YEAR_2000;
      }
      if ($this->year < self::YEAR_2000) {
          $this->year = $yearToUse;
      } else if ($this->year > $yearToUse) {
          $this->year = $yearToUse;
      }
    }
  }

  /**
   * Check if the matcher contains valid date components
   * @param array<string> $matcher the matcher
   * @return boolean true if contains date component, false otherwise
   */
  static function containsDateMatch(array $matcher): bool {
    return self::isMonthName($matcher[2][0]);
  }

  static function indexOfMonthFromShort(string $shortMonth): int {
    foreach (self::monthsNames as $index => $names) {
      foreach ($names as $name) {
        if (strcasecmp($name, $shortMonth) == 0) {
          return $index;
        }
      }
    }
    return -1;
  }

  static function isMonthName(string $month): bool {
    return self::indexOfMonthFromShort($month) != -1;
  }
}


