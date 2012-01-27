<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodRenderer - A base class for Frood renderers.
 *
 * @category Frood
 * @package  Renderer
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 */
abstract class FroodRenderer {
	/** @var string The content type. */
	protected $_contentType = null;

	/** @var FroodRequest The request we're working with. */
	protected $_request;
	
	/**
	 * The constructor.
	 *
	 * @param string $request
	 */
	public function __construct(FroodRequest $request) {
		$this->_request = $request;
	}

	/**
	 * The Frood calls this when appropriate.
	 * It should output directly.
	 *
	 * @param array $values The values assigned to the controller.
	 */
	abstract public function render(array &$values);

	/**
	 * The Frood explicitly sets the HTTP header Content-Type to what this returns.
	 *
	 * @return string The Content-Type this renderer generates.
	 */
	public function getContentType() {
		return $this->_contentType;
	}
	
	/**
	 * Override the default content type set by this renderer.
	 * 
	 * @param string $contentType
	 */
	public function setContentType($contentType) {
		$this->_contentType = $contentType;
	}
}
