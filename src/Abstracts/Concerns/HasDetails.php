<?php

namespace Tridmedia\Logging\Abstracts\Concerns;

use Tridmedia\Logging\Entities\Serializer;

/**
 * Позволяет записывать в лог дополнительную информацию,
 * которая представлена в виде одномерного json-объекта
 */
trait HasDetails
{
  /**
   * Список подробностей для серриализации
   * 
   * @return mixed
   */
  protected function details()
  {
    return null;
  }

  /**
   * Серриализованный список подробностей
   */
  protected function getSerializedDetails(): ?array
  {
    return Serializer::make()->serializeAsFlatDictionary($this->details());
  }
}
