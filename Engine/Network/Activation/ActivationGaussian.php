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
namespace Encog\Engine\Network\Activation;

class ActivationGaussian implements ActivationFunction {
	
	/**
	 * The parameters.
	 */
	private $params = array();

	public function __construct() {}

	/**
	 *
	 * @return ActivationFunction The object cloned.
	 */
	public function __clone() {
		return new ActivationGaussian();
	}

	/**
	 *
	 * @return bool Return true, gaussian has a derivative.
	 */
	public function hasDerivative() {
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function activationFunction( array &$x, $start, $size ) {
		for( $i = $start; $i < $start + $size; ++$i ) {
			$x[$i] = exp( - pow( 2.5 * $x[$i], 2.0 ) );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function derivativeFunction( $b, $a ) {
		return exp( pow( 2.5 * $b, 2.0 ) * 12.5 * $b );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParamNames() {
		$result = [];
		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParams() {
		return $params;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setParam( $index, $value ) {
		$this->params[$index] = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFactoryCode() {
		return ActivationUtil\generateActivationFactory( MLActivationFactor\AF_GAUSSIAN, $this );
	}
}