<?php
/**
 * FroodModuleConfiguration.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @author   Bo Thinggaard <both@fynskemedier.dk>
 * @since    2011-12-16
 */

/**
 * FroodModuleConfiguration - Base module configuration.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 * @author     Bo Thinggaard <both@fynskemedier.dk>
 */
class FroodModuleConfiguration {
	/**
	 * Get module autoload base paths relative to the module root.
	 *
	 * @return array Paths.
	 */
	protected function getAutoloadBasePaths() {
		return array(
			'public' => 'Public',
			'shared' => 'Shared',
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
}
