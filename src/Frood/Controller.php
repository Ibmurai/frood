<?php
/**
 * An abstract controller class to use as a base for Frood controllers.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @author   Johannes Frandsen <jsf@fynskemedier.dk>
 * @since    2011-06-16
 */

/**
 * FroodController - An abstract controller class to use as a base for Frood controllers.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 * @author     Johannes Frandsen <jsf@fynskemedier.dk>
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
abstract class FroodController {
	/** @var string Which application are we running? */
	private $_app;

	/** @var string The module we're working with. */
	private $_module = null;

	/** @var array This associative array contains the key-value pairs to output. */
	private $_values = array();

	/** @var string The output mode. Should be one of the class constants. */
	private $_outputMode = null;

	/** @var string Output mode Xoops. */
	const _XOOPS = 'Xoops';

	/** @var string Output mode JSON. */
	const _JSON = 'JSON';

	/** @var string Output mode Smarty. */
	const _SMARTY = 'Smarty';

	/** @var string Output mode AutoUtf8Json. */
	const _JSONAUTOUTF8 = 'Automatically UTF8 encoded JSON array';

	/**
	 * Construct a new controller instance.
	 * This is automatically called from The Frood.
	 *
	 * @param string $module The module we're working with.
	 * @param string $app    Which application are we running?
	 *
	 * @return void
	 */
	public function __construct($module, $app) {
		$this->_module = $module;
		$this->_app    = $app;

		$this->doOutputXoops();
	}

	/**
	 * Assign a value to the output.
	 *
	 * @param string $key   The key to assign.
	 * @param mixed  $value The value to assign.
	 *
	 * @return void
	 */
	final public function assign($key, $value) {
		$this->_values[$key] = $value;
	}

	/**
	 * Render the output.
	 * The Frood calls this when appropriate.
	 *
	 * @param string $action The action to render the view for.
	 *
	 * @return void
	 *
	 * @throws RuntimeException For undefined output modes.
	 */
	public function render($action) {
		switch ($this->_outputMode) {
			case self::_XOOPS:
				$renderer = new FroodRendererXoops($this->_module, $this->_app, get_class($this), $action);
				break;
			case self::_SMARTY:
				$renderer = new FroodRendererSmarty($this->_module, $this->_app, get_class($this), $action);
				break;
			case self::_JSON:
				$renderer = new FroodRendererJson($this->_module, $this->_app, get_class($this), $action);
			case self::_JSONAUTOUTF8:
				$renderer = new FroodRendererJsonAutoUtf8($this->_module, $this->_app, get_class($this), $action);
				break;
			default:
				throw new RuntimeException("Undefined output mode: {$this->_outputMode}.");
				break;
		}

		$renderer->render($this->_values);
	}

	/**
	 * Set the output mode to Json.
	 *
	 * @return void
	 */
	final public function doOutputJson() {
		$this->_doOutput(self::_JSON);
	}

	/**
	 * Set the output mode to Xoops.
	 *
	 * @return void
	 */
	final public function doOutputXoops() {
		$this->_doOutput(self::_XOOPS);
	}

	/**
	 * Set the output mode to Smarty.
	 *
	 * @return void
	 */
	final public function doOutputSmarty() {
		$this->_doOutput(self::_SMARTY);
	}

	/**
	 * Set the output mode to Automatically UTF8 encoded JSON array.
	 *
	 * @return void
	 */
	final public function doOutputJsonAutoUtf8() {
		$this->_doOutput(self::_JSONAUTOUTF8);
	}

	/**
	 * Set the output mode.
	 *
	 * @param string $mode Should be one of the class constants.
	 *
	 * @return void
	 */
	final protected function _doOutput($mode) {
		$this->_outputMode = $mode;
	}

	/**
	 * Get all assigned values.
	 *
	 * @return array An array of all assigned values.
	 */
	final protected function _getValues() {
		return $this->_values;
	}

	/**
	 * Get the value assigned to $key.
	 *
	 * @param string $key The key to get the value for.
	 *
	 * @return mixed It's like a box of chocolates.
	 *
	 * @throws RuntimeException For non-assigned keys.
	 */
	final protected function _getValue($key) {
		if (array_key_exists($key, $this->_values)) {
			return $this->_values[$key];
		} else {
			throw new RuntimeException("No value has been set for key, $key.");
		}
	}

	/**
	 * Check if a value has been assigned to $key.
	 *
	 * @param string $key The key to check.
	 *
	 * @return boolean True or false... Or maybe?!?
	 */
	final protected function _hasValue($key) {
		return array_key_exists($key, $this->_values);
	}

	/**
	 * Get the output mode.
	 *
	 * @return string The output mode.
	 */
	final protected function _getOutputMode() {
		return $this->_outputMode;
	}
}
