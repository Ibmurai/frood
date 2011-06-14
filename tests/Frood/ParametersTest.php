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
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp() {
		Frood::initialize();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown() {
	}

	public static function providerConvertPhpNameToHtmlName() {
		return array(
			array('TheNewWalrus', 'the_new_walrus'),
			array('IAm42Years', 'i_am42_years'),
			array('Param983', 'param983'),
			array('BJobs4All', 'b_jobs4_all'),
			array('AmIRite', 'am_i_rite'),
		);
	}

	/**
	 * Test FroodParameters::convertPhpNameToHtmlName()
	 *
	 * @dataProvider providerConvertPhpNameToHtmlName
	 *
	 * @return void
	 */
	public function testConvertPhpNameToHtmlName($input, $output) {
		$this->assertEquals(FroodParameters::convertPhpNameToHtmlName($input), $output);
	}

}
