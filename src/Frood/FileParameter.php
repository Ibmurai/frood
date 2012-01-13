<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodFileParameter - Frood uses this to represent file parameters.
 *
 * @category Frood
 * @package  Parameters
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class FroodFileParameter implements Serializable {
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

		if ($type === null && ($error === 0 || $error === null)) {
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

	/**
	 * Get a string describing the error code.
	 *
	 * @return string A string describing the error code.
	 *
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity) PHPMD hates switch statements.
	 */
	public function getErrorMessage() {
		if ($this->_error === null) {
			$message = 'Unknown error.';
		} else switch ($this->_error) {
			case UPLOAD_ERR_OK:
				$message = 'There is no error, the file uploaded with success.';
				break;
			case UPLOAD_ERR_INI_SIZE:
				$message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
				break;
			case UPLOAD_ERR_PARTIAL:
				$message = 'The uploaded file was only partially uploaded.';
				break;
			case UPLOAD_ERR_NO_FILE:
				$message = 'No file was uploaded.';
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$message = 'Missing a temporary folder.';
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$message = 'Failed to write file to disk.';
				break;
			case UPLOAD_ERR_EXTENSION:
				$message  = 'A PHP extension stopped the file upload. PHP does not provide a way to ';
				$message .= 'ascertain which extension caused the file upload to stop; examining the ';
				$message .= 'list of loaded extensions with phpinfo() may help.';
				break;
			default:
				$message = 'Unknown error.';
				break;
		}

		return $message;
	}

	/**
	 * Implementation of the Serializable interface.
	 *
	 * @return string
	 */
	public function serialize() {
		return serialize(
			array(
				'path'  => $this->_path,
				'name'  => $this->_name,
				'type'  => $this->_type,
				'size'  => $this->_size,
				'error' => $this->_error,
			)
		);
	}

	/**
	 * Implementation of the Serializable interface.
	 *
	 * @param string $data The serialized string.
	 *
	 * @return void
	 */
	public function unserialize($data) {
		$array = unserialize($data);

		$this->_path  = $array['path'];
		$this->_name  = $array['name'];
		$this->_type  = $array['type'];
		$this->_size  = $array['size'];
		$this->_error = $array['error'];
	}
}
