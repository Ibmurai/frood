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

The XML renderer, [`FroodRendererXml`](Frood/Class/FroodRendererXml.html) uses templates with the extension, `.tpl.xml`.


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

[`FroodController`](Frood/Class/FroodController.html) has five output modes, which can be changed in an action by calling any one of the following, anywhere in the action (or controller constructor):

		<?php
		// ...
		$this->doOutputXoops();        // Default
		$this->doOutputSmarty();       // Just plain smarty.
		$this->doOutputJson();         // JSON formatted output - ignores any template.
		$this->doOutputJsonAutoUtf8(); // Like ->doOutputJson but recursively calls utf8_encode on all
		                               // contained strings.
		$this->doOutputDisabled();     // Outputs nothing, with content type text/plain.
		$this->doOutputXml();          // Outputs a smarty rendered template, with content type text/xml.
		// ...
		?>


Legacy action parameters
========================

*This is still supported but you probably don't want to write your actions like this - read the next section instead ;) *

All actions take an instance of [`FroodParameters`](Frood/Class/FroodParameters.html) as the only parameter.

You should never access `$_GET`, `$_POST` and `$_FILES` directly. Instead you use the parameters instance, and call `getXxx()` methods on it:

		<?php
		// ...
		public function someAction(FroodParameters $params) {
			// Attempt to get the value of the get or post parameter, "id".
			// Will throw an exception if this parameter is not set!
			$id = $params->getId();

			// Same as above but will throw an exception
			// if a given value cannot be casted to integer.
			$id = $params->getId(FroodParameters::AS_INTEGER);

			// Same as above, but instead of an exception you get 42.
			$id = $params->getId(FroodParameters::AS_INTEGER, 42);
		}
		// ...
		?>

You can also test whether a given parameter is set, without having to catch an exception, by calling the `hasXxx()` functions:

		<?php
		// ...
		public function someAction(FroodParameters $params) {
			if ($params->hasBigSalmon()) {
				// This is invoked if a parameter called, "big_salmon" is given.
			}
			if ($params->hasHugeStork(FroodParameters::AS_STRING)) {
				// This is invoked if a parameter called, "huge_stork" is given,
				// and it can be typecast as a string.
			}
		}
		// ...
		?>

