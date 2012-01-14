<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodAutoloader - The Frood autoloader.
 *
 * @category Frood
 * @package  Autoloader
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodAutoloader {
	/** @var array An array of paths to use as the base of autoloading. */
	private $_classPaths;

	/**
	 * Construct a new autoloader.
	 * It will automatically register itself.
	 *
	 * @param array $classPaths An array of paths to use as the base of autoloading.
	 */
	public function __construct(array $classPaths) {
		$this->_classPaths = $classPaths;

		$this->_register();
	}

	/**
	 * Attempts to load the given class.
	 *
	 * @param string $name The name of the class to load.
	 */
	public function autoload($name) {
		if ($filePath = $this->_classNameToPath($name)) {
			include_once $filePath;
		}
	}

	/**
	 * Unregister the autoloader.
	 *
	 * @throws RumtimeException If the autoloader could not be unregistered.
	 */
	public function unregister() {
		if (!spl_autoload_unregister(array($this, 'autoload'))) {
			throw new RumtimeException('Could not unregister.');
		}
	}

	/**
	 * Register the autoloader.
	 */
	private function _register() {
		if (false === spl_autoload_functions()) {
			if (function_exists('__autoload')) {
				spl_autoload_register('__autoload', false);
			}
		}

		spl_autoload_register(array($this, 'autoload'));
	}

	/**
	 * Convert a class name to a path to a file containing the class
	 * definition.
	 * Used by the autoloader.
	 *
	 * @param string $name The name of the class.
	 *
	 * @return null|string A full path or null if no suitable file could be found.
	 */
	private function _classNameToPath($name) {
		if (preg_match('/^((?:[A-Z][a-z0-9]*)+)$/', $name)) {
			// Build a regular expression matching the end of the filepaths to accept...
			$regex = '/[\/\\\][a-z]+[\/\\\]' . substr($name, 0, 1) . preg_replace('/([A-Z])/', '[\/\\\\\\]?\\1', substr($name, 1)) . '\.php$/';

			foreach ($this->_classPaths as $classPath) {
				if ($path = $this->_recursiveFileSearch($classPath, $regex)) {
					return $path;
				}
			}
		}

		return null;
	}

	/**
	 * Internally used method. Used by _classNameToPath.
	 *
	 * @param string $directory The directory to search in.
	 * @param string $regex     The regular expression to match on the full path.
	 *
	 * @return null|string null if no match was found.
	 */
	private function _recursiveFileSearch($directory, $regex) {
		if (!is_dir($directory)) {
			return null;
		}

		$iterator = new DirectoryIterator($directory);

		$subFolders = array();

		foreach ($iterator as $finfo) {
			if (substr($finfo->getBasename(), 0, 1) != '.') {
				if ($finfo->isFile() && preg_match($regex, $finfo->getPathname())) {
					return $finfo->getPathname();
				} else if ($finfo->isDir()) {
					$subFolders[] = $finfo->getPathname();
				}
			}
		}

		foreach ($subFolders as $folder) {
			if ($sub = $this->_recursiveFileSearch($folder, $regex)) {
				return $sub;
			}
		}

		return null;
	}
}