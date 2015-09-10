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

/**
 * TODO(katrina) documentation
 * Java will sometimes return Math.NaN or Math.Infinity when numbers get to
 * large or too small. This can have undesirable effects. This class provides
 * some basic math functions that may be in danger of returning such a value.
 * This class imposes a very large and small ceiling and floor to keep the
 * numbers within range.
 */
namespace Encog\MathUtil\BoundMath;

use \Encog\MathUtil;

require_once("MathUtil/BoundNumbers.php");


/**
 * Calculate the cos.
 *
 * @param double a
 *            The value passed to the function.
 * @return double The result of the function.
 */
function cos($a) {
	return \Encog\MathUtil\BoundNumbers\bound(\cos($a));
}

/**
 * Calculate the exp.
 *
 * @param double a
 *            The value passed to the function.
 * @return double The result of the function.
 */
function exp($a) {
	return \Encog\MathUtil\BoundNumbers\bound(\exp($a));
}

/**
 * Calculate the log.
 *
 * @param double a
 *            The value passed to the function.
 * @return double The result of the function.
 */
function log($a) {
	return \Encog\MathUtil\BoundNumbers\bound(\log($a));
}

/**
 * Calculate the power of a number.
 *
 * @param double a
 *            The base.
 * @param double b
 *            The exponent.
 * @return double The result of the function.
 */
function pow($a, $b) {
	return \Encog\MathUtil\BoundNumbers\bound(\pow($a, $b));
}

/**
 * Calculate the sin.
 *
 * @param double a
 *            The value passed to the function.
 * @return double The result of the function.
 */
function sin( $a) {
	return \Encog\MathUtil\BoundNumbers\bound(\sin($a));
}

/**
 * Calculate the square root.
 *
 * @param double a
 *            The value passed to the function.
 * @return double The result of the function.
 */
function sqrt($a) {
	return \sqrt($a);
}

/**
 * Calculate TANH, within bounds.
 * @param double d The value to calculate for.
 * @return double The result.
 */
function tanh($d) {
	return \Encog\MathUtil\BoundNumbers\bound(\tanh($d));
}
