<?php

namespace AtlantisPHP\Medusa\AppDirectives;

class EndEmpty
{
	/**
	 * Override directive
	 *
	 * @var $directive
	 */

  public $extends = true;

	/**
	 * Directive name
	 *
	 * @var $name
	 */
	public $name = 'endempty';

	/**
	 * Handle directive
	 *
	 * @return string
	 */
	public function message()
	{
		return "<?php endif ;?>";
	}
}