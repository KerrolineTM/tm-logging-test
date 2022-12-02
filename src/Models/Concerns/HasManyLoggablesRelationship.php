<?php

namespace Tridmedia\Logging\Models\Concerns;

use Tridmedia\Logging\Relations\HasManyLoggables;

trait HasManyLoggablesRelationship
{
  /**
   * Define a one-to-many relationship.
   *
   * @param  string  $related
   * @param  string  $foreignKey
   * @param  string  $localKey
   */
  public function hasManyLoggables($related, $foreignKey = null, $localKey = null): HasManyLoggables
  {
    $instance = $this->newRelatedInstance($related);

    $foreignKey = $foreignKey ?: $this->getForeignKey();

    $localKey = $localKey ?: $this->getKeyName();

    return new HasManyLoggables(
      $instance->newQuery(),
      $this,
      $instance->getTable() . '.' . $foreignKey,
      $localKey
    );
  }
}
