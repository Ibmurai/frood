<?php
/**
 * An abstract controller class to use as a base for Frood controllers.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-16
 */

/**
 * FroodController - An abstract controller class to use as a base for Frood controllers.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
abstract class FroodController {
	/** @var boolean Are we handling admin pages? */
	private $_isAdmin;

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

	/**
	 * Construct a new controller instance.
	 * This is automatically called from The Frood.
	 *
	 * @param string  $module  The module we're working with.
	 * @param boolean $isAdmin Are we handling admin pages?
	 *
	 * @return void
	 */
	public function __construct($module, $isAdmin = false) {
		$this->_module  = $module;
		$this->_isAdmin = $isAdmin;

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
	public function assign($key, $value) {
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
				$this->_renderXoops($action);
				break;
			case self::_JSON:
				$this->_renderJson($action);
				break;
			default:
				throw new RuntimeException("Undefined output mode: {$this->_outputMode}.");
				break;
		}
	}

	/**
	 * Set the output mode to Json.
	 *
	 * @return void
	 */
	public function doOutputJson() {
		$this->_doOutput(self::_JSON);
	}

	/**
	 * Set the output mode to Xoops.
	 *
	 * @return void
	 */
	public function doOutputXoops() {
		$this->_doOutput(self::_XOOPS);
	}

	/**
	 * Render the output as JSON.
	 *
	 * @param string $action The action to render the view for. Ignored here.
	 *
	 * @return string The rendered output.
	 */
	private function _renderJson($action) {
		header('Content-type: application/json');

		echo json_encode($this->_values);
	}

	/**
	 * Render the output as HTML using Xoops/Smarty.
	 *
	 * @param string $action The action to render the view for.
	 *
	 * @return void
	 */
	private function _renderXoops($action) {
		if ($this->_isAdmin) {
			xoops_cp_header();

			$tpl = new XoopsTpl();
			foreach ($this->_values as $key => $value) {
				$tpl->assign($key, $value);
			}

			$tpl->display($this->_getSmartyResource($action));

			xoops_cp_footer();
		} else {
			extract($GLOBALS, EXTR_REFS);

			$xoopsOption['template_main'] = $this->_getSmartyResource($action);

			include_once XOOPS_ROOT_PATH . "/header.php";

			foreach ($this->_values as $key => $value) {
				$xoopsTpl->assign($key, $value);
			}

			include_once XOOPS_ROOT_PATH . "/footer.php";
		}
	}

	/**
	 * Get the Smarty resource for a given action.
	 *
	 * @param string $action The action to get a resource for.
	 *
	 * @return string
	 */
	public function _getSmartyResource($action) {
		$controllerName = strtolower(
			preg_replace(
				array(
					'/^' . $this->_module . '/i',
					'/Controller$/',
				),
				array('', ''),
				get_class($this)
			)
		);

		return 'blief:' . strtolower($this->_module) . '/' . ($this->_isAdmin ? 'admin/' : 'public/') . strtolower($controllerName) . '/' . strtolower($action) . '.tpl.html';
	}

	/**
	 * Set the output mode.
	 *
	 * @param string $mode Should be one of the class constants.
	 *
	 * @return void
	 */
	private function _doOutput($mode) {
		$this->_outputMode = $mode;
	}
}
