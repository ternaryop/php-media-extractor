<?php
namespace Ternaryop\MediaExtractor\Birthday;

class BirthdaySearcherService implements BirthdaySearcher {
  /**
   * @var array<BirthdaySearcher>
   */
  private array $searchers;

  function __construct() {
    $this->searchers = array(
      new FamousBirthdaySearcher,
      new FashionModelDirectoryBirthdaySearcher,
      new GoogleBirthdaySearcher,
      new WikipediaBirthdaySearcher
    );
  }

  public function searchBirthday(string $name): ?Birthdate {
    foreach ($this->searchers as $searcher) {
      $birthdate = $searcher->searchBirthday($name);
      if ($birthdate != null) {
        return $birthdate;
      }
    }
    return null;
  }

  public function sourceName(): string {
    return "controller";
  }
}


