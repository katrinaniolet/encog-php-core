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
use \Encog\Neural\Thermal\HopfieldNetwork;

require_once ("Neural/Pattern/NeuralNetworkPattern.php");

/**
 * Create a Hopfield pattern.
 * A Hopfield neural network has a single layer that
 * functions both as the input and output layers. There are no hidden layers.
 * Hopfield networks are used for basic pattern recognition. When a Hopfield
 * network recognizes a pattern, it "echos" that pattern on the output.
 *
 * @author jheaton
 *        
 */
class HopfieldPattern implements NeuralNetworkPattern {
	
	/**
	 * How many neurons in the Hopfield network.
	 * Default to -1, which is
	 * invalid. Therefore this value must be set.
	 * 
	 * @var int
	 */
	private $neuronCount = - 1;

	/**
	 * Add a hidden layer.
	 * This will throw an error, because the Hopfield neural
	 * network has no hidden layers.
	 *
	 * @param
	 *        	count
	 *        	The number of neurons.
	 */
	public function addHiddenLayer( $count ) {
		throw new PatternError( "A Hopfield network has no hidden layers." );
	}

	/**
	 * Nothing to clear.
	 */
	public function clear() {}

	/**
	 * Generate the Hopfield neural network.
	 *
	 * @return MLMethod The generated network.
	 */
	public function generate() {
		$logic = new HopfieldNetwork( $this->neuronCount );
		return $logic;
	}

	/**
	 * Set the activation function to use.
	 * This function will throw an error,
	 * because the Hopfield network must use the BiPolar activation function.
	 *
	 * @param
	 *        	activation
	 *        	The activation function to use.
	 */
	public function setActivationFunction( ActivationFunction $activation ) {
		throw new PatternError( "A Hopfield network will use the BiPolar activation " + "function, no activation function needs to be specified." );
	}

	/**
	 * Set the number of input neurons, this must match the output neurons.
	 *
	 * @param
	 *        	int count
	 *        	The number of neurons.
	 */
	public function setInputNeurons( $count ) {
		$this->neuronCount = $count;
	}

	/**
	 * Set the number of output neurons, should not be used with a hopfield
	 * neural network, because the number of input neurons defines the number of
	 * output neurons.
	 *
	 * @param
	 *        	int count
	 *        	The number of neurons.
	 */
	public function setOutputNeurons( $count ) {
		throw new PatternError( "A Hopfield network has a single layer, so no need " + "to specify the output count." );
	}
}