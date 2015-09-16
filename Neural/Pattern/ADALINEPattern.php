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
use \Encog\Engine\Network\Activation\ActivationLinear;
use \Encog\MathUtil\Randomize\RangeRandomizer;
use \Encog\ML\MLMethod;
use \Encog\Neural\Networks\BasicNetwork;
use \Encog\Neural\Networks\Layers\BasicLayer;
use \Encog\Neural\Networks\Layers\Layer;

require_once ("Neural\Pattern\NeuralNetworkPattern.php");
require_once ("Neural\Networks\BasicNetwork.php");
require_once ("MathUtil\Randomize\RangeRandomizer.php");

/**
 * Construct an ADALINE neural network.
 */
class ADALINEPattern implements NeuralNetworkPattern {
	
	/**
	 * The number of neurons in the input layer.
	 *
	 * @var int
	 */
	private $inputNeurons = 0;
	
	/**
	 * The number of neurons in the output layer.
	 *
	 * @var int
	 */
	private $outputNeurons = 0;

	/**
	 * Not used, the ADALINE has no hidden layers, this will throw an error.
	 *
	 * @param
	 *        	int count
	 *        	The neuron count.
	 */
	public function addHiddenLayer( $count ) {
		throw new PatternError( "An ADALINE network has no hidden layers." );
	}

	/**
	 * Clear out any parameters.
	 */
	public function clear() {
		$this->inputNeurons = 0;
		$this->outputNeurons = 0;
	}

	/**
	 * Generate the network.
	 *
	 * @return MLMethod The generated network.
	 */
	public function generate() {
		$network = new BasicNetwork();
		
		$inputLayer = new BasicLayer( new ActivationLinear(), true, $this->inputNeurons );
		$outputLayer = new BasicLayer( new ActivationLinear(), false, $this->outputNeurons );
		
		$network->addLayer( $inputLayer );
		$network->addLayer( $outputLayer );
		$network->getStructure()->finalizeStructure();
		
		(new RangeRandomizer( - 0.5, 0.5 ))->randomizeMLMethod( $network );
		
		return $network;
	}

	/**
	 * Not used, ADALINE does not use custom activation functions.
	 *
	 * @param
	 *        	ActivationFunction activation
	 *        	Not used.
	 */
	public function setActivationFunction( ActivationFunction $activation ) {
		throw new PatternError( "A ADALINE network can't specify a custom activation function." );
	}

	/**
	 * Set the input neurons.
	 *
	 * @param
	 *        	int count
	 *        	The number of neurons in the input layer.
	 */
	public function setInputNeurons( $count ) {
		$this->inputNeurons = $count;
	}

	/**
	 * Set the output neurons.
	 *
	 * @param
	 *        	int count
	 *        	The number of neurons in the output layer.
	 */
	public function setOutputNeurons( $count ) {
		$this->outputNeurons = $count;
	}
}