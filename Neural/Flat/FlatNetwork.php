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
namespace Encog\Neural\Flat;

use \Encog\Encog;
use \Encog\EncogError;
use \Encog\Engine\Network\Activation\ActivationFunction;
use \Encog\Engine\Network\Activation\ActivationLinear;
use \Encog\Engine\Network\Activation\ActivationSigmoid;
use \Encog\Engine\Network\Activation\ActivationTANH;
use \Encog\MathUtil\Error\ErrorCalculation;
use \Encog\ML\Data\MLDataPair;
use \Encog\ML\Data\MLDataSet;
use \Encog\ML\Data\Basic\BasicMLDataPair;
use \Encog\Neural\Networks\BasicNetwork;
use \Encog\Util\EngineArray;

/**
 * Implements a flat (vector based) neural network in the Encog Engine.
 * This is
 * meant to be a very highly efficient feedforward, or simple recurrent, neural
 * network. It uses a minimum of objects and is designed with one principal in
 * mind-- SPEED. Readability, code reuse, object oriented programming are all
 * secondary in consideration.
 *
 * Vector based neural networks are also very good for GPU processing. The flat
 * network classes will make use of the GPU if you have enabled GPU processing.
 * See the Encog class for more info.
 */
class FlatNetwork {
	
	/**
	 * The default bias activation.
	 */
	const DEFAULT_BIAS_ACTIVATION = 1.0;
	
	/**
	 * The value that indicates that there is no bias activation.
	 */
	const NO_BIAS_ACTIVATION = 0.0;
	
	/**
	 * The number of input neurons in this network.
	 *
	 * @var int intputCount
	 */
	private $inputCount = 0;
	
	/**
	 * The number of neurons in each of the layers.
	 *
	 * @var int[] layerCounts
	 */
	private $layerCounts = array();
	
	/**
	 * The number of context neurons in each layer.
	 * These context neurons will
	 * feed the next layer.
	 *
	 * @var int[] layerContextCount
	 */
	private $layerContextCount = array();
	
	/**
	 * The number of neurons in each layer that are actually fed by neurons in
	 * the previous layer.
	 * Bias neurons, as well as context neurons, are not fed
	 * from the previous layer.
	 *
	 * @var int[] layerFeedCounts
	 */
	private $layerFeedCounts = array();
	
	/**
	 * An index to where each layer begins (based on the number of neurons in
	 * each layer).
	 *
	 * @var int[] layerIndex
	 */
	private $layerIndex = array();
	
	/**
	 * The outputs from each of the neurons.
	 *
	 * @var double[] layerinput
	 */
	private $layerOutput = array();
	
	/**
	 * The sum of the layer, before the activation function is applied, producing the layerOutput.
	 *
	 * @var double[] layerSums
	 */
	private $layerSums = array();
	
	/**
	 * The number of output neurons in this network.
	 *
	 * @var int outputCount
	 */
	private $outputCount = 0;
	
	/**
	 * The index to where the weights that are stored at for a given layer.
	 *
	 * @var int[] weightIndex
	 */
	private $weightIndex = array();
	
	/**
	 * The weights for a neural network.
	 *
	 * @var double[] weights
	 */
	private $weights = array();
	
	/**
	 * The activation types.
	 *
	 * @var ActivationFunction[] activationFunction
	 */
	private $activationFunctions = array();
	
	/**
	 * The context target for each layer.
	 * This is how the backwards connections
	 * are formed for the recurrent neural network. Each layer either has a
	 * zero, which means no context target, or a layer number that indicates the
	 * target layer.
	 *
	 * @var int[] contextTargetOffset
	 */
	private $contextTargetOffset = array();
	
	/**
	 * The size of each of the context targets.
	 * If a layer's contextTargetOffset
	 * is zero, its contextTargetSize should also be zero. The contextTargetSize
	 * should always match the feed count of the targeted context layer.
	 *
	 * @var int[] contextTargetSize
	 */
	private $contextTargetSize = array();
	
	/**
	 * The bias activation for each layer.
	 * This is usually either 1, for a bias,
	 * or zero for no bias.
	 *
	 * @var double[] biasActivation
	 */
	private $biasActivation = array();
	
	/**
	 * The layer that training should begin on.
	 *
	 * @var int beginTraining
	 */
	private $beginTraining = 0;
	
	/**
	 * The layer that training should end on.
	 *
	 * @var int endTraining
	 */
	private $endTraining = 0;
	
