<?php

namespace AtlantisPHP\Medusa;

use Exception;
use ReflectionMethod;

class Directives
{
	/**
	 * Directory Separator
	 *
	 * @var $DS
	 */
	private $DS = DIRECTORY_SEPARATOR;

	/**
	 * Cached view
	 *
	 * @var $__cached
	 */
	private $__cached;

	/**
	 * Views directory
	 *
	 * @var $__directory
	 */
	private $__directory;

	/**
	 * View extension
	 *
	 * @var $__extension
	 */
	private $__extension;

	/**
	 * Custom directives
	 *
	 * @var $__directives
	 */
	private $__directives;

	/**
	 * View sections
	 *
	 * @var $sections
	 */
	public static $sections = [];

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
	 * Auto assign
	 *
	 * @param string $cache
	 * @param string $directory
	 * @param string $extension
	 * @param array  $directives
	 */
	public function __construct($cached, $directory, $extension, $directives)
	{
		$this->__cached = $cached;
		$this->__directory = $directory;
		$this->__extension = $extension;
		$this->__directives = $directives;
	}

	/**
	 * Handle directives
	 *
	 * @return string $this->__cached
	 */
	public function handle()
	{
		$this->mextend($this->__directory, $this->__extension);
		$this->mcustom($this->__directives);
		$this->mif();
		$this->mforeach();
		$this->mfor();
		$this->mswitch();
		$this->mwhile();
		$this->mecho();
		$this->msection();
		$this->mcode();
		$this->mcomment();
		return $this->__cached;
	}

	/**
	 * Extend view
	 *
	 * @param  string $dir
	 * @param  string $ext
	 * @return string $this->__cached
	 */
	private function mextend($dir, $ext)
	{
		$this->__cached = preg_replace_callback('/\{\% extend\((.*?)\) \%\}/', function($match)  use ($dir, $ext) {
			$view = str_replace("'", "", $match[1]);
			$view = str_replace('"', '', $view);

			if (file_exists($dir . $this->DS . $view . $ext)) {
				return '<!--%' .base64_encode($view). '%-->' . PHP_EOL . file_get_contents($dir . '/' . $view . $ext);
			}
			else {
				$this->exception('"' . $dir . $this->DS . $view . $ext . '" does not exist.');
			}
		}, $this->__cached);
	}

	/**
	 * Run custom directives
	 *
	 * @param  array $directives
	 * @return string $this->__cached
	 */
	private function mcustom($directives)
	{
		foreach($directives as $directive) {
			$class = new $directive['class'];
			$reflection = new ReflectionMethod($class, 'message');
			$subject = $directive['class'];
			$object = $this;

			if (isset($class->name)) {
				$name = $class->name;
				$pattern = "/\{\% $name\((.*?)\) \%\}/";
			}

			if (isset($class->directive)) {
				$pattern = $class->directive;
			}

			if (!isset($pattern)) return;

			if (count($reflection->getParameters()) > 0) {

				$this->__cached = preg_replace_callback($pattern, function($match) use($subject, $object) {
					return $object->persist($match, $subject);
				}, $this->__cached);

				return;
			}

			$this->__cached = preg_replace_callback($pattern, function($match) use($subject, $object) {
				return $object->persist(null, $subject);
			}, $this->__cached);
		}
	}

	/**
	 * If statment
	 *
	 * @return string $this->__cached
	 */
	private function mif()
	{
		$this->__cached = preg_replace('/\{\% if (.*?) \%\}/', '<?php if ($1) : ?>', $this->__cached);
		$this->__cached = preg_replace('/\{\% isset (.*?) \%\}/', '<?php if (isset($1)) : ?>', $this->__cached);
		$this->__cached = preg_replace('/\{\% empty (.*?) \%\}/', '<?php if (empty($1)) : ?>', $this->__cached);
		$this->__cached = preg_replace('/\{\% null (.*?) \%\}/', '<?php if ($1 == null) : ?>', $this->__cached);
		$this->__cached = preg_replace('/\{\% elseif (.*?) \%\}/', '<?php elseif ($1) : ?>', $this->__cached);
		$this->__cached = preg_replace('/\{\% else \%\}/', '<?php else : ?>', $this->__cached);
		$this->__cached = preg_replace('/\{\% endif \%\}/', '<?php endif ;?>', $this->__cached);
	}

