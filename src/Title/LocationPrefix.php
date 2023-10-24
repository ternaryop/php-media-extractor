<?php

namespace Ternaryop\MediaExtractor\Title;

/**
 * Created by dave on 04/12/17.
 * Use regular expression to match location prefix
 */
class LocationPrefix {
  private string $pattern;

  function __construct(string $regExp) {
    $this->pattern = "/$regExp/i";
  }

  function hasLocationPrefix(string $location): bool {
    return preg_match($this->pattern, $location) == 1;
  }

  function removePrefix(string $target): string {
    return preg_replace($this->pattern, "", $target, 1) ?: '';
  }
}
