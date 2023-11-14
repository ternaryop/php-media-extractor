<?php

declare(strict_types=1);

namespace Tests\Title;

use Ternaryop\MediaExtractor\Title\TitleDateParams;
use Tests\ParserTestCase;

class TitlesTest extends ParserTestCase
{
  public function testMultipleWho(): void {
    $title = 'Chloe, Amelia and Lauryn Goodman – ‘Chloe Goodmans’ New Cosmetics Collection in Brighton';
    $expectedTitle = "<strong>Chloe Goodman, Amelia Goodman</strong><em> and </em><strong>Lauryn Goodman</strong> <em>at the 'Chloe Goodmans' New Cosmetics Collection, Brighton (2019)</em>";

    $titleDateParams = new TitleDateParams();
    $titleDateParams->setCurrentDate(new \DateTime('2019-01-01'));

    $titleData = self::$titleParser->parseTitle($title, $titleDateParams);
    $formattedInput = $titleData->format("<strong>", "</strong>", "<em>", "</em>");
    $this->assertEquals($expectedTitle, $formattedInput);
  }
}