	/**
	 * Foreach statement
	 *
	 * @return string $this->__cached
	 */
	private function mforeach()
	{
		$this->__cached = preg_replace('/\{\% foreach (.*?) \%\}/', '<?php foreach ($1) : ?>', $this->__cached);
		$this->__cached = preg_replace('/\{\% endforeach \%\}/', '<?php endforeach ;?>', $this->__cached);
	}

	/**
	 * For statement
	 *
	 * @return string $this->__cached
	 */
	private function mfor()
	{
		$this->__cached = preg_replace('/\{\% for (.*?) \%\}/', '<?php for ($1) : ?>', $this->__cached);
   		$this->__cached = preg_replace('/\{\% endfor \%\}/', '<?php endfor ;?>', $this->__cached);
	}

	/**
	 * Switch statement
	 *
	 * @return string $this->__cached
	 */
	private function mswitch()
	{
		$this->__cached = preg_replace('/\{\% switch (.*?) \%\}/', '<?php switch ($1) : ?>', $this->__cached);
    	$this->__cached = preg_replace('/\{\% endswitch \%\}/', '<?php endswitch ;?>', $this->__cached);
	}

	/**
	 * While statement
	 *
	 * @return string $this->__cached
	 */
	private function mwhile()
	{
		$this->__cached = preg_replace('/\{\% while (.*?) \%\}/', '<?php while ($1) : ?>', $this->__cached);
    	$this->__cached = preg_replace('/\{\% endwhile \%\}/', '<?php endwhile ;?>', $this->__cached);
	}

	/**
	 * Echo
	 *
	 * @return string $this->__cached
	 */
	private function mecho()
	{
		$this->__cached = preg_replace('/\{\{(.*?)\}\}/', '<?php echo htmlspecialchars($1, ENT_QUOTES); ?>', $this->__cached);
		$this->__cached = preg_replace('/\{\!\!(.*?)\!\!\}/', '<?php echo $1; ?>', $this->__cached);
	}

	/**
	 * Sections
	 *
	 * @return string $this->__cached
	 */
	private function msection()
	{
		$this->__cached = preg_replace_callback('/\{\% in\((.*?)\) \%\}(.*?)\{\% endin \%\}/s', function($match) {
			$name = str_replace("'", "", $match[1]);
			$name = str_replace('"', '', $name);

			$code = $match[2];

			$section = array('name' => $name, 'code' => $code);
			array_push(static::$sections, $section);
		}, $this->__cached);

		$this->__cached = preg_replace_callback('/\{\% section\((.*?)\) \%\}/', function($match) {
			$name = str_replace("'", "", $match[1]);
			$name = str_replace('"', '', $name);

			foreach (static::$sections as $value) {
				if ($value['name'] == $name) {
					return $value['code'];
				}
			}

			return '';
		}, $this->__cached);

		static::$sections = [];
	}

	/**
	 * PHP Code
	 *
	 * @return string $this->__cached
	 */
	private function mcode()
	{
		$this->__cached = preg_replace('/\{\%(.*?)\%\}/s', '<?php $1 ?> ', $this->__cached);
	}

	/**
	 * Comment
	 *
	 * @return string $this->__cached
	 */
	private function mcomment()
	{
    	$this->__cached = preg_replace('/\{\{\-\-(.*?)\-\-\}\}/s', '<?php /*$1*/ ?>', $this->__cached);
	}

	/**
	 * Extend view
	 *
	 * @param  array  $match
	 * @param  object $subject
	 * @return string
	 */
	private function persist($match, $subject)
	{
		if ($match != null) {
			return "<?php echo (new $subject)->message($match[1]) ;?>";
		}

		return "<?php echo (new $subject)->message() ;?>";
	}
}
