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
namespace Encog\Neural\Pattern;

use \Encog\Engine\Network\Activation\ActivationFunction;
use \Encog\ML\MLMethod;

/**
 * Patterns are used to create common sorts of neural networks.
 * Information
 * about the structure of the neural network is communicated to the pattern, and
 * then generate is called to produce a neural network of this type.
 *
 * @author jheaton
 *        
 */
interface NeuralNetworkPattern {

	/**
	 * Add the specified hidden layer.
	 *
	 * @param
	 *        	int count
	 *        	The number of neurons in the hidden layer.
	 */
	public function addHiddenLayer( $count );

	/**
	 * Clear the hidden layers so that they can be redefined.
	 */
	public function clear();

	/**
	 * Generate the specified neural network.
	 *
	 * @return MLMethod The resulting neural network.
	 */
	public function generate();

	/**
	 * Set the activation function to be used for all created layers that allow
	 * an activation function to be specified.
	 * Not all patterns allow the
	 * activation function to be specified.
	 *
	 * @param
	 *        	ActivationFunction activation
	 *        	The activation function.
	 */
	public function setActivationFunction( ActivationFunction $activation );

	/**
	 * Set the number of input neurons.
	 *
	 * @param
	 *        	int count
	 *        	The number of input neurons.
	 */
	public function setInputNeurons( $count );

	/**
	 * Set the number of output neurons.
	 *
	 * @param
	 *        	int count
	 *        	The output neuron count.
	 */
	public function setOutputNeurons( $count );
}