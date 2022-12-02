<?php

namespace Tridmedia\Logging\Entities;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use Tridmedia\Logging\Legacy\TypeError;

class Interpretation
{
  /**
   * Класс модели
   *
   * @var string
   */
  protected $model;

  /**
   * Человеко-читаемое наименование в единственном числе
   *
   * @var string
   */
  protected $singular_alias;

  /**
   * Группа интерпретации
   *
   * @var string
   */
  protected $group = 'default';

  /**
   * Человеко-читаемое наименование во множественном числе
   *
   * @var null|string
   */
  protected $plural_alias = null;

  /**
   * Замыкание, которое расширяет SQL-запрос к модели 
   *
   * @var null|callable
   */
  protected $query = null;

  /**
   * Замыкание, интерпретирующее экземпляр модели как одно значение
   *
   * @var null|callable
   */
  protected $value = null;



  /**
   * Создает человеко-читаемую интерпретацию модели
   *
   * @param string $model Класс модели
   * @param string|array<string> $alias Человеко-читаемое наименование. 
   * Если передан массив, то первый элемент массива - это интерпретация 
   * для единственного числа, второй - для множественного 
   */
  public function __construct(string $model, $alias)
  {
    if (!(is_string($alias) || is_array($alias)))
      throw TypeError::ofArgument('Interpretation::__construct', 'alias', 'string|array<string>', $alias, 2);

    $this->model = $model;
    $this->as($alias);
  }

  /**
   * Создает интерпретацию модели
   *
   * @param string $model Класс модели
   * 
   * @return static
   */
  public static function of(string $model)
  {
    return new static($model, $model);
  }



  /**
   * Устанавливает человеко-читаемое наименование модели
   *
   * @param string|array<string> $alias Человеко-читаемое наименование. 
   * Если передан массив, то первый элемент массива - это интерпретация 
   * для единственного числа, второй - для множественного
   * 
   * @return static
   */
  public function as($alias)
  {
    $is_complex_alias = is_array($alias);

    $this->singular_alias = $is_complex_alias ? $alias[0] : $alias;
    $this->plural_alias = $is_complex_alias ? ($alias[1] ?? null) : null;

    return $this;
  }

  /**
   * Замыкание, с помощью которого можно расширить SQL-запрос к модели
   *
   * @param null|string|string[]|callable $callable Если указана строка или массив строк то к запросу добавиться select на указанные поля
   * 
   * @return static
   */
  public function query($callable)
  {
    if (is_array($callable) && !$callable) return $this;
    elseif (is_string($callable)) $callable = [$callable];

    $this->query = is_array($callable)
      ? function (QueryBuilder $query) use ($callable) {
        $query->select($callable);
      }
      : $callable;

    return $this;
  }

  /**
   * Замыкание, интерпретирующее экземпляр модели как одно значение
   *
   * @param null|string|callable $callable Если указана строка то будет установлена функция-геттер этого ключа из экземпляра модели
   * 
   * @return static
   */
  public function value($callable)
  {
    $this->value = is_string($callable)
      ? function ($item) use ($callable) {
        return $item[$callable] ?? $item->{$callable} ?? null;
      }
      : $callable;

    return $this;
  }

  /**
   * Устанавливает группу интерпретации
   *
   * @param string $name Группа интерпретации
   * 
   * @return static
   */
  public function group(string $name)
  {
    $this->group = $name;

    return $this;
  }



  /**
   * Получает коллекцию модели применяя к ней механизмы интерпретации
   *
   * @param array<int> $keys Id или массив Id записей которые нужно найти 
   */
  public function getListOfInterpretedModels(array $keys): Collection
  {
    $query = $this->getModelQuery();

    if ($keys) $query->whereKey($keys);

    if ($this->query) ($this->query)($query);

    $models = $query->get();

    $lost = $this->getCollectionDivergedIdentifiers($models, $keys);

    $models = $this->value
      ? $models->map($this->value)
      : $models->map->getKey();

    return $models->merge($lost);
  }



  /**
   * Возвращает класс модели
   */
  public function getModel(): string
  {
    return $this->model;
  }

  /**
   * Возвращает интерпретацию класса модели
   *
   * @param null|int $count Количество объектов для которых необходимо получить интерпретацию
   */
  public function getAlias(int $count = null): string
  {
    return $this->isComplex() && $count > 1
      ? $this->plural_alias
      : $this->singular_alias;
  }

  /**
   * Замыкание, с помощью которого можно расширить SQL-запрос к модели
   * 
   * @return null|callable
   */
  public function getQuery()
  {
    return $this->query;
  }

  /**
   * Замыкание, интерпретирующее экземпляр модели как одно значение
   * 
   * @return null|callable
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * Наименование группы в которой состоит интерпретация
   */
  public function getGroup(): string
  {
    return $this->group;
  }



  /**
   * Содержит ли интерпретацию как для единственного так и для множественного числа
   */
  public function isComplex(): bool
  {
    return !is_null($this->plural_alias);
  }



  /**
   * Создает инстанс QueryBuilder для интерпретируемой модели 
   */
  protected function getModelQuery(): QueryBuilder
  {
    return $this->model::query();
  }



  /**
   * Выявляет для каких ключей в `$keys` не были найдены соответствующие записи в `$collection`
   *
   * @param Collection<Model> $collection Коллекция моделей
   * @param array $identifiers Идентификаторы моделей
   */
  protected function getCollectionDivergedIdentifiers(Collection $collection, array $identifiers): array
  {
    if (count($identifiers) === $collection->count()) return [];

    $keyed = $collection->keyBy(function (Model $model) {
      return $model->getKey();
    });

    return collect($identifiers)->flip()->diffKeys($keyed)->keys();
  }
}
