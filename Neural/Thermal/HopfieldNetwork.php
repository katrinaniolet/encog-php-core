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
 * See the License for the specific language governing pe/**
 * This interface allows various activation functions to be used with the neural
 * network. Activation functions are applied to the output from each layer of a
 * neural network. Activation functions scale the output into the desired range.
 *
 * Methods are provided both to process the activation function, as well as the
 * derivative of the function. Some training algorithms, particularly back
 * propagation, require that it be possible to take the derivative of the
 * activation function.
 *
 * Not all activation functions support derivatives. If you implement an
 * activation function that is not derivable then an exception should be thrown
 * inside of the derivativeFunction method implementation.
 *
 * Non-derivable activation functions are perfectly valid, they simply cannot be
 * used with every training algorithm.
 */
namespace Encog\Neural\Thermal;

use \Encog\MathUtil\Matrices\BiPolarUtil;
use \Encog\MathUtil\Matrices\Matrix;
use \Encog\MathUtil\Matrices\MatrixMath;
use \Encog\ML\Data\MLData;
use \Encog\ML\Data\Specific\BiPolarNeuralData;
use \Encog\Neural\NeuralNetworkError;
use \Encog\Util\EngineArray;

require_once("MathUtil/Matrices/BiPolarUtil.php");
require_once("MathUtil/Matrices/Matrix.php");
require_once("MathUtil/Matrices/MatrixMath.php");
require_once("ML/Data/MLData.php");
require_once("ML/Data/Specific/BiPolarNeuralData");
require_once("Neural/NeuralNetworkError.php");
require_once("Util/EngineArray.php");

/**
 * Implements a Hopfield network.
 *
 */
class HopfieldNetwork extends ThermalNetwork {

	/**
	 * Construct a Hopfield with the specified neuron count.
	 * @param int neuronCount The neuron count.
	 */
	public function __construct($neuronCount = 0) {
		parent::__construct(neuronCount);
	}

	/**
	 * Train the neural network for the specified pattern. The neural network
	 * can be trained for more than one pattern. To do this simply call the
	 * train method more than once.
	 *
	 * @param MLData pattern
	 *            The pattern to train for.
	 */
	public function addPattern(MLData $pattern) {

		if ($pattern->size() != $this->getNeuronCount()) {
			throw new NeuralNetworkError("Network with " + $this->getNeuronCount()
					+ " neurons, cannot learn a pattern of size "
					+ $pattern->size());
		}

		// Create a row matrix from the input, convert boolean to bipolar
		$m2 = Matrix::createRowMatrix($pattern->getData());
		// Transpose the matrix and multiply by the original input matrix
		$m1 = MatrixMath\transpose($m2);
		$m3 = MatrixMath\multiply($m1, $m2);

		// matrix 3 should be square by now, so create an identity
		// matrix of the same size.
		$identity = MatrixMath\identity($m3->getRows());

		// subtract the identity matrix
		$m4 = MatrixMath\subtract($m3, $identity);

		// now add the calculated matrix, for this pattern, to the
		// existing weight matrix.
		$this->convertHopfieldMatrix($m4);
	}

	/**
	 * Note: for Hopfield networks, you will usually want to call the "run"
	 * method to compute the output.
	 *
	 * This method can be used to copy the input data to the current state. A
	 * single iteration is then run, and the new current state is returned.
	 *
	 * @param MLData input
	 *            The input pattern.
	 * @return MLData The new current state.
	 */
	public function compute(MLData $input) {
		$result = new BiPolarNeuralData($input->size());
		EngineArray\arrayCopy($input->getData(), $this->getCurrentState()->getData());
		$this->run();

		for ($i = 0; $i < $this->getCurrentState()->size(); ++$i) {
			$result->setData($i,
					BiPolarUtil\double2bipolar($this->getCurrentState()->getData($i)));
		}
		EngineArray\arrayCopy($this->getCurrentState()->getData(), $result->getData());
		return $result;
	}

	/**
	 * Update the Hopfield weights after training.
	 *
	 * @param Matrix delta
	 *            The amount to change the weights by.
	 */
	private function convertHopfieldMatrix(Matrix $delta) {
		// add the new weight matrix to what is there already
		for ($row = 0; $row < $delta->getRows(); ++$row) {
			for ($col = 0; $col < $delta->getRows(); ++$col) {
				$this->addWeight($row, $col, $delta->get($row, $col));
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getInputCount() {
		return parent::getNeuronCount();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOutputCount() {
		return parent::getNeuronCount();
	}

	/**
	 * Perform one Hopfield iteration.
	 */
	public function run() {

		for ($toNeuron = 0; $toNeuron < $this->getNeuronCount(); ++$toNeuron) {
			$sum = 0.0;
			for ($fromNeuron = 0; $fromNeuron < $this->getNeuronCount(); ++$fromNeuron) {
				$sum += $this->getCurrentState()->getData($fromNeuron)
				* $this->getWeight($fromNeuron, $toNeuron);
			}
			$this->getCurrentState()->setData($toNeuron, $sum);
		}
	}

	/**
	 * Run the network until it becomes stable and does not change from more
	 * runs.
	 *
	 * @param int max
	 *            The maximum number of cycles to run before giving up.
	 * @return int The number of cycles that were run.
	 */
	public function runUntilStable($max) {
		$done = false;
		$lastStateStr = $this->getCurrentState()->toString();
		$currentStateStr = $this->getCurrentState()->toString();

		$cycle = 0;
		do {
			run();
			++$cycle;

			$lastStateStr = $this->getCurrentState()->toString();

			if (!$currentStateStr->equals($lastStateStr)) {
				if ($cycle > $max) {
					$done = true;
				}
			} else {
				$done = true;
			}

			$currentStateStr = $lastStateStr;

		} while (!$done);

		return $cycle;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateProperties() {
		// nothing needed here
	}

}