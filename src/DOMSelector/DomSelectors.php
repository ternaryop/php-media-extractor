<?php
namespace Ternaryop\MediaExtractor\DOMSelector;

use Exception;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;

class DomSelectors {
  /**
   * @JMS\Serializer\Annotation\Type("integer")
   */
  private int $version;

  /**
   * @JMS\Serializer\Annotation\Type("array<Ternaryop\MediaExtractor\DOMSelector\Selector>")
   * @var array<Selector> $selectors
   */
  private array $selectors;

  public function getVersion(): int {
    return $this->version;
  }

  public function setVersion(int $version): void {
    $this->version = $version;
  }

  /**
   * @return array<Selector>
   */
  public function getSelectors(): array {
    return $this->selectors;
  }

  /**
   * @param  array<Selector> $selectors
   */
  public function setSelectors(array $selectors): void {
    $this->selectors = $selectors;
  }

  /**
   * @throws Exception
   */
  public static function fromJSONFile(string $selectorPath): DomSelectors {
    $string = file_get_contents($selectorPath);

    if ($string === false) {
      throw new Exception("Unable to read $selectorPath");
    }

    $serializerBuilder = SerializerBuilder::create();
    $serializerBuilder->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());
    $serializerBuilder->addDefaultHandlers();
    $serializer = $serializerBuilder->build();

    return $serializer->deserialize(
      $string,
      DomSelectors::class,
      'json');
  }
}


