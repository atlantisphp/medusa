<?php

namespace AtlantisPHP\Medusa\AppDirectives;

class EndIsset
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
	public $name = 'endisset';

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