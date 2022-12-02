<?php

namespace Tridmedia\Logging\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\{
  InputArgument,
  InputOption,
};

class LogMakeCommand extends GeneratorCommand
{
  protected const DescriptionLogConstructor = 'DescriptionLogConstructor';
  protected const MonoLogType               = 'MonoLogType';
  protected const MainLogType               = 'MainLogType';
  protected const FullfilledTableLogType    = 'FullfilledTableLogType';
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $name = 'make:log';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new log class.';

  /**
   * The type of class being generated.
   *
   * @var string
   */
  protected $type = 'Log';

  /**
   * Get the stub file for the generator.
   *
   * @return string
   */
  protected function getStub()
  {
    return __DIR__ . '/stubs/log.stub';
  }

  /**
   * Execute the console command.
   *
   * @return bool|null
   */
  public function handle()
  {
    if ($this->isTooManyOptions($this->getOptionsList())) {
      $this->error('Too many options');
      return false;
    }

    parent::handle();
  }

  /**
   * @return null|array<string,bool> 
   */

  protected function getOptionsList(): array
  {
    $optionsList = [
      self::DescriptionLogConstructor => $this->option(self::DescriptionLogConstructor),
      self::MonoLogType               => $this->option(self::MonoLogType),
      self::MainLogType               => $this->option(self::MainLogType),
      self::FullfilledTableLogType    => $this->option(self::FullfilledTableLogType),
    ];
    $inputOption = array_filter($optionsList, function ($val) {
      return $val;
    });
    return $inputOption;
  }

  /**
   * @param array<string,bool> $optionArray
   * @return bool
   */
  protected function isTooManyOptions($optionArray): bool
  {
    return array_sum($optionArray) > 1;
  }


  /**
   * @param array<string,bool> $optionArray
   * @return string
   */
  protected function getOptionAsString($optionArray): string
  {
    return empty($optionArray) ? '' : array_keys($optionArray)[0];
  }

  /**
   * Build the class with the given name.
   *
   * Remove the base controller import if we are already in base namespace.
   *
   * @param  string  $name
   * @return string
   */
  protected function buildClass($name)
  {
    $optionKey = $this->getOptionAsString($this->getOptionsList());

    $parentClassName = $optionKey
      ? $optionKey
      : 'AbstractLogConstructor';

    $usePath = $optionKey
      ? $this->rootNamespace() . config('logging.path') . '\\' . $optionKey
      : 'Tridmedia\Logging\Abstracts\AbstractLogConstructor';

    $replace = $this->buildParentReplacements($parentClassName, $usePath);

    return str_replace(
      array_keys($replace),
      array_values($replace),
      parent::buildClass($name)
    );
  }

  /**
   * Build the replacements for a parent class.
   *
   * @return array
   */
  protected function buildParentReplacements($parentClassName, $usePath)
  {
    $codeExamples = '';
    $useExamples = '';
    if ($this->option('CodeExamples')) {
      $codeExamples = $this->files->get($this->getCodeExamplesStub($parentClassName));
      $useExamples  = $this->files->get($this->getUseExamplesStub($parentClassName));
    }

    return [
      'ParentDummyLogTypeClass' => $parentClassName,
      'DummyParentUse' => $usePath,
      'DummyUseExamples' => $useExamples,
      'DummyCodeExamples' => $codeExamples,
    ];
  }

  /**
   * @param string $logClassName Имя родительского класса
   * @return string Шаблон, который подставляется в генерируемый класс
   */
  protected function getCodeExamplesStub(string $logClassName): string
  {
    return __DIR__ . "/stubs/code-examples/{$logClassName}.stub";
  }
  protected function getUseExamplesStub(string $logClassName): string
  {
    return __DIR__ . "/stubs/code-examples/{$logClassName}-use.stub";
  }

  /**
   * Get the default namespace for the class.
   *
   * @param  string  $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace . '\\' . config('logging.path');
  }

  protected function getArguments()
  {
    return [
      ['name', InputArgument::REQUIRED, 'The name of the Log class'],
    ];
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      [self::DescriptionLogConstructor, 'd', InputOption::VALUE_NONE, 'Generate a default log for the Description log type.'],

      [self::MonoLogType, 'a', InputOption::VALUE_NONE, 'Generate a default log for the mono log type.'],

      [self::MainLogType, 'b', InputOption::VALUE_NONE, 'Generate a default log for the main log type.'],

      [self::FullfilledTableLogType, 'c', InputOption::VALUE_NONE, 'Generate a default log for the full filled table log type.'],

      ['CodeExamples', 'e', InputOption::VALUE_NONE, 'Add an example code in generated log'],
    ];
  }
}
