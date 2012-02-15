<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodRemote - Interoperate with Frood enabled modules.
 *
 * @category Frood
 * @package  Remote
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class FroodRemote {
	/** @var FroodRequest The remote Frood request. */
	private $_request;

	/** @var string The name of the host to connect to. */
	private $_host;

	/** @var boolean If this is false (default) an exception is thrown if the remote action modifies the HTTP headers. */
	private $_ignoreModifiedHeaders;

	/**
	 * Do initialization stuff.
	 *
	 * @param string  $module                The dirname of the module to work with.
	 * @param string  $subModule             Which submodule are we remoting to?
	 * @param string  $host                  The name of the host to connect to. Don't specify this to work locally.
	 * @param boolean $ignoreModifiedHeaders Set to true to ignore if the remote action modified the headers.
	 *
	 * @return null
	 */
	public function __construct($module, $subModule = 'public', $host = null, $ignoreModifiedHeaders = false) {
		$this->_request = new FroodRequest();
		$this->_request
			->setModule($module)
			->setSubModule($subModule)
		;

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
	 */
	public function dispatch($controller, $action, FroodParameters $parameters = null, $jsonDecode = false) {
		if ($parameters === null) {
			$parameters = new FroodParameters(array());
		}

		if ($this->_host === null) {
			ob_start();

			$headers = headers_list();

			$this->_request
				->setController($controller)
				->setAction($action)
				->setParameters($parameters)
			;

			$extern = new Frood();
			$extern->dispatch($this->_request, false);
			$extern->unregisterAutoloader();

			if (!$this->_ignoreModifiedHeaders) {
				foreach (array_diff(headers_list(), $headers) as $modifiedHeader) {
					if (!preg_match('/^Content-Type:/', $modifiedHeader)) {
						ob_end_clean();
						throw new FroodExceptionRemoteDispatch(
							$this->_request,
							$this->_host,
							'',
							0,
							"The remote action added or modified an illegal header: $modifiedHeader"
						);
					}
				}
			}

			$result = ob_get_clean();
		} else {
			$httpRequest = $this->_getHttpRequest();

			try {
				$httpRequest->send();
			} catch (HttpException $e) {
				throw new FroodExceptionRemoteDispatch($this->_request, $this->_host);
			}

			$responseCode = $httpRequest->getResponseCode();

			if (floor($responseCode / 100) == 2) {
				$result = $httpRequest->getResponseBody();
			} else {
				$information = array();
				foreach ($httpRequest->getResponseHeader() as $name => $value) {
					$matches = array();
					if (preg_match('/X-Frood-(.*)$/', $name, $matches)) {
						$information[] = "{$matches[1]}: $value";
					}
				}
				throw new FroodExceptionRemoteDispatch($this->_request, $this->_host, '', $responseCode, "HTTP code $responseCode received" . ($information ? '. Header information: ' . implode(', ', $information) : ''));
			}
		}

		if ($jsonDecode) {
			if (!$json = json_decode($result, true)) {
				throw new FroodExceptionRemoteDispatch($this->_request, $this->_host, '', null, "Invalid JSON received");
			}
			return $json;
		} else {
			return $result;
		}
	}

	/**
	 * Create an HTTP POST request.
	 *
	 * @return HttpRequest
	 */
	private function _getHttpRequest() {
		$url = $this->_host;
		if (!preg_match('/\/$/', $url)) {
			$url .= '/';
		}

		$url .= "{$this->_request->getModule()}/{$this->_request->getController()}/{$this->_request->getAction()}";

		$httpRequest = new HttpRequest($url, HttpRequest::METH_POST);

		$fields = array();
		foreach ($this->_request->getParameters() as $key => $value) {
			if ($value instanceof FroodFileParameter) {
				$httpRequest->addPostFile(
					FroodUtil::convertPhpNameToHtmlName($key),
					$value->getPath(),
					$value->getType()
				);
			} else {
				$fields[FroodUtil::convertPhpNameToHtmlName($key)] = $value;
			}
		}
		$httpRequest->addPostFields($fields);

		return $httpRequest;
	}
}
