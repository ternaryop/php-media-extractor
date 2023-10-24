<?php

namespace Ternaryop\MediaExtractor\Birthday;

use DateTime;
use Ternaryop\PhotoshelfUtil\Html\HtmlUtil;

class FamousBirthdaySearcher implements BirthdaySearcher {
  public function searchBirthday(string $name): ?Birthdate {
    $cleanName = strtolower($name);
    $cleanName = str_replace("_", "-", $cleanName);
    $cleanName = str_replace(" ", "-", $cleanName);
    $cleanName = str_replace("\"", "", $cleanName);
    $url = "https://www.famousbirthdays.com/people/" . $cleanName . ".html";
    $htmlText = HtmlUtil::downloadHtml($url);

    if (!preg_match("/Birthday.*?<span.*?>(.*?)<\/span>.*?(\d+).*<a.*?>(\d+)<\/a>/m", $htmlText, $m)) {
      return null;
    }
    $textDate = $m[1] . " " . $m[2] . " " . $m[3];
    $dateTime = DateTime::createFromFormat('M d Y', $textDate);
    return Birthdate::createFromName(strtolower($name),
      $dateTime === false ? null : $dateTime,
      $this->sourceName());
  }

  public function sourceName(): string {
    return "famousbirthdays.com";
  }
}


