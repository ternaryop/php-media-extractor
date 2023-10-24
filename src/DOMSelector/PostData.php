<?php

namespace Ternaryop\MediaExtractor\DOMSelector;

class PostData {
  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private string $imgContinue;

  public function getImgContinue(): string {
    return $this->imgContinue;
  }

  public function setImgContinue(string $imgContinue): void {
    $this->imgContinue = $imgContinue;
  }
}
