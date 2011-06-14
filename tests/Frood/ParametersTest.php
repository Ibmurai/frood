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
		$this->_frood = new Frood();
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
	 * Test various $_GET and $_POST combinations.
	 *
	 * @return void
	 */
	public function testParameterCombinations() {
	}
}
