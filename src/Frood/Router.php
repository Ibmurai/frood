<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodRouter - An abstract class representing a routing strategy.
 *
 * @category Frood
 * @package  Routing
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author   Bo Thinggaard <akimsko@tnactas.dk>
 */
abstract class FroodRouter {
	/**
	 * This should modify the request, filling in subModule, controller and action, if the request could be routed by this router.
	 *
	 * @param FroodRequest $request The request to attempt to route.
	 */
	abstract public function route(FroodRequest $request);
}