There are various `AS_`-constants you can use. Find them on [the documentation page for [`FroodParameterCaster`](Frood/Class/FroodParameterCaster.html).


Action parameters
=================

Parameters for an action are specified and annotated like you're use to, with one addition to the `@param` annotation: What is normally the description of the parameter is interpreted as the default value for the parameter, up to and excluding the first `~`.

This is best illustrated with an example:

		<?php
		// ...
		/**
		 * A nifty description of what this action does.
		 *
		 * @param boolean $bool       <true> A boolean with a default value of true.
		 * @param integer $int               An integer with no default value.
		 * @param float   $aFloat     <42.0>
		 * @param string  $someString <I'm a nifty default for a string parameter.>
		 * @param json    $array      <null> A json encoded string, decoded to an associative array.
		 * @param array   $anArray    <null>
		 * @param file    $imAFile    <null> A FroodFileParameter instance or null.
		 *
		 * @return void
		 */
		public function someAction($bool, $int, $aFloat, $someString, $array, $anArray, $imAFile) {
			// ...
		}

The various supported types can be found in the documentation for [`FroodParameterCaster`](Frood/Class/FroodParameterCaster.html) (The `AS_`-constants).


File parameters
---------------

Submitted files are accessed through the parameters instance, like other parameters, but instead of an integer, string or array, you get an instance of [`FroodFileParameter`](Frood/Class/FroodFileParameter.html). See the documentation for the class, for a description of it's methods.


Action forwarding
=================

You can forward an action to another action on the same host. Forwarding will not change the url in the browser.

Forwarding is done by calling [`->_forward`](Frood/Class/FroodController.html#_forward) in an action. See the documentation for details on the parameters.

_Forwarding ends all execution after the call to `->_forward`._

Example
-------

A common use is to forward the index action to the list action:

		<?php
		// ...
		public function indexAction(FroodParameters $params) {
			$this->_forward($params, 'list');

			echo "You'll never see this output!";
		}

		public function listAction(FroodParameters $params) {
			// Fancy list code here.
		}
		// ...
		?>

Action redirecting
==================

This works exactly like action forwarding, except the browser is redirected, and you may redirect to a different host.

See [`->_redirect`](Frood/Class/FroodController.html#_redirect)


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

It is also possible to remote to other Froody sites:

		<?php
		$remote = new FroodRemote('fsArticle', 'public', 'http://fyens.dk');

		$articles = $remote->dispatch('articles', 'list');

		// Set the fourth argument to true, to enable automatic json decoding of the result:
		$articleArray = $remote->dispatch(
			'articles',
			'listJson',
			new FroodParameters(
				array(
					'limit' => 10,
				)
			),
			true // Enable json decoding
		);
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


Custom output renderers
=======================

It is possible to implement your own custom output renderer.

Example coming soon. For now, you can look at [`FroodController`](Frood/Class/FroodController.html) to see how to implement the `->doOutput` method, and probably guess how to do it.


Extending the Frood controller
==============================

It is not required to extend the [`FroodController`](Frood/Class/FroodController.html) to make use of Frood, but for various reasons it is usually a good idea:

  * You may need to include the header file in legacy modules.
  * You may need to output something which is not directly supported by Frood.

You should generally have one base controller for each app. For the module `lol`, for example:

  * `/admin/class/controllers/LolController.php`
  * `/public/class/controllers/LolController.php`
  * `/local/class/controllers/LolController.php`
  * `/cron/class/controllers/LolController.php`

If you need to share controller functionality across app base controllers, let them extend a controller in the shared class folder. For the module `lol`, for example:

  * `/class/LolControllerBase.php`

_The naming conventions in the above examples should be adhered to._

Example
-------

In this example we add a method, `_includeHeader()` for including the header file for the module, to support some legacy dependencies.

To make sure all our actions include the header we overwrite [`FroodController::__construct()`](Frood/Class/FroodController.html#__construct) and call the `_includeHeader()` there. If only some of your actions need XOOPS, you can also just call `_includeHeader()` in the individual actions. The latter is the better solution, performance wise, as loading the whole header if you don't need it is unnecessary. The former is convenient if you know you need it for all your actions.

		<?php
		abstract class SomeController extends FroodController {

			/**
			 * Construct a new controller instance.
			 * This is automatically called from The Frood.
			 *
			 * @param string $module The module we're working with.
			 * @param string $app    Which application are we running?
			 * @param string $action Which action is Frood invoking?
			 *
			 * @return void
			 */
			public function __construct($module, $app, $action) {
				parent::__construct($module, $app, $action);

				$this->_includeHeader();
			}

			/**
			 * Include the module header file.
			 *
			 * @return void
			 */
			protected function _includeHeader() {
				include_once dirname(__FILE__) . '/../../../header.php';
			}

		}
		?>

Building a versioned REST api with Frood
==============================

Frood 1.3.0+ supports versioned RESTful controllers when using the API router.

First you must tell Frood to use the API router in your module configuration.

		#modules/Lolmodule/Configuration.php
		<?php
		class LolmoduleConfiguration extends FroodModuleConfiguration {

			// ...

			/**
			 * Get the module router for the configured module.
			 *
			 * @return FroodModuleRouterApi
			 */
			public function getRouter() {
				static $router;
				return $router ? $router : ($router = new FroodModuleRouterApi($this->getModule()));
			}
		}

This enbales a new submodule called Api.

Create the Api submodule and a folder for the first version.

		$ mkdir modules/Lolmodule/Api
		$ mkdir modules/Lolmodule/Api/V1

Following Frood autoloader conventions, create a controller that enxtends FroodControllerRest.

		#modules/Lolmodule/Api/V1/Face.php
		<?php
        class LolmoduleApiControllerV1Face extends FroodControllerRest {

        }

Each controller will manage a single resource. (Faces in this case).

Now we need to override the methods we wish to react to for this resource.
Here's a quick example of basic CRUD functionality...

		#modules/Lolmodule/Api/V1/Face.php
		<?php
		class LolmoduleApiControllerV1Face extends FroodControllerRest {

			/**
			 * When this resource is requested with the GET method, this action is called.
			 *
			 * @param FroodParameters   $params   Additional frood params.
			 * @param FroodHttpRequest  $request  The client request.
			 * @param mixed|null        $item     The requested item (if any).
			 *
			 * @throws FroodHttpException If something goes wrong.
			 */
			protected function _get(FroodParameters $params, FroodHttpRequest $request, $item = null) {

				// fetch item with id $item

				if (!$fetchedItem) {
					throw new FroodHttpException('Item not found', FroodHttpResponseCode::CODE_NOT_FOUND);
				}

				$this->assign('item', $fetchedItem);
			}

			/**
			 * When this resource is requested with the POST method, this action is called.
			 *
			 * @param FroodParameters   $params   Additional frood params.
			 * @param FroodHttpRequest  $request  The client request.
			 * @param mixed|null        $item     The requested item (if any).
			 *
			 * @throws FroodHttpException If something goes wrong.
			 */
			protected function _post(FroodParameters $params, FroodHttpRequest $request, $item = null) {

				$newItem = json_decode($request->getMessage());

				// Validate item.

				if (!$valid) {
					throw new FroodHttpException('Invalid item', FroodHttpResponseCode::CODE_BAD_REQUEST);
				}

				// persist item.

				if (!$persisted) {
					throw new FroodHttpException('Failed to create item', FroodHttpResponseCode::CODE_INTERNAL_SERVER_ERROR);
				}

				$this->assign('item', $persistedItem);
				$this->setResponseCode(FroodHttpResponseCode::CODE_CREATED);
			}

			/**
			 * When this resource is requested with the PUT method, this action is called.
			 *
			 * @param FroodParameters   $params   Additional frood params.
			 * @param FroodHttpRequest  $request  The client request.
			 * @param mixed|null        $item     The requested item (if any).
			 *
			 * @throws FroodHttpException If something goes wrong.
			 */
			protected function _put(FroodParameters $params, FroodHttpRequest $request, $item = null) {

				// Validate item.

				if (!$valid) {
					throw new FroodHttpException('Invalid item', FroodHttpResponseCode::CODE_BAD_REQUEST);
				}

				// fetch item with id $item

				if (!$fetchedItem) {
					throw new FroodHttpException('Item not found', FroodHttpResponseCode::CODE_NOT_FOUND);
				}

				// update item.

				if (!$updated) {
					throw new FroodHttpException('Failed to update item', FroodHttpResponseCode::CODE_INTERNAL_SERVER_ERROR);
				}

				$this->assign('item', $updatedItem);
                $this->setResponseCode(FroodHttpResponseCode::CODE_CREATED);
			}

			/**
			 * When this resource is requested with the DELETE method, this action is called.
			 *
			 * @param FroodParameters   $params   Additional frood params.
			 * @param FroodHttpRequest  $request  The client request.
			 * @param mixed|null        $item     The requested item (if any).
			 *
			 * @throws FroodHttpException If something goes wrong.
			 */
			protected function _delete(FroodParameters $params, FroodHttpRequest $request, $item = null) {

				// fetch item with id $item

				if (!$fetchedItem) {
					throw new FroodHttpException('Item not found', FroodHttpResponseCode::CODE_NOT_FOUND);
				}

				// delete item

				if (!$deleted) {
					throw new FroodHttpException('Failed to delete item', FroodHttpResponseCode::CODE_INTERNAL_SERVER_ERROR);
				}

				$this->assign('item', $deletedItem);
				$this->setResponseCode(FroodHttpResponseCode::CODE_OK); // Default
			}

		}

FroodHttpException will be picked up by frameworks like Zaphod and rendered as the response.

You should NOT use FroodHttpException to create successful reponses (like 200 and 201). In these cases instead set the response code via $this->setResponseCode().
