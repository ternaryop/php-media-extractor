<?php

declare(strict_types=1);

namespace Ternaryop\MediaExtractor\Gallery;

use DateTime;
use QueryPath\DOMQuery;
use Ternaryop\MediaExtractor\DOMSelector\Gallery;
use Ternaryop\MediaExtractor\DOMSelector\ImageDOMSelectorFinder;
use Ternaryop\MediaExtractor\Title\TitleData;
use Ternaryop\MediaExtractor\Title\TitleDateParams;
use Ternaryop\MediaExtractor\Title\TitleParser;
use Ternaryop\PhotoshelfUtil\Html\HtmlUtil;
use Ternaryop\PhotoshelfUtil\String\StringUtil;

/**
 * @phpstan-import-type GalleryItem from GalleryItemBuilder
 */
class GalleryService {
  private ImageDOMSelectorFinder $domSelectorFinder;
  private TitleParser $titleParser;
  private GalleryItemBuilder $galleryItemBuilder;

  function __construct(
    ImageDOMSelectorFinder $domSelectorFinder,
    TitleParser $titleParser) {
    $this->domSelectorFinder = $domSelectorFinder;
    $this->titleParser = $titleParser;
    $this->galleryItemBuilder = new GalleryItemBuilder($this->domSelectorFinder);
  }

  /**
   * @param string $galleryUrl
   * @return array{domain: string, title: string, titleParsed: array<string, mixed>, gallery: array<GalleryItem>}
   */
  function read(string $galleryUrl): array {
    $content = HtmlUtil::downloadHtml($galleryUrl);
    $htmlDocument = HtmlUtil::htmlDocument($content);

    $comp = HtmlUtil::parseUrlOrThrow($galleryUrl);
    $baseuri = $comp['scheme'] . "://" . $comp['host'];

    $url = HtmlUtil::resolveUrl($galleryUrl, $htmlDocument);
    if ($url != $galleryUrl) {
      // file_put_contents('logs.txt', "The canonical url for $galleryUrl is $url" . PHP_EOL , FILE_APPEND | LOCK_EX);
      $galleryUrl = $url;
      $content = HtmlUtil::downloadHtml($galleryUrl);
      $htmlDocument = HtmlUtil::htmlDocument($content);
    }

    $selector = $this->domSelectorFinder->getSelectorFromUrl($galleryUrl)->getGallery();
    $content = preg_replace('/(\n|\r|\t)/', '', $content);
    $title = $this->findTitle($selector, $htmlDocument);

    $titleDateParams = new TitleDateParams();
    $titleDateParams->setCurrentDate(new DateTime());
    $titleDateParams->setSwapDayMonth(false);
    $titleDateParams->setCheckDateInTheFuture(true);
    $titleDateParams->setCheckDayMonthMatch(true);

    return array(
      "domain" => $comp['host'],
      "title" => $title,
      "titleParsed" => $this->titleParser->parseTitle($title, $titleDateParams)->toMap(true),
      "gallery" => $this->extractGallery($selector, $htmlDocument, $content, $baseuri)
    );
  }

  /**
   * @param Gallery $selector
   * @param DOMQuery $htmlDocument
   * @param string $content
   * @param string $baseuri
   * @return array<GalleryItem>
   */
  private function extractGallery(
    Gallery $selector,
    DOMQuery $htmlDocument,
    string $content,
    string $baseuri
  ): array {
    if ($selector->getRegExp()) {
      return $this->extractByRegExp($selector, $content, $baseuri);
    }
    $arr = array();
    $arr = array_merge($arr, $this->extractImages($selector, $htmlDocument, $baseuri));
    return array_merge($arr, $this->extractImageFromMultiPage($selector, $htmlDocument, $baseuri));
  }

  /**
   * @param Gallery $selector
   * @param string $html
   * @param string $baseuri
   * @return array<GalleryItem>
   */
  private function extractByRegExp(
    Gallery $selector,
    string $html,
    string $baseuri
  ): array {
    preg_match_all("/" . str_replace("/", "\/", $selector->getRegExp()) . "/", $html, $matches);
    $thumbIndex = $selector->getRegExpThumbUrlIndex();
    $imageIndex = $selector->getRegExpImageUrlIndex();

    $arr = array();

    for ($i = 0; $i < count($matches[1]); $i++) {
      $thumbnailURL = str_replace("\/", "/", $matches[$thumbIndex][$i]);
      $destinationDocumentURL = str_replace("\/", "/", $matches[$imageIndex][$i]);
      $arr[] = $this->galleryItemBuilder->build($selector, $thumbnailURL, $destinationDocumentURL);
    }

    return $arr;
  }

  private function findTitle(
    Gallery $selector,
    DOMQuery $htmlDocument
  ): string {
    $title = "";
    if ($selector->getTitle() !== null) {
        $title = trim($htmlDocument->find($selector->getTitle())->text());
    }
    if (empty($title)) {
        $title = trim($htmlDocument->find("title")->text());
    }
    $normalizedTitle = StringUtil::normalizeWhitespaces($title);
    if (is_string($normalizedTitle)) {
      return $normalizedTitle;
    }
    return '';
  }

  /**
   * @param Gallery $selector
   * @param DOMQuery $startPageDocument
   * @param string $baseuri
   * @return array<GalleryItem>
   */
  private function extractImageFromMultiPage(
    Gallery $selector,
    DOMQuery $startPageDocument,
    string $baseuri
  ): array {
    $arr = array();

    if ($selector->getMultiPage() === null) {
      return $arr;
    }
    $element = $startPageDocument->find($selector->getMultiPage())->first();
    while ($element->count() > 0) {
      $pageUrl = HtmlUtil::absUrl($baseuri, $element->attr('href'));
      $pageUrlSel = $this->domSelectorFinder->getSelectorFromUrl($pageUrl);
      $options = array(
        HtmlUtil::OPTION_USER_AGENT => $pageUrlSel->getUserAgent()
      );
      $pageContent = HtmlUtil::downloadHtml($pageUrl, $options);
      $pageDocument = HtmlUtil::htmlDocument($pageContent);
      $r = $this->extractImages($pageUrlSel->getGallery(), $pageDocument, $pageUrl);
      $arr = array_merge($arr, $r);
      $element = $pageDocument->find($selector->getMultiPage())->first();
    }
    return $arr;
  }

  /**
   * @param Gallery $selector
   * @param DOMQuery $htmlDocument
   * @param string $baseuri
   * @return array<GalleryItem>
   */
  private function extractImages(
    Gallery $selector,
    DOMQuery $htmlDocument,
    string $baseuri
  ): array {
    $thumbnailImages = $htmlDocument->find($selector->getContainer());

    $arr = array();
    foreach ($thumbnailImages as $thumbnailImage) {
      $galleryItem = $this->buildGalleryItem($selector, $thumbnailImage, $baseuri);

      if ($galleryItem) {
        $arr[] = $galleryItem;
      }
    }
    return $arr;
  }

  /**
   * @param Gallery $selector
   * @param DOMQuery $thumbnailImage
   * @param string $baseuri
   * @return GalleryItem|null
   */
  private function buildGalleryItem(
    Gallery $selector,
    DOMQuery $thumbnailImage,
    string $baseuri
  ): ?array {
    $galleryItem = $this->galleryItemBuilder->fromSrcSet($selector, $thumbnailImage, 400);

    if ($galleryItem == null) {
      $galleryItem = $this->galleryItemBuilder->fromThumbnailElement($selector, $thumbnailImage, $baseuri);
    }
    return $galleryItem;
  }
}

