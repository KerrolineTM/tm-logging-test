<?php

namespace Tridmedia\Logging\Entities;

use App\Models\City;
use App\Models\Report;
use App\Models\Territory;
use App\User;
use Illuminate\Support\Arr;

class Interpretations
{
  /**
   * Доступные группы интерпретаций 
   *
   * @var array
   */
  protected $groups = ['default'];

  /**
   * Интерпретации моделей
   *
   * @var array
   */
  protected $interpretations;



  /**
   * Создает коллекцию интерпретаций
   *
   * @param Interpretation ...$interpretations
   */
  public function __construct(Interpretation ...$interpretations)
  {
    $this->interpretations = $interpretations;
  }

  /**
   * Создает коллекцию интерпретаций
   *
   * @param null|array<Interpretation> $interpretations
   * 
   * @return static
   */
  public static function make(?array $interpretations = null)
  {
    return new static(...($interpretations ?? []));
  }



  /**
   * Добавляет новую интерпретацию модели
   *
   * @param string       $model Класс модели
   * @param string|array $alias Человеко-читаемое наименование
   */
  public function add(string $model, $alias): Interpretation
  {
    return $this->interpretations[] = new Interpretation($model, $alias);
  }



  /**
   * Находит интерпретацию для указанной модели и возвращает ее
   *
   * @param string $model Класс модели
   */
  public function find(string $model): ?Interpretation
  {
    return Arr::first($this->availableInterpretations(), function (Interpretation $interpretation) use ($model) {
      return $interpretation->getModel() === $model;
    });
  }



  /**
   * Добавляет ко всем манипуляциям интерпретации указанных групп
   *
   * @param string|string[] $group Группы интерпретаций
   * 
   * @return static
   */
  public function withGroup($group)
  {
    $group = is_array($group) ? $group : [$group];

    if ($items = array_diff($group, $this->groups))
      array_push($this->groups, ...$items);

    return $this;
  }

  /**
   * Убирает из всех манипуляций интерпретации указанных групп
   *
   * @param string|string[] $group Группы интерпретаций
   * 
   * @return static
   */
  public function withoutGroup($group)
  {
    $group = is_array($group) ? $group : [$group];

    $this->groups = array_values(array_diff($this->groups, $group));

    return $this;
  }

  /**
   * Возвращает новую коллекцию интерпретаций которая включает интерпретации указанных групп
   *
   * @param string|string[] $group Группы интерпретаций
   */
  public function whereGroup($group): Interpretations
  {
    $group = is_array($group) ? $group : [$group];

    $interpretations = Arr::where($this->interpretations, function (Interpretation $interpretation) use ($group) {
      return in_array($interpretation->getGroup(), $group);
    });

    return static::make($interpretations)->withGroup($group);
  }



  /**
   * Интерпретации доступных групп
   */
  public function availableInterpretations(): array
  {
    return Arr::where($this->interpretations, function (Interpretation $interpretation) {
      return in_array($interpretation->getGroup(), $this->groups);
    });;
  }
}
