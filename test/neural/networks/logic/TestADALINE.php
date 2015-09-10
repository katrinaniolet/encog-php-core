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
namespace Test\Neural\Networks\Logic;

use \Encog\ML\Data\MLDataSet;
use \Encog\ML\Data\Basic\BasicMLDataSet;
use \Encog\ML\Train\MLTrain;
use \Encog\Neural\Networks\BasicNetwork;
use \Encog\Neural\Networks\NetworkUtil;
use \Encog\Neural\Networks\Training\Simple\TrainAdaline;
use \Encog\Neural\Pattern\ADALINEPattern;
use \Test\Neural\Networks\TestXOR;

require_once ("Neural/Pattern/ADALINEPattern.php");

class TestADALINE extends \PHPUnit_Framework_TestCase {

	public function testAdalineNet() {
		$pattern = new ADALINEPattern();
		$pattern->setInputNeurons( 2 );
		$pattern->setOutputNeurons( 1 );
		$network = $pattern->generate();
		
		// train it
		$training = new BasicMLDataSet( TestXOR\XOR_INPUT, TestXOR\XOR_IDEAL );
		$train = new TrainAdaline( $network, $training, 0.01 );
		NetworkUtil\testTraining( $training, $train, 0.01 );
	}
}