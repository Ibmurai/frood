<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodModuleConfiguration - Base module configuration.
 *
 * @category Frood
 * @package  Module
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author   Bo Thinggaard <both@fynskemedier.dk>
 */
class FroodModuleConfiguration {
	private $_module;
	
	public function __construct($module) {
		$this->_module = $module;
	}

	public function getModule() {
		return $this->_module;
	}

		
	/**
	 * Get module autoload base paths relative to the module root.
	 *
	 * @return array Paths.
	 */
	protected function getAutoloadBasePaths() {
		return array(
			'public' => 'Public/',
			'shared' => 'Shared/',
		);
	}

	/**
	 * Get module autoload base path relative to the module root.
	 *
	 * @param string $subModule Sub module name.
	 *
	 * @return string Path if found.
	 *
	 * @throws FroodExceptionConfiguration Module configuration missing sub module path.
	 */
	final public function getAutoloadBasePath($subModule) {
		$paths = $this->getAutoloadBasePaths();

		if (!array_key_exists($subModule, $paths)) {
			throw new FroodExceptionConfiguration("Module configuration missing $subModule path.");
		}

		return $paths[$subModule];
	}
	
	public function getRouter() {
		return new FroodModuleRouter($this->_module);
	}
}
