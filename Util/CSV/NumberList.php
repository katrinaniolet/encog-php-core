<?php
/*
 * Encog(tm) Core v3.3 - PHP Version
 * https://github.com/katrinaniolet/encog-php-core
 *
 * http://www.heatonresearch.com/encog/
 * https://github.com/encog/encog-java-core
 *
 * Copyright 2008-2014 Heaton Research, Inc.
 * PHP port by Katrina Niolet <katrina@kf5utn.net>
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

/**
 * Class used to handle lists of numbers.
 */
namespace Encog\Util\CSV\NumberList;

use Encog\Util\CSV\CSVFormat;
/**
 * Get an array of double's from a string of comma separated text.
 * @param CSVFormat $format The format to use.
 * @param string $str The string to parse.
 * @return double[] An array of doubles parsed from the string.
 */
function fromList(CSVFormat $format, $str) {
	// handle empty string
	if( count(trim($str))==0 ) {
		return array();
	}

	// first count the numbers
	$count = 0;
	$tok = strtok($str, $format->getSeparator());
	
	while ($tok != false) {
		++$count;
		$tok = strtok($format->getSeparator());
	}

	// now allocate an object to hold that many numbers
	$result = array_fill(0, $count, 0.0);

	// and finally parse the numbers
	$index = 0;
	$tok2 = strtok($str,$format->getSeparator());
	while ($tok2 !=false ) {
		$num = $tok2;
		$value = $format->parse($num);
		$result[$index++] = $value;
		$tok2 = strtok($format->getSeparator());
	}

	return result;
}

/**
 * Convert an array of doubles to a comma separated list.
 * @param CSVFormat format The format to use.
 * @param string result
 *            This string will have the values appended to it.
 * @param double[] data
 *            The array of doubles to use.
 */
function toListDefaultPrecision(CSVFormat $format, &$result, array $data) {
	toList($format, 20, $result, $data);
}

/**
 * @param CSVFormat $format
 * @param string $str
 * @return int[]
 */
function fromListInt(CSVFormat $format, $str) {
	// handle empty string
	if( count(trim($str))==0 ) {
		return array();
	}

	// first count the numbers
	$count = 0;
	$tok = strtok($str, $format->getSeparator());
	while ($tok != false) {
		++$count;
		$tok = strtok($format->getSeparator());
	}

	// now allocate an object to hold that many numbers
	$result = array_fill(0,$count,0);

	// and finally parse the numbers
	$index = 0;
	$tok2 = strtok($str,$format->getSeparator());
	while ($tok2 !=false ) {
		$num = $tok2;
		$value = $format->parse($num);
		$result[$index++] = $value;
		$tok2 = strtok($format->getSeparator());
	}
	
	return $result;
}

function toList(CSVFormat $format, $precision, &$result, array $data) {
	$result = '';
	for ($i = 0; $i < count($data); ++$i) {
		if ($i != 0) {
			$result .= $format->getSeparator();
		}
		$result .= $format->format($data[$i], $precision);
	}

}

/**
 * @param CSVFormat $format
 * @param string& $result
 * @param int[] $data
 */
function toListInt(CSVFormat $format, &$result,
			array $data) {
	$result = '';
	for ($i = 0; $i < count($data); ++$i) {
		if ($i != 0) {
			$result .= $format->getSeparator();
		}
		$result .= $data[$i];
	}
}
