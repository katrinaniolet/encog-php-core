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
 * This class contains a number of utility methods used to work
 * with bipolar numbers.
 * A bipolar number is another way to represent binary
 * numbers. The value of true is defined to be one, where as false is defined to
 * be negative one.
 */
namespace Encog\MathUtil\Matrices\BiPolarUtil;

/**
 * Convert boolean to bipolar, true is 1 and false is -1.
 *
 * The input variable may be either a single boolean value or a
 * single- or two-dimensional array of boolean values(s).
 *
 * @param mixed $b
 *        	The binary value.
 * @return The bipolar value or array of values (depending on the input)
 */
function bipolar2double( $b ) {
	if( is_bool( $b ) ) {
		if( $b )
			return 1;
		else
			return - 1;
	}
	else if( is_array( $b ) ) {
		if( count( $b ) > 0 && is_array( $b[0] ) ) {
			$result = array();
			for( $row = 0; $row < count( $b ); ++$row ) {
				$result[$row] = array();
				for( $col = 0; $col < count( $b[$row] ); ++$col ) {
					$result[$row][$col] = bipolar2double( $b[$row][$col] );
				}
			}
			return $result;
		}
		else {
			$result = array();
			for( $i = 0; $i < count( $b ); ++$i ) {
				$result[$i] = bipolar2double( $b[$i] );
			}
			return $result;
		}
	}
}

/**
 * Convert bipolar to boolean, true is 1 and false is -1.
 *
 * The input variable may be either a single bipolar value or a
 * single- or two-dimensional array of bipolar values.
 *
 * @param mixed $b
 *        	The bipolar value(s).
 * @return The boolean value or array of values (depending on the input)
 */
function Double2bipolar( $d ) {
	if( is_numeric( $d ) ) {
		if( $d > 0 ) {
			return true;
		}
		else {
			return false;
		}
	}
	if( is_array( $d ) ) {
		if( count( $d ) > 0 && is_array( $d[0] ) ) {
			$result = array( 
					count( $d ) );
			
			for( $row = 0; $row < count( $d ); ++$row ) {
				$result[$row] = array( 
						$d[$row] );
				for( $col = 0; $col < count( $d[$row] ); ++$col ) {
					$result[$row][$col] = Double2bipolar( $d[$row][$col] );
				}
			}
			
			return $result;
		}
		else {
			$result = array( 
					count( $d ) );
			
			for( $i = 0; $i < count( $d ); ++$i ) {
				$result[$i] = double2bipolar( $d[$i] );
			}
			
			return $result;
		}
	}
}

/**
 * Normalize a binary number.
 * Greater than 0 becomes 1, zero and below are false.
 *
 * @param $d number
 *        	A binary number in a double.
 * @return A double that will be 0 or 1.
 */
function NormalizeBinary( $d ) {
	if( $d > 0 ) {
		return 1;
	}
	else {
		return 0;
	}
}

/**
 * Convert a single number from bipolar to binary.
 *
 * @param
 *        	number a bipolar number.
 * @return A binary number.
 */
function ToBinary( $d ) {
	return ($d + 1) / 2.0;
}

/**
 * Convert a number to bipolar.
 *
 * @param $d A
 *        	binary number.
 * @return A bipolar number.
 */
function ToBiPolar( $d ) {
	return (2 * normalizeBinary( $d )) - 1;
}

/**
 * Normalize a number and convert to binary.
 *
 * @param $d number
 *        	A bipolar number.
 * @return A binary number stored as a double
 */
function ToNormalizedBinary( $d ) {
	return normalizeBinary( toBinary( $d ) );
}
