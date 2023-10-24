<?php

namespace Ternaryop\MediaExtractor\DOMSelector;

class Gallery {
  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private string $container = "a img";

  /**
   * @JMS\Serializer\Annotation\Type("boolean")
   */
  private bool $isImageDirectUrl = false;

  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private ?string $regExp = null;

  /**
   * @JMS\Serializer\Annotation\Type("int")
   */
  private int $regExpImageUrlIndex = 0;

  /**
   * @JMS\Serializer\Annotation\Type("int")
   */
  private int $regExpThumbUrlIndex = 0;

  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private ?string $title = null;

  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private string $thumbImageSelAttr = "src";

  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private ?string $multiPage = null;

  public function getContainer(): string {
    return $this->container;
  }

  public function setContainer(string $container): void {
    $this->container = $container;
  }

  public function isImageDirectUrl(): bool {
    return $this->isImageDirectUrl;
  }

  public function setImageDirectUrl(bool $isImageDirectUrl): void {
    $this->isImageDirectUrl = $isImageDirectUrl;
  }

  public function getRegExp(): ?string {
    return $this->regExp;
  }

  public function setRegExp(?string $regExp): void {
    $this->regExp = $regExp;
  }

  public function getRegExpImageUrlIndex(): int {
    return $this->regExpImageUrlIndex;
  }

  public function setRegExpImageUrlIndex(int $regExpImageUrlIndex): void {
    $this->regExpImageUrlIndex = $regExpImageUrlIndex;
  }

  public function getRegExpThumbUrlIndex(): int {
    return $this->regExpThumbUrlIndex;
  }

  public function setRegExpThumbUrlIndex(int $regExpThumbUrlIndex): void {
    $this->regExpThumbUrlIndex = $regExpThumbUrlIndex;
  }

  public function getTitle(): ?string {
    return $this->title;
  }

  public function setTitle(?string $title): void {
    $this->title = $title;
  }

  public function getThumbImageSelAttr(): string {
    return $this->thumbImageSelAttr;
  }

  public function setThumbImageSelAttr(string $thumbImageSelAttr): void {
    $this->thumbImageSelAttr = $thumbImageSelAttr;
  }

  public function getMultiPage(): ?string {
    return $this->multiPage;
  }

  public function setMultiPage(?string $multiPage): void {
    $this->multiPage = $multiPage;
  }

  public function hasImage(): bool {
    return $this->isImageDirectUrl;
  }
}
