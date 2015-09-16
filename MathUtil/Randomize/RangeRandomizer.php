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
namespace Encog\MathUtil\Randomize;

require_once("MathUtil/Randomize/BasicRandomizer.php");

/**
 * A randomizer that will create random weight and bias values that are between
 * a specified range.
 */
class RangeRandomizer extends BasicRandomizer {

	/**
	 * Returns a random number in the range between min and max.
	 * @param int min The minimum desired random number.
	 * @param int max The maximum desired random number.
	 * @return int The random number.
	 */
	public static function randomInt($min, $max) {
		return intval(RangeRandomizer::randomizeRange($min, $max + 1));
	}

	/**
	 * Generate a random number in the specified range.
	 *
	 * @param double min
	 *            The minimum value.
	 * @param double max
	 *            The maximum value.
	 * @return double A random number.
	 */
	public static function randomizeRange($min, $max) {
		$range = $max - $min;
		return ($range * \random()) + $min;
	}

	public static function randomizeRandomRange($r, $min, $max) {
		$range = $max - $min;
		return ($range * $r->nextDouble()) + $min;
	}

	/**
	 * The minimum value for the random range.
	 * @var double min
	 */
	private $min = 0.0;

	/**
	 * The maximum value for the random range.
	 * @var double max
	 */
	private $max = 0.0;

	/**
	 * Construct a range randomizer.
	 *
	 * @param double min
	 *            The minimum random value.
	 * @param double max
	 *            The maximum random value.
	 */
	public function __construct($min, $max) {
		$this->max = $max;
		$this->min = $min;
	}

	/**
	 * Generate a random number based on the range specified in the constructor.
	 *
	 * @param double d
	 *            The range randomizer ignores this value.
	 * @return double The random number.
	 */
	public function randomizeDouble($d) {
		return $this->nextDoubleRange($this->min, $this->max);
	}

	/**
	 * @return double the min
	 */
	public function getMin() {
		return $this->min;
	}

	/**
	 * @return double the max
	 */
	public function getMax() {
		return $this->max;
	}

}