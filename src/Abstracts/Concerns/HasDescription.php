<?php

namespace Tridmedia\Logging\Abstracts\Concerns;

/**
 * Позволяет задавать описание
 */
trait HasDescription
{
  /**
   * Описание лога
   *
   * @var null|string
   */
  protected $description = null;



  /**
   * Геттер/Сеттер описания
   *
   * @param null|string $description Описание
   * 
   * @return null|string|static
   */
  public function description(?string $description = null)
  {
    if (!count(func_get_args())) return $this->description;

    $this->description = $description;

    return $this;
  }
}
