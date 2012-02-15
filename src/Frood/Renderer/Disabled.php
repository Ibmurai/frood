<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodRendererDisabled - A Frood renderer for no rendered output.
 *
 * @category Frood
 * @package  Renderer
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class FroodRendererDisabled extends FroodRenderer {
	/**
	 * Does not render output.
	 *
	 * @param array &$values The values assigned to the controller.
	 *
	 * @return null
	 */
	public function render(array &$values) {
	}
}
