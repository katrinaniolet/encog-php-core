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

use \Encog\ML\Factory\MLActivationFactory;
use \Encog\Util\Obj\ActivationUtil;

require_once("Engine/Network/Activation/ActivationFunction.php");
require_once("ML/Factory/MLActivationFactory.php");
require_once("Util/Obj/ActivationUtil.php");

/**
 * BiPolar activation function. This will scale the neural data into the bipolar
 * range. Greater than zero becomes 1, less than or equal to zero becomes -1.
 */
class ActivationBiPolar implements ActivationFunction {

	/**
	 * The parameters.
	 * @var double[] $params
	 */
	private $params = array();

	/**
	 * Construct the bipolar activation function.
	 */
	public function __construct() {
	}

	/**
	 * {@inheritDoc}
	 */
	public function activationFunction(array &$x, $start, $size) {

		for ($i = $start; $i < $start + $size; ++$i) {
			if ($x[$i] > 0) {
				$x[$i] = 1;
			} else {
				$x[$i] = -1;
			}
		}
	}

	/**
	 * @return ActivationBiPolar The object cloned.
	 */
	public function __clone() {
		return new ActivationBiPolar();
	}

	/**
	 * {@inheritDoc}
	 */
	public function derivativeFunction($b, $a) {
		return 1;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParamNames() {
		$result = array();
		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * @return bool Return true, bipolar has a 1 for derivative.
	 */
	public function hasDerivative() {
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setParam($index, $value) {
		$this->params[$index] = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFactoryCode() {
		return ActivationUtil\generateActivationFactory(MLActivationFactory\AF_BIPOLAR, $this);
	}
}
