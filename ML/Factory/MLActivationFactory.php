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
namespace Encog\ML\Factory;

use \Encog;
use \Encog\Engine\Network\Activation\ActivationFunction;
use \Encog\Plugin\EncogPluginBase;
use \Encog\Plugin\EncogPluginService1;

require_once ("Encog.php");
require_once ("Engine/Network/Activation/ActivationFunction.php");
require_once ("Plugin/EncogPluginBase.php");
require_once ("Plugin/EncogPluginService1.php");

class MLActivationFactory {
	const AF_BIPOLAR = "bipolar";
	const AF_COMPETITIVE = "comp";
	const AF_GAUSSIAN = "gauss";
	const AF_LINEAR = "linear";
	const AF_LOG = "log";
	const AF_RAMP = "ramp";
	const AF_SIGMOID = "sigmoid";
	const AF_SSIGMOID = "ssigmoid";
	const AF_SIN = "sin";
	const AF_SOFTMAX = "softmax";
	const AF_STEP = "step";
	const AF_TANH = "tanh";

	/**
	 *
	 * @param string $fn        	
	 * @return ActivationFunction
	 */
	public function create( $fn ) {
		foreach( \Encog\getInstance()->getPlugins() as $plugin ) {
			if( $plugin instanceof EncogPluginService1 ) {
				$result = $plugin->createActivationFunction( $fn );
				if( $result != null ) {
					return $result;
				}
			}
		}
		return null;
	}
}