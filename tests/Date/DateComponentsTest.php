<?php

declare(strict_types=1);

namespace Date;

use Ternaryop\MediaExtractor\Title\TitleDateParams;
use Tests\ParserTestCase;
use function PHPUnit\Framework\assertFalse;

class DateComponentsTest extends ParserTestCase
{
  public function testTitle(): void {
    $title = 'Sofie Rovenstine - Victoria\'s Secret models attended a pre-show fitting at the headquarters of Victoria\'s Secret in New York 01.11.2018 x17';
    $expectedTitle = '<strong>Sofie Rovenstine</strong> <em>at the Victoria\'s Secret models attended a pre-show fitting at the headquarters of Victoria\'s Secret, New York (1 November, 2018)</em>';

    $titleDateParams = new TitleDateParams();
    $titleDateParams->setCurrentDate(new \DateTime('2018-11-02'));
    $titleDateParams->setSwapDayMonth(false);
    $titleDateParams->setCheckDateInTheFuture(false);
    $titleDateParams->setCheckDayMonthMatch(false);

    $titleData = self::$titleParser->parseTitle($title, $titleDateParams);
    $formattedInput = $titleData->format("<strong>", "</strong>", "<em>", "</em>");
    $this->assertEquals($expectedTitle, $formattedInput);
  }

  public function testTitleSwap(): void {
    $title = 'Sofie Rovenstine - Victoria\'s Secret models attended a pre-show fitting at the headquarters of Victoria\'s Secret in New York 01.11.2018 x17';
    $expectedTitle = '<strong>Sofie Rovenstine</strong> <em>at the Victoria\'s Secret models attended a pre-show fitting at the headquarters of Victoria\'s Secret, New York (11 January, 2018)</em>';

    $titleDateParams = new TitleDateParams();
    $titleDateParams->setCurrentDate(new \DateTime('2018-11-02'));
    $titleDateParams->setSwapDayMonth(true);
    $titleDateParams->setCheckDateInTheFuture(false);
    $titleDateParams->setCheckDayMonthMatch(false);

    $titleData = self::$titleParser->parseTitle($title, $titleDateParams);
    $formattedInput = $titleData->format("<strong>", "</strong>", "<em>", "</em>");
    $this->assertEquals($expectedTitle, $formattedInput);
  }

  /**
   * @dataProvider providerTitles
   */
  public function testTitlesByProvider(string $title, string $expectedTitle): void {
    $titleData = self::$titleParser->parseTitle($title, self::$titleDateParams);
    $formattedInput = $titleData->format("<strong>", "</strong>", "<em>", "</em>");
    $this->assertEquals($expectedTitle, $formattedInput);
  }

  /**
   * @return array<string, string>
   */
  public static function providerTitles(): array {
    $input = preg_split("/\n/", self::readFile($_ENV['PROVIDER_TITLES_INPUT']));
    $results = preg_split("/\n/", self::readFile($_ENV['PROVIDER_TITLES_RESULTS']));

    assertFalse($input === false);
    assertFalse($results === false);

    // $input = [""];
    // $results = [""];

    self::$titleDateParams = new TitleDateParams();
    self::$titleDateParams->setCurrentDate(new \DateTime());
    self::$titleDateParams->setSwapDayMonth(false);
    self::$titleDateParams->setCheckDateInTheFuture(false);
    self::$titleDateParams->setCheckDayMonthMatch(false);

    $provider = [];
    $year = self::$titleDateParams->getCurrentDate()->format('Y');
    for ($i = 0; $i < count($input); $i++) {
      $resultLine = str_replace("%CURRENT_YEAR%", $year, $results[$i]);

      $provider[] = [$input[$i], $resultLine];
    }
    return $provider;
  }
}
