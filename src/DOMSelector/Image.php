<?php

namespace Ternaryop\MediaExtractor\DOMSelector;

class Image {
  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private ?string $css = null;

  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private ?string $regExp = null;

  /**
   * @JMS\Serializer\Annotation\Type("array<Ternaryop\MediaExtractor\DOMSelector\PageChain>")
   */
  private ?array $pageChain = null;

  /**
   * @JMS\Serializer\Annotation\Type("Ternaryop\MediaExtractor\DOMSelector\PostData")
   */
  private ?PostData $postData = null;

  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private string $selAttr = 'src';

  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private ?string $cookie = null;

  public function getCss(): ?string {
    return $this->css;
  }

  public function setCss(?string $css): void {
    $this->css = $css;
  }

  public function getRegExp(): ?string {
    return $this->regExp;
  }

  public function setRegExp(?string $regExp): void {
    $this->regExp = $regExp;
  }

  /**
   * @return ?array<PageChain>
   */
  public function getPageChain(): ?array {
    return $this->pageChain;
  }

  /**
   * @param ?array<PageChain> $pageChain
   */
  public function setPageChain(?array $pageChain): void {
    $this->pageChain = $pageChain;
  }

  public function getPostData(): ?PostData {
    return $this->postData;
  }

  public function setPostData(?PostData $postData): void {
    $this->postData = $postData;
  }

  public function getSelAttr(): string {
    return $this->selAttr;
  }

  public function setSelAttr(string $selAttr): void {
    $this->selAttr = $selAttr;
  }

  /**
   * @return ?string the cookie value to use for retrieve the image
   */
  public function getCookie(): ?string {
    return $this->cookie;
  }

  /**
   * @param ?string $cookie the cookie value
   */
  public function setCookie(?string $cookie): void {
    $this->cookie = $cookie;
  }

  public function hasImage(): bool {
    return $this->css !== null || $this->regExp !== null || $this->pageChain !== null || $this->cookie !== null;
  }
}
