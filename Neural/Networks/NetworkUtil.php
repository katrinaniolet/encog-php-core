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
namespace Encog\Neural\Networks\NetworkUtil;

use \Encog\Engine\Network\Activation\ActivationSigmoid;
use \Encog\MathUtil\Randomize\ConsistentRandomizer;
use \Encog\MathUtil\Randomize\NguyenWidrowRandomizer;
use \Encog\ML\MLError;
use \Encog\ML\Data\MLDataSet;
use \Encog\ML\Train\MLTrain;
use \Encog\Neural\Freeform\FreeformLayer;
use \Encog\Neural\Freeform\FreeformNetwork;
use \Encog\Neural\Networks\Layers\BasicLayer;

function createXORNetworkUntrained()
{
	// random matrix data.  However, it provides a constant starting point
	// for the unit tests.
	$network = new BasicNetwork();
	$network->addLayer(new BasicLayer(null,true,2));
	$network->addLayer(new BasicLayer(new ActivationSigmoid(),true,4));
	$network->addLayer(new BasicLayer(new ActivationSigmoid(),false,1));
	$network->getStructure()->finalizeStructure();

	(new ConsistentRandomizer(-1,1))->randomize($network);

	return $network;
}

function createXORNetworknNguyenWidrowUntrained()
{
	// random matrix data.  However, it provides a constant starting point
	// for the unit tests.

	$network = new BasicNetwork();
	$network->addLayer(new BasicLayer(null,true,2));
	$network->addLayer(new BasicLayer(new ActivationSigmoid(),true,3));
	$network->addLayer(new BasicLayer(new ActivationSigmoid(),false,3));
	$network->addLayer(new BasicLayer(null,false,1));
	$network->getStructure().finalizeStructure();
	(new NguyenWidrowRandomizer())->randomize( $network );

	return $network;
}

function testTraining(MLDataSet $dataSet, MLTrain $train, $requiredImprove)
{
	$train->iteration();
	$error1 = $train->getError();

	for($i=0;$i<10;++$i)
		$train->iteration();

	$error2 = $train->getError();

	if( $train->getMethod() instanceof MLError ) {
		$error3 = $train->getMethod()->calculateError($dataSet);
		$improve = ($error1-$error3)/$error1;
		assert($improve>=$requiredImprove,"Improve rate too low for " . get_class($train) .
				",Improve=".$improve.",Needed=".$requiredImprove);
	}

	$improve = ($error1-$error2)/$error1;
	assert($improve>=$requiredImprove,"Improve rate too low for " . get_claa($train) .
			",Improve=".$improve.",Needed=".$requiredImprove);
}

function createXORFreeformNetworkUntrained() {
	$network = new FreeformNetwork();
	$inputLayer = $network->createInputLayer(2);
	$hiddenLayer1 = $network->createLayer(3);
	$outputLayer = $network->createOutputLayer(1);

	$network->connectLayers($inputLayer, $hiddenLayer1, new ActivationSigmoid(), 1.0, false);
	$network->connectLayers($hiddenLayer1, $outputLayer, new ActivationSigmoid(), 1.0, false);

	$network->reset(1000);
	return $network;
}