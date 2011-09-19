<?php
/**
 * A Frood renderer for Smarty "powered" actions.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-08-10
 */

/**
 * FroodRendererSmarty - A Frood renderer for Smarty "powered" actions.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Renderer
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodRendererSmarty extends FroodRenderer {
	/** string The extension to use for template files. */
	protected $_fileExtension = 'tpl.html';

	/** @var boolean Enable or disable the XoopsLogger. */
	private $_xoopsLoggerActivated = false;

	/**
	 * Render the output as HTML using Smarty.
	 *
	 * @param array $values The values assigned to the controller.
	 *
	 * @return string
	 */
	public function render(array $values) {
		include_once XOOPS_ROOT_PATH.'/class/template.php';

		$GLOBALS['xoopsLogger']->activated = $this->_xoopsLoggerActivated;

		$tpl = new XoopsTpl();
		foreach ($values as $key => $value) {
			$tpl->assign($key, $value);
		}

		$tpl->display($this->_getSmartyResource());
	}

	/**
	 * The Frood explicitly sets the HTTP header Content-Type to what this returns.
	 *
	 * @return string The Content-Type this renderer generates.
	 */
	public function getContentType() {
		return 'text/html';
	}

	/**
	 * Get the Smarty resource for a given action.
	 *
	 * @return string
	 */
	protected function _getSmartyResource() {
		$controller = FroodUtil::convertPhpNameToHtmlName(
			preg_replace(
				array(
					'/^' . FroodUtil::convertHtmlNameToPhpName($this->_module) . '/',
					'/Controller$/',
				),
				array('', ''),
				$this->_controller
			)
		);

		$action = FroodUtil::convertPhpNameToHtmlName($this->_action);

		return "blief:{$this->_module}/{$this->_app}/$controller/$action.{$this->_fileExtension}";
	}

	/**
	 * Call this before rendering to enable the XoopsLogger.
	 *
	 * @return void
	 */
	protected function _enableXoopsLogger() {
		$this->_xoopsLoggerActivated = true;
	}
}
