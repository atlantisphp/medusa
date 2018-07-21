<?php

namespace AtlantisPHP\Medusa\AppDirectives;

use AtlantisPHP\Medusa\Directive;

class Not extends Directive
{
	/**
	 * Override directive
	 *
	 * @var $directive
	 */
	protected $directive = '/not\((.*?)\)/';

	/**
	 * Directive extends
	 *
	 * @var $extends
	 */
	protected $extends = true;

	/**
	 * Directive uses
	 *
	 * @var $uses
	 */
	protected $uses = 'if';

	/**
	 * Directive name
	 *
	 * @var $name
	 */
	protected $name = 'not';

	/**
	 * Handle directive
	 *
	 * @param string $variable
	 * @param string $not
	 */
	public function message($variable, $not)
	{
		if ($variable !== $not) {
			return true;
		}

		return false;
	}
}