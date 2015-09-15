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

namespace Encog\Neural\Networks;

use \Encog\Encog;
use \Encog\Engine\Network\Activation\ActivationElliott;
use \Encog\Engine\Network\Activation\ActivationElliottSymmetric;
use \Encog\Engine\Network\Activation\ActivationFunction;
use \Encog\Engine\Network\Activation\ActivationSigmoid;
use \Encog\Engine\Network\Activation\ActivationTANH;
use \Encog\MathUtil\Randomize\ConsistentRandomizer;
use \Encog\MathUtil\Randomize\NguyenWidrowRandomizer;
use \Encog\MathUtil\Randomize\Randomizer;
use \Encog\MathUtil\Randomize\RangeRandomizer;
use \Encog\ML\BasicML;
use \Encog\ML\MLClassification;
use \Encog\ML\MLContext;
use \Encog\ML\MLEncodable;
use \Encog\ML\MLError;
use \Encog\ML\MLFactory;
use \Encog\ML\MLRegression;
use \Encog\ML\MLResettable;
use \Encog\ML\Data\MLData;
use \Encog\ML\Data\MLDataSet;
use \Encog\ML\Data\Basic\BasicMLData;
use \Encog\ML\Factory\MLMethodFactory;
use \Encog\Neural\NeuralNetworkError;
use \Encog\Neural\Flat\FlatNetwork;
use \Encog\Neural\Networks\Layers\Layer;
use \Encog\Neural\Networks\Structure\NetworkCODEC;
use \Encog\Neural\Networks\Structure\NeuralStructure;
use \Encog\Util\EngineArray;
use \Encog\Util\CSV\CSVFormat;
use \Encog\Util\CSV\NumberList;
use \Encog\Util\Obj\ObjectCloner;
use \Encog\Util\Simple\EncogUtility;

/**
 * This class implements a neural network. This class works in conjunction the
 * Layer classes. Layers are added to the BasicNetwork to specify the structure
 * of the neural network.
 *
 * The first layer added is the input layer, the final layer added is the output
 * layer. Any layers added between these two layers are the hidden layers.
 *
 * The network structure is stored in the structure member. It is important to
 * call:
 *
 * network.getStructure().finalizeStructure();
 *
 * Once the neural network has been completely constructed.
 *
 */
