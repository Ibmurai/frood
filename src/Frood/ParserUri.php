<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodParserUri - The Frood URI parser.
 *
 * @category Frood
 * @package  Parser
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodParserUri {
	/** @var string */
	private $_uriFormat;

	/** @var string */
	private $_module = null;

	/** @var string */
	private $_subModule = null;

	/** @var string */
	private $_controller = null;

	/** @var string */
	private $_action = null;

	/**
	 * Constructor.
	 *
	 * @param string $uriFormat The URI format.
	 *
	 * @throws FroodException
	 */
	public function __construct($uriFormat) {
		$this->_uriFormat = $uriFormat;
	}

	/**
	 * Parse the URI.
	 *
	 * @param string $uri
	 *
	 * @throws FroodException
	 */
	public function parse($uri) {
		$matches = array();
		preg_match($this->_uriFormat, $uri, $matches);
		if (count($matches) != 5) {
			throw new FroodException(sprintf('Could not parse the URI: %s', $uri));
		}
		$this->_module     = $matches[1];
		$this->_subModule  = $matches[2];
		$this->_controller = $matches[3];
		$this->_action     = $matches[4];
	}

	/**
	 * @return string|null
	 */
	public function getModule() {
		return $this->_module;
	}

	/**
	 * @return string|null
	 */
	public function getSubModule() {
		return $this->_subModule;
	}

	/**
	 * @return string|null
	 */
	public function getController() {
		return $this->_controller;
	}

	/**
	 * @return string|null
	 */
	public function getAction() {
		return $this->_action;
	}
}
