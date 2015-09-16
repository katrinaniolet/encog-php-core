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
namespace Encog\ML\Data\Basic;

use \Encog\EncogError;
use \Encog\ML\Data\MLData;
use \Encog\ML\Data\MLDataPair;
use \Encog\Util\Format;
use \Encog\Util\KMeans\Centroid;

require_once ("ML/Data/MLDataPair.php");

/**
 * A basic implementation of the MLDataPair interface.
 * This implementation
 * simply holds and input and ideal MLData object.
 *
 * For supervised training both input and ideal should be specified.
 *
 * For unsupervised training the input property should be valid, however the
 * ideal property should contain null.
 */
class BasicMLDataPair implements MLDataPair {
	
	/**
	 * The significance.
	 */
	private $significance = 1.0;

	/**
	 * Create a new data pair object of the correct size for the machine
	 * learning method that is being trained.
	 * This object will be passed to the
	 * getPair method to allow the data pair objects to be copied to it.
	 *
	 * @param
	 *        	int inputSize
	 *        	The size of the input data.
	 * @param
	 *        	int idealSize
	 *        	The size of the ideal data.
	 * @return MLDataPair A new data pair object.
	 */
	public static function createPair( $inputSize, $idealSize ) {
		$result = null;
		
		if( $idealSize > 0 ) {
			$result = new BasicMLDataPair( new BasicMLData( $inputSize ), new BasicMLData( $idealSize ) );
		}
		else {
			$result = new BasicMLDataPair( new BasicMLData( $inputSize ) );
		}
		
		return $result;
	}
	
	/**
	 * The the expected output from the machine learning method, or null for
	 * unsupervised training.
	 */
	private $ideal = null;
	
	/**
	 * The training input to the machine learning method.
	 */
	private $inpu = null;

	/**
	 * Construct the object with only input.
	 * If this constructor is used, then
	 * unsupervised training is being used.
	 *
	 * @param
	 *        	MLData theInput
	 *        	The input to the machine learning method.
	 */
	/**
	 * Construct a BasicMLDataPair class with the specified input and ideal
	 * values.
	 *
	 * @param
	 *        	theInput
	 *        	The input to the machine learning method.
	 * @param
	 *        	theIdeal
	 *        	The expected results from the machine learning method.
	 */
	// TODO(katrina) documentation, merged methods
	public function __construct( MLData $theInput, MLData $ideal = null ) {
		$this->input = $theInput;
		$this->ideal = $ideal;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIdeal() {
		return $this->ideal;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIdealArray() {
		if( $this->ideal == null ) {
			return null;
		}
		return $this->ideal->getData();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getInput() {
		return $this->input;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getInputArray() {
		return $this->input->getData();
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSupervised() {
		return $this->ideal != null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setIdealArray( array $data ) {
		$this->ideal->setData( $data );
	}

	/**
	 * {@inheritDoc}
	 */
	public function setInputArray( array $data ) {
		$this->input->setData( $data );
	}

	/**
	 * {@inheritDoc}
	 */
	public function toString() {
		$builder = "[";
		$builder .= get_class( $this );
		$builder .= ":";
		$builder .= "Input:";
		$builder .= getInput();
		$builder .= "Ideal:";
		$builder .= getIdeal();
		$builder .= ",";
		$builder .= "Significance:";
		// TODO(katrina) format as percent
		$builder .= $this->significance;
		$builder .= "]";
		return $builder;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSignificance() {
		return $significance;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setSignificance( $significance ) {
		$this->significance = $significance;
	}

	/**
	 * {@inheritDoc}
	 */
	public function createCentroid() {
		if( ! ($this->input instanceof BasicMLData) ) {
			throw new EncogError( "The input data type of " + $this->input->getClass()->getSimpleName() + " must be BasicMLData." );
		}
		return new BasicMLDataPairCentroid( $this );
	}
}