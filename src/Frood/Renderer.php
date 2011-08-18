<?php
/**
 * A base class for Frood renderers.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-08-10
 */

/**
 * FroodRenderer - A base class for Frood renderers.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Renderer
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
abstract class FroodRenderer {
	/** @var string The module we're working with. */
	protected $_module;

	/** @var string Which application are we running? */
	protected $_app;

	/** @var string The controller we're rendering for. */
	protected $_controller;

	/** @var string The action invoked. */
	protected $_action;

	/**
	 * The constructor.
	 *
	 * @param string $module     The dirname of the module to work with.
	 * @param string $app        Which application are we running?
	 * @param string $controller The controller we're rendering for.
	 * @param string $action     The action invoked.
	 *
	 * @return void
	 */
	public function __construct($module, $app, $controller, $action) {
		$this->_module     = $module;
		$this->_app        = $app;
		$this->_controller = $controller;
		$this->_action     = $action;
	}

	/**
	 * The Frood calls this when appropriate.
	 *
	 * @param array $values The values assigned to the controller.
	 *
	 * @return string The rendered output.
	 */
	abstract public function render(array $values);
}
