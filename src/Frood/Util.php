<?php
/**
 * Abstract class with static utility methods.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-08-10
 */

/**
 * FroodUtil - Abstract class with static utility methods.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
abstract class FroodUtil {
	/**
	 * Converts a camelCased string to a lowercased_with_underscores string.
	 *
	 * @param string $name The CamelCased string to convert.
	 *
	 * @return string A lowercased_with_underscores version of $name.
	 */
	public static function convertPhpNameToHtmlName($name) {
		// First lowercase the first letter.
		$name = strtolower(substr($name, 0, 1)) . substr($name, 1);

		// Second replace capital letters with _ followed by the letter, lowercased.
		return preg_replace('/([A-Z])/e', "'_'.strtolower('\\1')", $name);
	}

	/**
	 * Converts a lowercased_with_underscores string to a CamelCased string.
	 *
	 * @param string  $name    The lowercased_with_underscores string to convert.
	 * @param boolean $ucFirst Set this to false to get a dromedaryCased string instead.
	 *
	 * @return string A CamelCased or dromedaryCased version of $name.
	 */
	public static function convertHtmlNameToPhpName($name, $ucFirst = true) {
		// First uppercase the first letter.
		if ($ucFirst) {
			$name = strtoupper(substr($name, 0, 1)) . substr($name, 1);
		}

		// Second replace _ followed by a letter with capital letters.
		return preg_replace('/(_[a-z0-9])/e', "substr(strtoupper('\\1'),1)", $name);
	}
}