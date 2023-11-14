<?php

declare(strict_types=1);

namespace Ternaryop\MediaExtractor\DOMSelector;

class ImageDOMSelectorFinder {
  private DomSelectors $domSelectors;

  function __construct(DomSelectors $domSelectors) {
    $this->domSelectors = $domSelectors;
  }

  public function getSelectorFromURL(string $url): Selector {
    if ($url != null) {
      foreach ($this->domSelectors->getSelectors() as $selector) {
        $domainRE = "/" . str_replace("/", "\/", $selector->getUrlPattern()) . "/";
        if (preg_match($domainRE, $url)) {
          return $selector;
        }
      }
    }
    return new Selector("", null, new Image(), new Gallery());
  }
}