	/**
	 * Does this network have some connections disabled.
	 *
	 * @var bool isLimited
	 */
	private $isLimited = false;
	
	/**
	 * The limit, under which, all a cconnection is not considered to exist.
	 *
	 * @var double connectionLimit
	 */
	private $connectionLimit = 0;
	
	/**
	 * True if the network has context.
	 *
	 * @var bool hasContext
	 */
	private $hasContext = false;
	
	/**
	 * Construct a flat neural network.
	 *
	 * @param
	 *        	int input
	 *        	Neurons in the input layer.
	 * @param
	 *        	int hidden1
	 *        	Neurons in the first hidden layer. Zero for no first hidden
	 *        	layer.
	 * @param
	 *        	int hidden2
	 *        	Neurons in the second hidden layer. Zero for no second hidden
	 *        	layer.
	 * @param
	 *        	int output
	 *        	Neurons in the output layer.
	 * @param
	 *        	bool tanh
	 *        	True if this is a tanh activation, false for sigmoid.
	 */
	public function __construct( $input = null, $hidden1 = null, $hidden2 = null, $output = null, $tanh = null ) {
		if( is_null( $input ) ) {
			// do nothing
		}
		else if( is_array( $input ) ) {
			$this->init( $input );
		}
		else {
			assert( $input != null && $hidden1 != null && $hidden2 != null && $output != null && tanh != null, "invalid call to FlatNetwork constructor" );
			$linearAct = new ActivationLinear();
			$layers = array();
			$act = $tanh ? new ActivationTANH() : new ActivationSigmoid();
			
			if( ($hidden1 == 0) && ($hidden2 == 0) ) {
				$layers = array();
				$layers[0] = new FlatLayer( $linearAct, $input, FlatNetwork::DEFAULT_BIAS_ACTIVATION );
				$layers[1] = new FlatLayer( $act, $output, FlatNetwork::NO_BIAS_ACTIVATION );
			}
			else if( ($hidden1 == 0) || ($hidden2 == 0) ) {
				$count = \max( $hidden1, $hidden2 );
				$layers = array();
				$layers[0] = new FlatLayer( $linearAct, $input, FlatNetwork::DEFAULT_BIAS_ACTIVATION );
				$layers[1] = new FlatLayer( $act, $count, FlatNetwork::DEFAULT_BIAS_ACTIVATION );
				$layers[2] = new FlatLayer( $act, $output, FlatNetwork::NO_BIAS_ACTIVATION );
			}
			else {
				$layers = array();
				$layers[0] = new FlatLayer( $linearAct, $input, FlatNetwork::DEFAULT_BIAS_ACTIVATION );
				$layers[1] = new FlatLayer( $act, $hidden1, FlatNetwork::DEFAULT_BIAS_ACTIVATION );
				$layers[2] = new FlatLayer( $act, $hidden2, FlatNetwork::DEFAULT_BIAS_ACTIVATION );
				$layers[3] = new FlatLayer( $act, $output, FlatNetwork::NO_BIAS_ACTIVATION );
			}
			
			$this->isLimited = false;
			$this->connectionLimit = 0.0;
			
			$this->init( $layers );
		}
	}

	/**
	 * Calculate the error for this neural network.
	 * The error is calculated
	 * using root-mean-square(RMS).
	 *
	 * @param
	 *        	MLDataSet data
	 *        	The training set.
	 * @return double The error percentage.
	 */
	public function calculateError( MLDataSet $data ) {
		$errorCalculation = new ErrorCalculation();
		
		$actual = array();
		$pair = BasicMLDataPair::createPair( $data->getInputSize(), $data->getIdealSize() );
		
		for( $i = 0; $i < $data->getRecordCount(); ++$i ) {
			$data->getRecord( $i, $pair );
			$this->compute( $pair->getInputArray(), $actual );
			$errorCalculation->updateError( $actual, $pair->getIdealArray(), $pair->getSignificance() );
		}
		return $errorCalculation->calculate();
	}

	/**
	 * Clear any connection limits.
	 */
	public function clearConnectionLimit() {
		$this->connectionLimit = 0.0;
		$this->isLimited = false;
	}

