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
namespace Encog\Neural\Networks\Training\Simple;

use \Encog\MathUtil\Error\ErrorCalculation;
use \Encog\ML\MLMethod;
use \Encog\ML\TrainingImplementationType;
use \Encog\ML\Data\MLData;
use \Encog\ML\Data\MLDataPair;
use \Encog\ML\Data\MLDataSet;
use \Encog\ML\Train\BasicTraining;
use \Encog\Neural\NeuralNetworkError;
use \Encog\Neural\Networks\BasicNetwork;
use \Encog\Neural\Networks\Training\LearningRate;
use \Encog\Neural\Networks\Training\Propagation\TrainingContinuation;

/**
 * Train an ADALINE neural network.
 */
class TrainAdaline extends BasicTraining implements LearningRate {

	/**
	 * The network to train.
	 * @var BasicNetwork network
	 */
	private $network = null;

	/**
	 * The training data to use.
	 * @var MLDataSet training
	 */
	private $training = null;

	/**
	 * The learning rate.
	 * @var double learningRate
	 */
	private $learningRate = 0.0;

	/**
	 * Construct an ADALINE trainer.
	 *
	 * @param BasicNetwork network
	 *            The network to train.
	 * @param MLDataSet training
	 *            The training data.
	 * @param double learningRate
	 *            The learning rate.
	 */
	public function __construct(BasicNetwork $network, MLDataSet $training,
			$learningRate) {
		parent::__construct(TrainingImplementationType\Iterative);
		if($network->getLayerCount() > 2) {
			throw new NeuralNetworkError(
					"An ADALINE network only has two layers.");
		}
		$this->network = $network;

		$this->training = $training;
		$this->learningRate = $learningRate;
	}

	/**
	 * {@inheritDoc}
	 */
	public function canContinue() {
		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLearningRate() {
		return $this->learningRate;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMethod() {
		return $this->network;
	}

	/**
	 * {@inheritDoc}
	 */
	public function iteration() {

		$errorCalculation = new ErrorCalculation();

		foreach ($this->training as $pair) {
			// calculate the error
			$output = $this->network->compute($pair->getInput());

			for ($currentAdaline = 0; $currentAdaline < $output->size(); ++$currentAdaline) {
				$diff = $pair->getIdeal()->getData($currentAdaline)
				- $output->getData($currentAdaline);

				// weights
				for ($i = 0; $i <= $this->network->getInputCount(); ++$i) {
					$input = 0.0;

					if ($i == $this->network->getInputCount()) {
						$input = 1.0;
					} else {
						$input = $pair->getInput()->getData($i);
					}

					$this->network->addWeight(0, $i, $currentAdaline,
							$this->learningRate * $diff * $input);
				}
			}

			$errorCalculation->updateError($output->getData(), $pair->getIdeal()
					->getData(),$pair->getSignificance());
		}

		// set the global error
		$this->setError($errorCalculation->calculate());
	}

	/**
	 * {@inheritDoc}
	 */
	public function pause() {
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function resume(TrainingContinuation $state) {

	}

	/**
	 * Set the learning rate.
	 *
	 * @param double rate
	 *            The new learning rate.
	 */
	public function setLearningRate($rate) {
		$this->learningRate = $rate;
	}

}