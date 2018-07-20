<?php

namespace AtlantisPHP\Medusa\AppDirectives;

class EndNull
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
	public $name = 'endnull';

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