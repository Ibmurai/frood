<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodUtil - Abstract class with static utility methods.
 *
 * @category Frood
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
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
		if (PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION >= 3) {
			// /e modifier is deprecated in 5.5 and the lambda function is the more elegang approach, so do that if 5.3 or better.
			return preg_replace_callback('/([A-Z])/', function($matches) { return '_' . strtolower($matches[1]); }, $name);
		} else {
			return preg_replace('/([A-Z])/e', "'_'.strtolower('\\1')", $name);
		}
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
		if (PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION >= 3) {
			// /e modifier is deprecated in 5.5 and the lambda function is the more elegang approach, so do that if 5.3 or better.
			return preg_replace_callback('/(_[a-z0-9])/', function($matches) { return substr(strtoupper($matches[1]),1); }, $name);
		} else {
			return preg_replace('/(_[a-z0-9])/e', "substr(strtoupper('\\1'),1)", $name);
		}
	}

	/**
	 * Comparison function to usort by string length.
	 *
	 * @param string $a The one string to compare.
	 * @param string $b The other string to compare.
	 *
	 * @return integer The difference of the string lengths.
	 */
	public static function cmplen($a, $b) {
		return strlen($b) - strlen($a);
	}
}
