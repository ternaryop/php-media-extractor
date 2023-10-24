<?php

namespace Ternaryop\MediaExtractor\DOMSelector;

class Selector {
  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private string $urlPattern = "";

  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private ?string $userAgent = null;
  /**
   * @JMS\Serializer\Annotation\Type("Ternaryop\MediaExtractor\DOMSelector\Image")
   */
  private Image $image;
  /**
   * @JMS\Serializer\Annotation\Type("Ternaryop\MediaExtractor\DOMSelector\Gallery")
   */
  private Gallery $gallery;

  function __construct(string $urlPattern, ?string $userAgent, Image $image, Gallery $gallery) {
    $this->urlPattern = $urlPattern;
    $this->userAgent = $userAgent;
    $this->image = $image;
    $this->gallery = $gallery;
  }

  public function getUrlPattern(): string {
    return $this->urlPattern;
  }

  public function setUrlPattern(string $urlPattern): void {
    $this->urlPattern = $urlPattern;
  }

  /**
   * @return string|null
   */
  public function getUserAgent(): string|null {
    return $this->userAgent;
  }

  /**
   * @param null $userAgent
   */
  public function setUserAgent($userAgent): void {
    $this->userAgent = $userAgent;
  }

  public function getImage(): Image {
    return $this->image;
  }

  public function setImage(Image $image): void {
    $this->image = $image;
  }

  public function getGallery(): Gallery {
    return $this->gallery;
  }

  public function setGallery(Gallery $gallery): void {
    $this->gallery = $gallery;
  }

  public function hasImage(): bool {
    return
      $this->gallery->hasImage() ||
      $this->image->hasImage();
  }

  /**
   * @JMS\Serializer\Annotation\PostDeserialize()
   */
  public function postDeserialize(): void
  {
    $this->image = $this->image ?? new Image();
    $this->gallery = $this->gallery ?? new Gallery();
  }
}
