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
	/**
	 * Get the full path to a template.
	 *
	 * @param string $module       The module to get a template for.
	 * @param string $templateFile The partial path to the template file to use, as returned by FroodRendererTemplate::_getTemplateFile().
	 *
	 * @return string
	 */
	public function getTemplateFile($module, $templateFile) {
		return "{$this->getTemplatesPath($module)}$templateFile";
	}

	/**
	 * Get the base path for templates for a given module.
	 *
	 * @param string $module The module to get the path for.
	 *
	 * @return string The path.
	 */
	public function getTemplatesPath($module) {
		return "{$this->getModuleBasePath($module)}templates/";
	}

	/**
	 * This function provides the base routes, i.e. relates uri prefixes to modules.
	 *
	 * @return array[] The keys are uri prefixes and the values are arrays of module names.
	 */
	public function getBaseRoutes() {
		$modules = array();
		$iterator = new DirectoryIterator($this->getModulesPath());
		foreach ($iterator as $module) {
			if ($module->isDir() && !$module->isDot() && substr($module->getFilename(), 0, 1) != '.') {
				$name = FroodUtil::convertPhpNameToHtmlName($module->getFilename());
				$modules["/$name"] = array(FroodUtil::convertHtmlNameToPhpName($module->getFilename()));
			}
		}
		return $modules;
	}

	/**
	 * Get the root path of a given module.
	 *
	 * @param string $module
	 *
	 * @return string
	 */
	public function getModuleBasePath($module) {
		return $this->getModulesPath() . FroodUtil::convertHtmlNameToPhpName($module) . '/';
	}

	/**
	 * Get the path, relative to Frood.php, where modules reside.
	 *
	 * @return string
	 */
	public function getModulesPath () {
		return realpath(Frood::getFroodPath() . '../../../modules') . '/';
	}

	/**
	 * Get the request URI.
	 *
	 * @return string
	 */
	public function getRequestUri() {
		$matches = array();
		if (preg_match('/^([^\?]*)/', $_SERVER['REQUEST_URI'], $matches)) {
			return $matches[1];
		}
	}

	/**
	 * Get the module configuration for a given module.
	 *
	 * @param string $module The module to get configuration for.
	 *
	 * @return FroodModuleConfiguration
	 *
	 * @throws FroodExceptionConfiguration If a Configuration.php is found, but it does not define a valid configuration.
	 */
	public function getModuleConfiguration($module) {
		/* @var FroodModuleConfiguration[] */
		static $moduleConfigurations = array();

		if (array_key_exists($module, $moduleConfigurations)) {
			return $moduleConfigurations[$module];
		}

		$moduleConfigurationPath = $this->getModuleBasePath($module) . 'Configuration.php';

		$moduleConfigurationClassName = 'FroodModuleConfiguration';
		if (file_exists($moduleConfigurationPath)) {
			require_once $moduleConfigurationPath;
			if (!class_exists($moduleConfigurationClassName = FroodUtil::convertHtmlNameToPhpName("{$module}_configuration"))) {
				if (!class_exists($moduleConfigurationClassName = '\\' . FroodUtil::convertHtmlNameToPhpName("{$module}") . '\\Configuration')) {
					throw new FroodExceptionConfiguration("$moduleConfigurationPath was found, but does not define a correctly named configuration class.");
				}
			}
		}

		return $moduleConfigurations[$module] = new $moduleConfigurationClassName($module);
	}

	/**
	 * This should return a string describing the remote uri for the given
	 * FroodRequest.
	 *
	 * @param FroodRequest $request
	 *
	 * @return string
	 */
	public function getRemoteUri(FroodRequest $request) {
		return "{$request->getModule()}/{$request->getController()}/{$request->getAction()}";
	}

	/**
	 * Get the path to froods cache dir.
	 * 
	 * @return string
	 */
	public function getCacheDir() {
		return Frood::getFroodPath() . 'cache/';
	}
	
	/**
	 * Should persistent miss cache be enabled?
	 * 
	 * @return boolean Enable/disable.
	 */
	public function isAutoloaderMissCacheEnabled() {
		return true;
	}
}
