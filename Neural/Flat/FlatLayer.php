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

use \Encog;
use \Encog\Engine\Network\Activation\ActivationFunction;

require_once ("Engine\Network\Activation\ActivationFunction.php");

/**
 * Used to configure a flat layer.
 * Flat layers are not kept by a Flat Network,
 * beyond setup.
 */
class FlatLayer {
	
	/**
	 * The activation function.
	 * 
	 * @var ActivationFunction $activation
	 */
	private $activation;
	
	/**
	 * The neuron count.
	 * 
	 * @var int $count
	 */
	private $count = 0;
	
	/**
	 * The bias activation, usually 1 for bias or 0 for no bias.
	 * 
	 * @var double $biasActivation
	 */
	private $biasActivation;
	
	/**
	 * The layer that feeds this layer's context.
	 * 
	 * @var FlatLayer $contextFedBy
	 */
	private $contextFedBy = null;

	/**
	 * Construct a flat layer.
	 *
	 * @param ActivationFunction $activation
	 *        	The activation function.
	 * @param int $count
	 *        	The neuron count.
	 * @param double $biasActivation
	 *        	The bias activation.
	 */
	public function __construct( ActivationFunction $activation, $count, $biasActivation ) {
		$this->activation = $activation;
		$this->count = $count;
		$this->biasActivation = $biasActivation;
	}

	/**
	 *
	 * @return ActivationFunction the activation
	 */
	public function getActivation() {
		return $this->activation;
	}

	/**
	 *
	 * @return double Get the bias activation.
	 */
	public function getBiasActivation() {
		if( $this->hasBias() ) {
			return $this->biasActivation;
		}
		else {
			return 0;
		}
	}

	/**
	 *
	 * @return int The number of neurons our context is fed by.
	 */
	public function getContextCount() {
		if( $this->contextFedBy == null ) {
			return 0;
		}
		else {
			return $this->contextFedBy->getCount();
		}
	}

	/**
	 *
	 * @return FlatLayer The layer that feeds this layer's context.
	 */
	public function getContextFedBy() {
		return $this->contextFedBy;
	}

	/**
	 *
	 * @return int the count
	 */
	public function getCount() {
		return $this->count;
	}

	/**
	 *
	 * @return int The total number of neurons on this layer, includes context, bias
	 *         and regular.
	 */
	public function getTotalCount() {
		if( $this->contextFedBy == null ) {
			return $this->getCount() + ($this->hasBias() ? 1 : 0);
		}
		else {
			return $this->getCount() + ($this->hasBias() ? 1 : 0) + $this->contextFedBy->getCount();
		}
	}

	/**
	 *
	 * @return bool the bias
	 */
	public function hasBias() {
		return abs( $this->biasActivation ) > Encog\DEFAULT_DOUBLE_EQUAL;
	}

	/**
	 *
	 * @param ActivationFunction $activation
	 *        	the activation to set
	 */
	public function setActivation( ActivationFunction $activation ) {
		$this->activation = $activation;
	}

	/**
	 * Set the bias activation.
	 *
	 * @param
	 *        	double a
	 *        	The bias activation.
	 */
	public function setBiasActivation( $a ) {
		$this->biasActivation = $a;
	}

	/**
	 * Set the layer that this layer's context is fed by.
	 *
	 * @param
	 *        	from
	 *        	The layer feeding.
	 */
	public function setContextFedBy( FlatLayer $from ) {
		$this->contextFedBy = $from;
	}

	/**
	 * {@inheritDoc}
	 * 
	 * @return string
	 */
	public function toString() {
		$result = '[';
		$result .= get_class( $this );
		$result .= ": count=";
		$result .= $this->count;
		$result .= ",bias=";
		
		if( $this->hasBias() ) {
			$result .= $this->biasActivation->toString();
		}
		else {
			$result .= "false";
		}
		if( $this->contextFedBy != null ) {
			$result .= ",contextFed=";
			if( $this->contextFedBy == this ) {
				$result .= "itself";
			}
			else {
				$result .= $this->contextFedBy;
			}
		}
		$result .= "]";
		return $result;
	}
}