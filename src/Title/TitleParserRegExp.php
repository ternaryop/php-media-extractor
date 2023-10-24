<?php

namespace Ternaryop\MediaExtractor\Title;

class TitleParserRegExp {
  public string $pattern;
  public string $replacer;

  function __construct(string $pattern, string $replacer) {
    $this->pattern = "#$pattern#";
    $this->replacer = $replacer;
  }
}
