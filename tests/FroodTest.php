<?php
/**
 * Test the base class for The Frood.
 *
 * PHP version 5
 *
 * @category Test
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-09
 */

/**
 * Frood - Testing it!
 *
 * @category   Test
 * @package    Frood
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class FroodTest extends PHPUnit_Framework_TestCase {
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
	 * Test getTitle
	 *
	 * @return void
	 */
	public function testGetTitle() {
		
		$this->assertEquals('This is a document title', 'This is a document title');
	}

}
