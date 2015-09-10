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

require_once("ML/Factory/MLActivationFactory.php");
require_once("Util/Obj/ActivationUtil.php");

/**
 * The Linear layer is really not an activation function at all. The input is
 * simply passed on, unmodified, to the output. This activation function is
 * primarily theoretical and of little actual use. Usually an activation
 * function that scales between 0 and 1 or -1 and 1 should be used.
 */
class ActivationLinear implements ActivationFunction {

	/**
	 * Default empty parameters.
	 * @var double[]
	 */
	public static $P = array();

	/**
	 * Default empty parameters.
	 * @var string[]
	*/
	public static $N = array();

	/**
	 * The parameters.
	 * @var double[]
	 */
	private $params = array();

	/**
	 * Construct a linear activation function, with a slope of 1.
	 */
	public function __construct() {
	}

	/**
	 * {@inheritDoc}
	 */
	public function activationFunction(array &$x, $start,$size) {
	}

	/**
	 * @return ActivationFunction The object cloned.
	 */
	public function __clone() {
		return new ActivationLinear();
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
		$result = [];
		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * @return boolean Return true, linear has a 1 derivative.
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
		return ActivationUtil\generateActivationFactory(MLActivationFactory\AF_LINEAR, $this);
	}

	public function getLabel() {
		return "linear";
	}
}