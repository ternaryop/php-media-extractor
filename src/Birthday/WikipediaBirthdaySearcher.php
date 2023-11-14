<?php

declare(strict_types=1);

namespace Ternaryop\MediaExtractor\Birthday;

use DateTime;
use Ternaryop\PhotoshelfUtil\Html\HtmlUtil;

class WikipediaBirthdaySearcher implements BirthdaySearcher {
  public function searchBirthday(string $name): ?Birthdate {
    $cleanName = ucwords(strtolower($name));
    $cleanName = str_replace(" ", "_", $cleanName);
    $cleanName = str_replace("\"", "", $cleanName);
    $url = "https://en.wikipedia.org/wiki/" . $cleanName;
    $htmlText = HtmlUtil::downloadHtml($url);

    if (!preg_match("/class=.bday.>(\d{4})-(\d{2})-(\d{2})<\/span>/", $htmlText, $m)) {
      return null;
    }
    $textDate = $m[1] . " " . $m[2] . " " . $m[3];

    $dateTime = DateTime::createFromFormat('Y m d', $textDate);
    return Birthdate::createFromName(
      strtolower($name),
      $dateTime === false ? null : $dateTime,
      $this->sourceName());
  }

  public function sourceName(): string {
    return "wikipedia.org";
  }
}


