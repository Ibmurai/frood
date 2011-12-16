<?php
/**
 * FroodNullParameter.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-30
 */

/**
 * FroodNullParameter - FroodParameters uses this singleton internally to indicate a true "no value".
 *
 * @category   Library
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodNullParameter {
	/** @var FroodNullParameter FroodParameters uses this instance internally to indicate a true "no value". */
	private static $_instance = null;

	/**
	 * Get the FroodNullParameter instance.
	 *
	 * @return FroodNullParameter
	 */
	public static function getInstance() {
		if (self::$_instance === null) {
			self::$_instance = new FroodNullParameter();
		}

		return self::$_instance;
	}
}
