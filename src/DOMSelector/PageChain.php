<?php

namespace Ternaryop\MediaExtractor\DOMSelector;

class PageChain {
  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private string $pageSel;

  /**
   * @JMS\Serializer\Annotation\Type("string")
   */
  private string $selAttr;

  public function getPageSel(): string {
    return $this->pageSel;
  }

  public function setPageSel(string $pageSel): void {
    $this->pageSel = $pageSel;
  }

  public function getSelAttr(): string {
    return $this->selAttr;
  }

  public function setSelAttr(string $selAttr): void {
    $this->selAttr = $selAttr;
  }
}
