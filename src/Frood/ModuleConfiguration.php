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
	/** @var string The module this configuration is for. */
	private $_module;

	/** @var FroodModuleRouter The router for this module. */
	private $_router;

	/**
	 * Construct a new module configuration for the given module.
	 *
	 * @param string $module The module to construct a configuration for.
	 */
	public function __construct($module) {
		$this->_module = $module;
	}

	/**
	 * Get the module this configuration is for.
	 *
	 * @return string
	 */
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

	/**
	 * Get the module router for the configured module.
	 *
	 * @return FroodModuleConfiguration
	 */
	public function getRouter() {
		return isset($this->_router) ? $this->_router : $this->_router = new FroodModuleRouter($this->_module);
	}
}
