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
namespace Encog\MathUtil\Randomize;

use \Encog\MathUtil\Matrices\Matrix;
use \Encog\MathUtil\Randomize\Generate\GenerateRandom;
use \Encog\MathUtil\Randomize\Generate\MersenneTwisterGenerateRandom;
use \Encog\ML\MLEncodable;
use \Encog\ML\MLMethod;
use \Encog\Neural\Networks\BasicNetwork;

require_once ("MathUtil/Matrices/Matrix.php");
require_once ("MathUtil/Randomize/Generate/GenerateRandom.php");
require_once ("MathUtil/Randomize/Generate/MersenneTwisterGenerateRandom.php");
require_once ("ML/MLEncodable.php");
require_once ("ML/MLMethod.php");
require_once ("Neural/Networks/BasicNetwork.php");
require_once ("MathUtil/Randomize/Randomizer.php");

/**
 * Provides basic functionality that most randomizers will need.
 */
abstract class BasicRandomizer implements Randomizer {
	
	/**
	 * The random number generator.
	 * 
	 * @var GenerateRandom random
	 */
	private $random = null;

	/**
	 * Construct a random number generator with a random(current time) seed.
	 * If
	 * you want to set your own seed, just call "getRandom().setSeed".
	 */
	public function __construct() {
		$this->random = new MersenneTwisterGenerateRandom( microtime() );
	}

	/**
	 *
	 * @return GenerateRandom The random number generator in use. Use this to set the seed, if
	 *         desired.
	 */
	public function getRandom() {
		return $this->random;
	}

	/**
	 *
	 * @return double The next double.
	 */
	// TODO(katrina) merge
	public function nextDouble() {
		return $this->random->nextDouble();
	}

	/**
	 * Generate a random number in the specified range.
	 *
	 * @param
	 *        	double min
	 *        	The minimum value.
	 * @param
	 *        	double max
	 *        	The maximum value.
	 * @return double A random number.
	 */
	// TODO(katrina) merge
	public function nextDoubleRange( $min, $max ) {
		$range = $max - $min;
		return ($range * $this->random->nextDouble()) + $min;
	}

	/**
	 * Randomize one level of a neural network.
	 *
	 * @param
	 *        	BasicNetwork network
	 *        	The network to randomize
	 * @param
	 *        	int fromLayer
	 *        	The from level to randomize.
	 */
	public function randomizeNetwork( BasicNetwork $network, $fromLayer ) {
		$fromCount = $network->getLayerTotalNeuronCount( $fromLayer );
		$toCount = $network->getLayerNeuronCount( $fromLayer + 1 );
		
		for( $fromNeuron = 0; $fromNeuron < $fromCount; ++$fromNeuron ) {
			for( $toNeuron = 0; $toNeuron < $toCount; ++$toNeuron ) {
				$v = $network->getWeight( $fromLayer, $fromNeuron, $toNeuron );
				$v = $this->randomizeDouble( $v );
				$network->setWeight( $fromLayer, $fromNeuron, $toNeuron, $v );
			}
		}
	}

	/**
	 * Randomize the array based on an array, modify the array.
	 * Previous values
	 * may be used, or they may be discarded, depending on the randomizer.
	 *
	 * @param
	 *        	&double[] d
	 *        	An array to randomize.
	 */
	/**
	 * Randomize the array based on an array, modify the array.
	 * Previous values
	 * may be used, or they may be discarded, depending on the randomizer.
	 *
	 * @param
	 *        	d
	 *        	An array to randomize.
	 * @param
	 *        	begin
	 *        	The beginning element of the array.
	 * @param
	 *        	size
	 *        	The size of the array to copy.
	 */
	// TODO(katrina) documentation, merged methods
	public function randomizeArray( array &$d, $begin = 0, $size = -1 ) {
		if( $size == - 1 )
			$size = count( $d );
		for( $i = 0; $i < $size; ++$i ) {
			$d[$begin + $i] = $this->randomizeDouble( $d[$begin + $i] );
		}
	}

	/**
	 * Randomize the 2d array based on an array, modify the array.
	 * Previous
	 * values may be used, or they may be discarded, depending on the
	 * randomizer.
	 *
	 * @param
	 *        	double[][] d
	 *        	An array to randomize.
	 */
	public function randomizeMatrixArray( array &$d ) {
		for( $r = 0; $r < count( $d ); ++$r ) {
			for( $c = 0; $c < count( $d[0] ); ++$c ) {
				$d[$r][$c] = $this->randomizeDouble( $d[$r][$c] );
			}
		}
	}

	/**
	 * Randomize the matrix based on an array, modify the array.
	 * Previous values
	 * may be used, or they may be discarded, depending on the randomizer.
	 *
	 * @param
	 *        	Matrix m
	 *        	A matrix to randomize.
	 */
	public function randomizeMatrix( Matrix &$m ) {
		$d = $m->getData();
		for( $r = 0; $r < $m->getRows(); ++$r ) {
			for( $c = 0; $c < $m->getCols(); ++$c ) {
				$d[$r][$c] = $this->randomizeDouble( $d[$r][$c] );
			}
		}
		$m->setData( $d );
	}

	/**
	 * Randomize the synapses and biases in the basic network based on an array,
	 * modify the array.
	 * Previous values may be used, or they may be discarded,
	 * depending on the randomizer.
	 *
	 * @param
	 *        	MLMethod method
	 *        	A network to randomize.
	 */
	public function randomizeMLMethod( MLMethod $method ) {
		if( $method instanceof BasicNetwork ) {
			$network = $method;
			for( $i = 0; $i < $network->getLayerCount() - 1; ++$i ) {
				$this->randomizeNetwork( $network, $i );
			}
		}
		else if( $method instanceof MLEncodable ) {
			$encode = $method;
			$encoded = array_fill( 0, $encode->encodedArrayLength(), 0.0 );
			$encode->encodeToArray( $encoded );
			$this->randomizeArray( $encoded );
			$encode->decodeFromArray( $encoded );
		}
	}

	/**
	 *
	 * @param
	 *        	GenerateRandom theRandom
	 *        	the random to set
	 */
	public function setRandom( GenerateRandom $theRandom ) {
		$this->random = $theRandom;
	}
}