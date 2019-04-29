<?php

namespace AtlantisPHP\Medusa\AppDirectives;

use AtlantisPHP\Medusa\Directive;

class EndEmpty extends Directive
{
	/**
	 * Directive name
	 *
	 * @var $name
	 */
	protected $name = 'endempty';

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