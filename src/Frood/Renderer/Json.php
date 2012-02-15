<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodRendererJson - A Frood renderer for JSON output.
 *
 * @category Frood
 * @package  Renderer
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class FroodRendererJson extends FroodRenderer {
	/** @var string The content type. */
	protected $_contentType = 'application/json';

	/**
	 * Render the output as JSON.
	 *
	 * @param array &$values The values assigned to the controller.
	 *
	 * @return null
	 */
	public function render(array &$values) {
		if (empty($values)) {
			echo '{}';
		} else {
			echo json_encode($values);
		}
	}
}
