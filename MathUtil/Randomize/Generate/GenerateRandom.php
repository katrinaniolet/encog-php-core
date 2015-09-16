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
namespace Encog\MathUtil\Randomize\Generate;

/**
 * Interface that defines how random numbers are generated.  Provides the means to generate both uniform and normal
 * (gaussian) distributed random numbers.
 */
interface GenerateRandom {

	/**
	 * @return double The next normally distributed random number.
	 */
	public function nextGaussian();

	/**
	 * @return bool The next random boolean.
	*/
	public function nextBoolean();

	/**
	 * @return long The next random long.
	*/
	public function nextLong();

	/**
	 * @return float The next random floating point.
	*/
	public function nextFloat();

	/**
	 * @return double The next random double.
	*/
	public function nextDouble();

	/**
	 * The next random double up to a non-inclusive range.
	 *
	 * @param double high The highest desired value.
	 * @return double The result.
	*/
	public function nextDoubleCapped($high);

	/**
	 * The next double between low (inclusive) and high (exclusive).
	 *
	 * @param double low  The inclusive low value.
	 * @param double high The exclusive high value.
	 * @return double The result.
	*/
	public function nextDoubleRange($low, $high);

	/**
	 * @return int The next random integer.
	*/
	public function nextInt();

	/**
	 * The next random int up to a non-inclusive range.
	 *
	 * @param int high The highest desired value.
	 * @return int The result.
	*/
	public function nextIntCapped($high);

	/**
	 * The next int between low (inclusive) and high (exclusive).
	 *
	 * @param int low  The inclusive low value.
	 * @param int high The exclusive high value.
	 * @return int The result.
	*/
	public function nextIntRange($low, $high);
}