<?php
/**
 * A Frood renderer for Smarty "powered" xml output.
 *
 * PHP version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-09-19
 */

/**
 * FroodRendererSmarty - A Frood renderer for Smarty "powered" xml output.
 *
 * @category   Library
 * @package    Frood
 * @subpackage Renderer
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodRendererXml extends FroodRendererSmarty {
	/** string The extension to use for template files. */
	protected $_fileExtension = 'tpl.xml';

	/**
	 * The Frood explicitly sets the HTTP header Content-Type to what this returns.
	 *
	 * @return string The Content-Type this renderer generates.
	 */
	public function getContentType() {
		return 'text/xml';
	}
}
