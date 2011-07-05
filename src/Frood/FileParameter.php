<?php
/**
 * FroodFileParameter.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-07-04
 */

/**
 * FroodFileParameter - Frood uses this to represent file parameters.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodFileParameter {
	/** @var string The path to the actual file. */
	private $_path;

	/** @var string The original name of the file. */
	private $_name;

	/** @var string The size of the file in bytes. */
	private $_size;

	/** @var string The MIME-type of the file. */
	private $_type;

	/** @var string The PHP file upload error code. */
	private $_error;

	/**
	 * The constructor.
	 *
	 * @param string  $path  The path to actual file.
	 * @param string  $name  The original name of the file.
	 * @param integer $size  The size of the file in bytes.
	 * @param string  $type  The MIME-type of the file.
	 * @param integer $error The PHP file upload error code.
	 *
	 * @return void
	 */
	public function __construct($path, $name = null, $size = null, $type = null, $error = null) {
		$this->_path = $path;

		if ($name === null) {
			$name = basename($path);
		}

		if ($size === null) {
			$size = filesize($path);
		}

		if ($type === null) {
			$matches = array();
			if (preg_match('/^(\S+)/', exec('file -bi ' . escapeshellarg($path)), $matches)) {
				$type = $matches[1];
			}
		}

		$this->_name  = $name;
		$this->_size  = $size;
		$this->_type  = $type;
		$this->_error = $error;
	}

	/**
	 * Get the path to actual file.
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->_path;
	}

	/**
	 * Get the original name of the file.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Get the size of the file in bytes.
	 *
	 * @return integer
	 */
	public function getSize() {
		return $this->_size;
	}

	/**
	 * Get the MIME-type of the file.
	 *
	 * @return string
	 */
	public function getType() {
		return $this->_type;
	}

	/**
	 * Returns one of the PHP file upload error codes.
	 *
	 * @see http://www.php.net/manual/en/features.file-upload.errors.php
	 *
	 * @return null|integer Null is returned if the error code has not been set.
	 */
	public function getError() {
		return $this->_error;
	}
}
