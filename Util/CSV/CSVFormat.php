<?php
/*
 * Encog(tm) Core v3.3 - PHP Version
 * https://github.com/katrinaniolet/encog-php-core
 *
 * http://www.heatonresearch.com/encog/
 * https://github.com/encog/encog-java-core
 *
 * Copyright 2008-2014 Heaton Research, Inc.
 * PHP port by Katrina Niolet <katria@kf5utn.net>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * For more information on Heaton Research copyrights, licenses
 * and trademarks visit:
 * http://www.heatonresearch.com/copyright
 */

namespace Encog\Util\CSV;

/**
 * Specifies a CSV format. This allows you to determine if a decimal point or
 * decimal comma is uses. It also specifies the character that should be used to
 * separate numbers.
 *
 */
class CSVFormat extends \Threaded {

	/**
	 * Use a decimal point, and a comma to separate numbers.
	 * @var CSVFormat
	 */
	public static $DECIMAL_POINT = null;

	/**
	 * Use a decimal comma, and a semicolon to separate numbers.
	*/
	public static $DECIMAL_COMMA = null;

	/**
	 * Decimal point is typically used in English speaking counties.
	*/
	public static $ENGLISH = null;

	/**
	 * EG files, internally use a decimal point and comma separator.
	 */
	public static $EG_FORMAT = null;

	/**
	 * Get the decimal character currently in use by the computer's default
	 * location.
	 *
	 * @return char The decimal character used.
	 */
	public static function getDecimalCharacter() {
		return localeconv()['decimal_point'];
	}

	/**
	 * The decimal character.
	 * @var char
	 */
	private $decimal;

	/**
	 * The separator character.
	 * @var char
	 */
	private $separator;

	/**
	 * The number formatter to use for this format.
	 * @var NumberFormatter
	 */
	private $numberFormatter;

	/**
	 * Construct a CSV format with he specified decimal and separator
	 * characters.
	 *
	 * @param decimal
	 *            The decimal character.
	 * @param separator
	 *            The separator character.
	 */
	public function __constructor($decimal = '.', $separator = ',') {
		
		$this->DECIMAL_POINT = new CSVFormat('.', ',');
		$this->DECIMAL_COMMA = new CSVFormat(',', ';');
		$this->ENGLISH = $this->DECIMAL_POINT;
		$this->EG_FORMAT = $this->DECIMAL_POINT;
		
		$this->decimal = $decimal;
		$this->separator = $separator;

		if ($decimal == '.') {
			$this->numberFormatter = NumberFormatter::create('en-US',\NumberFormatter::DECIMAL);
		} else if ($decimal == ',') {
			$this->numberFormatter = NumberFormatter::create('fr-FR',\NumberFormatter::DECIMAL);
		} else {
			$this->numberFormatter = NumberFormatter::create(Locale::getDefault(), \NumberFormatter::DECIMAL);
		}
	}

	/**
	 * Format the specified number to a string with the specified number of
	 * fractional digits.
	 *
	 * @param double d
	 *            The number to format.
	 * @param int digits
	 *            The number of fractional digits.
	 * @return string The number formatted as a string.
	 */
	public function format($d, $digits) {
		$this->synchronized(function($thread){
			if( is_infinite($d) || is_nan($d) )
				return "0";
			$this->numberFormatter->setAttribute(\NumberFormatter::GROUPING_USED,false);
			$this->numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $digits);
			
			return $this->numberFormatter->format($d);
				
		});
	}

	/**
	 * @return char The decimal character.
	 */
	public function getDecimal() {
		return $this->decimal;
	}

	/**
	 * @return NumberFormat The number formatter.
	 */
	public function getNumberFormatter() {
		return $this->numberFormatter;
	}

	/**
	 * @return char The separator character.
	 */
	public function getSeparator() {
		return $this->separator;
	}

	/**
	 * Determine if the string can be parsed.
	 * @param string str The string to compare.
	 * @return bool True, if the string can be parsed.
	 */
	public function isValid($str) {
		try {
			if( $str === "?") {
				return false;
			} if( strtolower($str) === strtolower("NaN") ) {
				return false;
			} else {
				 $this->numberFormatter->parse(doubleval(trim($str)));
				return true;
			}
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Parse the specified string to a double.
	 *
	 * @param string str
	 *            The string to parse.
	 * @return double The parsed number.
	 */
	public function parse($str) {
		$this->synchronized(function($thread){
			try {
				if( $str === "?") {
					return \NAN;
				} if( strtolower($str) === strtolower("NaN") ) {
					return \NAN;
				} else {
					return doubleval($this->numberFormatter->parse(doubleval(trim($str))));
				}
			} catch (Exception $e) {
				throw new CSVError("Error:" + $e->getMessage() + " on [" + $str + "], decimal:" + $this->decimal + ",sep: " + $this->separator);
			}				
		});
	}
}