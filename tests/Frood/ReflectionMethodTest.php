<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * FroodReflectionMethod - Testing it!
 *
 * @category Frood
 * @package  Test
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class FroodReflectionMethodTest extends PHPUnit_Framework_TestCase {
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return null
	 */
	protected function setUp() {
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return null
	 */
	protected function tearDown() {
	}

	/**
	 * Test it with a real docblocked action.
	 *
	 * @return null
	 */
	public function testDocblocked() {
		$request = new FroodRequest();
		$request
			->setModule('stupid')
			->setSubModule('public')
			->setController('awesome')
		;
		$controllerInstance = new StupidController($request);
		$parameters         = new FroodParameters(
			array(
				'int'      => '42',
				'required' => '42.42',
				'bool'     => 'true',
				'string'   => 'bo',
				'a_int'    => array(1, 2, 3),
				'a_string' => array('1', '2', '3'),
			)
		);
		$methodReflection   = new FroodReflectionMethod($controllerInstance, 'awesomeAction');

		$this->assertEquals(
			$methodReflection->call($parameters),
			array(
				true,
				'bo',
				42,
				42.42,
				array(1, 2, 3),
				array('1', '2', '3'),
			)
		);
	}

	/**
	 * Test it with an oldschool Frood action.
	 *
	 * @return null
	 */
	public function testOldschool() {
		$request = new FroodRequest();
		$request
			->setModule('stupid')
			->setSubModule('public')
			->setController('awesome')
		;
		$controllerInstance = new StupidController($request);
		$parameters         = new FroodParameters(array('param' => 'omgitworks'));
		$methodReflection   = new FroodReflectionMethod($controllerInstance, 'oldschoolAction');

		$this->assertEquals($methodReflection->call($parameters), 'omgitworks');
	}

	/**
	 * We want an exception when the docblock does not match the method signature.
	 *
	 * @expectedException RuntimeException
	 *
	 * @return null
	 */
	public function testWrongDocblockException() {
		$request = new FroodRequest();
		$request
			->setModule('stupid')
			->setSubModule('public')
			->setController('awesome')
		;
		$controllerInstance = new StupidController($request);
		$parameters         = new FroodParameters();
		$methodReflection   = new FroodReflectionMethod($controllerInstance, 'stupidAction');

		$methodReflection->call($parameters);
	}

	/**
	 * Test it with a real docblocked actiony and missing a required parameter.
	 *
	 * @expectedException RuntimeException
	 *
	 * @return null
	 */
	public function testMissingRequiredException() {
		$request = new FroodRequest();
		$request
			->setModule('stupid')
			->setSubModule('public')
			->setController('awesome')
		;
		$controllerInstance = new StupidController($request);
		$parameters         = new FroodParameters(
			array(
				'int'      => '42',
				'bool'     => 'true',
				'string'   => 'bo',
			)
		);
		$methodReflection   = new FroodReflectionMethod($controllerInstance, 'awesomeAction');

		$methodReflection->call($parameters);
	}
}

/**
 * Stupid class for testing purposes.
 *
 * @category   Test
 * @package    Frood
 * @subPackage Class
 * @author     Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class StupidController extends FroodController {
	/**
	 * An oldschool frood action. It returns something for testing purposes.
	 *
	 * @param FroodParameters $params The parameters for the action.
	 *
	 * @return string Yes it's an action that returns something. It's a test. Relax man.
	 */
	public function oldschoolAction(FroodParameters $params) {
		return $params->getParam(FroodParameters::AS_STRING);
	}

	/**
	 * A newschool awesome action with docblock params oh yeah.
	 *
	 * @param boolean   $bool     <true>    Some comment here.
	 * @param string    $string   <default>
	 * @param integer   $int      <101010>  101010 is 42...
	 * @param float     $required <>        This param is required.
	 * @param integer[] $aInt     <>        An array of integers.
	 * @param string[]  $aString  <>        An array of strings.
	 *
	 * @return array Yes it's an action that returns something. It's a test. Relax man.
	 */
	public function awesomeAction($bool, $string, $int, $required, $aInt, $aString) {
		return array(
			$bool,
			$string,
			$int,
			$required,
			$aInt,
			$aString,
		);
	}

	// @codingStandardsIgnoreStart
	/**
	 * This action has an incorrect docblock.
	 *
	 * @param integer $skolemad <42> Bras.
	 *
	 * @return null
	 */
	public function stupidAction($params) {
		echo $params;
	}
	// @codingStandardsIgnoreEnd

}
