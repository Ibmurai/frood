<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodNullParameter - FroodParameters uses this singleton internally to indicate a true "no value".
 *
 * @category Frood
 * @package  Parameters
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
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
