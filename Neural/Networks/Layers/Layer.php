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
namespace Encog\Neural\Networks\Layers;

use \Encog\Engine\Network\Activation\ActivationFunction;
use \Encog\Neural\Networks\BasicNetwork;

/**
 * This interface defines all necessary methods for a neural network layer.
 */
interface Layer {

	/**
	 *
	 * @return ActivationFunction The activation function used for this layer.
	 */
	public function getActivationFunction();

	/**
	 *
	 * @return Basic Network The network that this layer is attached to.
	 */
	public function getNetwork();

	/**
	 *
	 * @return int The neuron count.
	 */
	public function getNeuronCount();

	/**
	 *
	 * @return bool True if this layer has a bias.
	 */
	public function hasBias();

	/**
	 * Set the network that this layer belongs to.
	 *
	 * @param
	 *        	BasicNetwork network
	 *        	The network.
	 */
	public function setNetwork( BasicNetwork $network );

	/**
	 * Most layer types will default this value to one.
	 * However, it is possible
	 * to use other values. This is the activation that will be passed over the
	 * bias weights to the inputs of this layer. See the Layer interface
	 * documentation for more information on how Encog handles bias values.
	 *
	 * @param
	 *        	double activation
	 *        	The activation for the bias weights.
	 */
	public function setBiasActivation( $activation );

	/**
	 * Most layer types will default this value to one.
	 * However, it is possible
	 * to use other values. This is the activation that will be passed over the
	 * bias weights to the inputs of this layer. See the Layer interface
	 * documentation for more information on how Encog handles bias values.
	 *
	 * @return double The bias activation for this layer.
	 */
	public function getBiasActivation();

	/**
	 * Set the activation function.
	 *
	 * @param
	 *        	ActivationFunction activation The activation function.
	 */
	public function setActivation( ActivationFunction $activation );
}