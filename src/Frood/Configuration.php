<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodConfiguration - The Frood configuration.
 *
 * @category   Frood
 * @package    Configuration
 * @author     Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author     Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodConfiguration {
	/** @var FroodParserUri */
	private $_uriParser = null;
	
	public function getModuleBasePath($module) {
		return $this->getModulesPath() . FroodUtil::convertHtmlNameToPhpName($module) . '/';
	}
	
	/**
	 * Get the path, relative to Frood.php, where modules reside.
	 *
	 * @return string
	 */
	public function getModulesPath () {
		return '../../../modules/';
	}

	/**
	 * Get the regex format used to parse the URI.
	 *
	 * @return string
	 */
	protected function _getUriFormat() {
		return '/
			^
			\/([a-z][a-z0-9_]*) # 1 : module
			\/([a-z][a-z0-9_]*) # 2 : subModule
			\/([a-z][a-z0-9_]*) # 3 : controller
			\/([a-z][a-z0-9_]*) # 4 : action
		/x';
	}
	
	/**
	 * Get the request URI.
	 *
	 * @return string
	 */
	public function getRequestUri() {
		return $_SERVER['REQUEST_URI'];
	}
	
	/**
	 * Get the URI parser
	 * 
	 * @return FroodParserUri
	 */
	public function getUriParser() {
		return $this->_uriParser ? $this->_uriParser : $this->_uriParser = new FroodParserUri($this->_getUriFormat());
	}
}
