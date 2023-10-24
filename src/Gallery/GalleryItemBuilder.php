<?php
namespace Ternaryop\MediaExtractor\Gallery;

use QueryPath\DOMQuery;
use Ternaryop\MediaExtractor\DOMSelector\Gallery;
use Ternaryop\MediaExtractor\DOMSelector\ImageDOMSelectorFinder;
use Ternaryop\PhotoshelfUtil\Html\HtmlUtil;

class GalleryItemBuilder {
  private ImageDOMSelectorFinder $domSelectorFinder;

  function __construct(ImageDOMSelectorFinder $domSelectorFinder) {
    $this->domSelectorFinder = $domSelectorFinder;
  }

  public function fromSrcSet(
    Gallery  $gallery,
    DOMQuery $thumbnailImage,
    int      $min_thumb_width): ?array {
    $srcSet = HtmlUtil::parseSrcSet($thumbnailImage->attr('srcset'));

    if ($srcSet) {
      $thumb_url = null;

      foreach ($srcSet as $width => $url) {
        $thumb_url = $url;
        if ($width >= $min_thumb_width) {
          break;
        }
      }
      $large_image = $thumbnailImage->parent()->attr('href');

      // Prefer always the parent url if present
      // this could resolve from small images on srcSet
      if ($large_image == '') {
        $keys = array_keys($srcSet);
        $large_key = $keys[count($keys) - 1];
        $large_image = $srcSet[$large_key];
      }

      // INSANE HACK: this should skip small images
      // It isn't sure it works always
      if ($thumb_url != $large_image) {
        return $this->build($gallery, $thumb_url, $large_image);
      }
    }

    return null;
  }

  public function fromThumbnailElement(
    Gallery  $gallery,
    DOMQuery $thumbnailImage,
    string   $baseuri
  ): ?array {
    $href = $thumbnailImage->parent()->attr('href');
    if ($href == '') {
      return null;
    }

    $destinationDocumentURL = HtmlUtil::absUrl($baseuri, $href);
    $destinationSelector = $this->domSelectorFinder->getSelectorFromUrl($destinationDocumentURL);
    $thumbImageSelAttr = $destinationSelector->getGallery()->getThumbImageSelAttr();
    if ($destinationSelector->hasImage()) {
      $thumbnailURL = HtmlUtil::absUrl($baseuri, $thumbnailImage->attr($thumbImageSelAttr));
      return $this->build($gallery, $thumbnailURL, $destinationDocumentURL);
    }
    return null;
  }

  public function build(
    Gallery $gallery,
    string  $thumbnailURL,
    string  $destinationDocumentURL
  ): array {
    $destinationKey = $gallery->isImageDirectUrl() ? 'imageUrl' : 'documentUrl';
    return array(
        'thumbnailUrl' => $thumbnailURL,
        $destinationKey => $destinationDocumentURL
    );
  }
}

