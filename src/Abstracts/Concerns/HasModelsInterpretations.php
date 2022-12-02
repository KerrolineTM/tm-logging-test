<?php

namespace Tridmedia\Logging\Abstracts\Concerns;

use Illuminate\Database\Eloquent\Model;

use Tridmedia\Logging\Entities\Interpretations;
use Tridmedia\Logging\Entities\Interpretation;

/**
 * Позволяет добавить человеко-читаемые наименования для моделей,
 * которые используются в логе
 */
trait HasModelsInterpretations
{
  /**
   * Словарь человеко-читаемых наименований для моделей
   * 
   * @return Interpretation|Interpretations
   */
  protected function interpretations()
  {
    return new Interpretations;
  }



  /**
   * Интерпретирует словарь {модель:значения}
   * 
   * @param array<string,array> $dictionary Словарь, в котором ключи - классы моделей, а значения - массивы идентификаторов моделей
   * @param null|Interpretation|Interpretations $interpretations Интерпретации моделей
   */
  protected function interpretDictionary(array $dictionary, $interpretations = null): array
  {
    $interpretations = $this->getQualifiedInterpretations($interpretations);

    $result = [];
    foreach ($dictionary as $model => $identifiers) {
      $can_be_interpreted = (bool) $interpretation = $interpretations->find($model);

      if ($can_be_interpreted) {
        $collection = $interpretation->getListOfInterpretedModels($identifiers);

        if ($collection->isEmpty()) continue;

        $count = $collection->count();

        $identifiers = $count === 1 ? $collection->first() : $collection->all();
        $model = $interpretation->getAlias($count);
      }

      $result[$model] = $identifiers;
    }

    return $result;
  }

  /**
   * Интерпретирует экземпляр модели
   *
   * @param Model $model Экземпляр модели
   * @param null|Interpretation|Interpretations $interpretations Интерпретации моделей
   * 
   * @return mixed
   */
  protected function interpretModel(Model $model, $interpretations = null)
  {
    $interpretation = $this->getQualifiedInterpretations($interpretations)->find(get_class($model));

    return $interpretation ? $interpretation->getValue()($model) : null;
  }



  /**
   * Приводит `Interpretation` к `Interpretations`
   * 
   * @param null|Interpretation|Interpretations $interpretations Интерпретации моделей
   */
  private function getQualifiedInterpretations($interpretations = null): Interpretations
  {
    $interpretations = is_null($interpretations) ? $this->interpretations() : $interpretations;
    return $interpretations instanceof Interpretation
      ? Interpretations::make([$interpretations])
      : $interpretations;
  }
}