	/**
	 * Clear any context neurons.
	 */
	public function clearContext() {
		$index = 0;
		
		for( $i = 0; $i < count( $this->layerIndex ); ++$i ) {
			
			$hasBias = ($this->layerContextCount[$i] + $this->layerFeedCounts[$i]) != $this->layerCounts[$i];
			
			// fill in regular neurons
			$this->layerOutput = array_fill( $index, $index + $this->layerFeedCounts[$i], 0 );
			$index += $this->layerFeedCounts[$i];
			
			// fill in the bias
			if( $hasBias ) {
				$this->layerOutput[$index++] = $this->biasActivation[$i];
			}
			
			// fill in context
			$this->layerOutput = array_fill( $index, $index + $this->layerContextCount[$i], 0 );
			$index += $this->layerContextCount[$i];
		}
	}

	/**
	 * Clone the network.
	 *
	 * @return FlatNetwork A clone of the network.
	 */
	public function __clone() {
		$result = new FlatNetwork();
		$this->cloneFlatNetwork( $result );
		return $result;
	}

	/**
	 * Clone into the flat network passed in.
	 *
	 * @param
	 *        	FlatNetwork result
	 *        	The network to copy into.
	 */
	public function cloneFlatNetwork( FlatNetwork &$result ) {
		$result->inputCount = $this->inputCount;
		$result->layerCounts = EngineArray\arrayCopy( $this->layerCounts );
		$result->layerIndex = EngineArray\arrayCopy( $this->layerIndex );
		$result->layerOutput = EngineArray\arrayCopy( $this->layerOutput );
		$result->layerSums = EngineArray\arrayCopy( $this->layerSums );
		$result->layerFeedCounts = EngineArray\arrayCopy( $this->layerFeedCounts );
		$result->contextTargetOffset = EngineArray\arrayCopy( $this->contextTargetOffset );
		$result->contextTargetSize = EngineArray\arrayCopy( $this->contextTargetSize );
		$result->layerContextCount = EngineArray\arrayCopy( $this->layerContextCount );
		$result->biasActivation = EngineArray\arrayCopy( $this->biasActivation );
		$result->outputCount = $this->outputCount;
		$result->weightIndex = $this->weightIndex;
		$result->weights = $this->weights;
		
		$result->activationFunctions = array();
		for( $i = 0; $i < count( $this->activationFunctions ); ++$i ) {
			$result->activationFunctions[$i] = clone ($this->activationFunctions[$i]);
		}
		
		$result->beginTraining = $this->beginTraining;
		$result->endTraining = $this->endTraining;
	}

	/**
	 * Calculate the output for the given input.
	 *
	 * @param
	 *        	double[] input
	 *        	The input.
	 * @param
	 *        	double[] output
	 *        	Output will be placed here.
	 */
	public function computeArray( array $input, array $output ) {
		$sourceIndex = count( $this->layerOutput ) - $this->layerCounts[count( $this->layerCounts ) - 1];
		
		EngineArray\arrayCopy( $input, 0, $this->layerOutput, $sourceIndex, $this->inputCount );
		
		for( $i = count( $this->layerIndex ) - 1; $i > 0; --$i ) {
			$this->computeLayer( $i );
		}
		
		// update context values
		$offset = $this->contextTargetOffset[0];
		
		EngineArray\arrayCopy( $this->layerOutput, 0, $layerOutput, $offset, $this->contextTargetSize[0] );
		
		EngineArray\arrayCopy( $this->layerOutput, 0, $output, 0, $this->outputCount );
	}

	/**
	 * Calculate a layer.
	 *
	 * @param
	 *        	int currentLayer
	 *        	The layer to calculate.
	 */
	protected function computeLayer( $currentLayer ) {
		$inputIndex = $this->layerIndex[$currentLayer];
		$outputIndex = $this->layerIndex[$currentLayer - 1];
		$inputSize = $this->layerCounts[$currentLayer];
		$outputSize = $this->layerFeedCounts[$currentLayer - 1];
		
		$index = $this->weightIndex[$currentLayer - 1];
		
		$limitX = $outputIndex + $outputSize;
		$limitY = $inputIndex + $inputSize;
		
		// weight values
		for( $x = $outputIndex; $x < $limitX; ++$x ) {
			$sum = 0.0;
			for( $y = $inputIndex; $y < $limitY; ++$y ) {
				$sum += $this->weights[$index++] * $this->layerOutput[$y];
			}
			$this->layerSums[$x] = $sum;
			$this->layerOutput[$x] = $sum;
		}
		
		$this->activationFunctions[$currentLayer - 1]->activationFunction( $this->layerOutput, $outputIndex, $outputSize );
		
		// update context values
		$offset = $this->contextTargetOffset[$currentLayer];
		
		EngineArray\arrayCopy( $this->layerOutput, $outputIndex, $this->layerOutput, $offset, $this->contextTargetSize[$currentLayer] );
	}

