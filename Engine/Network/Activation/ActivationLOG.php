<?php
/*
 * Encog(tm) Core v3.3 - PHP Version
 * https://github.com/katrinaniolet/encog-php-core
 *
 * http://www.heatonresearch.com/encog/
 * https://github.com/encog/encog-java-core
 *
 * Copyright 2008-2014 Heaton Research, Inc.
 * PHP port by Katrina Niolet <katria@kf5utn.net>
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

use \Encog\MathUtil\BoundMath;
use \Encog\ML\Factory\MLActivationFactory;
use \Encog\Util\Obj\ActivationUtil;

require_once("MathUtil/BoundMath.php");
require_once("ML/Factory/MLActivationFactory.php");
require_once("Util/Obj/ActivationUtil.php");

/**
 * An activation function based on the logarithm function.
 *
 * This type of activation function can be useful to prevent saturation. A
 * hidden node of a neural network is said to be saturated on a given set of
 * inputs when its output is approximately 1 or -1 "most of the time". If this
 * phenomena occurs during training then the learning of the network can be
 * slowed significantly since the error surface is very at in this instance.
 */
class ActivationLOG implements ActivationFunction {

	/**
	 * The parameters.
	 * @var double[]
	 */
	private $params = array();

	/**
	 * Construct the activation function.
	 */
	public function __construct() {
	}

	/**
	 * {@inheritDoc}
	 */
	public function activationFunction(array &$x, $start,$size) {

		for ($i = $start; $i < $start + $size; ++$i) {
			if ($x[$i] >= 0) {
				$x[$i] = BoundMath\log(1 + $x[$i]);
			} else {
				$x[$i] = -BoundMath\log(1 - $x[$i]);
			}
		}
	}

	/**
	 * @return ActivationFunction The object cloned.
	 */
	public function __clone() {
		return new ActivationLOG();
	}

	/**
	 * {@inheritDoc}
	 */
	public function derivativeFunction($b, $a) {
		if ($b >= 0) {
			return 1 / (1 + $b);
		} else {
			return 1 / (1 - $b);
		}
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
	 * @return boolean Return true, log has a derivative.
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
		return ActivationUtil\generateActivationFactory(MLActivationFactory\AF_LOG, $this);
	}

	public function getLabel() {
		return "log";
	}
}