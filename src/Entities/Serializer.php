<?php

namespace Tridmedia\Logging\Entities;

class Serializer
{
  /**
   * Объединять массивы `array<scalar>` в `string`
   *
   * @var bool
   */
  protected $implodeArrays = false;

  /**
   * Разделитель для склейки массивов
   *
   * @var string
   */
  protected $implodeArraysSeparator = ', ';



  /**
   * Создает сериалайзер
   *
   * @return static
   */
  public static function make()
  {
    return new static;
  }



  /**
   * Активирует режим склеивания массивов в строки
   *
   * @param bool|string $separator Разделитель для склеивания массивов
   * 
   * @return static
   */
  public function implodeArrays($separator = true)
  {
    if (is_bool($separator)) $this->implodeArrays = $separator;
    else {
      $this->implodeArrays = true;
      $this->implodeArraysSeparator = $separator;
    }

    return $this;
  }



  /**
   * Приводит к одноуровневому списку
   * 
   * @param mixed $data Данные для серриализации
   * @param bool|string $implode_array Склеивать ли массив `array<scalar>` в строку. Если передана строка, то она будет использована как разделитель
   * 
   * Схема сериализации значений по типам:
   * - `scalar`        => `scalar`
   * - `array<scalar>` => implodeArrays ? `string` : `array<scalar>`
   * - `null`          => `null`
   * - `array<array>`  => `null`
   * - `object`        => `null`
   */
  public function serializeAsFlatDictionary($data): ?array
  {
    if (!$data) return null;

    return collect($data)
      ->map(function ($value) {
        if (is_scalar($value)) return $value;
        if ($this->isFlatArray($value)) return $this->implodeArrays
          ? implode($this->implodeArraysSeparator, $value)
          : $value;
        return null;
      })
      ->all();
  }



  /**
   * Проверяет является ли значение одноуровневым массивом
   *
   * @param mixed $value
   */
  protected function isFlatArray($value): bool
  {
    return is_array($value) && array_flatten($value) === array_values($value);
  }
}
