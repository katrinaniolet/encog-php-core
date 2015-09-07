<?php
/*
 * Encog(tm) Core v3.3 - PHP Version
 * https://github.com/katrinaniolet/encog-php-core
 *
 * http://www.heatonresearch.com/encog/
 * https://github.com/encog/encog-java-core
 *
 * Copyright 2008-2014 Heaton Research, Inc.
 * PHP port by Katrina Niolet <katria@kf5utn.net>
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

/**
 * Used to validate if training is valid.
 */
namespace Encog\Util\EncogValidate;

use \Encog\ML\Data\MLDataSet;
use \Encog\Neural\NeuralNetworkError;
use \Encog\Neural\Networks\ContainsFlat;

require_once("ML\Data\MLDataSet.php");
require_once("Neural\NeuralNetworkError.php");
require_once("Neural\Networks\ContainsFlat.php");


/**
 * Validate a network for training.
 *
 * @param ContainsFlat $network The network to validate.
 * @param MLDataSet $training The training set to validate.
 */
function validateNetworkForTraining(ContainsFlat $network, MLDataSet $training) {

	$inputCount = $network->getFlat()->getInputCount();
	$outputCount = $network->getFlat()->getOutputCount();

	if ($inputCount != $training->getInputSize()) {
		throw new NeuralNetworkError("The input layer size of "
				+ $inputCount
				+ " must match the training input size of "
				+ $training->getInputSize() + ".");
	}

	if (($training->getIdealSize() > 0)
			&& ($outputCount != $training->getIdealSize())) {
				throw new NeuralNetworkError("The output layer size of "
						+ $outputCount
						+ " must match the training input size of "
						+ $training->getIdealSize() + ".");
			}
}
