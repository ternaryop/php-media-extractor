<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Ternaryop\MediaExtractor\Title\TitleDateParams;
use Ternaryop\MediaExtractor\Title\TitleParser;
use Ternaryop\MediaExtractor\Title\TitleParserConfig;

class ParserTestCase extends TestCase
{
  protected static TitleParser $titleParser;
  protected static TitleDateParams $titleDateParams;

  public static function setUpBeforeClass(): void
  {
    $titleParserConfig = new TitleParserConfig($_ENV['TITLE_PARSER_JSON_PATH']);
    self::$titleParser = new TitleParser($titleParserConfig);
  }

  public static function readFile(string $path): string {
    $content = file_get_contents($path);
    self::assertFalse($content === false, "Unable to read $path");

    return $content;
  }
}
