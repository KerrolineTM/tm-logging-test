<?php

namespace Tridmedia\Logging\Abstracts\Concerns;

/**
 * Позволяет добавлять для лога ссылки на модели
 */
trait HasReferences
{
  /**
   * Ссылки конструируемого лога на модели
   *
   * @var array
   */
  protected $references = [];



  /**
   * Добавляет одну или несколько ссылок на одну модель
   * 
   * @param string    $model Класс модели
   * @param int|int[] $keys  Id моделей
   * 
   * @return static
   */
  public function referTo(string $model, $keys)
  {
    if (!$keys) return $this;

    $keys = is_array($keys) ? $keys : [$keys];

    $this->references[$model] = array_key_exists($model, $this->references)
      ? $this->mergeReferences($this->references[$model], $keys)
      : $keys;

    return $this;
  }

  /**
   * Добавляет одну или несколько ссылок на несколько моделей
   *
   * @param array<string,int|int[]> $pairs Массив, в котором ключи - классы моделей, значения - массив id моделей или id модели 
   * 
   * @return static
   */
  public function refersTo(array $pairs)
  {
    foreach ($pairs as $model => $keys) {
      $this->referTo($model, $keys);
    }

    return $this;
  }



  /** Объединяет два массива ссылок на модели */
  protected function mergeReferences(array $a, array $b): array
  {
    return array_values(array_sort(array_unique(array_merge($a, $b))));
  }
}
