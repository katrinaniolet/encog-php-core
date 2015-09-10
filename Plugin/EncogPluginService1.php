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
namespace Encog\Plugin;

use \Encog\Engine\Network\Activation\ActivationFunction;
use \Encog\ML\MLMethod;
use \Encog\ML\Data\MLDataSet;
use \Encog\ML\Train\MLTrain;

require_once("Engine/Network/Activation/ActivationFunction.php");
require_once("ML/MLMethod.php");
require_once("ML/Data/MLDataSet.php");

/**
 * A service plugin provides services, such as the creation of activation
 * functions, machine learning methods and training methods.
 *
 */
interface EncogPluginService1 extends EncogPluginBase {

	/**
	 * Create an activation function.
	 * @param string name The name of the activation function.
	 * @return ActivationFunctoin The newly created activation function.
	 */
	public function createActivationFunction($name);

	/**
	 * Create a new machine learning method.
	 * @param string methodType The method to create.
	 * @param string architecture The architecture string.
	 * @param int input The input count.
	 * @param int output The output count.
	 * @return MLMethod The newly created machine learning method.
	*/
	public function createMethod($methodType,
			$architecture,
			$input, $output);

	/**
	 * Create a trainer.
	 * @param MLMethod method The method to train.
	 * @param MLDataSet training The training data.
	 * @param string type Type type of trainer.
	 * @param string args The training args.
	 * @return MLTrain The new training method.
	*/
	public function createTraining(MLMethod $method,
			MLDataSet $training,
			$type, $args);

}