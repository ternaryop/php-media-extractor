<?php

declare(strict_types=1);

namespace Ternaryop\MediaExtractor\Birthday;

use DateTime;
use Ternaryop\PhotoshelfUtil\Html\HtmlUtil;

class FashionModelDirectoryBirthdaySearcher implements BirthdaySearcher {
  public function searchBirthday(string $name): ?Birthdate {
    $cleanName = strtolower($name);
    $cleanName = str_replace("-", "_", $cleanName);
    $cleanName = str_replace(" ", "_", $cleanName);
    $cleanName = str_replace("\"", "", $cleanName);
    $url = "https://www.fashionmodeldirectory.com/models/" . $cleanName . "/";
    $htmlText = HtmlUtil::downloadHtml($url);

    if (!preg_match("/<div itemprop=\"birthDate\">Born ([a-z]*) (\d{1,2}),? (\d{4})<\/div>/i", $htmlText, $m)) {
        return null;
    }
    $textDate = $m[1] . " " . $m[2] . " " . $m[3];
    $dateTime = DateTime::createFromFormat('M d Y', $textDate);
    return Birthdate::createFromName(strtolower($name),
      $dateTime === false ? null : $dateTime,
      $this->sourceName());
  }

  public function sourceName(): string {
    return "fashionmodeldirectory.com";
  }
}


