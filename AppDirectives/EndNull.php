<?php

namespace AtlantisPHP\Medusa\AppDirectives;

use AtlantisPHP\Medusa\Directive;

class EndNull extends Directive
{
	/**
	 * Directive extends
	 *
	 * @var $extends
	 */
  protected $extends = true;

	/**
	 * Directive name
	 *
	 * @var $name
	 */
	protected $name = 'endnull';

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