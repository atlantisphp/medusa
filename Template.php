<?php

namespace AtlantisPHP\Medusa;

use Exception;
use AtlantisPHP\Medusa\AppDirectives\{EndEmpty, EndIsset, EndNot, EndNull, Not};

class Template
{
  /**
	 * Directory Separator
	 *
	 * @var $DS
	 */
  private $DS = DIRECTORY_SEPARATOR;

  /**
   * Cache directory
   *
   * @var $cacheDirectory
  */
  private $cacheDirectory = 'storage' . DIRECTORY_SEPARATOR . 'cache';

  /**
   * Views directory
   *
   * @var $viewsDirectory
   */
  private $viewsDirectory;

  /**
   * Views extension
   *
   * @var $viewsExtension
   */
  private $viewsExtension = '.medusa.php';

  /**
   * Default templating engine
   *
   * @var $engine
   */
  private $engine = 'modulus';

  /**
   * Custom directives
   *
   * @var $directives
   */
  private $directives = [];

  /**
   * __construct
   *
   * @param string $engine
   * @return void
   */
  public function __construct($engine = 'modulus')
  {
    if (in_array($engine, ['modulus', 'blade'])) {
      $this->engine = $engine;
      return true;
    }

    $this->exception('Engine "' . $engine . '" does not exist.');
    return false;
  }

  /**
   * Register custom directives
   *
   * @return boolean
   */
  private function registerDirectives()
  {
    $this->register(Not::class);
    $this->register(EndEmpty::class);
    $this->register(EndIsset::class);
    $this->register(EndNot::class);
    $this->register(EndNull::class);

    return true;
  }

  /**
	 * Throw new Exception
	 *
	 * @param string $message
	 */
	private function exception($message)
	{
		throw new Exception($message);
	}

  /**
   * Set default caching directory.
   *
   * @param  string $directory
   * @return Template
   */
  public function setCacheDirectory(string $directory)
  {
    $this->cacheDirectory = $directory;
    return $this;
  }

  /**
   * Set default views directory.
   *
   * @param  string $directory
   * @return Template
   */
  public function setViewsDirectory(string $directory)
  {
    if (!is_dir($directory))
      return $this->exception(
        'Views directory "' . $directory . '" does not exist.'
      );

    $this->viewsDirectory = $directory;
    return $this;
  }

  /**
   * Set default caching directory.
   *
   * @param  string $directory
   * @return Template
   */
  public function setViewsExtension(string $extension)
  {
    if (substr($extension, 0, 1) != '.') {
      $this->viewsExtension = '.' . $extension;
      return $this;
    }

    $this->viewsExtension = $extension;
    return $this;
  }

  /**
   * Set engine
   *
   * @param string $engine
   * @return Template
   */
  public function setEngine(string $engine)
  {
    if (in_array($engine, ['modulus', 'blade'])) {
      $this->engine = $engine;
      return $this;
    }

    $this->exception('Engine "' . $engine . '" does not exist.');
  }

  /**
   * Clean path
   *
   * @param  string $path
   * @return string $path
   */
  private function cleanPath($path)
  {
    $path = substr($path, 0, 1) == '/' ? substr($path, 1, strlen($path) - 1) : $path;
    $path = substr($path, strlen($path) - 1, 1) == '/' ? substr($path, 0, strlen($path) - 1) : $path;

    $path = substr($path, 0, 1) == '\\' ? substr($path, 1, strlen($path) - 1) : $path;
    $path = substr($path, strlen($path) - 1, 1) == '\\' ? substr($path, 1, strlen($path) - 1) : $path;

    $path = preg_replace('#' . $this->DS . '+#', $this->DS, $path);

    return $path;
  }

  /**
   * Register a new directive
   *
   * @param  string  $class
   * @return Template
   */
  public function register(string $class)
  {
    if (!class_exists($class))
      return $this->exception(
        'Class "' . $class . '" does not exist.'
      );

    $this->directives[] = ['class' => $class];
    return $this;
  }

  /**
   * Return a new view
   *
   * @param  string $view
   * @param  array  $data
   * @return Template
   */
  public function view(string $view, array $data = [])
  {
    $path = $this->viewsDirectory . $this->DS . $this->cleanPath($view) . $this->viewsExtension;
    $this->verify($path);
    $this->registerDirectives();

    $compiler = new Compiler($this->cacheDirectory, $this->viewsDirectory, $this->viewsExtension, $this->directives, $this->engine);
    $compiler->makeView($path, $data);
    return $this;
  }

  /**
   * Make a new view
   *
   * @param  string $view
   * @param  array  $data
   * @return string
   */
  public function make(string $view, array $data = [])
  {
    $path = $this->viewsDirectory . $this->DS . $this->cleanPath($view) . $this->viewsExtension;
    $this->verify($path);
    $this->registerDirectives();

    $compiler = new Compiler($this->cacheDirectory, $this->viewsDirectory, $this->viewsExtension, $this->directives, $this->engine);
    return $compiler->make($path, $data);
  }

  /**
   * Check if folders/file exist
   *
   * @param string $path
   */
  private function verify(string $path)
  {
    if (!is_dir($this->cacheDirectory))
      mkdir($this->cacheDirectory, 0777, true);

      if (!is_dir($this->cacheDirectory)) {
        return $this->exception(
          'Caching directory "' . $this->cacheDirectory . '" does not exist.'
        );
      }

    if (!is_dir($this->viewsDirectory))
      return $this->exception(
        'Views directory "' . $this->viewsDirectory . '" does not exist.'
      );

    if (!file_exists($path))
      return $this->exception(
        'View file "' . $path . '" does not exist.'
      );
  }
}
