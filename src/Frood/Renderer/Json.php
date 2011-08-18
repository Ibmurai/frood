<?php
/**
 * A Frood renderer for JSON output.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-08-10
 */

/**
 * FroodRendererJson - A Frood renderer for JSON output.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Renderer
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodRendererJson extends FroodRenderer {
	/**
	 * Render the output as JSON.
	 *
	 * @param array $values The values assigned to the controller.
	 *
	 * @return string
	 */
	public function render(array $values) {
		header('Content-type: application/json');

		$GLOBALS['xoopsLogger']->activated = false;

		echo json_encode($values);
	}
}
