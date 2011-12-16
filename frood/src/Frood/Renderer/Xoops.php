<?php
/**
 * A Frood renderer for Xoops "powered" actions.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-08-10
 */

/**
 * FroodRendererXoops - A Frood renderer for Xoops "powered" actions.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Renderer
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodRendererXoops extends FroodRendererSmarty {
	/**
	 * Render the output as HTML using Xoops/Smarty.
	 *
	 * @param array $values The values assigned to the controller.
	 *
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function render(array $values) {
		if ($this->_app == 'admin') {
			xoops_cp_header();

			$this->_enableXoopsLogger();
			parent::render($values);

			xoops_cp_footer();
		} else {
			extract($GLOBALS, EXTR_REFS);

			$xoopsOption['template_main'] = $this->_getSmartyResource();
			$xoopsLogger->activated = true;

			include_once XOOPS_ROOT_PATH . "/header.php";

			foreach ($values as $key => $value) {
				$xoopsTpl->assign($key, $value);
			}

			include_once XOOPS_ROOT_PATH . "/footer.php";
		}
	}
}
