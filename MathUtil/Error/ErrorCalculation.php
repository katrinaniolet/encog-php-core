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
namespace Encog\MathUtil\Error;

require_once("MathUtil/Error/ErrorCalculationMode.php");

/**
 * Calculate the error of a neural network. Encog currently supports three error
 * calculation modes. See ErrorCalculationMode for more info.
 */
class ErrorCalculation {

	/**
	 * The current error calculation mode.
	 */
	private static $mode = ErrorCalculationMode::MSE;

	/**
	 * get the error calculation mode, this is static and therefore global to
	 * all Enocg training. If a particular training method only supports a
	 * particular error calculation method, it may override this value. It will
	 * not change the value set here, rather the training will occur with its
	 * preferred training method. Currently the only training method that does
	 * this is Levenberg Marquardt (LMA).
	 *
	 * The default error mode for Encog is MSE.
	 *
	 * @return ErrorCalculationMode The current mode.
	 */
	public static function getMode() {
		return ErrorCalculation::$mode;
	}

	/**
	 * Set the error calculation mode, this is static and therefore global to
	 * all Enocg training. If a particular training method only supports a
	 * particular error calculation method, it may override this value. It will
	 * not change the value set here, rather the training will occur with its
	 * preferred training method. Currently the only training method that does
	 * this is Levenberg Marquardt (LMA).
	 *
	 * @param ErrorCalculationMode theMode
	 *            The new mode.
	 */
	//TODO(katrina) enum ErrorCalculationMode
	public static function setMode($theMode) {
		ErrorCalculation::$mode = $theMode;
	}

	/**
	 * The overall error.
	 */
	private $globalError;

	/**
	 * The size of a set.
	 */
	private $setSize;

	/**
	 * Returns the root mean square error for a complete training set.
	 *
	 * @return double The current error for the neural network.
	 */
	public function calculate() {
		if ($this->setSize == 0) {
			return 0;
		}

		switch (ErrorCalculation::getMode()) {
			case RMS:
				return $this->calculateRMS();
			case MSE:
				return $this->calculateMSE();
			case ESS:
				return $this->calculateESS();
			default:
				return $this->calculateMSE();
		}

	}

	/**
	 * Calculate the error with MSE.
	 *
	 * @return double The current error for the neural network.
	 */
	public function calculateMSE() {
		if ($this->setSize == 0) {
			return 0;
		}
		$err = $this->globalError / $this->setSize;
		return $err;

	}

	/**
	 * Calculate the error with SSE.
	 *
	 * @return double The current error for the neural network.
	 */
	public function calculateESS() {
		if ($this->setSize == 0) {
			return 0;
		}
		$err = $this->globalError / 2;
		return $err;

	}

	/**
	 * Calculate the error with RMS.
	 *
	 * @return double The current error for the neural network.
	 */
	public function calculateRMS() {
		if ($this->setSize == 0) {
			return 0;
		}
		$err = \sqrt($this->globalError / $this->setSize);
		return $err;
	}

	/**
	 * Reset the error accumulation to zero.
	 */
	public function reset() {
		$this->globalError = 0;
		$this->setSize = 0;
	}

	/**
	 * Called to update for each number that should be checked.
	 *
	 * @param double[] actual
	 *            The actual number.
	 * @param double[] ideal
	 *            The ideal number.
	 */
	/**
	 * Update the error with single values.
	 *
	 * @param double actual
	 *            The actual value.
	 * @param double ideal
	 *            The ideal value.
	 */
	//TODO(katrina) documentation, merged methods
	public function updateError(array $actual, array $ideal, $significance = null) {
		if(is_null($significance)) {
			$delta = $ideal - $actual;
			$this->globalError += $delta * $delta;
			++$this->setSize;
		}
		else {
			for ($i = 0; $i < count($actual); ++$i) {
				$delta = ($ideal[$i] - $actual[$i]) * $significance;
		
				$this->globalError += $delta * $delta;
			}
		
			$this->setSize += count($idea);
		}
	}
}