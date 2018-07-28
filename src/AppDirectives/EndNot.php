<?php

namespace AtlantisPHP\Medusa\AppDirectives;

use AtlantisPHP\Medusa\Directive;

class EndNot extends Directive
{
	/**
	 * Directive name
	 *
	 * @var $name
	 */
	protected $name = 'endnot';

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