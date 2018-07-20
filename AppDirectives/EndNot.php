<?php

namespace AtlantisPHP\Medusa\AppDirectives;

class EndNot
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
	public $name = 'endnot';

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