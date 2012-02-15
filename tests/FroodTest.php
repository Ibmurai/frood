<?php
/**
 * This file is part of The Frood framework.
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
/**
 * Frood - Testing it!
 *
 * @category Frood
 * @package  Test
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 */
class FroodTest extends PHPUnit_Framework_TestCase {
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
	 * Provides test data for ->testConvertPhpNameToHtmlName
	 *
	 * @return array Some test data.
	 */
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
	 * Test FroodUtil::convertPhpNameToHtmlName()
	 *
	 * @param string $input  Input
	 * @param string $output Output
	 *
	 * @dataProvider providerConvertPhpNameToHtmlName
	 *
	 * @return null
	 */
	public function testConvertPhpNameToHtmlName($input, $output) {
		$this->assertEquals($output, FroodUtil::convertPhpNameToHtmlName($input));
	}

	/**
	 * Test FroodUtil::convertHtmlNameToPhpName()
	 *
	 * @param string $output Output
	 * @param string $input  Input
	 *
	 * @dataProvider providerConvertPhpNameToHtmlName
	 *
	 * @return null
	 */
	public function testConvertHtmlNameToPhpName($output, $input) {
		$this->assertEquals($output, FroodUtil::convertHtmlNameToPhpName($input));
	}

	/**
	 * Test request parsing and exceptions.
	 *
	 * @return null
	 */
	public function testRequestParsing() {
		$_SERVER['REQUEST_URI'] = '/frood/public/buildresults/frood?tab=coverage';

		$frood = new Frood(new FroodTestConfiguration());

		try {
			$frood->dispatch();
			$this->fail('Expected an Exception before this!');
		} catch (FroodExceptionDispatch $e) {
			$this->assertEquals(
				'buildresults',
				$e->getRequest()->getController()
			);
			$this->assertEquals(
				'frood',
				$e->getRequest()->getAction()
			);
		}
	}

	/**
	 * Test request parsing with no specified action.
	 *
	 * @return null
	 */
	public function testRequestParsingNoAction() {
		$_SERVER['REQUEST_URI'] = '/frood/public/test';

		$frood = new Frood(new FroodTestConfiguration());

		try {
			$frood->dispatch();
			$this->fail('Expected an Exception before this!');
		} catch (FroodExceptionDispatch $e) {
			$this->assertEquals(
				'test',
				$e->getRequest()->getController()
			);
			$this->assertEquals(
				'index',
				$e->getRequest()->getAction()
			);
		}
	}

	/**
	 * Test request parsing with no specified action.
	 *
	 * @return null
	 */
	public function testRequestParsingNoActionTrailingSlash() {
		$_SERVER['REQUEST_URI'] = '/frood/public/test/';

		$frood = new Frood(new FroodTestConfiguration());

		try {
			$frood->dispatch();
			$this->fail('Expected an Exception before this!');
		} catch (FroodExceptionDispatch $e) {
			$this->assertEquals(
				'test',
				$e->getRequest()->getController()
			);
			$this->assertEquals(
				'index',
				$e->getRequest()->getAction()
			);
		}
	}
}

class FroodTestConfiguration extends FroodConfiguration {
	/**
	 * Get the path, relative to Frood.php, where modules reside.
	 *
	 * @return string
	 */
	public function getModulesPath () {
		return realpath(dirname(__FILE__)) . '/';
	}
}
