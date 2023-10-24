<?php
namespace Ternaryop\MediaExtractor\Birthday;

interface BirthdaySearcher {
  public function searchBirthday(string $name): ?Birthdate;
  public function sourceName(): string;
}


