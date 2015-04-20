<?php
/**
 * This file is part of The Frood framework.
 *
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
	private $_classPaths = array();
	
	/** @var boolean Is miss cache enabled. */
	private static $_missCacheEnabled = true;

	/** @var string Complete path to cache directory. */
	private static $_cacheDir;
	
	/** @var array[] Stores cached classes, as class name => path to class. */
	private static $_classCache = array();

	/** @var array[] Store classes that are not found in this autoloader. */
	private static $_missCache = array();

	/** @var array[] Class cache dirty flags. */
	private static $_classCacheDirty = array();

	/** @var array[] Miss cache dirty flags. */
	private static $_missCacheDirty = array();

	/** @var array[] Store the file paths for all files in a class path. */
	private $_fileCache = array();
	
	/** @var string Filename extension of hit cache file. */
	const EXT_CACHE_HITS = '.hits';
	
	/** @var string Filename extension of miss cache file. */
	const EXT_CACHE_MISS = '.miss';

	/**
	 * Construct a new autoloader.
	 * It will automatically register itself.
	 *
	 * @param array $classPaths An array of paths to use as the base of autoloading.
	 */
	public function __construct(array $classPaths) {
		if (self::$_cacheDir === null) {
			self::setCacheDir(dirname(__FILE__) . '/autoloader_cache/');
		}

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
		$classPath = realpath($classPath);
		if (!isset(self::$_classCache[$classPath])) {
			$this->_classPaths[] = $classPath;
			self::$_classCache[$classPath] = self::$_missCache[$classPath] = array();
			self::_loadCache($classPath);
		}
	}

	/**
	 * Converts a classpath to a valid filename.
	 *
	 * @param string $classPath The class path.
	 *
	 * @return string The classpath as a valid filename.
	 */
	private static function _classPathToFilename($classPath) {
		static $filenames = array();

		if (!isset($filenames[$classPath])) {
			$filenames[$classPath] = preg_replace('/[\/\\\: ]/', '_', $classPath);
		}

		return $filenames[$classPath];
	}

	/**
	 * Set the cache directory for all frood autoloaders.
	 * Will try to create the directory if it doesnt exist.
	 *
	 * @param string $cacheDir Full path to the caching directory.
	 */
	public static function setCacheDir($cacheDir) {
		if (is_dir($cacheDir) || @mkdir($cacheDir, 0777)) {
			self::$_cacheDir = $cacheDir;
		}
	}
	
	/**
	 * Enable or disabled persistant miss caching.
	 * 
	 * @param boolean $enabled Enable/disable.
	 */
	public static function setMissCacheEnabled($enabled = true) {
		if (!self::$_missCacheEnabled = $enabled) {
			self::$_missCache = array();
		}
	}

	/**
	 * Check if the classes in the cache can still be found at the cached location.
	 * Cleares cache if invalid.
	 *
	 * @param string $classPath The class path.
	 */
	private function _validateCache($classPath) {
		foreach (self::$_classCache[$classPath] as $class) {
			if (!in_array($class, $this->_fileCache[$classPath])) {
				self::_clearCache($classPath);
				break;
			}
		}
	}

	/**
	 * Get path to hits file.
	 *
	 * @param string $classPath The classpath.
	 *
	 * @return string The path to the hits file.
	 */
	private static function _hitsFile($classPath) {
		return self::$_cacheDir . self::_classPathToFilename($classPath) . self::EXT_CACHE_HITS;
	}

	/**
	 * Get path to miss file.
	 *
	 * @param string $classPath The classpath.
	 *
	 * @return string The path to the miss file.
	 */
	private static function _missFile($classPath) {
		return self::$_cacheDir . self::_classPathToFilename($classPath) . self::EXT_CACHE_MISS;
	}

	/**
	 * Load the class cache for a class path.
	 *
	 * @param string $classPath The class path.
	 */
	private static function _loadCache($classPath) {
		if (self::$_cacheDir) {
			self::$_classCache[$classPath] = (($classCache = @file_get_contents(self::_hitsFile($classPath))) && ($classCache = @unserialize($classCache))) ? $classCache : array();
			if (self::$_missCacheEnabled) {
				self::$_missCache[$classPath]  = (($missCache  = @file_get_contents(self::_missFile($classPath))) && ($missCache  = @unserialize($missCache)))  ? $missCache  : array();
			}
		}
	}

	/**
	 * Write file atomically
	 *
	 * @param string $filename The file to write
	 * @param string $contens  The contents to write to the file
	 *
	 * @return boolean Success
	 */
	private static function _filePutContentsAtomic($filename, $contents) {
		$tmpdir = dirname($filename);
		$tmpfile = @tempnam($tmpdir, 'frood');
		if (!$tmpfile) {
			trigger_error("Failed to create temporary file in folder $tmpdir", E_USER_WARNING);
			return false;
		}
		if (!@file_put_contents($tmpfile, $contents)) {
			unlink($tmpfile);
			trigger_error("Unable to write to temporary file $tmpfile", E_USER_WARNING);
			return false;
		}
		if (!@chmod($tmpfile, 0644)) {
			unlink($tmpfile);
			trigger_error("Unable to make temporary file $tmpfile world readable", E_USER_WARNING);
			return false;
		}
		if (!rename($tmpfile, $filename)) {
			unlink($tmpfile);
			trigger_error("Unable to overwrite destination file $filename", E_USER_WARNING);
			return false;
		}

		return true;
	}

	/**
	 * Persist the class cache for a class path.
	 *
	 * @param string $classPath The class path.
	 *
	 * @return boolean Success.
	 */
	private static function _persistCache($classPath) {
		if (!self::$_cacheDir) {
			return;
		}

		if (isset(self::$_classCacheDirty[$classPath]) && isset(self::$_classCache[$classPath])) {
			self::_filePutContentsAtomic(self::_hitsFile($classPath), @serialize(self::$_classCache[$classPath]));
		}
		if (self::$_missCacheEnabled && isset(self::$_missCacheDirty[$classPath]) && isset(self::$_missCache[$classPath])) {
			self::_filePutContentsAtomic(self::_missFile($classPath), @serialize(self::$_missCache[$classPath]));
		}
	}

	/**
	 * Cleares the file and static memory cache.
	 *
	 * @param string $classPath The class path.
	 *
	 * @return boolean Success.
	 */
	private static function _clearCache($classPath) {
		if (!self::$_cacheDir) {
			return;
		}
		
		self::$_classCache[$classPath] = array();
		self::$_missCache[$classPath]  = array();
		unset(self::$_classCache[$classPath]);
		unset(self::$_missCache[$classPath]);

		@unlink(self::_hitsFile($classPath));
		@unlink(self::_missFile($classPath));
	}

	/**
	 * Get a cached path to a class.
	 *
	 * @param string $name The class name.
	 *
	 * @return string|null The cached path to the class.
	 */
	private static function _checkClassCache($name) {
		foreach (self::$_classCache as $classes) {
			if (isset($classes[$name])) {
				return $classes[$name];
			}
		}
	}
	
	/**
	 * Check if this class name is registered as a miss in all known classPaths.
	 * 
	 * @param string $name The class name.
	 * 
	 * @return boolean Is a miss.
	 */
	private static function _checkMissCache($name) {
		if (!self::$_missCache) {
			return false;
		}
		
		$miss = true;
		foreach (self::$_missCache as $classes) {
			if (!isset($classes[$name])) {
				$miss = false;
				break;
			}
		}
		
		return $miss;
	}
	
	/**
	 * Register miss for this autoloaders classPaths.
	 * 
	 * @param string $name The class name.
	 */
	private function _registerMiss($name) {
		foreach ($this->_classPaths as $classPath) {
			if (!isset(self::$_missCache[$classPath])) {
				self::$_missCache[$classPath] = array();
			}
			self::$_missCache[$classPath][$name] = true;
			self::$_missCacheDirty[$classPath] = true;
		}
	}

	/**
	 * Attempts to load the given class.
	 *
	 * @param string $name The name of the class to load.
	 */
	public function autoload($name) {
		if (self::_checkMissCache($name)) {
			return;
		}

		if (($path = self::_checkClassCache($name)) || ($path = $this->_classNameToPath($name))) {
			require_once $path;
			return;
		}

		$this->_registerMiss($name);
	}

	/**
	 * Unregister the autoloader. Persist and clean memory cache.
	 *
	 * @throws RuntimeException If the autoloader could not be unregistered.
	 */
	public function unregister() {
		if (!spl_autoload_unregister(array($this, 'autoload'))) {
			throw new RuntimeException('Could not unregister.');
		}

		foreach ($this->_classPaths as $classPath) {
			self::_persistCache($classPath);
			unset(self::$_classCache[$classPath]);
			unset(self::$_missCache[$classPath]);
			unset(self::$_classCacheDirty[$classPath]);
			unset(self::$_missCacheDirty[$classPath]);
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
		if (preg_match('/^((?:\\\\?[A-Z][a-z0-9]*)+)$/', $name)) {
			// Build a regular expression matching the end of the filepaths to accept...
			$regex = '/[\/\\\][a-z]+[A-Za-z_-]*[\/\\\]' . substr($name, 0, 1) . preg_replace('/\\\\?([A-Z])/', '[\/\\\\\\]?\\1', substr($name, 1)) . '\.php$/';

			foreach ($this->_classPaths as $classPath) {
				if ($path = $this->_searchFiles($classPath, $regex)) {
					self::$_classCache[$classPath][$name] = $path;
					self::$_classCacheDirty[$classPath] = true;

					return $path;
				}
			}
		}

		return null;
	}

	/**
	 * Internally used method. Used by _classNameToPath.
	 *
	 * @param string $classPath The directory to search in.
	 * @param string $regex     The regular expression to match on the full path.
	 *
	 * @return null|string null if no match was found.
	 */
	private function _searchFiles($classPath, $regex) {
		if (!isset($this->_fileCache[$classPath])) {
			$this->_fileCache[$classPath] = self::_getFiles($classPath);
			$this->_validateCache($classPath);
		}
		
		foreach ($this->_fileCache[$classPath] as $filePath) {
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
	private static function _getFiles($classPath, array &$files = array()) {
		if (!is_dir($classPath)) {
			return $files;
		}

		$iterator = new DirectoryIterator($classPath);

		foreach ($iterator as $finfo) {
			if (substr($finfo->getBasename(), 0, 1) != '.') {
				if ($finfo->isFile()) {
					$files[] = $finfo->getPathname();
				} else {
					if ($finfo->isDir()) {
						self::_getFiles($finfo->getPathname(), $files);
					}
				}
			}
		}

		return $files;
	}

	/**
	 * Persist memory cache for known class paths.
	 */
	public function __destruct() {
		foreach ($this->_classPaths as $classPath) {
			self::_persistCache($classPath);
		}
	}
}
