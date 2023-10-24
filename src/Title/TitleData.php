<?php

namespace Ternaryop\MediaExtractor\Title;

class TitleData {
  private TitleParserConfig $config;
  /**
   * @var array<string>
   */
  private array $who;
  private ?string $location;
  private ?string $city;
  /**
   * @var array<string>
   */
  private array $tags;
  private ?DateComponents $eventDate;

  function __construct(TitleParserConfig $titleParserConfig) {
    $this->config = $titleParserConfig;
    $this->who = [];
    $this->location = null;
    $this->city = null;
    $this->tags = [];
    $this->eventDate = null;
  }

  /**
   * @return array<string>
   */
  function getWho(): array {
    return $this->who;
  }

  /**
   * @param array<string> $arr
   */
  function setWho(array $arr): void {
    $this->who = $arr;
  }

  function setWhoFromString(string $string): void {
    $this->who = self::appendSurname(preg_split("/\s*(?:,|&|\band\b)\s*/i", trim($string)));
  }

  /**
   * @param array<string> $list
   * @return array<string>
   */
  public static function appendSurname(array $list): array {
    $listCount = count($list);
    if ($listCount < 2) {
      return $list;
    }
    // the last element always contains the surname
    $lastName = explode(' ', $list[$listCount - 1]);
    $count = count($lastName);
    if ($count == 1) {
      return $list;
    }
    $surname = $lastName[$count - 1];
    $surnameList = [];
    for ($i = 0; $i < ($listCount - 1); $i++) {
      $name = $list[$i];
      $hasSingleWord = strpos($name, ' ') === FALSE;
      if ($hasSingleWord && stripos($name, $surname) === FALSE) {
        $name = "$name $surname";
      }
      $surnameList[] = $name;
    }
    $surnameList[] = $list[$listCount - 1];
    return $surnameList;
  }

  function getLocation(): ?string {
    return $this->location;
  }

  function setLocation(?string $value): void {
    if ($value == null) {
      $this->location = null;
      return;
    }
    $location = trim((string)preg_replace("/[^\w\"']*$/", "", $value));

    if (empty($location)) {
      $this->location = null;
      return;
    }
    if ($this->hasLocationPrefix($location)) {
      $location = strtolower(substr($location, 0, 1)) . substr($location, 1);
    } else {
      $location = "at the $location";
    }
    $this->location = $location;
  }

  function getCity(): ?string {
    return $this->city;
  }

  function setCity(string $city): void {
    if ($city == null) {
      $this->city = null;
    } else {
      $this->city = $this->expandAbbreviation(trim($city));
    }
  }

  /**
   * @return array<string>
   */
  public function getTags(): array {
    return $this->tags;
  }

  /**
   * @param array<string> $tags
   * @return void
   */
  public function setTags(array $tags): void {
    $list = [];

    foreach ($tags as $tag1) {
      if ($tag1 == null) {
        continue;
      }
      $repl = preg_replace("/[0-9]*(st|nd|rd|th)?/", "", $tag1, 1);
      $tag = $repl && is_scalar($repl) ? trim($repl) : '';
      $tag = (string)preg_replace("/\"|'/", "", $tag);
      if (strlen($tag) > 0) {
        $list[] = $tag;
      }
    }
    $this->tags = $list;
  }

  function getEventDate(): ?DateComponents {
    return $this->eventDate;
  }

  function setEventDate(DateComponents $value): void {
    $this->eventDate = $value;
  }

  private function hasLocationPrefix(string $location): bool {
    foreach ($this->config->locationPrefixes as $lp) {
      if ($lp->hasLocationPrefix($location)) {
        return true;
      }
    }
    return false;
  }

  private function expandAbbreviation(string $city): ?string {
    if (empty($city)) {
      return null;
    }
    foreach ($this->config->cities as $key => $value) {
      if (strcasecmp($key, $city) == 0 || preg_match($value, $city) === 1) {
        return $key;
      }
    }
    return $city;
  }

  function toHtml(): string {
    return $this->format("<strong>", "</strong>", "<em>", "</em>");
  }

  /**
   * @param bool $includesHTML
   * @return array<string, mixed>
   */
  function toMap(bool $includesHTML = false): array {
    $map = [
      'who' => $this->who,
      'location' => $this->location,
      'city' => $this->city,
      'tags' => $this->tags,
      'eventDate' => [
        'day' => $this->eventDate?->day,
        'month' => $this->eventDate?->month,
        'year' => $this->eventDate?->year
      ]
    ];
    if ($includesHTML) {
      $map['html'] = $this->toHtml();
    }
    return $map;
  }

  function format(
    string $whoTagOpen,
    string $whoTagClose,
    string $descTagOpen,
    string $descTagClose
  ): string {
    $sb = $this->formatWho($whoTagOpen, $whoTagClose, $descTagOpen, $descTagClose);
    if ($this->location != null || $this->eventDate != null || $this->city != null) {
      if (strlen($sb) > 0) {
        $sb .= " ";
      }
      $sb .= $descTagOpen;
      if ($this->location != null) {
        $sb .= $this->location;
        if ($this->city == null) {
          $sb .= " ";
        } else {
          $sb .= ", ";
        }
      }
      if ($this->city != null) {
        $sb .= $this->city . " ";
      }
      if ($this->eventDate != null) {
        $sb .= "(" . $this->eventDate->format() . ")";
      }
      $sb .= $descTagClose;
    }
    return $sb;
  }

  function formatWho(
    string $whoTagOpen,
    string $whoTagClose,
    string $descTagOpen,
    string $descTagClose): string {
    $sb = '';
    if (count($this->who) == 0) {
      return $sb;
    }
    $appendSep = false;
    for ($i = 0; $i < count($this->who) - 1; $i++) {
      if ($appendSep) {
        $sb .= ", ";
      } else {
        $appendSep = true;
      }
      $sb .= $this->who[$i];
    }
    if (count($this->who) > 1) {
      $sb = $whoTagOpen . $sb;
      $sb .= $whoTagClose;
      $sb .= $descTagOpen;
      $sb .= " and ";
      $sb .= $descTagClose;
    }
    $sb .= $whoTagOpen;
    $sb .= $this->who[count($this->who) - 1];
    $sb .= $whoTagClose;
    return $sb;
  }

}


