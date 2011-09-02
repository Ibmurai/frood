<?php
/**
 * A Frood renderer for no rendered output.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-09-02
 */

/**
 * FroodRendererDisabled - A Frood renderer for no rendered output.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Renderer
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodRendererDisabled extends FroodRenderer {
	/**
	 * Does not render output.
	 *
	 * @param array $values The values assigned to the controller.
	 *
	 * @return string
	 */
	public function render(array $values) {
		$GLOBALS['xoopsLogger']->activated = false;
	}
}
