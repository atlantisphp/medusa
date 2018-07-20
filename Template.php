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
     * Custom directives
     *
     * @var $directives
     */
    private $directives = [];

    /**
     * Register custom directives
     *
     * @return boolean
     */
    public function __construct()
    {
        $this->register(EndEmpty::class);
        $this->register(EndIsset::class);
        $this->register(EndNot::class);
        $this->register(EndNull::class);
        $this->register(Not::class);

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
     * @return
     */
    public function setCacheDirectory(string $directory)
    {
        $directory = $this->cleanPath($directory);

        $this->cacheDirectory = $directory;
        return true;
    }

    /**
     * Set default views directory.
     *
     * @param  string $directory
     * @return
     */
    public function setViewsDirectory(string $directory)
    {
        $directory = $this->cleanPath($directory);

        if (!is_dir($directory))
            return $this->exception(
                'Views directory "' . $directory . '" does not exist.'
            );

        $this->viewsDirectory = $directory;
        return true;
    }

    /**
     * Set default caching directory.
     *
     * @param  string $directory
     * @return
     */
    public function setViewsExtension(string $extension)
    {
        if (substr($extension, 0, 1) != '.') {
            $this->viewsExtension = '.' . $extension;
            return true;
        }

        $this->viewsExtension = $extension;
        return true;
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
     * @return boolean
     */
    public function register(string $class)
    {
        if (!class_exists($class))
            return $this->exception(
                'Class "' . $class . '" does not exist.'
            );

        $this->directives[] = ['class' => $class];
        return true;
    }

    /**
	 * Return a new view
	 *
	 * @param  string $view
	 * @param  array  $data
	 * @return
	 */
    public function view(string $view, array $data = [])
    {
        $path = $this->viewsDirectory . $this->DS . $view . $this->viewsExtension;
        $this->verify($path);

        $compiler = new Compiler($this->cacheDirectory, $this->viewsDirectory, $this->viewsExtension, $this->directives);
        $compiler->makeView($path, $data);
    }

    /**
	 * Make a new view
	 *
	 * @param  string $view
	 * @param  array  $data
	 * @return
	 */
    public function make(string $view, array $data = [])
    {
        $path = $this->viewsDirectory . $this->DS . $view . $this->viewsExtension;
        $this->verify($path);

        $compiler = new Compiler($this->cacheDirectory, $this->viewsDirectory, $this->viewsExtension, $this->directives);
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
