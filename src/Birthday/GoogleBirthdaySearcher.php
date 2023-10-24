<?php
namespace Ternaryop\MediaExtractor\Birthday;

use DateTime;
use Ternaryop\PhotoshelfUtil\Html\HtmlUtil;

class GoogleBirthdaySearcher implements BirthdaySearcher {
  public function searchBirthday(string $name): ?Birthdate {
    $cleanName = http_build_query(array(
      "hl" => "en",
      "q" => str_replace('"', '', $name)
      ));
    $url = "https://www.google.com/search?" . $cleanName;
    $htmlText = HtmlUtil::htmlDocument(HtmlUtil::downloadHtml($url))->text();
    if (!preg_match("/Born: ([a-zA-Z]+ \\d{1,2}, \\d{4})/", $htmlText, $m)) {
      return null;
    }
    $textDate = $m[1];

    $dateTime = DateTime::createFromFormat('M d, Y', $textDate);
    return Birthdate::createFromName(
      strtolower($name),
      $dateTime === false ? null : $dateTime,
      $this->sourceName());
  }

  public function sourceName(): string {
    return "google.com";
  }
}


