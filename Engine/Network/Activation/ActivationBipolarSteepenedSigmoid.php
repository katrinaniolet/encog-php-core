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

/**
 * The bipolar sigmoid activation function is like the regular sigmoid activation function,
 * except Bipolar sigmoid activation function.
 * TheOutput range is -1 to 1 instead of the more normal 0 to 1.
 *
 * This activation is typically part of a CPPN neural network, such as
 * HyperNEAT.
 *
 * The idea for this activation function was developed by Ken Stanley, of
 * the University of Texas at Austin.
 * http://www.cs.ucf.edu/~kstanley/
 */
class ActivationBipolarSteepenedSigmoid implements ActivationFunction {

	/**
	 * {@inheritDoc}
	 */
	public function activationFunction( array &$d, $start, $size ) {
		for( $i = $start; $i < $start + $size; ++$i ) {
			$d[$i] = (2.0 / (1.0 + exp( - 4.9 * $d[$i] ))) - 1.0;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function derivativeFunction( $b, $a ) {
		return 1;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasDerivative() {
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParams() {
		// TODO(katrina) was: return ActivationLinear.P;
		return array();
	}

	/**
	 * {@inheritDoc}
	 */
	public function setParam( $index, $value ) {}

	/**
	 * {@inheritDoc}
	 */
	public function getParamNames() {
		// TODO(katrina) was return ActivationLinear.N;
		return array();
	}

	/**
	 * {@inheritDoc}
	 */
	public function __clone() {
		return new ActivationBipolarSteepenedSigmoid();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFactoryCode() {
		return null;
	}
}