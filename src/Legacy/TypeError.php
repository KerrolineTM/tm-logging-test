<?php

namespace Tridmedia\Logging\Legacy;

use TypeError as GlobalTypeError;

abstract class TypeError
{
  public static function ofArgument(
    string $function_name,
    string $argument_name,
    string $argument_type,
    $argument_value,
    ?string $argument_position = null
  ) {
    $position = $argument_position ? " #$argument_position " : '';
    $value_type = gettype($argument_value);
    return new GlobalTypeError("$function_name(): Argument$position(\$$argument_name) must be of type $argument_type, $value_type given.");
  }
}
