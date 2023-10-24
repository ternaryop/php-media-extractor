<?php

namespace Ternaryop\MediaExtractor\Title;

use DateTime;

class TitleDateParams {
  private DateTime $currentDate;
  private bool $swapDayMonth = false;
  private bool $checkDateInTheFuture = false;
  private bool $checkDayMonthMatch = false;

  public function getCurrentDate(): DateTime {
    return $this->currentDate;
  }

  public function setCurrentDate(DateTime $currentDate): void {
    $this->currentDate = $currentDate;
  }

  public function isSwapDayMonth(): bool {
    return $this->swapDayMonth;
  }

  public function setSwapDayMonth(bool $swapDayMonth): void {
    $this->swapDayMonth = $swapDayMonth;
  }

  public function isCheckDateInTheFuture(): bool {
    return $this->checkDateInTheFuture;
  }

  public function setCheckDateInTheFuture(bool $checkDateInTheFuture): void {
    $this->checkDateInTheFuture = $checkDateInTheFuture;
  }

  public function isCheckDayMonthMatch(): bool {
    return $this->checkDayMonthMatch;
  }

  public function setCheckDayMonthMatch(bool $checkDayMonthMatch): void {
    $this->checkDayMonthMatch = $checkDayMonthMatch;
  }
}

