<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodController - An abstract controller class to use as a base for Frood controllers.
 *
 * @category Frood
 * @package  Controller
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 * @author   Johannes Frandsen <jsf@fynskemedier.dk>
 */
abstract class FroodController {
	/** @var string Which action is Frood invoking? */
	private $_action;

	/** @var string Which application are we running? */
	private $_subModule;

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
	 * @param string $module    The module we're working with.
	 * @param string $subModule Which sub module are we running?
	 * @param string $action    Which action is Frood invoking?
	 */
	public function __construct($module, $subModule, $action) {
		$this->_module    = $module;
		$this->_subModule = $subModule;
		$this->_action    = $action;
	}

	/**
	 * Assign a value to the output.
	 *
	 * @param string $key   The key to assign.
	 * @param mixed  $value The value to assign.
	 *
	 * @return null
	 */
	final public function assign($key, $value) {
		$this->_values[$key] = $value;
	}

	/**
	 * Render the output.
	 * The Frood calls this when appropriate.
	 *
	 * @return null
	 *
	 * @throws RuntimeException For undefined output modes.
	 */
	final public function render() {
		header('Content-Type: ' . $this->_renderer->getContentType());

		$this->_renderer->render($this->_values);
	}

	/**
	 * Set the output mode to Json.
	 *
	 * @return null
	 */
	final public function doOutputJson() {
		$this->_setRenderer('FroodRendererJson');
	}

	/**
	 * Set the output mode to disabled.
	 *
	 * @return null
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
	 * @param string          $subModule  The sub module to forward to. Defaults to current sub module.
	 *
	 * @return null
	 */
	final protected function _forward(FroodParameters $parameters = null, $action = null, $controller = null, $module = null, $subModule = null) {
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
		if ($subModule === null) {
			$subModule = $this->_subModule;
		}

		$remote = new FroodRemote($module, $subModule, null, true);

		echo $remote->dispatch($controller, $action, $parameters);

		exit;
	}

	/**
	 * Forward to another action. This ends all local execution and displays the results of the remote action.
	 *
	 * @param FroodParameters $parameters The parameters for the action. Defaults to no parameters.
	 * @param string          $action     The action to forward to. Defaults to current action.
	 * @param string          $controller The controller to forward to. Defaults to current controller.
	 * @param string          $module     The module to forward to. Defaults to current module.
	 * @param string          $app        The app to forward to. Defaults to current app.
	 * @param string          $host       The host to forward to. Remember to put the protocol in front (i.e. http://). Defaults to current host.
	 *
	 * @return null
	 *
	 * @throws RuntimeException If you attempt to redirect with a file parameter, or a multidimensional array.
	 */
	final protected function _redirect(FroodParameters $parameters = null, $action = null, $controller = null, $module = null, $app = null, $host = null) {
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
			$app = $this->_subModule;
		}
		if ($host === null) {
			$host = XOOPS_URL;
		}

		$url = $host;
		if (!preg_match('/\/$/', $url)) {
			$url .= '/';
		}

		$url .= "modules/$module/$app/$controller/$action";

		$getString = $parameters->toGetString();

		if (strlen($getString) > 0) {
			$url .= '?' . $getString;
		}

		header("Location: $url");

		exit;
	}

	/**
	 * Set the output renderer class.
	 *
	 * @param string $renderer The name of the class to use for rendering output.
	 *
	 * @return null
	 */
	final protected function _setRenderer($renderer) {
		$this->_renderer = new $renderer($this->_module, $this->_subModule, $this->_getBasename(), $this->_action);
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
	 * Get the output renderer instance.
	 *
	 * @return FroodRenderer
	 */
	final protected function _getRenderer() {
		return $this->_renderer;
	}

	/**
	 * Get the lowercased_with_underscores name of this controller.
	 *
	 * @return string The lowercased_with_underscores name of this controller.
	 */
	protected function _getBasename() {
		return FroodUtil::convertPhpNameToHtmlName(
			preg_replace(
				'/^' .
					FroodUtil::convertHtmlNameToPhpName($this->_module) .
					FroodUtil::convertHtmlNameToPhpName($this->_subModule) .
					'Controller' .
				'/',
				'',
				get_class($this)
			)
		);
	}

	/**
	 * Map doOutput calls to attempt to use external renderers.
	 *
	 * @param string $name      The name of the method being called.
	 * @param array  $arguments An enumerated array containing the parameters passed to the $name'ed method.
	 *
	 * @return null
	 */
	public function __call($name, array $arguments) {
		$matches = array();
		if (preg_match('/^doOutput(.+)$/', $name, $matches)) {
			$this->_setRenderer("FroodRenderer{$matches[1]}");
		} else {
			trigger_error('Call to undefined method ' . get_class($this) . "::$name()", E_USER_ERROR);
		}
	}
}