	/**
	 * Decode the specified data into the weights of the neural network.
	 * This
	 * method performs the opposite of encodeNetwork.
	 *
	 * @param
	 *        	double[] data
	 *        	The data to be decoded.
	 */
	public function decodeNetwork( array $data ) {
		if( count( $data ) != count( $this->weights ) ) {
			throw new EncogError( "Incompatible weight sizes, can't assign length=" + count( $data ) + " to length=" + count( $this . weights ) );
		}
		$this->weights = EngineArray\arrayCopy( $data );
	}

	/**
	 * Encode the neural network to an array of doubles.
	 * This includes the
	 * network weights. To read this into a neural network, use the
	 * decodeNetwork method.
	 *
	 * @return double[] The encoded network.
	 */
	public function encodeNetwork() {
		return $this->weights;
	}

	/**
	 *
	 * @return ActivationFunction[] The activation functions.
	 */
	public function getActivationFunctions() {
		return $this->activationFunctions;
	}

	/**
	 *
	 * @return int the beginTraining
	 */
	public function getBeginTraining() {
		return $this->beginTraining;
	}

	/**
	 *
	 * @return double[] The bias activation.
	 */
	public function getBiasActivation() {
		return $this->biasActivation;
	}

	/**
	 *
	 * @return double the connectionLimit
	 */
	public function getConnectionLimit() {
		return $this->connectionLimit;
	}

	/**
	 *
	 * @return int[] The offset of the context target for each layer.
	 */
	public function getContextTargetOffset() {
		return $this->contextTargetOffset;
	}

	/**
	 *
	 * @return int[] The context target size for each layer. Zero if the layer does
	 *         not feed a context layer.
	 */
	public function getContextTargetSize() {
		return $this->contextTargetSize;
	}

	/**
	 *
	 * @return int The length of the array the network would encode to.
	 */
	public function getEncodeLength() {
		return count( $this->weights );
	}

	/**
	 *
	 * @return int the endTraining
	 */
	public function getEndTraining() {
		return $this->endTraining;
	}

	/**
	 *
	 * @return bool True if this network has context.
	 */
	public function getHasContext() {
		return $this->hasContext;
	}

	/**
	 *
	 * @return int The number of input neurons.
	 */
	public function getInputCount() {
		return $this->inputCount;
	}

	/**
	 *
	 * @return int[] The layer context count.
	 */
	public function getLayerContextCount() {
		return $this->layerContextCount;
	}

	/**
	 *
	 * @return int[] The number of neurons in each layer.
	 */
	public function getLayerCounts() {
		return $this->layerCounts;
	}

	/**
	 *
	 * @return int[] The number of neurons in each layer that are fed by the previous
	 *         layer.
	 */
	public function getLayerFeedCounts() {
		return $this->layerFeedCounts;
	}

	/**
	 *
	 * @return int[] Indexes into the weights for the start of each layer.
	 */
	public function getLayerIndex() {
		return $this->layerIndex;
	}

	/**
	 *
	 * @return double[] The output for each layer.
	 */
	public function getLayerOutput() {
		return $this->layerOutput;
	}

	/**
	 *
	 * @return int The neuron count.
	 */
	public function getNeuronCount() {
		$result = 0;
		foreach( $this->layerCounts as $element ) {
			$result += $element;
		}
		return $result;
	}

	/**
	 *
	 * @return int The number of output neurons.
	 */
	public function getOutputCount() {
		return $this->outputCount;
	}

	/**
	 *
	 * @return int[] The index of each layer in the weight and threshold array.
	 */
	public function getWeightIndex() {
		return $this->weightIndex;
	}

	/**
	 *
	 * @return double[] The index of each layer in the weight and threshold array.
	 */
	public function getWeights() {
		return $this->weights;
	}

	/**
	 * Neural networks with only one type of activation function offer certain
	 * optimization options.
	 * This method determines if only a single activation
	 * function is used.
	 *
	 * @return The number of the single activation function, or -1 if there are
	 *         no activation functions or more than one type of activation
	 *         function.
	 */
	public function hasSameActivationFunction() {
		$map = array();
		
		foreach( $this->activationFunctions as $activation ) {
			if( ! in_array( get_class( $activation ), $map ) ) {
				array_push( $map, get_class( $activation ) );
			}
		}
		
		if( count( $map ) != 1 ) {
			return null;
		}
		else {
			return $map[0];
		}
	}

