<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * The Frood URI parser.
 *
 * @category   Frood
 * @package    Parser
 * @subpackage Uri
 * @author     Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author     Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodParserUri {
	/** @var FroodConfiguration The Frood configuration */
	private $_froodConfiguration;

	/** @var string */
	private $_module;

	/** @var string */
	private $_subModule;

	/** @var string */
	private $_controller;

	/** @var string */
	private $_action;

	/**
	 * Constructor.
	 */
	public function __construct(FroodConfiguration $froodConfiguration) {
		$this->_froodConfiguration = $froodConfiguration;
	}

	/**
	 * Parse the given URI.
	 *
	 * @param string $uri The URI to parse.
	 */
	private function _parse($uri) {

	}
}
