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
	/** @var string Which action is Frood invoking? */
	private $_action;

	/** @var string Which application are we running? */
	private $_app;

	/** @var string The module we're working with. */
	private $_module = null;

	/** @var array This associative array contains the key-value pairs to output. */
	private $_values = array();

	/** @var string The output renderer class to use when render is called. */
	private $_renderer = null;

	/**
	 * Construct a new controller instance.
	 * This is automatically called from The Frood.
	 *
	 * @param string $module The module we're working with.
	 * @param string $app    Which application are we running?
	 * @param string $action Which action is Frood invoking?
	 *
	 * @return void
	 */
	public function __construct($module, $app, $action) {
		$this->_module = $module;
		$this->_app    = $app;
		$this->_action = $action;

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
	 * @return void
	 *
	 * @throws RuntimeException For undefined output modes.
	 */
	final public function render() {
		$renderer = new $this->_renderer($this->_module, $this->_app, get_class($this), $this->_action);

		header('Content-Type: ' . $renderer->getContentType());

		$renderer->render($this->_values);
	}

	/**
	 * Set the output mode to Json.
	 *
	 * @return void
	 */
	final public function doOutputJson() {
		$this->_setRenderer('FroodRendererJson');
	}

	/**
	 * Set the output mode to Xoops.
	 *
	 * @return void
	 */
	final public function doOutputXoops() {
		$this->_setRenderer('FroodRendererXoops');
	}

	/**
	 * Set the output mode to Smarty.
	 *
	 * @return void
	 */
	final public function doOutputSmarty() {
		$this->_setRenderer('FroodRendererSmarty');
	}

	/**
	 * Set the output mode to Automatically UTF8 encoded JSON array.
	 *
	 * @return void
	 */
	final public function doOutputJsonAutoUtf8() {
		$this->_setRenderer('FroodRendererJsonAutoUtf8');
	}

	/**
	 * Set the output mode to disabled.
	 *
	 * @return void
	 */
	final public function doOutputDisabled() {
		$this->_setRenderer('FroodRendererDisabled');
	}

	/**
	 * Forward to another action. This ends all local execution and displays the results of the remote action.
	 *
	 * @param FroodParameters $parameters The parameters for the action. Defaults to no parameters.
	 * @param string          $action     The action to forward to. Defaults to current action.
	 * @param string          $controller The controller to forward to. Defaults to current controller.
	 * @param string          $module     The module to forward to. Defaults to current module.
	 * @param string          $app        The app to forward to. Defaults to current app.
	 *
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.ExitExpression) Yeah, well I need an exit!
	 */
	final protected function _forward(FroodParameters $parameters = null, $action = null, $controller = null, $module = null, $app = null) {
		if ($parameters === null) {
			$parameters = new FroodParameters(array());
		}
		if ($action === null) {
			$action = $this->_action;
		}
		if ($controller === null) {
			$controller = $this->_getBasename();
		}
		if ($module === null) {
			$module = $this->_module;
		}
		if ($app === null) {
			$app = $this->_app;
		}

		$remote = new FroodRemote($module, $app, null, true);

		echo $remote->dispatch($controller, $action, $parameters);

		exit;
	}

	/**
	 * Set the output renderer class.
	 *
	 * @param string $renderer The name of the class to use for rendering output.
	 *
	 * @return void
	 */
	final protected function _setRenderer($renderer) {
		$this->_renderer = $renderer;
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
	final protected function _getRenderer() {
		return $this->_renderer;
	}

	/**
	 * Get the lowercased_with_underscores name of this controller.
	 *
	 * @return string The lowercased_with_underscores name of this controller.
	 */
	public function _getBasename() {
		return FroodUtil::convertPhpNameToHtmlName(
			preg_replace(
				array(
					'/^' . FroodUtil::convertHtmlNameToPhpName($this->_module) . '/',
					'/Controller$/',
				),
				array('', ''),
				get_class($this)
			)
		);
	}
}
