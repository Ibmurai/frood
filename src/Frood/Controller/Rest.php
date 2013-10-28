<?php
/**
 * This file is part of The Frood framework.
 * @link      https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * RESTful Frood controller.
 *
 * @category Frood
 * @package  Controller
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodControllerRest extends FroodController {

	/** @var array HTTP methods to class methods map. */
	protected $_methods = array(
		'HEAD'    => '_head',
		'GET'     => '_get',
		'PUT'     => '_put',
		'DELETE'  => '_delete',
		'POST'    => '_post',
		'OPTIONS' => '_options',
		'TRACE'   => '_trace',
		'CONNECT' => '_connect',
	);

	/** @var FroodHttpResponse Response object. */
	private $_response;

	/**
	 * Override this to do any initialization you need, such as setting a
	 * default output method or including external resources.
	 *
	 * @return null
	 */
	protected function initialize() {
		$this->doOutputJson();
	}

	/**
	 * Get the response object.
	 *
	 * @return FroodHttpResponse The response object.
	 */
	public function getResponse() {
		return $this->_response ? $this->_response : ($this->_response = new FroodHttpResponse());
	}

	/**
	 * Set the HTTP response code.
	 *
	 * @param integer $code The HTTP response code.
	 *
	 * @return FroodControllerRest This.
	 */
	public function setResponseCode($code) {
		$this->getResponse()->setResponseCode($code);
		return $this;
	}

	/**
	 * Set the HTTP response message body.
	 *
	 * @param string $message The message body.
	 *
	 * @return FroodControllerRest This.
	 */
	public function setResponseMessage($message) {
		$this->getResponse()->setMessage($message);
		return $this;
	}

	/**
	 * Add a response header.
	 *
	 * @param string $name  The name.
	 * @param string $value The value.
	 *
	 * @return FroodControllerRest This.
	 */
	public function addResponseHeader($name, $value) {
		$this->getResponse()->addHeader($name, $value);
		return $this;
	}

	/**
	 * Main action. Entry point for API routed requests.
	 *
	 * @param FroodParameters $params <null> The frood parameters.
	 *
	 * @return null
	 */
	public function apiAction(FroodParameters $params) {
		$response = $this->getResponse();
		$request  = new FroodHttpRequest();

		try {
			if (($method = $request->getMethod()) && isset($this->_methods[$method])) {
				$this->{$this->_methods[$method]}($params, $request, $this->_getItem($params));
			} else {
				throw new FroodHttpException('Method not allowed', FroodHttpResponseCode::CODE_METHOD_NOT_ALLOWED);
			}

		} catch (FroodHttpException $e) {
			$response->setMessage($e->getMessage());
			$response->setResponseCode($e->getCode());
		}

		$this->_handleResponse($response);
	}

	/**
	 * Handle the response object.
	 *
	 * @param FroodHttpResponse $response The response object to handle.
	 *
	 * @return null
	 */
	private function _handleResponse(FroodHttpResponse $response) {
		header(FroodHttpResponseCode::getHeaderString($response->getResponseCode()), $response->getResponseCode());

		foreach ($response->getHeaders() as $name => $value) {
			header("$name: $value");
		}

		if ($message = $response->getMessage()) {
			$this->doOutputDisabled();
			echo $message;
		}
	}

	/**
	 * Get item.
	 *
	 * @param FroodParameters $params The params.
	 *
	 * @return mixed|null The item param.
	 */
	private function _getItem(FroodParameters $params) {
		$item = null;
		if ($params && $params->hasParameter(FroodModuleRouterApi::ITEM_INDEX)) {
			$item = $params->getParameter(FroodModuleRouterApi::ITEM_INDEX);
			$params->unsetParameter(FroodModuleRouterApi::ITEM_INDEX);
		}
		return $item;
	}

	/**
	 * HEAD action.
	 *
	 * @param FroodParameters   $params   Additional frood params.
	 * @param FroodHttpRequest  $request  The client request.
	 * @param mixed|null        $item     The requested item (if any).
	 *
	 * @throws FroodHttpException If something goes wrong.
	 */
	protected function _head(FroodParameters $params, FroodHttpRequest $request, $item = null) {
		throw new FroodHttpException('Not implemented', FroodHttpResponseCode::CODE_NOT_IMPLEMENTED);
	}

	/**
	 * GET action.
	 *
	 * @param FroodParameters   $params   Additional frood params.
	 * @param FroodHttpRequest  $request  The client request.
	 * @param mixed|null        $item     The requested item (if any).
	 *
	 * @throws FroodHttpException If something goes wrong.
	 */
	protected function _get(FroodParameters $params, FroodHttpRequest $request, $item = null) {
		throw new FroodHttpException('Not implemented', FroodHttpResponseCode::CODE_NOT_IMPLEMENTED);
	}

	/**
	 * PUT action.
	 *
	 * @param FroodParameters   $params   Additional frood params.
	 * @param FroodHttpRequest  $request  The client request.
	 * @param mixed|null        $item     The requested item (if any).
	 *
	 * @throws FroodHttpException If something goes wrong.
	 */
	protected function _put(FroodParameters $params, FroodHttpRequest $request, $item = null) {
		throw new FroodHttpException('Not implemented', FroodHttpResponseCode::CODE_NOT_IMPLEMENTED);
	}

	/**
	 * DELETE action.
	 *
	 * @param FroodParameters   $params   Additional frood params.
	 * @param FroodHttpRequest  $request  The client request.
	 * @param mixed|null        $item     The requested item (if any).
	 *
	 * @throws FroodHttpException If something goes wrong.
	 */
	protected function _delete(FroodParameters $params, FroodHttpRequest $request, $item = null) {
		throw new FroodHttpException('Not implemented', FroodHttpResponseCode::CODE_NOT_IMPLEMENTED);
	}

	/**
	 * POST method action.
	 *
	 * @param FroodParameters   $params   Additional frood params.
	 * @param FroodHttpRequest  $request  The client request.
	 * @param mixed|null        $item     The requested item (if any).
	 *
	 * @throws FroodHttpException If something goes wrong.
	 */
	protected function _post(FroodParameters $params, FroodHttpRequest $request, $item = null) {
		throw new FroodHttpException('Not implemented', FroodHttpResponseCode::CODE_NOT_IMPLEMENTED);
	}

	/**
	 * OPTIONS action.
	 *
	 * @param FroodParameters   $params   Additional frood params.
	 * @param FroodHttpRequest  $request  The client request.
	 * @param mixed|null        $item     The requested item (if any).
	 *
	 * @throws FroodHttpException If something goes wrong.
	 */
	protected function _options(FroodParameters $params, FroodHttpRequest $request, $item = null) {
		throw new FroodHttpException('Not implemented', FroodHttpResponseCode::CODE_NOT_IMPLEMENTED);
	}

	/**
	 * TRACE action.
	 *
	 * @param FroodParameters   $params   Additional frood params.
	 * @param FroodHttpRequest  $request  The client request.
	 * @param mixed|null        $item     The requested item (if any).
	 *
	 * @throws FroodHttpException If something goes wrong.
	 */
	protected function _trace(FroodParameters $params, FroodHttpRequest $request, $item = null) {
		throw new FroodHttpException('Not implemented', FroodHttpResponseCode::CODE_NOT_IMPLEMENTED);
	}

	/**
	 * CONNECT action.
	 *
	 * @param FroodParameters   $params   Additional frood params.
	 * @param FroodHttpRequest  $request  The client request.
	 * @param mixed|null        $item     The requested item (if any).
	 *
	 * @throws FroodHttpException If something goes wrong.
	 */
	protected function _connect(FroodParameters $params, FroodHttpRequest $request, $item = null) {
		throw new FroodHttpException('Not implemented', FroodHttpResponseCode::CODE_NOT_IMPLEMENTED);
	}


}