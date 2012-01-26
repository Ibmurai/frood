<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodRouterChain - Manages a collection of Frood routers.
 * 
 * @category   Frood
 * @package    Routing
 * @author     Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @author     Bo Thinggaard <akimsko@tnactas.dk>
 */
class FroodRouterChain {
	/** @var FroodRouter[] The Frood routers. */
	private $_routers = array();
	
	public function add(FroodRouter $router) {
		$this->_routers[] = $router;
	}
	
	public function route(FroodRequest $request) {
		$routerIterator = new ArrayIterator($this->_routers);
		while (!$request->isComplete()) {
			$routerIterator->next()->route($request);
		}
	}
}
