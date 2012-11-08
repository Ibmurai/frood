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

	/** @var string[] An array of cached classes. */
	private $_classCache = array();

	/** @var string Complete path to cache directory. */
	private $_cacheDir;
	
	/** @var string[] Stores classes that are not found by this autoloader. */
	private $_fileCache = array();

	/**
	 * Construct a new autoloader.
	 * It will automatically register itself.
	 *
	 * @param array $classPaths An array of paths to use as the base of autoloading.
	 */
	public function __construct(array $classPaths, $cacheDir = null) {
		if ($cacheDir === null) {
			$cacheDir = dirname(__FILE__) . '/../cache/'; // TODO: cacheDir should not be part of the constructor. Either change to a static setter, or make it part of an injected cache component.
		}
		
		$this->_cacheDir = $cacheDir;
		
		foreach ($classPaths as $classPath) {
			$this->addClassPath($classPath);
		}

		$this->_register();
	}

	/**
	 * Dynamically add a class path to this autoloader.
	 *
	 * @param string $classPath
	 */
	public function addClassPath($classPath) {
		$this->_classPaths[] = $classPath;
		
		$this->_classCache[$classPath] = $this->_loadCache($classPath);
		$this->_fileCache[$classPath] = $this->_getFiles($classPath);
		
		$this->_validateCache($classPath);
	}
	
	/**
	 * Converts a classpath to a valid filename.
	 * 
	 * @param string $classPath The class path.
	 * 
	 * @return string The classpath as a valid filename.
	 */
	private static function _classPathToFilename($classPath) {
		static $namespaces = array();
		
		if (!isset($namespaces[$classPath])) {
			$namespaces[$classPath] = preg_replace('/[\/\\\: ]/', '_', $classPath);
		}
		
		return $namespaces[$classPath];
	}
	
	/**
	 * Check if the classes if the cache can still be found at the cached location.
	 * Cleares cache if invalid.
	 * 
	 * @param string $classPath The class path.
	 */
	private function _validateCache($classPath) {
		foreach ($this->_classCache[$classPath] as $class) {
			if (!in_array($class, $this->_fileCache[$classPath])) {
				$this->_classCache[$classPath] = array();
				@unlink($this->_cacheDir . self::_classPathToFilename($classPath));
				break;
			}
		}
	}

	/**
	 * Load the class cache for a class cache.
	 * 
	 * @param string $classPath The class path.
	 *
	 * @return array Class names => filepaths.
	 */
	private function _loadCache($classPath) {
		$namespace = self::_classPathToFilename($classPath);
		return ($this->_cacheDir && $classCache = @file_get_contents($this->_cacheDir . $namespace)) ? unserialize($classCache) : array();
	}

	/**
	 * Persist the class cache for a class path.
	 * 
	 * @param string $classPath The class path.
	 *
	 * @return boolean Success.
	 */
	private function _persistCache($classPath) {
		$namespace = self::_classPathToFilename($classPath);
		return $this->_cacheDir ? @file_put_contents($this->_cacheDir . $namespace, serialize($this->_classCache[$classPath])) : false;
	}

	/**
	 * Attempts to load the given class.
	 *
	 * @param string $name The name of the class to load.
	 */
	public function autoload($name) {
		foreach ($this->_classCache as $classes) {
			if (isset($classes[$name])) {
				include_once $classes[$name];
				return;
			}
		}

		if ($path = $this->_classNameToPath($name)) {
			include_once $path;
			return;
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
			$regex = '/[\/\\\][a-z]+[A-Za-z_-]*[\/\\\]' . substr($name, 0, 1) . preg_replace('/([A-Z])/', '[\/\\\\\\]?\\1', substr($name, 1)) . '\.php$/';

			foreach ($this->_classPaths as $classPath) {
				if ($path = $this->_searchFiles($classPath, $regex)) {
					
					$this->_classCache[$classPath][$name] = $path;
					$this->_persistCache($classPath);
					
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
	private function _searchFiles($classpath, $regex) {
		foreach ($this->_fileCache[$classpath] as $filePath) {
			if (preg_match($regex, $filePath)) {
				return $filePath;
			}
		}
	}
	
	/**
	 * Internally used method. Used by addClassPath to cache all files in the newly added class path.
	 *
	 * @param string $classPath The directory to search in.
	 * @param array  &$files    The array to store filepaths in.
	 *
	 * @return array The file paths.
	 */
	private function _getFiles($classPath, array &$files = array()) {
		if (!is_dir($classPath)) {
			return $files;
		}

		$iterator = new DirectoryIterator($classPath);

		foreach ($iterator as $finfo) {
			if (substr($finfo->getBasename(), 0, 1) != '.') {
				if ($finfo->isFile()) {
					$files[] = $finfo->getPathname();
				} else if ($finfo->isDir()) {
					$this->_getFiles($finfo->getPathname(), $files);
				}
			}
		}

		return $files;
	}
}
