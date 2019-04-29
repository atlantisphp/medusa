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
	 * Default templating engine
	 *
	 * @var $__engine
	 */
	private $__engine = 'modulus';

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
	public function __construct($cached, $directory, $extension, $directives, $engine)
	{
		$this->__cached = $cached;
		$this->__directory = $directory;
		$this->__extension = $extension;
		$this->__directives = $directives;
		$this->__engine = $engine;
	}

	/**
	 * Handle directives
	 *
	 * @return string $this->__cached
	 */
	public function handle()
	{
		$this->mcustom($this->__directives);
		$this->mextend($this->__directory, $this->__extension);
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
		$this->__cached = preg_replace_callback($this->usingDirective('extends\((.*?)\)'), function($match)  use ($dir, $ext) {
			$view = str_replace("'", "", $match[1]);
			$view = str_replace('"', '', $view);

			$path = $dir . $this->DS .$view . $ext;
			$view = $view;

			if (file_exists($path)) {
				return '<!--%' .base64_encode($view). '%-->' . PHP_EOL . file_get_contents($path);
			}
			else {
				$this->exception('"' . $path . '" does not exist.');
			}
		}, $this->__cached);

		$this->mcustom($this->__directives);
	}

	/**
	 * Run custom directives
	 *
	 * @param  array $directives
	 * @return string $this->__cached
	 */
	private function mcustom($directives)
	{
		$e = $this->__engine;

		foreach($directives as $directive) {
			$class = new $directive['class'];
			$reflection = new ReflectionMethod($class, 'message');
			$subject = $directive['class'];
			$object = $this;

			if ($class->name() !== false) {
				$name = $class->name();

				if ($e == 'blade') {
					$pattern = "/\@$name\((.*?)\)/";
				}
				else {
					$pattern = "/\{\% $name\((.*?)\) \%\}/";
				}
			}

			if ($class->directive() !== false) {
				$pattern = $class->directive();
			}

			if (!isset($pattern)) return;

			if (($class->directive() === false) && count($reflection->getParameters()) == 0) {
				if ($e == 'blade') {
					$pattern = "/\@$name/";
				}
				else {
					$pattern = "/\{\% $name \%\}/";
				}
			}

			if (count($reflection->getParameters()) > 0) {
				$this->__cached = preg_replace_callback($pattern, function($match) use($subject, $object, $class, $pattern) {
					return $object->persist($match, $subject, $class);
				}, $this->__cached);
			}
			else {
				$this->__cached = preg_replace_callback($pattern, function($match) use($subject, $object, $class, $pattern) {
					return $object->persist(null, $subject, $class, true);
				}, $this->__cached);
			}
		}
	}

	/**
	 * If statement
	 *
	 * @return string $this->__cached
	 */
	private function mif()
	{
		$this->__cached = preg_replace($this->usingDirective('if', true), '<?php if ($1) : ?>', $this->__cached);
		$this->__cached = preg_replace($this->usingDirective('isset', true), '<?php if (isset($1)) : ?>', $this->__cached);
		$this->__cached = preg_replace($this->usingDirective('empty', true), '<?php if (empty($1)) : ?>', $this->__cached);
		$this->__cached = preg_replace($this->usingDirective('null', true), '<?php if ($1 == null) : ?>', $this->__cached);
		$this->__cached = preg_replace($this->usingDirective('elseif', true), '<?php elseif ($1) : ?>', $this->__cached);
		$this->__cached = preg_replace($this->usingDirective('else'), '<?php else : ?>', $this->__cached);
		$this->__cached = preg_replace($this->usingDirective('endif'), '<?php endif ;?>', $this->__cached);
	}

	/**
	 * Foreach statement
	 *
	 * @return string $this->__cached
	 */
	private function mforeach()
	{
		$this->__cached = preg_replace($this->usingDirective('foreach', true), '<?php foreach ($1) : ?>', $this->__cached);
		$this->__cached = preg_replace($this->usingDirective('endforeach'), '<?php endforeach ;?>', $this->__cached);
	}

	/**
	 * For statement
	 *
	 * @return string $this->__cached
	 */
	private function mfor()
	{
		$this->__cached = preg_replace($this->usingDirective('for', true), '<?php for ($1) : ?>', $this->__cached);
		$this->__cached = preg_replace($this->usingDirective('endfor'), '<?php endfor ;?>', $this->__cached);
	}

	/**
	 * Switch statement
	 *
	 * @return string $this->__cached
	 */
	private function mswitch()
	{
		$this->__cached = preg_replace($this->usingDirective('switch', true), '<?php switch ($1) : ?>', $this->__cached);
		$this->__cached = preg_replace($this->usingDirective('endswitch'), '<?php endswitch ;?>', $this->__cached);
	}

	/**
	 * While statement
	 *
	 * @return string $this->__cached
	 */
	private function mwhile()
	{
		$this->__cached = preg_replace($this->usingDirective('while', true), '<?php while ($1) : ?>', $this->__cached);
		$this->__cached = preg_replace($this->usingDirective('endwhile'), '<?php endwhile ;?>', $this->__cached);
	}

	/**
	 * Echo
	 *
	 * @return string $this->__cached
	 */
	private function mecho()
	{
		$this->__cached = preg_replace('/\{\{(.*?)\}\}/', '<?php echo htmlspecialchars($1, ENT_QUOTES); ?>', $this->__cached);
		$this->__cached = preg_replace('/\{\!\!(.*?)\!\!\}/s', '<?php echo $1; ?>', $this->__cached);
	}

	/**
	 * Sections
	 *
	 * @return string $this->__cached
	 */
	private function msection()
	{
		if ($this->__engine == 'blade') {
			$pattern = '/\@section\((.*?)\)(.*?)\@endsection/s';
		}
		else {
			$pattern = '/\{\% section\((.*?)\) \%\}(.*?)\{\% endsection \%\}/s';
		}

		$this->__cached = preg_replace_callback($pattern, function($match) {
			$name = str_replace("'", "", $match[1]);
			$name = str_replace('"', '', $name);

			$code = $match[2];

			$section = array('name' => $name, 'code' => $code);
			array_push(static::$sections, $section);
		}, $this->__cached);

		$this->__cached = preg_replace_callback($this->usingDirective('yield', true, true), function($match) {
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
		$this->__cached = preg_replace('/\{\-\-(.*?)\-\-\}/s', '<?php /*$1*/ ?>', $this->__cached);
	}

	/**
	 * Return code based on specified engine
	 *
	 * @param  string  $pattern
	 * @param  boolean $control
	 */
	private function usingDirective($pattern, $control = false, $capture = false)
	{
		if ($control) {
			switch ($this->__engine) {
				case 'blade':
					return "/\@$pattern\((.*?)\)/";
					break;

				case 'modulus':
					return $capture == true ? "/\{\% $pattern\((.*?)\) \%\}/" : "/\{\% $pattern (.*?) \%\}/";
					break;

				default:
					return '';
					break;
			}

			return;
		}

		switch ($this->__engine) {
			case 'blade':
				return "/\@$pattern/";
				break;

			case 'modulus':
				return "/\{\% $pattern \%\}/";
				break;

			default:
				return '';
				break;
		}
	}

	/**
	 * Extend view
	 *
	 * @param  array  $match
	 * @param  object $subject
	 * @return string
	 */
	private function persist($match, $subject, $directive, $isstring = false)
	{
		$match = isset($match[1]) ? $match[1] : null;
		$uses = $directive->uses() !== false ? $directive->uses() : null;

		$e = $this->__engine;

		if ($uses !== null && $directive->name() !== false && $this->_control($uses)) {
			return "<?php $uses ((new $subject)->message($match)) : ?>";
		}
		else if ($uses !== null && $directive->name() !== false && $this->_control($uses) === false) {
			return "<?php $uses((new $subject)->message($match)); ?>";
		}
		else if ($directive->name() !== true && $uses === null) {
			return $directive->message($match);
		}
	}

	/**
	 * Checks if directive uses control structure
	 *
	 * @param string $uses
	 * @return bool
	 */
	private function _control($uses)
	{
		if (in_array($uses, ['if', 'isset', 'empty', 'null', 'elseif', 'foreach', 'for', 'switch', 'while'])) {
			return true;
		}

		return false;
	}

	/**
	 * Clean url
	 *
	 * @param  string $path
	 * @return string $path
	 */
	private function cleanPath($path)
	{
		$path = str_replace('/', $this->DS, $path);
		$path = str_replace('\\', $this->DS, $path);
		$path = preg_replace('#' . $this->DS . '+#', $this->DS, $path);

		return $path;
	}
}
