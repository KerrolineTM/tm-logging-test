<?php

namespace Tridmedia\Logging\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManyLoggables extends HasMany
{
  /**
   * Добавляет запись в пивот Loggable для одного отношения
   * 
   * @param string $loggable_type Класс модели
   * @param array  $ids           Массив id моделей
   */
  public function attach(string $loggable_type, array $ids): void
  {
    if (!$ids) return;

    $records = $this->createAttachablesFromIds($loggable_type, $ids);

    $this->createMany($records);
  }

  /**
   * Добавляет запись в пивот Loggable для нескольких отношений
   *
   * @param array<string,int|int[]> $pairs Массив, в котором ключи - классы моделей, значения - массив id моделей или id модели
   */
  public function attachMany(array $pairs): void
  {
    if (!$pairs) return;

    $records = [];
    foreach ($pairs as $loggable_type => $loggable_id) {
      if (!$loggable_id) continue;

      if (is_array($loggable_id)) {
        $nested_records = $this->createAttachablesFromIds($loggable_type, $loggable_id);
        array_push($records, ...$nested_records);
        continue;
      }

      $records[] = compact('loggable_type', 'loggable_id');
    }

    $this->createMany($records);
  }


  /**
   * Конструирует массивы, на основе которых могут быть созданы записи в пивоте Loggable 
   *
   * @param string $loggable_type Класс модели
   * @param array  $ids           Массив id моделей
   */
  protected function createAttachablesFromIds(string $loggable_type, array $ids): array
  {
    $results = [];
    foreach ($ids as $loggable_id) {
      $results[] = compact('loggable_type', 'loggable_id');
    }
    return $results;
  }
}
