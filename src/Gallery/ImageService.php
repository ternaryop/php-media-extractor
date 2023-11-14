<?php

declare(strict_types=1);

namespace Ternaryop\MediaExtractor\Gallery;

use DateTime;
use DateTimeInterface;
use QueryPath\DOMQuery;
use Ternaryop\MediaExtractor\DOMSelector\Image;
use Ternaryop\MediaExtractor\DOMSelector\ImageDOMSelectorFinder;
use Ternaryop\MediaExtractor\DOMSelector\PageChain;
use Ternaryop\MediaExtractor\DOMSelector\PostData;
use Ternaryop\MediaExtractor\DOMSelector\Selector;
use Ternaryop\PhotoshelfUtil\Html\HtmlUtil;
use Ternaryop\PhotoshelfUtil\Html\ParseUrlException;

class ImageService {
  private ImageDOMSelectorFinder $domSelectorFinder;

  function __construct(ImageDOMSelectorFinder $domSelectorFinder) {
    $this->domSelectorFinder = $domSelectorFinder;
  }

  public function getImageURL(string $documentUrl): string {
    $comp = HtmlUtil::parseUrlOrThrow($documentUrl);
    $baseuri = $comp['scheme'] . "://" . $comp['host'];

    $selector = $this->domSelectorFinder->getSelectorFromUrl($documentUrl);
    $image = $selector->getImage();
    $url = null;
    if ($image->getCss()) {
      $url = $this->urlFromCSS3Selector($image, $documentUrl);
    }
    if ($this->isEmpty($url) && $image->getRegExp()) {
      $url = $this->urlFromRegExp($selector, $documentUrl);
    }
    if ($this->isEmpty($url) && $image->getPageChain()) {
      $url = $this->urlFromChain($image, $documentUrl);
    }
    if ($this->isEmpty($url)) {
      $url = $documentUrl;
    }

    return HtmlUtil::encode_url_rfc_3986(HtmlUtil::absUrl($baseuri, $url));
  }

  private function isEmpty(?string $str): bool {
    return $str === null || empty(trim($str));
  }

  private function urlFromCSS3Selector(Image $image, string $documentUrl): string {
    $cssSelector = $image->getCss();
    $selAttr = $image->getSelAttr();
    return $this->getDocumentFromUrl($documentUrl)->find($cssSelector)->attr($selAttr);
  }

  private function urlFromChain(Image $image, string $documentUrl): ?string {
    return $this->getImageUrlFromPageSel($image->getPageChain(), $documentUrl);
  }

  private function urlFromRegExp(Selector $selector, string $documentUrl): string {
    $re = $selector->getImage()->getRegExp();
    $html = HtmlUtil::downloadHtml($documentUrl, $this->optionsFromSelector($selector));
    $html = preg_replace('/(\n|\r|\t)/', '', $html);
    if (preg_match('/' . $re . '/', (string)$html, $m)) {
      return $m[1];
    }
    throw new ParseUrlException("Unable to find image url for " . $documentUrl);
  }

  /**
   * Iterate all PageSelector to find the destination image url.
   * Every PageSelector moves to an intermediate document page
   * @param array<PageChain> $selectorInfoList the list to traverse
   * @param string $url the starting document url
   * @return string|null the imageUrl
   */
  private function getImageUrlFromPageSel(array $selectorInfoList, string $url): ?string {
    $imageUrl = $url;
    foreach ($selectorInfoList as $si) {
      $imageUrl = $this->getDocumentFromUrl($imageUrl)->find($si->getPageSel())->attr($si->getSelAttr());
    }
    return $imageUrl;
  }

  private function getDocumentFromUrl(string $url): DOMQuery {
    $domSelector = $this->domSelectorFinder->getSelectorFromUrl($url);
    $options = $this->optionsFromSelector($domSelector);
    return HtmlUtil::htmlDocument(HtmlUtil::downloadHtml($url, $options));
  }

  /**
   * @param Selector $selector
   * @return array{post_data: PostData|null, user_agent: string|null, cookie: string|null}
   */
  private function optionsFromSelector(Selector $selector): array {
    $image = $selector->getImage();
    $cookie = $image->getCookie();

    if ($cookie !== null) {
      $cookie_date = (new DateTime('+2 hours'))->format(DateTimeInterface::COOKIE);
      $cookie = str_ireplace('%cookie_date%', $cookie_date, $cookie);
    }

    return array(
      HtmlUtil::OPTION_POST_DATA => $image->getPostData(),
      HtmlUtil::OPTION_USER_AGENT => $selector->getUserAgent(),
      HtmlUtil::OPTION_COOKIE => $cookie
    );
  }
}


