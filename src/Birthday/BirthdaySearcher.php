<?php

declare(strict_types=1);

namespace Ternaryop\MediaExtractor\Birthday;

interface BirthdaySearcher {
  public function searchBirthday(string $name): ?Birthdate;
  public function sourceName(): string;
}


