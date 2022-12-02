<?php

namespace Tridmedia\Logging\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class LoggerMakeCommand extends GeneratorCommand
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $name = 'make:logger';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new logger class.';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Logger';

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return __DIR__ . '/stubs/logger.stub';
  }

  /**
   * Execute the console command.
   *
   * @return bool|null
   */

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace): string
  {
    return $rootNamespace . '\\' . config('logging.path');
  }

  protected function getArguments()
  {
    return [
      ['name', InputArgument::REQUIRED, 'The name of the Logger class'],
    ];
  }
}