class BasicNetwork extends BasicML implements ContainsFlat, MLContext,
MLRegression, MLEncodable, MLResettable, MLClassification, MLError,
MLFactory {

	/**
	 * Tag used for the connection limit.
	 */
	const TAG_LIMIT = "CONNECTION_LIMIT";

	/**
	 * The default connection limit.
	 */
	const DEFAULT_CONNECTION_LIMIT = 0.0000000001;

	/**
	 * The property for connection limit.
	 */
	const TAG_CONNECTION_LIMIT = "connectionLimit";

	/**
	 * The property for begin training.
	 */
	const TAG_BEGIN_TRAINING = "beginTraining";

	/**
	 * The property for context target offset.
	 */
	const TAG_CONTEXT_TARGET_OFFSET = "contextTargetOffset";

	/**
	 * The property for context target size.
	 */
	const TAG_CONTEXT_TARGET_SIZE = "contextTargetSize";

	/**
	 * The property for end training.
	 */
	const TAG_END_TRAINING = "endTraining";

	/**
	 * The property for has context.
	 */
	const TAG_HAS_CONTEXT = "hasContext";

	/**
	 * The property for layer counts.
	 */
	const TAG_LAYER_COUNTS = "layerCounts";

	/**
	 * The property for layer feed counts.
	 */
	const TAG_LAYER_FEED_COUNTS = "layerFeedCounts";

	/**
	 * The property for layer index.
	 */
	const TAG_LAYER_INDEX = "layerIndex";

	/**
	 * The property for weight index.
	 */
	const TAG_WEIGHT_INDEX = "weightIndex";

	/**
	 * The property for bias activation.
	 */
	const TAG_BIAS_ACTIVATION = "biasActivation";

	/**
	 * The property for layer context count.
	 */
	const TAG_LAYER_CONTEXT_COUNT = "layerContextCount";

	/**
	 * Holds the structure of the network. This keeps the network from having to
	 * constantly lookup layers and synapses.
	 * @var NeuralStructure structure
	 */
	private $structure = null;

	/**
	 * Construct an empty neural network.
	 */
	public function __construct() {
		$this->structure = new NeuralStructure($this);
	}

	/**
	 * Add a layer to the neural network. If there are no layers added this
	 * layer will become the input layer. This function automatically updates
	 * both the input and output layer references.
	 *
	 * @param Layer layer The layer to be added to the network.
	 */
	public function addLayer(Layer $layer) {
		$layer->setNetwork($this);
		$this->structure->getLayers().add($layer);
	}

	/**
	 * Add to a weight.
	 *
	 * @param int fromLayer
	 *            The from layer.
	 * @param int fromNeuron
	 *            The from neuron.
	 * @param int toNeuron
	 *            The to neuron.
	 * @param double value
	 *            The value to add.
	 */
	public function addWeight($fromLayer, $fromNeuron,
			$toNeuron, $value) {
		$old = $this->getWeight($fromLayer, $fromNeuron, $toNeuron);
		$this->setWeight($fromLayer, $fromNeuron, $toNeuron, $old + $value);
	}

	/**
	 * Calculate the error for this neural network. We always calculate the
	 * error using the "regression" calculator. Neural networks don't directly
	 * support classification, rather they use one-of-encoding or similar. So
	 * just using the regression calculator gives a good approximation.
	 *
	 * @param MLDataSet data
	 *            The training set.
	 * @return double The error percentage.
	 */
	public function calculateError(MLDataSet $data) {
		return EncogUtility\calculateRegressionError($this, $data);
	}

	/**
	 * Calculate the total number of neurons in the network across all layers.
	 *
	 * @return int The neuron count.
	 */
	public function calculateNeuronCount() {
		$result = 0;
		foreach ($this->structure->getLayers() as $layer) {
			$result += $layer->getNeuronCount();
		}
		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function classify(MLData $input) {
		return $this->winner($input);
	}

	/**
	 * Clear any data from any context layers.
	 */
	public function clearContext() {

		if ($this->structure->getFlat() != null) {
			$this->structure->getFlat()->clearContext();
		}
	}

	/**
	 * Return a clone of this neural network. Including structure, weights and
	 * bias values. This is a deep copy.
	 *
	 * @return BasicNetwork A cloned copy of the neural network.
	 */
	/*TODO(katrina) public function __clone() {
		$result = (BasicNetwork) ObjectCloner.deepCopy(this);
		return result;
	}*/

	/**
	 * Compute the output for this network.
	 *
	 * @param double[] input
	 *            The input.
	 * @param double[] output
	 *            The output.
	 */
	public function compute(array $input, array &$output) {
		$input2 = new BasicMLData($input);
		$output2 = $this->compute($input2);
		EngineArray\arrayCopy($output2->getData(), $output);
	}

	/**
	 * Compute the output for a given input to the neural network.
	 *
	 * @param MLData input
	 *            The input to the neural network.
	 * @return MLData The output from the neural network.
	 */
	public function compute(MLData $input) {
		try {
			$result = new BasicMLData($this->structure->getFlat()->getOutputCount());
			$this->structure->getFlat()->compute($input->getData(), $result->getData());
			return $result;
		} catch (ArrayIndexOutOfBoundsException $ex) {
			throw new NeuralNetworkError(
					"Index exception: there was likely a mismatch between layer sizes, or the size of the input presented to the network.",
					$ex);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function decodeFromArray(array $encoded) {
		$this->structure->requireFlat();
		$weights = $this->structure->getFlat()->getWeights();
		if ($weights->length != $encoded->length) {
			throw new NeuralNetworkError(
					"Size mismatch, encoded array should be of length "
					+ $weights->length);
		}

		EngineArray\arrayCopy($encoded, $weights);
	}

	/**
	 * @return string The weights as a comma separated list.
	 */
	public function dumpWeights() {

		$result = '';
		NumberList\toList(CSVFormat\EG_FORMAT, $result, $this->structure->getFlat()->getWeights());
		return $result->toString();
	}

	/**
	 * @return string
	 */
	public function dumpWeightsVerbose() {
		$result = '';

		for ($layer = 0; $layer < $this->getLayerCount() - 1; ++$layer) {
			$bias = 0;
			if ($this->isLayerBiased($layer)) {
				$bias = 1;
			}

			for ($fromIdx = 0; $fromIdx < $this->getLayerNeuronCount($layer)
			+ $bias; ++$fromIdx) {
				for ($toIdx = 0; $toIdx < $this->getLayerNeuronCount($layer + 1); ++$toIdx) {
					$type1 = "";
					$type2 = "";

					if ($layer == 0) {
						$type1 = "I";
						$type2 = "H" . $layer . ",";
					} else {
						$type1 = "H" . $layer - 1 . ",";
						if ($layer == ($this->getLayerCount() - 2)) {
							$type2 = "O";
						} else {
							$type2 = "H" . $layer . ",";
						}
					}
						
					if( $bias ==1 && ($fromIdx ==  $this->getLayerNeuronCount($layer))) {
						$type1 = "bias";
					} else {
						$type1 = $type1 + $fromIdx;
					}

					$result .= ($type1 . "-->" . $type2 . $toIdx
							. " : " . $this->getWeight($layer, $fromIdx, $toIdx)
							. "\n");
				}
			}
		}

		return $result;
	}

	/**
	 * Enable, or disable, a connection.
	 *
	 * @param int fromLayer
	 *            The layer that contains the from neuron.
	 * @param int fromNeuron
	 *            The source neuron.
	 * @param int toNeuron
	 *            The target connection.
	 * @param boolean enable
	 *            True to enable, false to disable.
	 */
	public function enableConnection($fromLayer, $fromNeuron,
			$toNeuron, $enable) {

		$value = $this->getWeight($fromLayer, $fromNeuron, $toNeuron);

		if ($enable) {
			if (!$this->structure->isConnectionLimited()) {
				return;
			}

			if (\abs($value) < $this->structure->getConnectionLimit()) {
				$this->setWeight($fromLayer, $fromNeuron, $toNeuron,
						RangeRandomizer\randomize(-1, 1));
			}
		} else {
			if (!$this->structure->isConnectionLimited()) {
				$this->setProperty(BasicNetwork\TAG_LIMIT,
						BasicNetwork\DEFAULT_CONNECTION_LIMIT);
				$this->structure->updateProperties();

			}
			$this->setWeight($fromLayer, $fromNeuron, $toNeuron, 0);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function encodedArrayLength() {
		$this->structure->requireFlat();
		return $this->structure->getFlat()->getEncodeLength();
	}

	/**
	 * {@inheritDoc}
	 */
	public function encodeToArray(array $encoded) {
		$this->structure->requireFlat();
		$weights = $this->structure->getFlat()->getWeights();
		if (count($weights) != count($encoded)) {
			throw new NeuralNetworkError(
					"Size mismatch, encoded array should be of length "
					+ count($weights));
		}

		EngineArray\arrayCopy($weights, $encoded);
	}

	/**
	 * Compare the two neural networks. For them to be equal they must be of the
	 * same structure, and have the same matrix values.
	 *
	 * @param Object other
	 *            The other neural network.
	 * @return True if the two networks are equal.
	 */
	/**
	 * Determine if this neural network is equal to another. Equal neural
	 * networks have the same weight matrix and bias values, within a specified
	 * precision.
	 *
	 * @param other
	 *            The other neural network.
	 * @param precision
	 *            The number of decimal places to compare to.
	 * @return True if the two neural networks are equal.
	 */
	//TODO(katrina) Documentation, merged functions
	public function equals($other, $precision = \Encog\DEFAULT_PRECISION) {
		if ($other === null)
			return false;
		if ($other === $this)
			return true;
		if (!($other instanceof BasicNetwork))
			return false;

		return NetworkCODEC\equals($this, $other, $precision);
	}

	/**
	 * Get the activation function for the specified layer.
	 *
	 * @param int layer
	 *            The layer.
	 * @return ActivationFunction The activation function.
	 */
	public function getActivation($layer) {
		$this->structure->requireFlat();
		$layerNumber = $this->getLayerCount() - $layer - 1;
		return $this->structure->getFlat()->getActivationFunctions()[$layerNumber];
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFlat() {
		return $this->getStructure()->getFlat();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getInputCount() {
		$this->structure->requireFlat();
		return getStructure()->getFlat()->getInputCount();
	}

	/**
	 * Get the bias activation for the specified layer.
	 *
	 * @param int l
	 *            The layer.
	 * @return double The bias activation.
	 */
	public function getLayerBiasActivation($l) {
		if (!$this->isLayerBiased($l)) {
			throw new NeuralNetworkError(
					"Error, the specified layer does not have a bias: " + $l);
		}

		$this->structure->requireFlat();
		$layerNumber = $this->getLayerCount() - $l - 1;

		$layerOutputIndex = $this->structure->getFlat()->getLayerIndex()[$layerNumber];
		$count = $this->structure->getFlat()->getLayerCounts()[$layerNumber];
		return $this->structure->getFlat()->getLayerOutput()[$layerOutputIndex
				+ $count - 1];
	}

	/**
	 * @return int The layer count.
	 */
	public function getLayerCount() {
		$this->structure->requireFlat();
		return count($this->structure->getFlat()->getLayerCounts());
	}

	/**
	 * Get the neuron count.
	 *
	 * @param int l
	 *            The layer.
	 * @return int The neuron count.
	 */
	public function getLayerNeuronCount($l) {
		$this->structure->requireFlat();
		$layerNumber = $this->getLayerCount() - $l - 1;
		return $this->structure->getFlat()->getLayerFeedCounts()[$layerNumber];
	}

	/**
	 * Get the layer output for the specified neuron.
	 *
	 * @param int layer
	 *            The layer.
	 * @param int neuronNumber
	 *            The neuron number.
	 * @return double The output from the last call to compute.
	 */
	public function getLayerOutput($layer, $neuronNumber) {
		$this->structure->requireFlat();
		$layerNumber = $this->getLayerCount() - $layer - 1;
		$index = $this->structure->getFlat()->getLayerIndex()[$layerNumber]
		+ $neuronNumber;
		$output = $this->structure->getFlat()->getLayerOutput();
		if ($index >= $output->length) {
			throw new NeuralNetworkError("The layer index: " + $index
					+ " specifies an output index larger than the network has.");
		}
		return $output[$index];
	}

	/**
	 * Get the total (including bias and context) neuron cont for a layer.
	 *
	 * @param int l
	 *            The layer.
	 * @return int The count.
	 */
	public function getLayerTotalNeuronCount($l) {
		$this->structure->requireFlat();
		$layerNumber = $this->getLayerCount() - $l - 1;
		return $this->structure->getFlat()->getLayerCounts()[$layerNumber];
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOutputCount() {
		$this->structure->requireFlat();
		return $this->getStructure()->getFlat()->getOutputCount();
	}

	/**
	 * @return NeuralStructure Get the structure of the neural network. The structure allows you
	 *         to quickly obtain synapses and layers without traversing the
	 *         network.
	 */
	public function getStructure() {
		return $this->structure;
	}

	/**
	 * Get the weight between the two layers.
	 *
	 * @param int fromLayer
	 *            The from layer.
	 * @param int fromNeuron
	 *            The from neuron.
	 * @param int toNeuron
	 *            The to neuron.
	 * @return double The weight value.
	 */
	public function getWeight($fromLayer, $fromNeuron,
			$toNeuron) {
		$this->structure->requireFlat();
		$this->validateNeuron($fromLayer, $fromNeuron);
		$this->validateNeuron($fromLayer + 1, $toNeuron);
		$fromLayerNumber = $this->getLayerCount() - $fromLayer - 1;
		$toLayerNumber = $fromLayerNumber - 1;

		if ($toLayerNumber < 0) {
			throw new NeuralNetworkError(
					"The specified layer is not connected to another layer: "
					+ $fromLayer);
		}

		$weightBaseIndex = $this->structure->getFlat()->getWeightIndex()[$toLayerNumber];
		$count = $this->structure->getFlat()->getLayerCounts()[$fromLayerNumber];
		$weightIndex = $weightBaseIndex + $fromNeuron
		+ ($toNeuron * $count);

		return $this->structure->getFlat()->getWeights()[$weightIndex];
	}

	/**
	 * Generate a hash code.
	 *
	 * @return int The hash code.
	 */
	public function hashCode() {
		return parent::hashCode();
	}

	/**
	 * Determine if the specified connection is enabled.
	 *
	 * @param int layer
	 *            The layer to check.
	 * @param int fromNeuron
	 *            The source neuron.
	 * @param int toNeuron
	 *            THe target neuron.
	 * @return bool True, if the connection is enabled, false otherwise.
	 */
	public function isConnected($layer, $fromNeuron,
			$toNeuron) {

		if (!$this->structure->isConnectionLimited()) {
			return true;
		}

		$value = $this->getWeight($layer, $fromNeuron, $toNeuron);

		return (\abs($value) > $this->structure->getConnectionLimit());
	}

	/**
	 * Determine if the specified layer is biased.
	 *
	 * @param int l
	 *            The layer number.
	 * @return bool True, if the layer is biased.
	 */
	public function isLayerBiased($l) {
		$this->structure->requireFlat();
		$layerNumber = $this->getLayerCount() - $l - 1;
		return $this->structure->getFlat()->getLayerCounts()[$layerNumber] != $this->structure->getFlat()->getLayerFeedCounts()[$layerNumber];
	}

	/**
	 * Reset the weight matrix and the bias values. This will use a
	 * Nguyen-Widrow randomizer with a range between -1 and 1. If the network
	 * does not have an input, output or hidden layers, then Nguyen-Widrow
	 * cannot be used and a simple range randomize between -1 and 1 will be
	 * used.
	 *
	 */
	public function reset() {
		$this->getRandomizer()->randomize($this);
	}

	/**
	 * Reset the weight matrix and the bias values. This will use a
	 * RangeRandomizer with a range between -1 and 1.
	 * @param int seen
	 *
	 */
	public function reset($seed) {
		(new ConsistentRandomizer(-1, 1, $seed))->randomize($this);
	}

	/**
	 * Determines the randomizer used for resets. This will normally return a
	 * Nguyen-Widrow randomizer with a range between -1 and 1. If the network
	 * does not have an input, output or hidden layers, then Nguyen-Widrow
	 * cannot be used and a simple range randomize between -1 and 1 will be
	 * used. Range randomizer is also used if the activation function is not
	 * TANH, Sigmoid, or the Elliott equivalents.
	 *
	 * @return Randomizer the randomizer
	 */
	private function getRandomizer() {
		$useNWR = true;

		for ($i = 0; i < $this->getLayerCount(); ++$i) {
			$af = getActivation($i);
			if ($af->getClass() != ActivationSigmoid
					$$ $af->getClass() != ActivationTANH
					&& $af->getClass() != ActivationElliott
					&& $af->getClass() != ActivationElliottSymmetric) {
						$useNWR = false;
					}
		}

		if ($this->getLayerCount() < 3) {
			$useNWR = false;
		}

		if ($useNWR) {
			return new NguyenWidrowRandomizer();
		} else {
			return new RangeRandomizer(-1, 1);
		}
	}

	/**
	 * Sets the bias activation for every layer that supports bias. Make sure
	 * that the network structure has been finalized before calling this method.
	 *
	 * @param double activation
	 *            The new activation.
	 */
	public function setBiasActivation($activation) {
		// first, see what mode we are on. If the network has not been
		// finalized, set the layers
		if ($this->structure->getFlat() == null) {
			foreach ($this->structure->getLayers() as $layer ) {
				if ($layer->hasBias()) {
					$layer->setBiasActivation($activation);
				}
			}
		} else {
			for ($i = 0; $i < $this->getLayerCount(); ++$i) {
				if ($this->isLayerBiased($i)) {
					$this->setLayerBiasActivation($i, $activation);
				}
			}
		}
	}

	/**
	 * Set the bias activation for the specified layer.
	 *
	 * @param int l
	 *            The layer to use.
	 * @param double value
	 *            The bias activation.
	 */
	public function setLayerBiasActivation($l, $value) {
		if (!$this->isLayerBiased($l)) {
			throw new NeuralNetworkError(
					"Error, the specified layer does not have a bias: " + $l);
		}

		$this->structure->requireFlat();
		$layerNumber = $this->getLayerCount() - $l - 1;

		$layerOutputIndex = $this->structure->getFlat()->getLayerIndex()[$layerNumber];
		$count = $this->structure->getFlat()->getLayerCounts()[$layerNumber];
		$this->structure->getFlat()->getLayerOutput()[$layerOutputIndex + $count - 1] = $value;
	}

	/**
	 * Set the weight between the two specified neurons. The bias neuron is
	 * always the last neuron on a layer.
	 *
	 * @param int fromLayer
	 *            The from layer.
	 * @param int fromNeuron
	 *            The from neuron.
	 * @param int toNeuron
	 *            The to neuron.
	 * @param double value
	 *            The to value.
	 */
	public function setWeight($fromLayer, $fromNeuron,
			$toNeuron, $value) {
		$this->structure->requireFlat();
		$fromLayerNumber = $this->getLayerCount() - $fromLayer - 1;
		$toLayerNumber = $fromLayerNumber - 1;

		if ($toLayerNumber < 0) {
			throw new NeuralNetworkError(
					"The specified layer is not connected to another layer: "
					+ $fromLayer);
		}

		$weightBaseIndex = $this->structure->getFlat()->getWeightIndex()[$toLayerNumber];
		$count = $this->structure->getFlat()->getLayerCounts()[$fromLayerNumber];
		$weightIndex = $weightBaseIndex + $fromNeuron
		+ ($toNeuron * $count);

		$this->structure->getFlat()->getWeights()[$weightIndex] = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toString() {
		$builder = "[BasicNetwork: Layers=";
		$layers = 0;
		if ($this->structure->getFlat() == null) {
			$layers = $this->structure->getLayers()->size();
		} else {
			$layers = count($this->structure->getFlat()->getLayerCounts());
		}

		$builder .= $layers;
		$builder .= "]";
		return $builder;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateProperties() {
		$this->structure->updateProperties();

	}

	/**
	 * Validate the the specified targetLayer and neuron are valid.
	 *
	 * @param int targetLayer
	 *            The target layer.
	 * @param int neuron
	 *            The target neuron.
	 */
	public function validateNeuron($targetLayer, $neuron) {
		if (($targetLayer < 0) || ($targetLayer >= $this->getLayerCount())) {
			throw new NeuralNetworkError("Invalid layer count: " + $targetLayer);
		}

		if (($neuron < 0) || ($neuron >= $this->getLayerTotalNeuronCount($targetLayer))) {
			throw new NeuralNetworkError("Invalid neuron number: " + $neuron);
		}
	}

	/**
	 * Determine the winner for the specified input. This is the number of the
	 * winning neuron.
	 *
	 * @param MLDATA input
	 *            The input patter to present to the neural network.
	 * @return int The winning neuron.
	 */
	public function winner( MLData $input) {
		$output = $this->compute($input);
		return EngineArray\maxIndex($output->getData());
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFactoryType() {
		return MLMethodFactory\TYPE_FEEDFORWARD;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFactoryArchitecture() {
		$result = '';

		// ?:B->SIGMOID->4:B->SIGMOID->?

		for ($currentLayer = 0; $currentLayer < $this->getLayerCount(); ++$currentLayer) {

			// need arrow from prvious levels?
			if ($currentLayer > 0) {
				$result .= "->";
			}

			// handle activation function
			if ($currentLayer > 0 && $this->getActivation($currentLayer) != null) {
				$activationFunction = $this->getActivation($currentLayer);
				$result .= $activationFunction->getFactoryCode();
				$result .= "->";
			}

			$result .= $this.getLayerNeuronCount($currentLayer);
			if ($this->isLayerBiased($currentLayer)) {
				$result->append(":B");
			}
		}

		return $result.toString();
	}

}