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

use \Encog\ML\MLMethod;
use \Encog\ML\TrainingImplementationType;
use \Encog\ML\Data\MLDataSet;
use \Encog\ML\Train\Strategy\Strategy;
use \Encog\Neural\Networks\Training\Propagation\TrainingContinuation;

/**
 * Defines a training method for a machine learning method. Most MLMethod
 * objects need to be trained in some way before they are ready for use.
 */
interface MLTrain {

	/**
	 * @return TrainingImplementationType The training implementation type.
	 */
	 public function getImplementationType();

	/**
	 * @return bool True if training can progress no further.
	*/
	public function isTrainingDone();

	/**
	 * @return MLDataSet The training data to use.
	*/
	public function getTraining();

	/**
	 * @return double Returns the training error. This value is calculated as the
	 *         training data is evaluated by the iteration function. This has
	 *         two important ramifications. First, the value returned by
	 *         getError() is meaningless prior to a call to iteration. Secondly,
	 *         the error is calculated BEFORE training is applied by the call to
	 *         iteration. The timing of the error calculation is done for
	 *         performance reasons.
	*/
	public function getError();

	/**
	 * Should be called once training is complete and no more iterations are
	 * needed. Calling iteration again will simply begin the training again, and
	 * require finishTraining to be called once the new training session is
	 * complete.
	 *
	 * It is particularly important to call finishTraining for multithreaded
	 * training techniques.
	*/
	public function finishTraining();

	/**
	 * Perform one iteration of training.
	 */
	//TODO(katrina) public function iteration();
	
	/**
	 * Perform a number of training iterations.
	 *
	 * @param int count
	 *            The number of iterations to perform.
	*/
	public function iteration($count);

	/**
	 * @return int The current training iteration.
	*/
	public function getIteration();

	/**
	 * @return bool True if the training can be paused, and later continued.
	*/
	public function canContinue();

	/**
	 * Pause the training to continue later.
	 *
	 * @return TrainingContinuation A training continuation object.
	*/
	public function pause();

	/**
	 * Resume training.
	 *
	 * @param TrainingContinuation state
	 *            The training continuation object to use to continue.
	*/
	public function resume(TrainingContinuation $state);

	/**
	 * Training strategies can be added to improve the training results. There
	 * are a number to choose from, and several can be used at once.
	 *
	 * @param Strategy strategy
	 *            The strategy to add.
	*/
	public function addStrategy(Strategy $strategy);

	/**
	 * Get the current best machine learning method from the training.
	 *
	 * @return MLMethod The best machine learningm method.
	*/
	public function getMethod();

	/**
	 * @return Strategy[] The strategies to use.
	*/
	public function getStrategies();

	/**
	 * @param double error
	 *            Set the current error rate. This is usually used by training
	 *            strategies.
	*/
	public function setError($error);

	/**
	 * Set the current training iteration.
	 *
	 * @param int iteration
	 *            Iteration.
	*/
	public function setIteration($iteration);

}