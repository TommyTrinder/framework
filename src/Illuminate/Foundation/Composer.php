<?php namespace Illuminate\Foundation;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class Composer {

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The working path to regenerate from.
	 *
	 * @var string
	 */
	protected $workingPath;

	/**
	 * Create a new Composer manager instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @param  string  $workingPath
	 * @return void
	 */
	public function __construct(Filesystem $files, $workingPath = null)
	{
		$this->files = $files;
		$this->workingPath = $workingPath;
	}

	/**
	 * Regenerate the Composer autoloader files.
	 *
	 * @param  string  $extra
	 * @return void
	 */
	public function dumpAutoloads($extra = '')
	{
		$process = $this->getProcess(array_merge($this->findComposer(), ['dump-autoload', $extra]));

		$process->run();
	}

	/**
	 * Regenerate the optimized Composer autoloader files.
	 *
	 * @return void
	 */
	public function dumpOptimized()
	{
		$this->dumpAutoloads('--optimize');
	}

	/**
	 * Get the composer command for the environment.
	 */
	protected function findComposer(): array
	{
		if ($this->files->exists($this->workingPath.'/composer.phar'))
		{
			return [PHP_BINARY, 'composer.phar'];
		}

		return ['composer'];
	}

	/**
	 * Get a new Symfony process instance.
	 */
	protected function getProcess(array $command): Process
	{
		return new Process($command, $this->workingPath, null, null, null);
	}

	/**
	 * Set the working path used by the class.
	 *
	 * @param  string  $path
	 * @return $this
	 */
	public function setWorkingPath($path)
	{
		$this->workingPath = realpath($path);

		return $this;
	}

}
