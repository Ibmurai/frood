<?php
/**
 * Test the parameters class for The Frood.
 *
 * PHP version 5
 *
 * @category Test
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-09
 */

/**
 * FroodParameters - Testing it!
 *
 * @category   Test
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodParametersTest extends PHPUnit_Framework_TestCase {
	/** @var Frood The Frood instance used for these tests. */
	private $_frood = null;

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
	 * Provides test data for testGetParameter and testHasParameter.
	 *
	 * @return FroodParameters Test data for testGetParameter and testHasParameter.
	 */
	public static function providerSomeFroodParameters() {
		return array(array(new FroodParameters(array(
			'on_your_face'   => 'bunched',
			'on_your_face2'  => 'The munchies',
			'IAmYourFather'  => 'Luke',
			'iAmYour_mother' => 'yo YO!',
			'a'              => 'A',
			'B'              => 'b',
		))));
	}

	/**
	 * Test parameter get's.
	 *
	 * @dataProvider providerSomeFroodParameters
	 *
	 * @param FroodParameters $params
	 *
	 * @return void
	 */
	public function testGetParameter(FroodParameters $params) {
		$this->assertEquals('bunched', $params->getOnYourFace());
		$this->assertEquals('The munchies', $params->getOnYourFace2());
		$this->assertEquals('Luke', $params->getIAmYourFather());
		$this->assertEquals('yo YO!', $params->getIAmYourMother());
		$this->assertEquals('A', $params->getA());
		$this->assertEquals('b', $params->getB());
	}
}
