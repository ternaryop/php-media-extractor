<?php

namespace Ternaryop\MediaExtractor\Title;

class TitleParserConfig {
  /**
   * @var array<TitleParserRegExp>
   */
  public array $titleCleanerList;
  public string $titleParserPattern;
  /**
   * @var array<LocationPrefix>
   */
  public array $locationPrefixes;
  /**
   * @var array<string, string>
   */
  public array $cities;

  function __construct(string $configPath) {
    $string = file_get_contents($configPath);
    $jsonObject = json_decode((string)$string, true);

    $this->titleCleanerList = $this->createList($jsonObject, "titleCleaner", "regExprs");
    $this->titleParserPattern = "/" . $jsonObject["titleParserRegExp"] . "/i";
    $this->locationPrefixes = $this->readLocationPrefixes($jsonObject);
    $this->cities = $this->readCities($jsonObject);
  }

  /**
   * @param mixed $jsonObject
   * @return array<string, string>
   */
  private function readCities(mixed $jsonObject): array {
    $map = [];

    foreach ($jsonObject["cities"] as $key => $value) {
      $map[$key] = "/$value/";
    }
    return $map;
  }

  /**
   * @param mixed $jsonObject
   * @return array<LocationPrefix>
   */
  private function readLocationPrefixes(mixed $jsonObject): array {
    $arr = [];

    foreach ($jsonObject["locationPrefixes"] as $key => $value) {
      $arr[] = new LocationPrefix($value);
    }
    return $arr;
  }

  /**
   * @param mixed $jsonAssets
   * @param string $rootName
   * @param string $replacers
   * @return array<TitleParserRegExp>
   */
  function createList(mixed $jsonAssets, string $rootName, string $replacers): array {
      $list = [];
      $array = ($jsonAssets[$rootName])[$replacers];
      foreach ($array as $reArray) {
        $list[] = new TitleParserRegExp($reArray[0], $reArray[1]);
      }
      return $list;
  }

  /**
   * @param array<TitleParserRegExp> $titleParserRegExpList
   * @param string $input
   * @return string
   */
  function applyList(array $titleParserRegExpList, string $input): string {
    $result = $input;

    foreach ($titleParserRegExpList as $re) {
      $result = preg_replace($re->pattern, $re->replacer, $result) ?: $result;
    }
    return $result;
  }

}


