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


Class file locations
--------------------

Frood will always autoload all classes in `class/`.

In addition it will also autoload the classes in the app class folder, depending on which app you are running, i.e. one of:

  * `public/class/`
  * `admin/class/`
  * `local/class/`
  * `cron/class/`

It will recursively scan all folders in the class folders, giving you the freedom to organize your classes as you see fit. That being said, the following _conventions_ should be adhered to, for consistency:

  * Model classes should be placed in `class/model/`.
  * Controller classes should be placed in `class/controllers/`.


Template file locations
-----------------------

Templates are placed in `templates/[app]/[controller]/[action].tpl.html`, where `app`, `controller` and `action` are on LOW form.

Example: `LolBananaController::kebabAction()`, in the `public` app would be rendered with the following template:

	templates/public/lol/kebab.tpl.html


Controllers and actions
=======================

Controllers must extend [`FroodController`](Frood/Class/FroodController.html). The class name must begin with the module name on CC form, and end with `Controller`. They should be placed in an apps `class/controllers` folder, to ensure that they only are accesible from a specific app.

Controllers should implement some actions. These must be public methods, taking one parameter of the class, [`FroodParameters`](Frood/Class/FroodParameters.html).

Controller actions are invoked by http requesting a URI or by [`FroodRemote`](Frood/Class/FroodRemote.html)

Example: `LolBananaController::kebabAction()`, in the `public` app would be invoked with the following URI:

	/modules/lol/public/banana/kebab

or

	/modules/lol/banana/kebab

because the public app is default.

The same action could be reached by [`FroodRemote`](Frood/Class/FroodRemote.html), using this code:

		<?php
		$remote = new FroodRemote('lol');

		$output = $remote->dispatch('banana', 'kebab');
		?>

See the section called `FroodRemote` for more information.


Action output
=============

By default actions are rendered in a Xoops context. This means that `admin` pages will get the menu and look like admin pages, and that `public` pages will be rendered in the theme.

[`FroodController`](Frood/Class/FroodController.html) has three output modes, which can be changed in an action by calling any one of the following, anywhere in the action (or controller constructor):

		<?php
		// ...
		$this->doOutputXoops();  // Default
		$this->doOutputSmarty(); // Just smarty
		$this->doOutputJson();   // JSON formatted output - ignores any template
		// ...
		?>


Action parameters
=================

All actions take an instance of [`FroodParameters`](Frood/Class/FroodParameters.html) as the first, and often only, parameter.

You should never access `$_GET`, `$_POST` and `$_FILES` directly. Instead you use the parameters instance, and call `getXxx()` methods on it:

		<?php
		// ...
		public function someAction(FroodParameters $params) {
			// Attempt to get the value of the get or post parameter, "id".
			// Will throw an exception if this parameter is not set!
			$id = $this->getId();

			// Same as above but will throw an exception
			// if a given value cannot be casted to integer.
			$id = $this->getId(FroodParameters::AS_INTEGER);

			// Same as above, but instead of an exception you get 42.
			$id = $this->getId(FroodParameters::AS_INTEGER, 42);
		}
		// ...
		?>

You can also test whether a given parameter is set, without having to catch an exception, by calling the `hasXxx()` functions:

		<?php
		// ...
		public function someAction(FroodParameters $params) {
			if ($this->hasBigSalmon()) {
				// This is invoked if a parameter called, "big_salmon" is given.
			}
			if ($this->hasHugeStork(FroodParameters::AS_STRING)) {
				// This is invoked if a parameter called, "huge_stork" is given,
				// and it can be typecast as a string.
			}
		}
		// ...
		?>

There are various `AS_`-constants you can use. Find them on [the documentation page for [`FroodParameters`](Frood/Class/FroodParameters.html).


File parameters
---------------

Submitted files are accessed through the parameters instance, like other parameters, but instead of an integer, string or array, you get an instance of [`FroodFileParameter`](Frood/Class/FroodFileParameter.html). See the documentation for the class, for a description of it's methods.


`FroodRemote`
=============

The [`FroodRemote`](Frood/Class/FroodRemote.html) facilitates working with other modules Frood enabled modules, without the hassle of HTTP'ing yourself. As an added bonus, it will work directly with PHP when communicating with other local modules, eliminating the overhead of HTTP requests.

To call an action on a local module, simply instantiate a [`FroodRemote`](Frood/Class/FroodRemote.html) and call [`dispatch`](Frood/Class/FroodRemote.html#dispatch):

		<?php
		$remote = new FroodRemote('lol');

		$output = $remote->dispatch('banana', 'kebab');

		// And with some parameters:
		$output = $remote->dispatch('banana', 'kebab', new FroodParameters(
			array(
				'id'         => 42,
				'some_thing' => 'Meget hest',
				'some_file'  => new FroodFileParameter(
					'/path/to/local/file'
				),
			)
		));

		?>


The apps
========

The Frood makes 4 distinctively different apps available for implementation. Every app is optional.

Generally, the apps can be accessed via http requests to URI's of the following format:

	/modules/[moduleName]/[app]/[controller]/[action]

If no action is given The Frood defaults to the `index` action.


The `public` app
----------------

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
				header('Content-Disposition: attachment; filename="'
					. $this->_getValue('imageId')
					. $image->getExtension() . '"');
				header('Content-Length: ' . filesize($image->getFile(true)));
				echo file_get_contents($image->getFile(true));
			}
		}
		?>
