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
 * Provides a foundation for most random number generation.  This allows the nextDouble to generate
 * the other types.
 */
abstract class AbstractGenerateRandom implements GenerateRandom {

	/**
	 * {@inheritDoc}
	 */
	public function nextIntRange($low, $high) {
		return ($low + intval($this->nextDouble() * (($high - $low))));
	}

	/**
	 * {@inheritDoc}
	 */
	public function nextDoubleCapped($high) {
		return $this->nextDoubleRange(0, $high);
	}

	/**
	 * {@inheritDoc}
	 */
	public function nextDoubleRange($low, $high) {
		return ($low + ($this->nextDouble() * (($high - $low))));
	}

	/**
	 * {@inheritDoc}
	 */
	public function nextIntCapped($range) {
		return $this->nextIntRange(0, range);
	}
}