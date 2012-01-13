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
 */
abstract class FroodRenderer {
	/** @var string The module we're working with. */
	protected $_module;

	/** @var string Which sub module are we running? */
	protected $_subModule;

	/** @var string The controller we're rendering for. */
	protected $_controller;

	/** @var string The action invoked. */
	protected $_action;

	/**
	 * The constructor.
	 *
	 * @param string $module     The dirname of the module to work with.
	 * @param string $subModule  The sub module to work with.
	 * @param string $controller The controller we're rendering for.
	 * @param string $action     The action invoked.
	 *
	 * @return null
	 */
	public function __construct($module, $subModule, $controller, $action) {
		$this->_module     = $module;
		$this->_subModule  = $subModule;
		$this->_controller = $controller;
		$this->_action     = $action;
	}

	/**
	 * The Frood calls this when appropriate.
	 * It should output directly.
	 *
	 * @param array $values The values assigned to the controller.
	 *
	 * @return null
	 */
	abstract public function render(array &$values);

	/**
	 * The Frood explicitly sets the HTTP header Content-Type to what this returns.
	 *
	 * @return string The Content-Type this renderer generates.
	 */
	abstract public function getContentType();
}
