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

		return preg_replace_callback('/([A-Z])/', array(__CLASS__, 'convertPhpNameToHtmlNameHelper'), $name);
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

		return preg_replace_callback('/(_[a-z0-9])/', array(__CLASS__, 'convertHtmlNameToPhpNameHelper') , $name);
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

	/**
	 * Damn U PHP5.2!
	 *
	 * @param array $matches :(
	 *
	 * @return string
	 */
	public static function convertHtmlNameToPhpNameHelper($matches) {
		return substr(strtoupper($matches[1]),1);
	}

	/**
	 * Damn U PHP5.2!
	 *
	 * @param array $matches :(
	 *
	 * @return string
	 */
	public static function convertPhpNameToHtmlNameHelper($matches) {
		return '_' . strtolower($matches[1]);
	}
}
