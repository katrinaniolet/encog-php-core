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
namespace Encog\ML\Train;

use \Encog\ML\TrainingImplementationType;
use \Encog\ML\Data\MLDataSet;
use \Encog\ML\Train\Strategy\Strategy;
use \Encog\ML\Train\Strategy\End\EndTrainingStrategy;

require_once("ML\Train\MLTrain.php");

/**
 * An abstract class that implements basic training for most training
 * algorithms.
 * Specifically training strategies can be added to enhance the
 * training.
 */
abstract class BasicTraining implements MLTrain {
	
	/**
	 * The training strategies to use.
	 * 
	 * @var Strategy[] $strategies
	 */
	private $strategies = array();
	
	/**
	 * The training data.
	 * 
	 * @var MLDataSet training
	 */
	private $training = null;
	
	/**
	 * The current error rate.
	 * 
	 * @var double error
	 */
	private $error = 0.0;
	
	/**
	 * The current iteration.
	 * 
	 * @var int iteration
	 */
	private $iteration = 0;
	private $implementationType = null;

	//TODO(katrina) enum type TrainingImplementationType 
	public function __construct( $implementationType ) {
		$this->implementationType = $implementationType;
	}

	/**
	 * Training strategies can be added to improve the training results.
	 * There
	 * are a number to choose from, and several can be used at once.
	 *
	 * @param
	 *        	Strategy strategy
	 *        	The strategy to add.
	 */
	public function addStrategy( Strategy $strategy ) {
		$strategy->init( $this );
		$this->strategies->add( $strategy );
	}

	/**
	 * Should be called after training has completed and the iteration method
	 * will not be called any further.
	 */
	public function finishTraining() {}

	/**
	 * {@inheritDoc}
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 *
	 * @return int the iteration
	 */
	public function getIteration() {
		return $this->iteration;
	}

	/**
	 *
	 * @return Strategy[] The strategies to use.
	 */
	public function getStrategies() {
		return $this->strategies;
	}

	/**
	 *
	 * @return MLDataSet The training data to use.
	 */
	public function getTraining() {
		return $this->training;
	}

	/**
	 *
	 * @return bool True if training can progress no further.
	 */
	public function isTrainingDone() {
		foreach( $this->strategies as $strategy ) {
			if( $strategy instanceof EndTrainingStrategy ) {
				$end = $strategy;
				if( $end->shouldStop() ) {
					return true;
				}
			}
		}
		
		return false;
	}

	/**
	 * Perform the specified number of training iterations.
	 * This is a basic
	 * implementation that just calls iteration the specified number of times.
	 * However, some training methods, particularly with the GPU, benefit
	 * greatly by calling with higher numbers than 1.
	 *
	 * @param
	 *        	int count
	 *        	The number of training iterations.
	 */
	public function iteration( $count ) {
		for( $i = 0; $i < $count; ++$i ) {
			$this->iteration();
		}
	}

	/**
	 * Call the strategies after an iteration.
	 */
	public function postIteration() {
		foreach( $this->strategies as $strategy ) {
			$strategy->postIteration();
		}
	}

	/**
	 * Call the strategies before an iteration.
	 */
	public function preIteration() {
		++$this->iteration;
		
		foreach( $this->strategies as $strategy ) {
			$strategy->preIteration();
		}
	}

	/**
	 *
	 * @param
	 *        	double error
	 *        	Set the current error rate. This is usually used by training
	 *        	strategies.
	 */
	public function setError( $error ) {
		$this->error = $error;
	}

	/**
	 *
	 * @param
	 *        	int iteration
	 *        	the iteration to set
	 */
	public function setIteration( $iteration ) {
		$this->iteration = $iteration;
	}

	/**
	 * Set the training object that this strategy is working with.
	 *
	 * @param
	 *        	MLDataSet training
	 *        	The training object.
	 */
	public function setTraining( MLDataSet $training ) {
		$this->training = $training;
	}

	public function getImplementationType() {
		return $this->implementationType;
	}
}