<?php

namespace AtlantisPHP\Medusa\AppDirectives;

use AtlantisPHP\Medusa\Directive;

class EndIsset extends Directive
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
	protected $name = 'endisset';

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