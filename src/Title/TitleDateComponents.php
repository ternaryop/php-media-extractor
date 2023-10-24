<?php

namespace Ternaryop\MediaExtractor\Title;

class TitleDateComponents {
  static function extract(
    string $text,
    TitleDateParams $titleDateParams
  ): DateComponents {
    $dc = NumericDateComponents::extract($text);
    if ($dc == null) {
      $dc = TextualDateComponents::extract($text);
    }
    if ($dc == null) {
      $dc = new DateComponents();
      $dc->year = intval($titleDateParams->getCurrentDate()->format('Y'));
      return $dc;
    }
    self::fix($dc, $titleDateParams);
    return $dc;
  }

  private static function fix(
    DateComponents $dateComponents,
    TitleDateParams $titleDateParams
  ): void {
    if ($dateComponents->month > DateComponents::MONTH_COUNT) {
      self::swapDayMonth($dateComponents);
    }
    $dateComponents->fixYear(intval($titleDateParams->getCurrentDate()->format('Y')));

    // maybe the components format is mm/dd/yyyy, so we switch day and month to try dd/mm/yyyy
    if ($titleDateParams->isSwapDayMonth() || ($titleDateParams->isCheckDateInTheFuture() && $dateComponents->isDateInTheFuture())) {
      self::swapDayMonth($dateComponents);
    } else if (self::isDayEqualsToCurrentMonth($dateComponents, $titleDateParams)) {
      self::swapDayMonth($dateComponents);
    }
  }

  private static function isDayEqualsToCurrentMonth(
    DateComponents $dateComponents,
    TitleDateParams $titleDateParams
  ): bool {
    if ($dateComponents->format != DateComponents::NUMERIC_FORMAT) {
      return false;
    }
    if (!$titleDateParams->isCheckDayMonthMatch()) {
      return false;
    }
    $nowYear = $titleDateParams->getCurrentDate()->format('Y');
    $nowMonth = $titleDateParams->getCurrentDate()->format('m');
    return $dateComponents->year == $nowYear && $dateComponents->day == $nowMonth;
  }

  private static function swapDayMonth(DateComponents $dateComponents): void {
    // if day isn't present doesn't swap
    // doesn't generate illegal month value if day > MONTH_COUNT
    if ($dateComponents->day == 0 || $dateComponents->day > DateComponents::MONTH_COUNT) {
      return;
    }
    $tmp = $dateComponents->month;
    $dateComponents->month = $dateComponents->day;
    $dateComponents->day = $tmp;
  }
}

