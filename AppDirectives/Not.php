<?php

namespace AtlantisPHP\Medusa\AppDirectives;

class Not
{
	/**
	 * Override directive
	 *
	 * @var $directive
	 */
	public $directive = '/not\((.*?)\)/';

	/**
	 * Directive extends
	 *
	 * @var $extends
	 */
	public $extends = true;

	/**
	 * Directive uses
	 *
	 * @var $uses
	 */
	public $uses = 'if';

	/**
	 * Directive name
	 *
	 * @var $name
	 */
	public $name = 'not';

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