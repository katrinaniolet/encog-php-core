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
namespace Test\Neural\Networks\TestXOR;

const XOR_INPUT = [ 
		[ 
				0.0,
				0.0 ],
		[ 
				1.0,
				0.0 ],
		[ 
				0.0,
				1.0 ],
		[ 
				1.0,
				1.0 ] ];
const XOR_IDEAL = [ 
		[ 
				0.0 ],
		[ 
				1.0 ],
		[ 
				1.0 ],
		[ 
				0.0 ] ];
const XOR_IDEAL2 = [ 
		[ 
				1.0,
				0.0 ],
		[ 
				0.0,
				1.0 ],
		[ 
				1.0,
				0.0 ],
		[ 
				0.0,
				1.0 ] ];

/**
 *
 * @param
 *        	MLRegression network
 * @param
 *        	double tolerance
 * @return bool
 */
function verifyXOR( MLRegression $network, $tolerance ) {
	for( $trainingSet = 0; $trainingSet < count( XOR_IDEAL ); ++$trainingSet ) {
		$actual = $network->compute(new BasicMLData(XOR_INPUT[$trainingSet]));

		for($i=0;$i<count(XOR_IDEAL[0]);++$i)
		{
			$diff = \abs($actual->getData($i)-XOR_IDEAL[$trainingSet][$i]);
			if( $diff > $tolerance )
				return false;
		}
	}
	
	return true;
}

/**
 *
 * @return MLDataSet
 */
function createXORDataSet() {
	return new BasicMLDataSet( XOR_INPUT, XOR_IDEAL );
}

function testXORDataSet( MLDataSet $set ) {
	$row = 0;
	foreach( $set as $item ) {
		for($i=0;$i<count(XOR_INPUT[0]);++$i)
		{
			$this->assertEquals($item->getInput()->getData($i),
					XOR_INPUT[$row][$i]);
		}

		for($i=0;$i<count(XOR_IDEAL[0]);++$i)
		{
			$this->assertEquals($item->getIdeal()->getData($i),
					XOR_IDEAL[$row][$i]);
		}
		
		++$row;
	}
}

function createTrainedXOR() {
	$TRAINED_XOR_WEIGHTS = [ 
			25.427193285452972,
			- 26.92000502099534,
			20.76598054603445,
			- 12.921266548020219,
			- 0.9223427050161919,
			- 1.0588373209475093,
			- 3.80109620509867,
			3.1764938777876837,
			80.98981535707951,
			- 75.5552829139118,
			37.089976176012634,
			74.85166823997326,
			75.20561368661059,
			- 37.18307123471437,
			- 21.044949631177417,
			43.81815044327334,
			9.648991753485689 ];
	$network = EncogUtility\simpleFeedForward( 2, 4, 0, 1, false );
	NetworkCODEC\arrayToNetwork( $TRAINED_XOR_WEIGHTS, $network );
	return $network;
}

function createUnTrainedXOR() {
	$TRAINED_XOR_WEIGHTS = [ 
			- 0.427193285452972,
			0.92000502099534,
			- 0.76598054603445,
			- 0.921266548020219,
			- 0.9223427050161919,
			- 0.0588373209475093,
			- 0.80109620509867,
			3.1764938777876837,
			0.98981535707951,
			- 0.5552829139118,
			0.089976176012634,
			0.85166823997326,
			0.20561368661059,
			0.18307123471437,
			0.044949631177417,
			0.81815044327334,
			0.648991753485689 ];
	$network = EncogUtility\simpleFeedForward( 2, 4, 0, 1, false );
	$NetworkCODEC\arrayToNetwork( $TRAINED_XOR_WEIGHTS, $network );
	return $network;
}

function createThreeLayerNet() {
	$network = new BasicNetwork();
	$network->addLayer( new BasicLayer( 2 ) );
	$network->addLayer( new BasicLayer( 3 ) );
	$network->addLayer( new BasicLayer( 1 ) );
	$network->getStructure()->finalizeStructure();
	$network->reset();
	return $network;
}

function createNoisyXORDataSet( $count ) {
	$result = new BasicMLDataSet();
	for( $i = 0; $i < $count; ++$i ) {
		for( $j = 0; $j < 4; ++$j ) {
			$inputData = new BasicMLData(XOR_INPUT[$j]);
			$idealData = new BasicMLData(XOR_IDEAL[$j]);
			$pair = new BasicMLDataPair( $inputData, $idealData );
			$inputData->setData( 0, $inputData->getData( 0 ) + RangeRandomizer\randomize( - 0.1, 0.1 ) );
			$inputData->setData( 1, $inputData->getData( 1 ) + RangeRandomizer\randomize( - 0.1, 0.1 ) );
			$result->add( $pair );
		}
	}
	return $result;
}

function createTrainedFreeformXOR() {
	$network = createTrainedXOR();
	return new FreeformNetwork( $network );
}