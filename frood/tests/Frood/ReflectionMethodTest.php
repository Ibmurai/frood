<?php
/**
 * Test the magic docblock parameters for action thing.
 *
 * PHP version 5
 *
 * @category Test
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-09-20
 */

/**
 * FroodReflectionMethod - Testing it!
 *
 * @category   Test
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodReflectionMethodTest extends PHPUnit_Framework_TestCase {
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp() {
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown() {
	}

	/**
	 * Test it with a real docblocked action.
	 *
	 * @return void
	 */
	public function testDocblocked() {
		$controllerInstance = new StupidController('stupid', 'public', 'awesome');
		$parameters         = new FroodParameters(
			array(
				'int'      => '42',
				'required' => '42.42',
				'bool'     => 'true',
				'string'   => 'bo',
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
				null
			)
		);
	}

	/**
	 * Test it with an oldschool Frood action.
	 *
	 * @return void
	 */
	public function testOldschool() {
		$controllerInstance = new StupidController('stupid', 'public', 'oldschool');
		$parameters         = new FroodParameters(array('param' => 'omgitworks'));
		$methodReflection   = new FroodReflectionMethod($controllerInstance, 'oldschoolAction');

		$this->assertEquals($methodReflection->call($parameters), 'omgitworks');
	}

	/**
	 * We want an exception when the docblock does not match the method signature.
	 *
	 * @expectedException RuntimeException
	 *
	 * @return void
	 */
	public function testWrongDocblockException() {
		$controllerInstance = new StupidController('stupid', 'public', 'stupid');
		$parameters         = new FroodParameters();
		$methodReflection   = new FroodReflectionMethod($controllerInstance, 'stupidAction');

		$methodReflection->call($parameters);
	}

	/**
	 * Test it with a real docblocked actiony and missing a required parameter.
	 *
	 * @expectedException RuntimeException
	 *
	 * @return void
	 */
	public function testMissingRequiredException() {
		$controllerInstance = new StupidController('stupid', 'public', 'awesome');
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
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
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
	 * @param boolean $bool     <true>    Some comment here.
	 * @param string  $string   <default>
	 * @param integer $int      <101010>  101010 is 42...
	 * @param float   $required <>        This param is required.
	 * @param file    $file     <null>
	 *
	 * @return array Yes it's an action that returns something. It's a test. Relax man.
	 */
	public function awesomeAction($bool, $string, $int, $required, $file) {
		return array(
			$bool,
			$string,
			$int,
			$required,
			$file,
		);
	}

	// @codingStandardsIgnoreStart
	/**
	 * This action has an incorrect docblock.
	 *
	 * @param integer $skolemad <42> Bras.
	 *
	 * @return void
	 */
	public function stupidAction($params) {
		echo $params;
	}
	// @codingStandardsIgnoreEnd

}
