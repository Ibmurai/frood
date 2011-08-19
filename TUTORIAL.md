*Authors*: [Jens Riisom Schultz](jers@fynskemedier.dk), [Johannes Skov Frandsen](jsf@fynskemedier.dk)

*Since*: 2011-06-21

The Frood standardizes the VC in MVC for XOOPS.


MVC
===

For an extensive description of MVC in general, read [this wiki article](http://en.wikipedia.org/wiki/Model-view-controller).


Notations
=========

The Frood works with two different notations. `CamelCased` (CC) and `lower_cased_with_underscores` (LOW).

The Frood contains static utility methods to convert back and forth between the two, should you need them:

  * [`FroodUtil::convertPhpNameToHtmlName()`](Frood/Class/FroodUtil.html#convertPhpNameToHtmlName) converts from CC to LOW.
  * [`FroodUtil::convertHtmlNameToPhpName()`](Frood/Class/FroodUtil.html#convertHtmlNameToPhpName) converts from LOW to CC.

Here are a few examples of equivalent strings of the two formats:

	CC      | c_c
	URL     | u_r_l      <-- Yuck!   :'(
	Json    | json       <-- Hooray! <3
	OhMyGod | oh_my_god
	ImageId | image_id

*Because of the ugliness of strings like `u_r_l` it is recommended to always type acronyms in CC with only the first letter as a capital.*


The autoloader
==============

Frood sets an autoloader up for you.

To ensure your classes are autoloaded you simply need to place your classes in the correct files and the correct locations.


Class and file name conventions
-------------------------------

Classes always start with the module name.

Classes are defined in files with the same name as the class.

Part of the file name may be the path to the file (confusing... see the examples).


### Examples ###

`XoopsImageSomething` should be defined in one of:

  * `XoopsImageSomething.php`
  * `Xoops/ImageSomething.php`
  * `XoopsImage/Something.php`
  * `Xoops/Image/Something.php`


File locations
--------------

Frood will always autoload all classes in `class/`.

In addition it will also autoload the classes in the app class folder, depending on which app you are running, i.e. one of:

  * `public/class/`
  * `admin/class/`
  * `local/class/`
  * `cron/class/`

It will recursively scan all folders in the class folders, giving you the freedom to organize your classes as you see fit. That being said, the following _conventions_ should be adhered to, for consistency:

  * Model classes should be placed in `class/model/`.
  * Controller classes should be placed in `class/controllers/`.


Controllers and actions
=======================

TODO: Write this :)


Action output
=============

TODO: Write this :)


Action parameters
=================

TODO: Write this :)


File parameters
---------------

TODO: Write this :)


The apps
========

The Frood makes 4 distinctively different apps available for implementation. Every app is optional.

Generally, the apps can be accessed via http requests to URI's of the following format:

	/modules/[moduleName]/[app]/[controller]/[action]

If no action is given The Frood defaults to the `index` action.


The `public` app
--------------

This app is what anonymous users will access from their browsers.

It can be used by requesting the following URI's:

  * /modules/[moduleName]/public/[controller]/[action]
  * /modules/[moduleName]/[controller]/[action]
  * /modules/[moduleName]

The latter of these will default to the `index` controller and the `index` action.


The other apps
--------------

  * The `admin` app can only be accessed with a valid XOOPS login.
  * The `local` app can only be accessed from our web servers. It is used to provide API's to interoperate between our modules.
  * The `cron` app is intended for cronjobs and can only be accessed from the shell.


Commandline Frood
=================

The Frood apps can all be invoked from the command line. This is useful for testing, but most significantly it allows you to use The Frood for cronjobs:

`shell.php` is located in the `lib/frood/run/` folder. You invoke it like this:

	php shell.php [module] [app] [controller] [action] [parameter1]=[value1]...


Extending the Frood controller
==============================

It is not required to extend the [`FroodController`](Frood/Class/FroodController.html) to make use of Frood, but for various reasons it is usually a good idea:

  * You may need to include the header file in legacy modules.
  * You may need to output something which is not directly supported by Frood.


Example
-------

In this example we overwrite the [`FroodController::render()`](Frood/Class/FroodController.html#render) method to add a new output method, `doOutputImage()` for outputting images. Additionally we need to create a method, `_renderImage()` and a constant `_IMAGE`.

We also add a method, `_includeHeader()` for including the header file for the module, to support some legacy dependencies.

To make sure all our actions include the header we overwrite [`FroodController::__construct()`](Frood/Class/FroodController.html#__construct) and call the `_requireHeader()` there. If only some of your actions need XOOPS, you can also just call `_requireHeader()` in the individual actions. The latter is the better solution, performance wise, as loading the whole header if you don't need it is unnecessary. The former is convenient if you know you need it for all your actions.

		<?php
		abstract class SomeController extends FroodController {

			/** @var string Output mode Image. */
			const _IMAGE = 'Image';

			/**
			 * Construct a new controller instance.
			 * This is automatically called from The Frood.
			 *
			 * @param string $module The module we're working with.
			 * @param string $app    Which application are we running?
			 *
			 * @return void
			 */
			public function __construct($module, $app) {
				parent::__construct($module, $app);
				$this->_requireHeader();
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
				switch ($this->_getOutputMode()) {
					case self::_IMAGE:
						$this->_renderImage($action);
						break;
					default:
						parent::render($action);
						break;
				}
			}

			/**
			 * Include the module header file.
			 *
			 * @return void
			 */
			protected function _requireHeader() {
				include_once dirname(__FILE__) . '/../../../header.php';
			}

			/**
			 * Set the output mode to image.
			 *
			 * @return void
			 */
			final public function doOutputImage() {
				$this->_doOutput(self::_IMAGE);
			}

			/**
			 * Render the output as an image.
			 *
			 * @param string $action The action to render the view for.
			 *
			 * @return string The rendered output.
			 */
			private function _renderImage($action) {
				$image = new XphotoImage($this->_getValue('imageId'));
				header('Content-Type: ' . $image->getMimeType());
				header('Content-Disposition: attachment; filename="' . $this->_getValue('imageId') . $image->getExtension() . '"');
				header('Content-Length: ' . filesize($image->getFile(true)));
				echo file_get_contents($image->getFile(true));
			}
		}
		?>
