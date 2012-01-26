<?php
class FroodModuleRouter extends FroodRouter {
	private $_module;
	
	public function __construct($module) {
		$this->_module = $module;
	}
	
	public function route(FroodRequest $request) {
		$exp = '/
			^
			\/([a-z][a-z0-9_]*) # 3 : controller
			\/([a-z][a-z0-9_]*) # 4 : action
		/x';
		$matches = array();
		if (preg_match($exp, $request->getRequestString(), $matches)) {
			$request
				->setModule($this->_module)
				->setSubModule('public')
				->setController($matches[1])
				->setAction($matches[2])
			;
		}
	}
}
