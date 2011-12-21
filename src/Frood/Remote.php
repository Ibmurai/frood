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

	/** @var string Which application are we running? */
	private $_app;

	/** @var string The name of the host to connect to. */
	private $_host;

	/** @var boolean If this is false (default) an exception is thrown if the remote action modifies the HTTP headers. */
	private $_ignoreModifiedHeaders;

	/**
	 * Do initialization stuff.
	 *
	 * @param string  $module                The dirname of the module to work with.
	 * @param string  $app                   Which application are we remoting to?
	 * @param string  $host                  The name of the host to connect to. Don't specify this to work locally.
	 * @param boolean $ignoreModifiedHeaders Set to true to ignore if the remote action modified the headers.
	 *
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.LongVariable) Stupid f'ing rule.
	 */
	public function __construct($module, $app = 'public', $host = null, $ignoreModifiedHeaders = false) {
		$this->_module                = $module;
		$this->_app                   = $app;
		$this->_host                  = $host;
		$this->_ignoreModifiedHeaders = $ignoreModifiedHeaders;
	}

	/**
	 * Dispatch an action to a controller.
	 *
	 * @param string          $controller The controller to call.
	 * @param string          $action     The action to invoke.
	 * @param FroodParameters $parameters The parameters for the action.
	 * @param boolean         $jsonDecode Set this to true, to automatically attempt to
	 *                                    json decode the result.
	 *
	 * @return string|array The response as a string or as decoded json (array).
	 *
	 * @throws FroodExceptionRemoteDispatch If Frood cannot dispatch, or the remote
	 *                                      action modifies HTTP headers illegally, or
	 *                                      json decoding fails.
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public function dispatch($controller, $action, FroodParameters $parameters = null, $jsonDecode = false) {
		if ($parameters === null) {
			$parameters = new FroodParameters(array());
		}

		if ($this->_host === null) {
			ob_start();

			$headers = headers_list();

			$extern = new Frood($this->_module, $this->_app, false);
			$extern->dispatch($controller, $action, $parameters);
			$extern->unregisterAutoloader();

			if (!$this->_ignoreModifiedHeaders) {
				foreach (array_diff(headers_list(), $headers) as $modifiedHeader) {
					if (!preg_match('/^Content-Type:/', $modifiedHeader)) {
						ob_end_clean();
						throw new FroodExceptionRemoteDispatch(
							$this->_host,
							$this->_module,
							$controller,
							$action,
							$parameters,
							$this->_app,
							'',
							0,
							"The remote action added or modified an illegal header: $modifiedHeader"
						);
					}
				}
			}

			$result = ob_get_clean();
		} else {
			$request = $this->_getRequest($controller, $action, $parameters);

			try {
				$request->send();
			} catch (HttpException $e) {
				throw new FroodExceptionRemoteDispatch($this->_host, $this->_module, $controller, $action, $parameters, $this->_app);
			}

			if (($code = $request->getResponseCode()) == 200) {
				$result = $request->getResponseBody();
			} else {
				$information = array();
				foreach ($request->getResponseHeader() as $name => $value) {
					$matches = array();
					if (preg_match('/X-Frood-(.*)$/', $name, $matches)) {
						$information[] = "{$matches[1]}: $value";
					}
				}
				throw new FroodExceptionRemoteDispatch($this->_host, $this->_module, $controller, $action, $parameters, $this->_app, '', $code, "HTTP code $code received" . ($information ? '. Header information: ' . implode(', ', $information) : ''));
			}
		}

		if ($jsonDecode) {
			// TODO: Do better than this regex, to determine valid JSON.
			if (preg_match('/^(?:{|\[)/s', $result)) {
				return json_decode($result, true);
			} else {
				throw new FroodExceptionRemoteDispatch($this->_host, $this->_module, $controller, $action, $parameters, $this->_app, '', null, "Invalid JSON received");
			}
		} else {
			return $result;
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

		$url .= "modules/{$this->_module}/" . ($this->_app != 'public' ? "{$this->_app}/" : '') . "$controller/$action";

		$request = new HttpRequest($url, HttpRequest::METH_POST);

		$fields = array();
		foreach ($parameters as $key => $value) {
			if ($value instanceof FroodFileParameter) {
				$request->addPostFile(
					FroodUtil::convertPhpNameToHtmlName($key),
					$value->getPath(),
					$value->getType()
				);
			} else {
				$fields[FroodUtil::convertPhpNameToHtmlName($key)] = $value;
			}
		}
		$request->addPostFields($fields);

		return $request;
	}
}
