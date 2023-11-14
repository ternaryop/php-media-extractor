<?php

declare(strict_types=1);

namespace Ternaryop\MediaExtractor\Birthday;

use DateTime;
use Exception;

class Birthdate {
  protected string $name;
  protected ?DateTime $birthdate;
  protected ?string $source;
  protected ?DateTime $createTime;
  protected ?DateTime $updateTime;

  public function setName(string $value): void {
    $this->name = strtolower($value);
  }

  public function setBirthdate(?DateTime $value): void {
    $this->birthdate = $value;
  }

  public function setSource(?string $value): void {
    $this->source = $value;
  }

  public function setCreateTime(?DateTime $value): void {
    $this->createTime = $value;
  }

  public function setUpdateTime(?DateTime $value): void {
    $this->updateTime = $value;
  }

  public function getName(): string {
    return $this->name;
  }

  public function getBirthdate(): ?DateTime {
    return $this->birthdate;
  }

  public function getSource(): ?string {
    return $this->source;
  }

  public function getCreateTime(): ?DateTime {
    return $this->createTime;
  }

  public function getUpdateTime(): ?DateTime {
    return $this->updateTime;
  }

  public function getBirthdateAsString(): ?string {
    return $this->birthdate?->format('Y-m-d');
  }

  public function getCreateTimeAsString(): ?string {
    return $this->createTime?->format('Y-m-d H:i:s');
  }

  public function getUpdateTimeAsString(): ?string {
    return $this->updateTime?->format('Y-m-d H:i:s');
  }

  public static function createFromName(
    string   $name,
    ?DateTime $birthdate,
    ?string  $source = null,
    ?DateTime  $updateTime = null
  ): Birthdate {
    $now = new DateTime();
    $b = new Birthdate();
    $b->setName($name);
    $b->setSource($source);
    $b->setBirthdate($birthdate);
    $b->setCreateTime($now);
    $b->setUpdateTime($updateTime);

    return $b;
  }

  /**
   * @param array<string, mixed> $array
   * @return Birthdate
   */
  public static function createFromArray(array $array): Birthdate {
    $b = new Birthdate();
    $b->setName(Birthdate::stringOrNull($array, 'name') ?? 'no-name');
    $b->setSource(Birthdate::stringOrNull($array, 'source'));
    $b->setBirthdate(Birthdate::dateTimeOrNull($array, 'birthdate'));
    $b->setCreateTime(Birthdate::dateTimeOrNull($array, 'createTime'));
    $b->setUpdateTime(Birthdate::dateTimeOrNull($array, 'updateTime'));

    return $b;
  }

  /**
   * @param array<string, mixed> $array
   * @param string $key
   * @return string|null
   */
  private static function stringOrNull(array $array, string $key): ?string {
    return $array[$key] ?? null;
  }

  /**
   * @param array<string, mixed> $array
   * @param string $key
   * @return DateTime|null
   */
  private static function dateTimeOrNull(array $array, string $key): ?DateTime {
    if (!isset($array[$key])) {
      return null;
    }
    $o = $array[$key];
    if (is_string($o)) {
      try {
        return new DateTime($o);
      } catch (Exception $e) {
        return null;
      }
    }
    return $o;
  }
}
