<?php

declare(strict_types=1);

namespace Ternaryop\MediaExtractor\Title;

interface WhoExtractor {
  public function extractFromString(string $text): ?string;

  /**
   * @param array<string> $text
   * @return array<string>
   */
  public function resolveAlias(array $text): array;
}

