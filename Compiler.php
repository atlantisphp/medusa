<?php

namespace AtlantisPHP\Medusa;

use Exception;

class Compiler
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
	private $cacheDirectory;

	/**
	 * Views directory
	 *
	 * @var $viewsDirectory
	 */
	private $viewsDirectory;

	/**
	 * View extension
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
	private $directives;

	/**
	 * Auto assign
	 *
	 * @param string $cache
	 * @param string $views
	 * @param string $extension
	 * @param array  $directives
	 */
	public function __construct($cache, $views, $extension = '.medusa.php', $directives, $engine = 'modulus')
	{
		$this->cacheDirectory = $cache;
		$this->viewsDirectory = $views;
		$this->viewsExtension = $extension;
		$this->directives = $directives;
		$this->engine = $engine;
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
	 * Return a new view
	 *
	 * @param  string $path
	 * @param  array  $data
	 * @return
	 */
	public function makeView(string $path, array $data = [])
	{
		$cached = $this->isCached($path);
		$this->persist($cached, $data);
	}

	/**
	 * Make a new view
	 *
	 * @param  string $path
	 * @param  array  $data
	 * @return
	 */
	public function make(string $path, array $data = [])
	{
		$cached = $this->isCached($path);
		extract($data);

		ob_start();
		eval('?> '.$cached);
		$output = ob_get_contents();;
		ob_end_clean();
		return $output;
	}

	/**
	 * Compile view
	 *
	 * @param  string $cached
	 * @return
	 */
	private function response($cached)
	{
		$directives = new Directives($cached, $this->viewsDirectory, $this->viewsExtension, $this->directives, $this->engine);
		return $directives->handle();
	}

	/**
	 * Execute compiled code
	 *
	 * @param  string $cached
	 * @param  array  $data
	 * @return
	 */
	private function persist($cached, $data)
	{
		extract($data);
		eval('?> '.$cached);
	}

	/**
	 * Cache view and return cached view
	 *
	 * @param  string $path
	 * @return string $cached
	 */
	private function isCached(string $path)
	{
		$views = glob($this->cacheDirectory . $this->DS . '*'. str_replace($this->DS, '___', $path));

		if (count($views) > 0) {
			$this->canRecache($path, $views[0]);
			return file_get_contents($views[0]) == null ? ' ' : file_get_contents($views[0]);
		}
		else {
			$this->cache($path, uniqid());
		}

		$views = glob($this->cacheDirectory . $this->DS . '*'. str_replace($this->DS, '___', $path));
		if (count($views) > 0) {
			return file_get_contents($views[0]) == null ? ' ' : file_get_contents($views[0]);
		}
	}

	/**
	 * Check if view needs to be cached and return cached view
	 *
	 * @param  string $path
	 * @param  string $cached
	 * @return string $cached
	 */
	private function canRecache($path, $cached)
	{
		// check the cached file if it needs to be recached
		$old = file_get_contents($cached);
		preg_match_all('/\<\!\-\-\%(.*?)\%\-\-\>/', $old, $views);

		foreach($views[1] as $view) {
			$fp = $this->viewsDirectory . $this->DS . base64_decode($view, true) . $this->viewsExtension;

			if (file_exists($fp)) {
				if (filemtime($fp) > filemtime($cached)) {
					return file_put_contents($cached, $this->recache($path));
				}

				continue;
			}

			return file_put_contents($cached, $this->recache($path));
		}

		if (filemtime($path) > filemtime($cached)) {
			file_put_contents($cached, $this->recache($path));
		}
	}

	/**
	 * Recache view
	 *
	 * @param  string $path
	 * @return string $cached
	 */
	private function recache($path)
	{
		return $this->response(file_get_contents($path));
	}

	/**
	 * Cache view
	 *
	 * @param  string $file
	 * @param  string $name
	 * @return string $cached
	 */
	private function cache(string $file, string $name)
	{
		$cache = str_replace($this->DS, '___', $name) . '@' . str_replace($this->DS, '___', $file);

		$compiled = $this->response(file_get_contents($file));

		file_put_contents($this->cacheDirectory . $this->DS . $cache, $compiled);
	}
}
