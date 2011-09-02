<?php
/**
 * A Frood renderer for JSON output.
 * This one automatically UTF8 encodes every contained string.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-09-02
 */

/**
 * FroodRendererJsonAutoUtf8 - A Frood renderer for JSON output.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Renderer
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodRendererJsonAutoUtf8 extends FroodRendererJson {
	/**
	 * Render the output as JSON.
	 *
	 * @param array $values The values assigned to the controller.
	 *
	 * @return string
	 */
	public function render(array $values) {
		parent::render(self::_utf8EncodeStrings($values));
	}

	/**
	 * Recursively UTF8 encode strings in an array.
	 *
	 * @param array $values The array to encode.
	 *
	 * @return array The array of UTF8 encoded strings.
	 */
	public function _utf8EncodeStrings(array $values) {
		foreach ($values as &$value) {
			if (is_string($value)) {
				$value = utf8_encode($value);
			} else if (is_array($value)) {
				$value = self::_utf8EncodeStrings($value);
			}
		}

		return $values;
	}
}
