<?php

namespace Tridmedia\Logging\Abstracts\Concerns;

trait HasChanges
{
  /**
   * Создает массив различий между `$before` и `$after`
   *
   * @param array $before Состояние "до" изменений
   * @param array $after  Состояние "после" изменений
   */
  protected function changes(array $before, array $after): array
  {
    $before_diff = array_diff_key($before, $after);
    $after_diff  = array_diff_key($after, $before);

    $before = array_intersect_key($before, $after);
    $after  = array_intersect_key($after, $before);

    if ($before_diff || $after_diff) {
      $before_missing = array_fill_keys(array_keys($after_diff), null);
      $after_missing  = array_fill_keys(array_keys($before_diff), null);
      $before = array_merge($before, $before_missing, $before_diff);
      $after  = array_merge($after, $after_missing, $after_diff);
    }

    ksort($before);
    ksort($after);

    $changes = array_combine(array_keys($before), array_map(null, $before, $after));

    return $this->removeInsignificantChanges($changes);
  }



  /**
   * Удаляет все изменения в которых "до" и "после" имеют одинаковое значение
   */
  protected function removeInsignificantChanges(array $changes): array
  {
    return array_filter($changes, function (array $change) {
      return $change[0] !== $change[1];
    });
  }
}