	/**
	 * Construct a flat network.
	 *
	 * @param
	 *        	FlatLayers[] layers
	 *        	The layers of the network to create.
	 */
	public function init( array $layers ) {
		$layerCount = count( $layers );
		
		$this->inputCount = $layers[0]->getCount();
		$this->outputCount = $layers[$layerCount - 1]->getCount();
		
		$this->layerCounts = array_fill( 0, $layerCount, 0 );
		$this->layerContextCount = array_fill( 0, $layerCount, 0 );
		$this->weightIndex = array_fill( 0, $layerCount, 0 );
		$this->layerIndex = array_fill( 0, $layerCount, 0 );
		$this->activationFunctions = array_fill( 0, $layerCount, null );
		;
		$this->layerFeedCounts = array_fill( 0, $layerCount, 0 );
		$this->contextTargetOffset = array_fill( 0, $layerCount, 0 );
		$this->contextTargetSize = array_fill( 0, $layerCount, 0 );
		$this->biasActivation = array_fill( 0, $layerCount, 0.0 );
		
		$index = 0;
		$neuronCount = 0;
		$weightCount = 0;
		
		for( $i = count( $layers ) - 1; $i >= 0; --$i ) {
			
			$layer = $layers[$i];
			$nextLayer = null;
			
			if( $i > 0 ) {
				$nextLayer = $layers[$i - 1];
			}
			
			$this->biasActivation[$index] = $layer->getBiasActivation();
			$this->layerCounts[$index] = $layer->getTotalCount();
			$this->layerFeedCounts[$index] = $layer->getCount();
			$this->layerContextCount[$index] = $layer->getContextCount();
			$this->activationFunctions[$index] = $layer->getActivation();
			
			$neuronCount += $layer->getTotalCount();
			
			if( $nextLayer != null ) {
				$weightCount += $layer->getCount() * $nextLayer->getTotalCount();
			}
			
			if( $index == 0 ) {
				$this->weightIndex[$index] = 0;
				$this->layerIndex[$index] = 0;
			}
			else {
				$this->weightIndex[$index] = $this->weightIndex[$index - 1] + ($this->layerCounts[$index] * $this->layerFeedCounts[$index - 1]);
				$this->layerIndex[$index] = $this->layerIndex[$index - 1] + $this->layerCounts[$index - 1];
			}
			
			$neuronIndex = 0;
			for( $j = count( $layers ) - 1; $j >= 0; --$j ) {
				if( $layers[$j]->getContextFedBy() == $layer ) {
					$this->hasContext = true;
					$this->contextTargetSize[$index] = $layers[$j]->getContextCount();
					$this->contextTargetOffset[$index] = $neuronIndex + ($layers[$j]->getTotalCount() - $layers[$j]->getContextCount());
				}
				$neuronIndex += $layers[$j]->getTotalCount();
			}
			
			++$index;
		}
		
		$this->beginTraining = 0;
		$this->endTraining = count( $this->layerCounts ) - 1;
		
		$this->weights = array_fill( 0, $weightCount, 0.0 );
		$this->layerOutput = array_fill( 0, $weightCount, 0.0 );
		$this->layerSums = array_fill( 0, $weightCount, 0.0 );
		
		$this->clearContext();
	}

	/**
	 *
	 * @return bool the isLimited
	 */
	public function isLimited() {
		return $this->isLimited;
	}

	/**
	 * Perform a simple randomization of the weights of the neural network
	 * between -1 and 1.
	 */
	/**
	 * TODO(katrina)public function randomize() {
	 * randomize(1, -1);
	 * }
	 */
	
	/**
	 * Perform a simple randomization of the weights of the neural network
	 * between the specified hi and lo.
	 *
	 * @param
	 *        	double hi
	 *        	The network high.
	 * @param
	 *        	double lo
	 *        	The network low.
	 */
	public function randomize( $hi, $lo ) {
		for( $i = 0; $i < count( $this->weights ); ++$i ) {
			$this->weights[$i] = (\random() * ($hi - $lo)) + $lo;
		}
	}

	/**
	 * Set the activation functions.
	 *
	 * @param
	 *        	ActivationFunction[] af The activation functions.
	 */
	public function setActivationFunctions( array $af ) {
		$this->activationFunctions = $af;
	}

