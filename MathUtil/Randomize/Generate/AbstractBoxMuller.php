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

require_once("MathUtil/Randomize/Generate/AbstractGenerateRandom.php");

/**
 * Provides the ability for subclasses to generate normally distributed random numbers.
 */
abstract class AbstractBoxMuller extends AbstractGenerateRandom {

	/**
	 * The y2 value.
	 * @var double y2
	 */
	private $y2 = 0.0;

	/**
	 * Should we use the last value.
	 * @var bool useLast
	 */
	private $useLast = false;

	/**
	 * The mean.
	 * @var double MU
	 */
	const MU = 0.0;

	/**
	 * The standard deviation.
	 */
	const SIGMA = 1;


	/**
	 * {@inheritDoc}
	 */
	public function nextGaussian() {
		$x1 = 0.0;
		$x2 = 0.0;
		$w = 0.0;
		$y1 = 0.0;

		// use value from previous call
		if ($this->useLast) {
			$y1 = $this->y2;
			$this->useLast = false;
		} else {
			do {
				$x1 = 2.0 * $this->nextDouble() - 1.0;
				$x2 = 2.0 * $this->nextDouble() - 1.0;
				$w = $x1 * $x1 + $x2 * $x2;
			} while ($w >= 1.0);

			$w = \sqrt((-2.0 * \log($w)) / $w);
			$y1 = $x1 * $w;
			$this->y2 = $x2 * $w;
			$this->useLast = true;
		}

		return (AbstractBoxMuller::MU + $y1 * AbstractBoxMuller::SIGMA);
	}
}