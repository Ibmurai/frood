<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodRendererTemplate - A base class for Frood renderers which require templates.
 *
 * @category   Frood
 * @package    Renderer
 * @subPackage Php
 * @author     Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author     Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodRendererPhp extends FroodRendererTemplate {
	/** @var string The content type. */
	protected $_contentType = 'text/html';

	/**
	 * The Frood calls this when appropriate.
	 *
	 * @param array &$values The values assigned to the controller.
	 *
	 * @return null
	 */
	public function render(array &$values) {
		$templateFile = Frood::getFroodConfiguration()->getTemplateFile($this->_request->getModule(), $this->_getTemplateFile());
		if (file_exists($templateFile)) {
			new FroodRendererPhpTemplateScoper($templateFile, $values);
		} else {
			throw new FroodExceptionRenderer("Template file not found: $templateFile.");
		}
	}

	/**
	 * Get the template extension.
	 *
	 * @return string The full template. Not a path to a file.
	 */
	protected function _getTemplateFileExtension() {
		return 'tpl.php';
	}
}

/**
 * FroodRendererPhpTemplateScoper - This class exists to create a clean scope for the php template.
 *
 * @category   Frood
 * @package    Renderer
 * @subPackage Php
 * @author     Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author     Bo Thinggaard <akimsko@tnactas.dk>
 */
final class FroodRendererPhpTemplateScoper {
	private static $_values;
	private static $_templateFile;

	/**
	 * @param string $templateFile The full path to the php template to render.
	 * @param array  $values       The values assigned to the controller.
	 */
	public function __construct($templateFile, $values) {
		self::$_values       = $values;
		self::$_templateFile = $templateFile;

		unset($templateFile);
		unset($values);

		require self::$_templateFile;
	}

	/**
	 * This facilitates accessing assigned values from the PHP template, by calling $this->[propertyName].
	 *
	 * @param string $name The property to get.
	 *
	 * @return mixed
	 */
	public function __get($name) {
		if (array_key_exists($name, self::$_values)) {
			return self::$_values[$name];
		} else {
			throw new FroodExceptionRenderer("Value, $name, has not been assigned.");
		}
	}
}
