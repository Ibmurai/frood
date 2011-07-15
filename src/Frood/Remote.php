<?php
/**
 * Cross site Frooding.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-07-05
 */

/**
 * FroodRemote - Interoperate with Frood enabled modules.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodRemote {
	/** @var string The module we're working with. */
	private $_module;

	/** @var string The name of the host to connect to. */
	private $_host;

	/**
	 * Do initialization stuff.
	 *
	 * @param string $host   The name of the host to connect to.
	 * @param string $module The dirname of the module to work with.
	 *
	 * @return void
	 */
	public function __construct($host, $module) {
		$this->_host   = $host;
		$this->_module = $module;
	}

	/**
	 * Dispatch an action to a controller.
	 * Call with no parameters to determine everything from the request.
	 *
	 * @param string          $controller The controller to call.
	 * @param string          $action     The action to invoke.
	 * @param FroodParameters $parameters The parameters for the action.
	 *
	 * @return string The response as a string.
	 *
	 * @throws FroodRemoteDispatchException If Frood cannot dispatch.
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function dispatch($controller, $action, FroodParameters $parameters = null) {
		if ($parameters === null) {
			$parameters = new FroodParameters(array());
		}

		$request = $this->_getRequest($controller, $action, $parameters);

		try {
			$request->send();
		} catch (HttpException $e) {
			throw new FroodRemoteDispatchException($this->_host, $controller, $action, $parameters);
		}

		if ($request->getResponseCode() == 200) {
			return $request->getResponseBody();
		} else {
			throw new FroodRemoteDispatchException($this->_host, $controller, $action, $parameters);
		}
	}

	/**
	 * Create an HTTP post request.
	 *
	 * @param string          $controller The controller to call.
	 * @param string          $action     The action to invoke.
	 * @param FroodParameters $parameters The parameters for the action.
	 *
	 * @return HttpRequest
	 */
	public function _getRequest($controller, $action, FroodParameters $parameters) {
		$url = $this->_host;
		if (!preg_match('/\/$/', $url)) {
			$url .= '/';
		}

		$url .= "modules/{$this->_module}/$controller/$action";

		$request = new HttpRequest($url, HttpRequest::METH_POST);

		$fields = array();
		foreach ($parameters as $key => $value) {
			if ($value instanceof FroodFileParameter) {
				$request->addPostFile(
					Frood::convertPhpNameToHtmlName($key),
					$value->getPath(),
					$value->getType()
				);
			} else {
				$fields[$key] = $value;
			}
		}
		$request->addPostFields($fields);

		return $request;
	}
}
