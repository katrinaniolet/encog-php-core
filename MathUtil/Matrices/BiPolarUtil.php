<?php
/*
 * Encog(tm) Core v3.3 - PHP Version
 * https://github.com/katrinaniolet/encog-php-core
 *
 * http://www.heatonresearch.com/encog/
 * https://github.com/encog/encog-java-core

 * Copyright 2008-2014 Heaton Research, Inc.
 * PHP port by Katrina Niolet <katria@kf5utn.net>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
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


namespace Encog\MathUtil\Matrices\BiPolarUtil;

/// <summary>
/// This class contains a number of utility methods used to work
/// with bipolar numbers. A bipolar number is another way to represent binary
/// numbers. The value of true is defined to be one, where as false is defined to
/// be negative one.
/// </summary>

/// <summary>
/// Convert binary to bipolar, true is 1 and false is -1.
/// </summary>
/// <param name="b">The binary value.</param>
/// <returns>The bipolar value.</returns>
public static function Bipolar2double( $b )
{
	if( $b > 0 )
	{
		return 1;
	}
	else
	{
		return -1;
	}
}

/// <summary>
/// Convert a boolean array to bipolar, true is 1 and false is -1.
/// </summary>
/// <param name="b">The binary array to convert.</param>
/// <returns></returns>
public static function Bipolar2double( $b )
{
	$result = array(count($b));

	for($i = 0; $i < count($b); ++$i)
	{
		$result[$i] = Bipolar2double($b[$i]);
	}

	return $result;
}

/// <summary>
/// Convert a 2D boolean array to bipolar, true is 1 and false is -1.
/// </summary>
/// <param name="b">The 2D array to convert.</param>
/// <returns>A bipolar array.</returns>
public static function Bipolar2double( $b )
{
	$result = array( count($b) );

	for( $row = 0; $row < count( $b ); ++$row )
	{
		$result[row] = array(count($b[$row]));
		for( $col = 0; $col < count($b[$row]); ++$col )
		{
			$result[$row][$col] = Bipolar2double($b[$row][$col]);
		}
	}

	return $result;
}

/// <summary>
/// Convert biploar to boolean, true is 1 and false is -1.
/// </summary>
/// <param name="d">A bipolar value.</param>
/// <returns>A boolean value.</returns>
public static function Double2bipolar( $d )
{
	if( $d > 0 )
	{
		return true;
	}
	else
	{
		return false;
	}
}

/// <summary>
/// Convert a bipolar array to a boolean array, true is 1 and false is -1.
/// </summary>
/// <param name="d">A bipolar array.</param>
/// <returns>A boolean array.</returns>
public static function Double2bipolar( $d )
{
	$result = array( count($d) );

	for( $i = 0; $i < count( $d ); ++$i )
	{
		$result[$i] = Double2bipolar( $d[$i] );
	}

	return $result;
}

/// <summary>
/// Convert a 2D bipolar array to a boolean array, true is 1 and false is -1.
/// </summary>
/// <param name="d">A 2D bipolar array.</param>
/// <returns>A 2D boolean array.</returns>
public static function Double2bipolar( $d )
{
	$result = array( count( $d ) );

	for( $row = 0; $row < count($d); ++$row )
	{
		$result[$row] = array( $d[$row] );
		for( $col = 0; $col < count($d[$row]); ++$col )
		{
			$result[$row][$col] = Double2bipolar($d[$row][$col]);
		}
	}

	return $result;
}

/// <summary>
/// Normalize a binary number.  Greater than 0 becomes 1, zero and below are false.
/// </summary>
/// <param name="d">A binary number in a double.</param>
/// <returns>A double that will be 0 or 1.</returns>
public static function NormalizeBinary( $d )
{
	if( $d > 0 )
	{
		return 1;
	}
	else
	{
		return 0;
	}
}

/// <summary>
/// Convert a single number from bipolar to binary.
/// </summary>
/// <param name="d">a bipolar number.</param>
/// <returns>A binary number.</returns>
public static function ToBinary( $d )
{
	return ($d + 1)/2.0;
}

/// <summary>
/// Convert a number to bipolar.
/// </summary>
/// <param name="d">A binary number.</param>
/// <returns></returns>
public static function ToBiPolar( $d )
{
	return (2*NormalizeBinary($d)) - 1;
}

/// <summary>
/// Normalize a number and convert to binary.
/// </summary>
/// <param name="d">A bipolar number.</param>
/// <returns>A binary number stored as a double</returns>
public static function ToNormalizedBinary( $d )
{
	return NormalizeBinary(ToBinary(d));
}
