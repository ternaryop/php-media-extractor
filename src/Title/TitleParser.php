<?php

declare(strict_types=1);

namespace Ternaryop\MediaExtractor\Title;

use Ternaryop\PhotoshelfUtil\String\StringUtil;

class TitleParser {
  private TitleParserConfig $config;
  private ?WhoExtractor $whoExtractor;

  function __construct(
    TitleParserConfig $titleParserConfig,
    WhoExtractor $whoExtractor = null) {
    $this->config = $titleParserConfig;
    $this->whoExtractor = $whoExtractor;
  }

  function parseTitle(
    string $sourceTitle,
    TitleDateParams $titleDateParams): TitleData {
    $title = $sourceTitle;
    $titleData = new TitleData($this->config);

    $title = $this->config->applyList($this->config->titleCleanerList, $title);
    $title = trim($title);
    $title = StringUtil::replaceUnicodeWithClosestAscii($title);
    $title = StringUtil::stripAccents($title);

    $title = $this->setWho($title, $titleData);

    $dateComponents = TitleDateComponents::extract($title, $titleDateParams);
    $this->setLocationAndCity($titleData, $this->parseLocation($title, $dateComponents));

    $titleData->setEventDate($dateComponents);
    $titleData->setTags($titleData->getWho());

    return $titleData;
  }

  private function setWho(string $sourceTitle, TitleData $titleData): string {
    $title = $sourceTitle;
    $result = preg_match($this->config->titleParserPattern, $title, $m);
    if ($result == 1) {
      $who = $m[1];
      $titleData->setWhoFromString(StringUtil::capitalizeAll($who));
      // remove the 'who' chunk and any not alphabetic character
      // (e.g. the dash used to separated "who" from location)
      if ($this->isLetter($m[2][0])) {
        $title = substr($title, strlen($who));
      } else {
        $title = substr($title, strlen($m[0]));
      }
    } else if ($this->whoExtractor) {
      $who = $this->whoExtractor->extractFromString($title);
      if ($who != null) {
        $titleData->setWhoFromString(StringUtil::capitalizeAll($who));
        $title = substr($title, strlen($who) + 1);
      }
    }
    if ($this->whoExtractor) {
      $titleData->setWho($this->whoExtractor->resolveAlias($titleData->getWho()));
    }
    return $title;
  }

  private function parseLocation(string $title, DateComponents $dateComponents): string {
    if ($dateComponents->startDatePosition < 0) {
      // no date found so use all substring as location
      return $title;
    }
    $remainingText = substr($title, 0, $dateComponents->startDatePosition);
    if ($dateComponents->endDatePosition > 0) {
      $endString = substr($title, $dateComponents->endDatePosition);
      // remove any initial separator
      $endString = preg_replace("/^\s?[-' .]/", "", $endString);

      $remainingText = $remainingText . $endString;
    }
    return $remainingText;
  }

  private function setLocationAndCity(TitleData $titleData, string $loc): void {
    // city names can be multi words so allow whitespaces
    $result = preg_match("/\s?(.*)?\s?\bin\b([a-z.\s']*).*$/i", $loc, $m);
    if ($result == 0) {
      $titleData->setLocation($loc);
    } else {
      $titleData->setLocation($m[1]);
      $titleData->setCity(trim($m[2]));
    }
  }

  function isLetter(string $ch): bool {
    $result = preg_match("/[a-z]/i", $ch);
    return $result == 1;
  }
}


