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
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class FroodParametersTest extends PHPUnit_Framework_TestCase {
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
	 * Test parameter get's.
	 *
	 * @return void
	 */
	public function testGetParameter() {
		$params = new FroodParameters(
			array(
				'on_your_face'   => 'bunched',
				'on_your_face2'  => 'The munchies',
				'IAmYourFather'  => 'Luke',
				'iAmYour_mother' => 'yo YO!',
				'a'              => 'A',
				'B'              => 'b',
			)
		);

		$this->assertEquals('bunched', $params->getOnYourFace());
		$this->assertEquals('The munchies', $params->getOnYourFace2());
		$this->assertEquals('Luke', $params->getIAmYourFather());
		$this->assertEquals('yo YO!', $params->getIAmYourMother());
		$this->assertEquals('A', $params->getA());
		$this->assertEquals('b', $params->getB());
	}

	/**
	 * Test parameter has's.
	 *
	 * @return void
	 */
	public function testHasParameter() {
		$params = new FroodParameters(
			array(
				'on_your_face'   => 'bunched',
				'on_your_face2'  => 'The munchies',
				'IAmYourFather'  => 'Luke',
				'iAmYour_mother' => 'yo YO!',
				'a'              => 'A',
				'B'              => 'b',
				'C'              => null,
			)
		);

		$this->assertTrue($params->hasOnYourFace());
		$this->assertTrue($params->hasOnYourFace2());
		$this->assertTrue($params->hasIAmYourFather());
		$this->assertTrue($params->hasIAmYourMother());
		$this->assertTrue($params->hasA());
		$this->assertTrue($params->hasB());
		$this->assertTrue($params->hasC());
		$this->assertFalse($params->hasOnyourFace());
		$this->assertFalse($params->hasNop());
		$this->assertFalse($params->hasStuff());
	}

	/**
	 * Test typed parameter has's.
	 *
	 * @return void
	 */
	public function testHasTypedParameter() {
		$params = new FroodParameters(
			array(
				'a' => 'A',
				'B' => 2,
				'f' => array(),
				'j' => 42.1,
			)
		);

		$this->assertTrue($params->hasA(FroodParameters::AS_STRING));
		$this->assertTrue($params->hasB(FroodParameters::AS_INTEGER));
		$this->assertTrue($params->hasF(FroodParameters::AS_ARRAY));
		$this->assertTrue($params->hasJ(FroodParameters::AS_FLOAT));
	}

	/**
	 * Test parameter default values.
	 *
	 * @return void
	 */
	public function testDefaultValues() {
		$params = new FroodParameters(
			array(
				'a' => 'A',
				'B' => 'b',
				'f' => null,
			)
		);

		$this->assertEquals('A', $params->getA(null, 'd'));
		$this->assertEquals('b', $params->getB(null, 'e'));
		$this->assertEquals('c', $params->getC(null, 'c'));
		$this->assertEquals(null, $params->getF(null, 'f'));
		$this->assertEquals(null, $params->getNull(null, null));
	}

	/**
	 * Test parameter values from GET/POST merging.
	 *
	 * @return void
	 */
	public function testGetPostMerging() {
		$_GET['lam']    = 'hej';
		$_GET['hest']   = 'not this';
		$_POST['lagen'] = 'strut';
		$_POST['hest']  = 'this';

		$params = new FroodParameters();

		$this->assertEquals('hej', $params->getLam());
		$this->assertEquals('strut', $params->getLagen());
		$this->assertEquals('this', $params->getHest());
	}

	/**
	 * Test parameter toString method.
	 *
	 * @return void
	 */
	public function testToString() {
		$params = new FroodParameters(
			array(
				'a' => 'A',
				'B' => 'b',
				's_m' => 'hej',
				'Nitrat' => 'uetUHet',
			)
		);

		$this->assertEquals('A=A, B=b, SM=hej, Nitrat=uetUHet', '' . $params);
	}

	/**
	 * Test parameter get with too many arguments.
	 *
	 * @expectedException RuntimeException
	 *
	 * @return void
	 */
	public function testTooManyArgumentsToGet() {
		$params = new FroodParameters();

		$params->getMechaSalmon('type', 'default', 'too many parameters');
	}

	/**
	 * Test parameter has with too many arguments.
	 *
	 * @expectedException RuntimeException
	 *
	 * @return void
	 */
	public function testTooManyArgumentsToHas() {
		$params = new FroodParameters();

		$params->hasMechaSalmon('type', 'too many parameters');
	}


	/**
	 * Test call to missing method.
	 *
	 * @expectedException RuntimeException
	 *
	 * @return void
	 */
	public function testMissingMethod() {
		$params = new FroodParameters();

		$params->superMechaSalmon('too many parameters');
	}

	/**
	 * Test parameter get with undefined parameter and no default.
	 *
	 * @expectedException RuntimeException
	 *
	 * @return void
	 */
	public function testNoDefaultValue() {
		$params = new FroodParameters();

		$params->getMechaSalmon();
	}

	/**
	 * Provides test data for the count test.
	 *
	 * @return array
	 */
	public static function providerCount() {
		return array(
			array(
				array(
					'a' => 1,
				),
				1
			),
			array(
				array(
					'a' => 1,
					'b' => 2,
				),
				2
			),
			array(
				array(
					'a' => 1,
					'b' => 2,
					'c' => 3,
				),
				3
			),
			array(
				array(
					'a' => 1,
					'b' => 2,
					'aa' => 1,
					'bo' => 2,
					'aeo' => 1,
					'ueub' => 2,
					'ieuia' => 1,
					'ueb' => 2,
					'auoeueo' => 1,
					'aaab' => 2,
					'ab' => 1,
					'bueueoueo' => 2,
					'az' => 1,
					'bz' => 2,
				),
				14
			),
		);
	}

	/**
	 * Test counting parameters.
	 *
	 * @param array   $values        An associative array to use as parameters.
	 * @param integer $expectedCount The number count should return.
	 *
	 * @dataProvider providerCount
	 *
	 * @return void
	 */
	public function testCount(array $values, $expectedCount) {
		$params = new FroodParameters($values);

		$this->assertEquals($expectedCount, count($params));
	}

	/**
	 * Test foreachability.
	 *
	 * @param array   $values        An associative array to use as parameters.
	 * @param integer $expectedCount The number of iterations expected.
	 *
	 * @dataProvider providerCount
	 *
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function testForeach(array $values, $expectedCount) {
		$params = new FroodParameters($values);

		$count = 0;
		foreach ($params as $key => $value) {
			$count++;
		}

		$this->assertEquals($expectedCount, $count);
	}

	/**
	 * Test integer conversion of parameter values.
	 *
	 * @return void
	 */
	public function testIntegerConversion() {
		$params = new FroodParameters(
			array(
				'laks'  => 'madpakke',
				'tal'   => '42',
				'mecha' => null,
			)
		);

		$this->assertEquals(9, $params->getLaks(FroodParameters::AS_INTEGER, 9));
		$this->assertEquals(42, $params->getTal(FroodParameters::AS_INTEGER));
		$this->assertEquals(12, $params->getMecha(FroodParameters::AS_INTEGER, 12));
	}

	/**
	 * Test string conversion of parameter values.
	 *
	 * @return void
	 */
	public function testStringConversion() {
		$params = new FroodParameters(
			array(
				'laks'  => 'madpakke',
				'tal'   => '42',
				'svar'  => 42,
				'mecha' => null,
			)
		);

		$this->assertEquals('madpakke', $params->getLaks(FroodParameters::AS_STRING));
		$this->assertEquals('42', $params->getTal(FroodParameters::AS_STRING));
		$this->assertEquals('42', $params->getSvar(FroodParameters::AS_STRING));
		$this->assertEquals('12', $params->getMecha(FroodParameters::AS_STRING, 12));
		$this->assertEquals('sko', $params->getMecha(FroodParameters::AS_STRING, 'sko'));
	}

	/**
	 * Test float conversion of parameter values.
	 *
	 * @return void
	 */
	public function testFloatConversion() {
		$params = new FroodParameters(
			array(
				'skam'  => '11',
				'laks'  => '22.2',
				'tal'   => '22,4',
				'svar'  => 42,
				'mecha' => 32.42,
				'slut'  => null,
				'spam'  => 'orm',
			)
		);

		$this->assertEquals(11.0, $params->getSkam(FroodParameters::AS_FLOAT));
		$this->assertEquals(22.2, $params->getLaks(FroodParameters::AS_FLOAT));
		$this->assertEquals(22.4, $params->getTal(FroodParameters::AS_FLOAT));
		$this->assertEquals(42.0, $params->getSvar(FroodParameters::AS_FLOAT));
		$this->assertEquals(32.42, $params->getMecha(FroodParameters::AS_FLOAT));
		$this->assertEquals(33.0, $params->getSlut(FroodParameters::AS_FLOAT, 33));
		$this->assertEquals(33.0, $params->getSlut(FroodParameters::AS_FLOAT, 33.0));
		$this->assertEquals(33.0, $params->getSlut(FroodParameters::AS_FLOAT, '33'));
		$this->assertEquals(33.0, $params->getSlut(FroodParameters::AS_FLOAT, '33.0'));
		$this->assertEquals(33.0, $params->getSlut(FroodParameters::AS_FLOAT, '33,0'));
		$this->assertEquals(0.0, $params->getSpam(FroodParameters::AS_FLOAT, 0));
	}

	/**
	 * Test array conversion of parameter values.
	 *
	 * @return void
	 */
	public function testArrayConversion() {
		$params = new FroodParameters(
			array(
				'skam'  => array(),
				'laks'  => array('2'),
				'tal'   => '22,4',
				'svar'  => 42,
				'mecha' => 32.42,
				'slut'  => null,
			)
		);

		$this->assertEquals(array(), $params->getSkam(FroodParameters::AS_ARRAY));
		$this->assertEquals(array('2'), $params->getLaks(FroodParameters::AS_ARRAY));
		$this->assertEquals(array(), $params->getTal(FroodParameters::AS_ARRAY, array()));
		$this->assertEquals(array(''), $params->getSvar(FroodParameters::AS_ARRAY, array('')));
		$this->assertEquals(array('1'), $params->getMecha(FroodParameters::AS_ARRAY, array('1')));
		$this->assertEquals(array('1', '2'), $params->getSlut(FroodParameters::AS_ARRAY, array('1', '2')));
	}

	/**
	 * Test json conversion of parameter values.
	 *
	 * @return void
	 */
	public function testJsonConversion() {
		$params = new FroodParameters(
			array(
				'skam'   => '["32","thirtytwo"]',
				'laks'   => '{"32":"thirtytwo"}',
				'tal'    => '[22,4.0]',
				'svar'   => '{"32":"thirtytwo","42":"fortytwo"}',
				'empty'  => '{}',
				'empty2' => '[]',
			)
		);

		$this->assertEquals(array('32','thirtytwo'), $params->getSkam(FroodParameters::AS_JSON));
		$this->assertEquals(array('32' => 'thirtytwo'), $params->getLaks(FroodParameters::AS_JSON));
		$this->assertEquals(array(22, 4.0), $params->getTal(FroodParameters::AS_JSON));
		$this->assertEquals(array('32' => 'thirtytwo', '42' => 'fortytwo'), $params->getSvar(FroodParameters::AS_JSON));
		$this->assertEquals(array(), $params->getEmpty(FroodParameters::AS_JSON));
		$this->assertEquals(array(), $params->getEmpty2(FroodParameters::AS_JSON));
	}

	/**
	 * Test file "conversion" of parameter values.
	 *
	 * @return void
	 */
	public function testFileConversion() {
		$params = new FroodParameters(
			array(
				'skam' => new FroodFileParameter(__FILE__),
				'guf'  => 'not a file',
			)
		);

		$this->assertEquals('FroodFileParameter', get_class($params->getSkam(FroodParameters::AS_FILE)));
		$this->assertEquals('FroodFileParameter', get_class($params->getGuf(FroodParameters::AS_FILE, new FroodFileParameter(__FILE__))));
	}

	/**
	 * Test boolean conversion of parameter values.
	 *
	 * @return void
	 */
	public function testBooleanConversion() {
		$params = new FroodParameters(
			array(
				'one'                    => 'true',
				'two'                    => 'TRUE',
				'three'                  => 'on',
				'four'                   => 'ON',
				'five'                   => 'checked',
				'five_and_a_half'        => 'CHECKED',
				'six'                    => true,
				'six_and_a_quarter'      => '1',
				'six_and_a_half'         => 1,
				'six_and_three_quarters' => 42,
				'six_and_four_fifths'    => -21,
				'seven'                  => 'false',
				'eight'                  => 'FALSE',
				'nine'                   => 'off',
				'ten'                    => 'OFF',
				'eleven'                 => '',
				'twelve'                 => 0,
				'thirteen'               => false,
				'fourteen'               => '0',
			)
		);

		$this->assertTrue($params->getOne(FroodParameters::AS_BOOLEAN));
		$this->assertTrue($params->getTwo(FroodParameters::AS_BOOLEAN));
		$this->assertTrue($params->getThree(FroodParameters::AS_BOOLEAN));
		$this->assertTrue($params->getFour(FroodParameters::AS_BOOLEAN));
		$this->assertTrue($params->getFive(FroodParameters::AS_BOOLEAN));
		$this->assertTrue($params->getFiveAndAHalf(FroodParameters::AS_BOOLEAN));
		$this->assertTrue($params->getSix(FroodParameters::AS_BOOLEAN));
		$this->assertTrue($params->getSixAndAQuarter(FroodParameters::AS_BOOLEAN));
		$this->assertTrue($params->getSixAndAHalf(FroodParameters::AS_BOOLEAN));
		$this->assertTrue($params->getSixAndThreeQuarters(FroodParameters::AS_BOOLEAN));
		$this->assertTrue($params->getSixAndFourFifths(FroodParameters::AS_BOOLEAN));
		$this->assertFalse($params->getSeven(FroodParameters::AS_BOOLEAN));
		$this->assertFalse($params->getEight(FroodParameters::AS_BOOLEAN));
		$this->assertFalse($params->getNine(FroodParameters::AS_BOOLEAN));
		$this->assertFalse($params->getTen(FroodParameters::AS_BOOLEAN));
		$this->assertFalse($params->getEleven(FroodParameters::AS_BOOLEAN));
		$this->assertFalse($params->getTwelve(FroodParameters::AS_BOOLEAN));
		$this->assertFalse($params->getThirteen(FroodParameters::AS_BOOLEAN));
		$this->assertFalse($params->getFourteen(FroodParameters::AS_BOOLEAN));
	}

	/**
	 * Test the integer array type.
	 *
	 * @return void
	 */
	public function testIntegerArrayConversion() {
		$params = new FroodParameters(
			array(
				'skam'      => array(),
				'skule'     => array(1),
				'skratmaas' => array(1, 42, 21, 7),
			)
		);

		$this->assertEquals(array(), $params->getSkam(FroodParameters::AS_INTEGER_ARRAY));
		$this->assertEquals(array(1), $params->getSkule(FroodParameters::AS_INTEGER_ARRAY));
		$this->assertEquals(array(1, 42, 21, 7), $params->getSkratmaas(FroodParameters::AS_INTEGER_ARRAY));
	}

	/**
	 * Test the string array type.
	 *
	 * @return void
	 */
	public function testStringArrayConversion() {
		$params = new FroodParameters(
			array(
				'skam'        => array(),
				'skule'       => array(42, 42, 42),
				'skratmaas'   => array('42', '42', '42'),
				'skridt_bind' => array('madpakke', 'madpakke', 'madpakke'),
			)
		);

		$this->assertEquals(array(), $params->getSkam(FroodParameters::AS_STRING_ARRAY));
		$this->assertEquals(array('42', '42', '42'), $params->getSkule(FroodParameters::AS_STRING_ARRAY));
		$this->assertEquals(array('42', '42', '42'), $params->getSkratmaas(FroodParameters::AS_STRING_ARRAY));
		$this->assertEquals(array('madpakke', 'madpakke', 'madpakke'), $params->getSkridtBind(FroodParameters::AS_STRING_ARRAY));
	}

	/**
	 * Provides data for testBooleanConversionException.
	 *
	 * @return array
	 */
	public function providerBooleanConversionException() {
		return array(
			array('a string'),
			array(array(42)),
			array(new FroodParameters(array())),
			array(new FroodFileParameter(__FILE__)),
			array('hello world'),
			array('TRU'),
			array('Nay'),
			array(0.42),
			array(null),
		);
	}

	/**
	 * Test boolean conversion exceptions when parameter value cannot be
	 * interpreted as boolean.
	 *
	 * @param mixed $value The value to fail casting to a boolean.
	 *
	 * @dataProvider      providerBooleanConversionException
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testBooleanConversionException($value) {
		$params = new FroodParameters(
			array(
				'value' => $value,
			)
		);

		$params->getValue(FroodParameters::AS_BOOLEAN);
	}

	/**
	 * Provides data for testFileConversionException.
	 *
	 * @return array
	 */
	public function providerFileConversionException() {
		return array(
			array('a string'),
			array(42),
			array(42.9),
			array(array('some array')),
			array(new FroodParameters(array())),
		);
	}

	/**
	 * Test file conversion exceptions when parameter value cannot be
	 * interpreted as file.
	 *
	 * @param mixed $value The value to fail casting to a file.
	 *
	 * @dataProvider      providerFileConversionException
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testFileConversionException($value) {
		$params = new FroodParameters(
			array(
				'value' => $value,
			)
		);

		$params->getValue(FroodParameters::AS_FILE);
	}

	/**
	 * Test integer conversion exceptions when parameter value cannot be
	 * interpreted as integer. When value is a string.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testIntegerConversionExceptionString() {
		$params = new FroodParameters(
			array(
				'laks' => 'madpakke',
			)
		);

		$params->getLaks(FroodParameters::AS_INTEGER);
	}

	/**
	 * Test integer conversion exceptions when parameter value cannot be
	 * interpreted as integer. When value is null.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testIntegerConversionExceptionNull() {
		$params = new FroodParameters(
			array(
				'laks'  => null,
			)
		);

		$params->getLaks(FroodParameters::AS_INTEGER);
	}

	/**
	 * Test integer conversion exceptions when parameter value cannot be
	 * interpreted as integer. When value is an array.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testIntegerConversionExceptionArray() {
		$params = new FroodParameters(
			array(
				'laks' => array(32),
			)
		);

		$params->getLaks(FroodParameters::AS_INTEGER);
	}

	/**
	 * Test string conversion exceptions when parameter value cannot be
	 * interpreted as string. When value is null.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testStringConversionExceptionNull() {
		$params = new FroodParameters(
			array(
				'laks' => null,
			)
		);

		$params->getLaks(FroodParameters::AS_STRING);
	}

	/**
	 * Test string conversion exceptions when parameter value cannot be
	 * interpreted as string. When value is an array.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testStringConversionExceptionArray() {
		$params = new FroodParameters(
			array(
				'laks' => array('vildt', 'meget', 32),
			)
		);

		$params->getLaks(FroodParameters::AS_STRING);
	}

	/**
	 * Test float conversion exceptions when parameter value cannot be
	 * interpreted as float. When value is a string.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testFloatConversionExceptionString() {
		$params = new FroodParameters(
			array(
				'laks'  => 'madpakke',
			)
		);

		$params->getLaks(FroodParameters::AS_FLOAT);
	}

	/**
	 * Test float conversion exceptions when parameter value cannot be
	 * interpreted as float. When value is null.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testFloatConversionExceptionNull() {
		$params = new FroodParameters(
			array(
				'laks' => null,
			)
		);

		$params->getLaks(FroodParameters::AS_FLOAT);
	}

	/**
	 * Test float conversion exceptions when parameter value cannot be
	 * interpreted as float. When value is an array.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testFloatConversionExceptionArray() {
		$params = new FroodParameters(
			array(
				'laks' => array('vildt', 'meget', 32),
			)
		);

		$params->getLaks(FroodParameters::AS_FLOAT);
	}

	/**
	 * Test integer array conversion exceptions when parameter value cannot be
	 * interpreted as an integer array. When value is an array of strings.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testIntegerArrayConversionExceptionStringArray() {
		$params = new FroodParameters(
			array(
				'laks' => array('vildt', 'meget', 'laks'),
			)
		);

		$params->getLaks(FroodParameters::AS_INTEGER_ARRAY);
	}

	/**
	 * Test integer array conversion exceptions when parameter value cannot be
	 * interpreted as an integer array. When value is a string.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testIntegerArrayConversionExceptionString() {
		$params = new FroodParameters(
			array(
				'laks' => 'vildt',
			)
		);

		$params->getLaks(FroodParameters::AS_INTEGER_ARRAY);
	}

	/**
	 * Test integer array conversion exceptions when parameter value cannot be
	 * interpreted as an integer array. When value is an integer.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testIntegerArrayConversionExceptionInteger() {
		$params = new FroodParameters(
			array(
				'laks' => 42,
			)
		);

		$params->getLaks(FroodParameters::AS_INTEGER_ARRAY);
	}

	/**
	 * Test integer array conversion exceptions when parameter value cannot be
	 * interpreted as an integer array. When value is an array of strings.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testIntegerArrayConversionExceptionFloat() {
		$params = new FroodParameters(
			array(
				'laks' => 42.0,
			)
		);

		$params->getLaks(FroodParameters::AS_INTEGER_ARRAY);
	}

	/**
	 * Test integer array conversion exceptions when parameter value cannot be
	 * interpreted as an integer array. When value is a boolean.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testIntegerArrayConversionExceptionBoolean() {
		$params = new FroodParameters(
			array(
				'laks' => true,
			)
		);

		$params->getLaks(FroodParameters::AS_INTEGER_ARRAY);
	}

	/**
	 * Test string array conversion exceptions when parameter value cannot be
	 * interpreted as string array. When value is a string.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testStringArrayConversionExceptionString() {
		$params = new FroodParameters(
			array(
				'laks' => 'vildt',
			)
		);

		$params->getLaks(FroodParameters::AS_STRING_ARRAY);
	}

	/**
	 * Test string array conversion exceptions when parameter value cannot be
	 * interpreted as string array. When value is an integer.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testStringArrayConversionExceptionInteger() {
		$params = new FroodParameters(
			array(
				'laks' => 42,
			)
		);

		$params->getLaks(FroodParameters::AS_STRING_ARRAY);
	}

	/**
	 * Test string array conversion exceptions when parameter value cannot be
	 * interpreted as string array. When value is an array of strings.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testStringArrayConversionExceptionFloat() {
		$params = new FroodParameters(
			array(
				'laks' => 42.0,
			)
		);

		$params->getLaks(FroodParameters::AS_STRING_ARRAY);
	}

	/**
	 * Test string array conversion exceptions when parameter value cannot be
	 * interpreted as string array. When value is a boolean.
	 *
	 * @expectedException FroodExceptionCasting
	 *
	 * @return void
	 */
	public function testStringArrayConversionExceptionBoolean() {
		$params = new FroodParameters(
			array(
				'laks' => true,
			)
		);

		$params->getLaks(FroodParameters::AS_STRING_ARRAY);
	}

	/**
	 * Tests that a RuntimeException is thrown when passing bs to the AS_ parameter.
	 *
	 * @expectedException RuntimeException
	 *
	 * @return void
	 */
	public function testConvertingToUnknownTypeException() {
		$params = new FroodParameters(
			array(
				'laks' => array('vildt', 'meget', 32),
			)
		);

		$params->getLaks('as a string, please!');
	}
}