	/**
	 *
	 * @param
	 *        	int beginTraining
	 *        	the beginTraining to set
	 */
	public function setBeginTraining( $beginTraining ) {
		$this->beginTraining = $beginTraining;
	}

	/**
	 * Set the bias activation.
	 *
	 * @param
	 *        	double[] biasActivation The bias activation.
	 */
	public function setBiasActivation( array $biasActivation ) {
		$this->biasActivation = $biasActivation;
	}

	/**
	 *
	 * @param
	 *        	double connectionLimit
	 *        	the connectionLimit to set
	 */
	public function setConnectionLimit( $connectionLimit ) {
		$this->connectionLimit = $connectionLimit;
		if( \abs( $this->connectionLimit - BasicNetwork::DEFAULT_CONNECTION_LIMIT ) < Encog\DEFAULT_DOUBLE_EQUAL ) {
			$this->isLimited = true;
		}
	}

	/**
	 * Set the context target offset.
	 *
	 * @param
	 *        	int[] contextTargetOffset The context target offset.
	 */
	public function setContextTargetOffset( $contextTargetOffset ) {
		$this->contextTargetOffset = EngineArray\arrayCopy( $contextTargetOffset );
	}

	/**
	 * Set the context target size.
	 *
	 * @param
	 *        	int[] contextTargetSize The context target size.
	 */
	public function setContextTargetSize( array $contextTargetSize ) {
		$this->contextTargetSize = EngineArray\arrayCopy( $contextTargetSize );
	}

	/**
	 *
	 * @param
	 *        	int endTraining
	 *        	the endTraining to set
	 */
	public function setEndTraining( $endTraining ) {
		$this->endTraining = $endTraining;
	}

	/**
	 * Set the hasContext property.
	 *
	 * @param
	 *        	bool hasContext True if the network has context.
	 */
	public function setHasContext( $hasContext ) {
		$this->hasContext = $hasContext;
	}

	/**
	 * Set the input count.
	 *
	 * @param
	 *        	int inputCount The input count.
	 */
	public function setInputCount( $inputCount ) {
		$this->inputCount = $inputCount;
	}

	/**
	 * Set the layer context count.
	 *
	 * @param
	 *        	int[] layerContextCount The layer context count.
	 */
	public function setLayerContextCount( array $layerContextCount ) {
		$this->layerContextCount = EngineArray\arrayCopy( $layerContextCount );
	}

	/**
	 * Set the layer counts.
	 *
	 * @param
	 *        	int[] layerCounts The layer counts.
	 */
	public function setLayerCounts( array $layerCounts ) {
		$this->layerCounts = EngineArray\arrayCopy( $layerCounts );
	}

	public function setLayerFeedCounts( array $layerFeedCounts ) {
		$this->layerFeedCounts = EngineArray\arrayCopy( $layerFeedCounts );
	}

	/**
	 * Set the layer index.
	 *
	 * @param
	 *        	int[] i The layer index.
	 */
	public function setLayerIndex( array $i ) {
		$this->layerIndex = EngineArray\arrayCopy( $i );
	}

	/**
	 * Set the layer output.
	 *
	 * @param
	 *        	double[] layerOutput The layer output.
	 */
	public function setLayerOutput( array $layerOutput ) {
		$this->layerOutput = EngineArray\arrayCopy( $layerOutput );
	}

	/**
	 * Set the output count.
	 *
	 * @param
	 *        	int outputCount The output count.
	 */
	public function setOutputCount( $outputCount ) {
		$this->outputCount = $outputCount;
	}

	/**
	 * Set the weight index.
	 *
	 * @param
	 *        	int[] weightIndex The weight index.
	 */
	public function setWeightIndex( array $weightIndex ) {
		$this->weightIndex = EngineArray\arrayCopy( $weightIndex );
	}

	/**
	 * Set the weights.
	 *
	 * @param
	 *        	double[] weights The weights.
	 */
	public function setWeights( array $weights ) {
		$this->weights = EngineArray\arrayCopy( $weights );
	}

	/**
	 *
	 * @return double[] the layerSums
	 */
	public function getLayerSums() {
		return $this->layerSums;
	}

	/**
	 * Set the layer sums.
	 *
	 * @param
	 *        	double[] d The layer sums.
	 */
	public function setLayerSums( array $d ) {
		$this->layerSums = EngineArray\arrayCopy( $d );
	}
}