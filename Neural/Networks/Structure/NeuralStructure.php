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
namespace Encog\Neural\Networks\Structure;

use \Encog\Engine\Network\Activation\ActivationLinear;
use \Encog\Neural\NeuralNetworkError;
use \Encog\Neural\Flat\FlatLayer;
use \Encog\Neural\Flat\FlatNetwork;
use \Encog\Neural\Networks\BasicNetwork;
use \Encog\Neural\Networks\Layers\BasicLayer;
use \Encog\Neural\Networks\Layers\Layer;

require_once("Engine/Network/Activation/ActivationLinear.php");
require_once("Neural/NeuralNetworkError.php");
require_once("Neural/Flat/FlatLayer.php");
require_once("Neural/Flat/FlatNetwork.php");
require_once("Neural/Networks/BasicNetwork.php");
require_once("Neural/Networks/Layers/BasicLayer.php");
require_once("Neural/Networks/Layers/Layer.php");


/**
 * Holds "cached" information about the structure of the neural network. This is
 * a very good performance boost since the neural network does not need to
 * traverse itself each time a complete collection of layers or synapses is
 * needed.
 */
class NeuralStructure {

	/**
	 * The layers in this neural network.
	 * @var Layer[] layers
	 */
	private $layers = array();

	/**
	 * The neural network this class belongs to.
	 * @var BasicNetwork network
	*/
	private $network = null;

	/**
	 * The limit, below which a connection is treated as zero.
	 * @var double connectionLimit
	 */
	private $connectionLimit = 0.0;

	/**
	 * Are connections limited?
	 * @var bool connectionLimited
	 */
	private $connectionLimited = false;

	/**
	 * The flattened form of the network.
	 * @var FlatNetwork flat
	 */
	private $flat = null;

	/**
	 * Construct a structure object for the specified network.
	 *
	 * @param BasicNetwork network
	 *            The network to construct a structure for.
	 */
	public function __construct(BasicNetwork $network) {
		$this->network = $network;
	}

	/**
	 * Calculate the size that an array should be to hold all of the weights and
	 * bias values.
	 *
	 * @return int The size of the calculated array.
	 */
	public function calculateSize() {
		return NetworkCODEC\networkSize($this->network);
	}

	/**
	 * Enforce that all connections are above the connection limit. Any
	 * connections below this limit will be severed.
	 */
	public function enforceLimit() {
		if (!$this->connectionLimited) {
			return;
		}

		$weights = $this->flat->getWeights();

		for ($i = 0; $i < count($weights); ++$i) {
			if (\abs($weights[$i]) < $this->connectionLimit) {
				$weights[$i] = 0;
			}
		}
	}

	/**
	 * Parse/finalize the limit value for connections.
	 */
	public function finalizeLimit() {
		// see if there is a connection limit imposed
		$limit = $this->network->getPropertyString(BasicNetwork::TAG_LIMIT);
		if ($limit != null) {
			try {
				$this->connectionLimited = true;
				$this->connectionLimit = doubleval(limit);
				$this->enforceLimit();
			} catch (NumberFormatException $e) {
				throw new NeuralNetworkError("Invalid property("
						+ BasicNetwork::TAG_LIMIT + "):" + $limit);
			}
		} else {
			$this->connectionLimited = false;
			$this->connectionLimit = 0;
		}
	}

	/**
	 * Build the synapse and layer structure. This method should be called after
	 * you are done adding layers to a network, or change the network's logic
	 * property.
	 */
	public function finalizeStructure() {

		if (count($this->layers) < 2) {
			throw new NeuralNetworkError(
					"There must be at least two layers before the structure is finalized.");
		}

		$flatLayers = array();

		for ($i = 0; $i < count($this->layers); ++$i) {
			$layer = $this->layers[$i];
			if ($layer->getActivation() == null) {
				$layer->setActivation(new ActivationLinear());
			}

			$flatLayers[$i] = $layer;
		}

		$this->flat = new FlatNetwork($flatLayers);

		$this->finalizeLimit();
		$this->layers = array();
		$this->enforceLimit();
	}

	/**
	 * @return double The connection limit.
	 */
	public function getConnectionLimit() {
		return $this->connectionLimit;
	}

	/**
	 * @return FlatNetwork The flat network.
	 */
	public function getFlat() {
		$this->requireFlat();
		return $this->flat;
	}

	/**
	 * @return &Layer[] The layers in this neural network.
	 */
	public function &getLayers() {
		return $this->layers;
	}

	/**
	 * @return BasicNetwork The network this structure belongs to.
	 */
	public function getNetwork() {
		return $this->network;
	}

	/**
	 * @return bool True if this is not a fully connected feedforward network.
	 */
	public function isConnectionLimited() {
		return $this->connectionLimited;
	}

	/**
	 * Throw an error if there is no flat network.
	 */
	public function requireFlat() {
		if ($this->flat == null) {
			throw new NeuralNetworkError(
					"Must call finalizeStructure before using this network.");
		}
	}

	/**
	 * Set the flat network.
	 * @param FlatNetwork flat The flat network.
	 */
	public function setFlat(FlatNetwork $flat) {
		$this->flat = $flat;
	}

	/**
	 * Update any properties from the property map.
	 */
	public function updateProperties() {
		if ($this->network->getProperties()->containsKey(BasicNetwork::TAG_LIMIT)) {
			$this->connectionLimit = $this->network->getPropertyDouble(BasicNetwork::TAG_LIMIT);
			$this->connectionLimited = true;
		} else {
			$this->connectionLimited = false;
			$this->connectionLimit = 0;
		}

		if ($this->flat != null) {
			$this->flat->setConnectionLimit($this->connectionLimit);
		}

	}

}