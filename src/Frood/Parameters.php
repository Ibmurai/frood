<?php
/**
 * The parameters class for The Frood.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-09
 */

/**
 * FroodParameters - All controller actions are called with an
 * instance of this class.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodParameters {
	/** @var array This associative array contains the actual parameter values. */
	private $_values = array();

	/**
	 * The constructor.
	 *
	 * The constructor generates parameters from GET and POST by default.
	 * If the same parameter(s) exist in both, POST "wins".
	 * Pass an associative array to override this behaviour.
	 *
	 * @param array $from An associative array to generate parameters from.
	 *
	 * @return void
	 */
	public function __construct(array $from = null) {
		if ($from === null) {
			$from = array_merge($_GET, $_POST);
		}
	}

	/**
	 * This handles all calls to ->getXxx() methods.
	 *
	 * @param string $name The name of the method being called.
	 * @param array  $args An enumerated array containing the parameters passed to the $name'ed method.
	 *
	 * @return mixed It's like a box of chocolates.
	 *
	 * @throws RuntimeException For non-existing methods and parameters.
	 */
	public function __call($name, array $args) {
		$matches = array();
		if (preg_match('/^get([A-Z][A-Za-z0-9]*)$/', $name, $matches)) {
			return $this->_getParameter($matches[1]);
		}

		throw new RuntimeException("Call to undefined method, $name.");
	}

	/**
	 * Converts a camelCased string to a lowercased_with_underscores string.
	 *
	 * @param $name The camelCased string to convert.
	 *
	 * @return A lowercased_with_underscores version of $name.
	 */
	public static function convertPhpNameToHtmlName($name) {
		// First lowercase the first letter.
		$name = strtolower(substr($name, 0, 1)) . substr($name, 1);

		// Second replace capital letters with _ followed by the letter, lowercased.
		return preg_replace('/([A-Z])/e', "'_'.strtolower('\\1')", $name);
	}

	/**
	 * Get the named parameter.
	 *
	 * @param string $name The name of the parameter to get.
	 *
	 * @return mixed It's like a box of chocolates.
	 *
	 * @throws RuntimeException For non-existing parameters.
	 */
	private function _getParameter($name) {
		$name = self::convertPhpNameToHtmlName($name);


	}
}
