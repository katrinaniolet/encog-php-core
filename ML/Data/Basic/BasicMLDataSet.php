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
use \Encog\ML\Data\MLDataSet;
use \Encog\Util\EngineArray;
use \Encog\Util\Obj\ObjectCloner;

require_once ("ML/Data/Basic/BasicMLData.php");
require_once ("ML/Data/Basic/BasicMLDataPair.php");

/**
 * An iterator to be used with the BasicMLDataSet.
 * This iterator does not
 * support removes.
 */
class BasicMLIterator {
	
	/**
	 * The index that the iterator is currently at.
	 */
	private $currentIndex = 0;
	private $data;

	public function __construct( array &$data ) {
		$this->data = &$data;
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasNext() {
		return $this->currentIndex < count( $this->data );
	}

	/**
	 * {@inheritDoc}
	 */
	public function next() {
		if( ! $this->hasNext() ) {
			return null;
		}
		
		return $this->data[$this->currentIndex++];
	}

	/**
	 * {@inheritDoc}
	 */
	public function remove() {
		throw new EncogError( "Called remove, unsupported operation." );
	}
}

/**
 * Stores data in an ArrayList.
 * This class is memory based, so large enough
 * datasets could cause memory issues. Many other dataset types extend this
 * class.
 */
class BasicMLDataSet implements MLDataSet {
	
	/**
	 * The data held by this object.
	 */
	private $data = array();

	/**
	 * Construct a data set from an input and ideal array.
	 *
	 * @param
	 *        	double[][] input
	 *        	The input into the machine learning method for training.
	 * @param
	 *        	double[][] ideal
	 *        	The ideal output for training.
	 */
	/**
	 * Construct a data set from an already created list.
	 * Mostly used to
	 * duplicate this class.
	 *
	 * @param
	 *        	&MLDataPair[] theData
	 *        	The data to use.
	 */
	// TODO(katrina) documentation, merged methods
	public function __construct( array &$input, array $ideal = null ) {
		if( is_null( $ideal ) && (count( $input ) > 0) && ($input[0] instanceof MLDataPair) ) {
			$this->data = &$input;
		}
		else if( $ideal != null ) {
			for( $i = 0; $i < count( $input ); ++$i ) {
				$inputData = new BasicMLData( $input[$i] );
				$idealData = new BasicMLData( $ideal[$i] );
				$this->add( $inputData, $idealData );
			}
		}
		else {
			foreach( $input as $element ) {
				$inputData = new BasicMLData( $element );
				$this->add( $inputData );
			}
		}
	}

	/**
	 * Copy whatever dataset type is specified into a memory dataset.
	 *
	 * @param
	 *        	MLDataSet set
	 *        	The dataset to copy.
	 */
	public function copy( MLDataSet $set ) {
		$inputCount = $set->getInputSize();
		$idealCount = $set->getIdealSize();
		
		foreach( $set as $pair ) {
			
			$input = null;
			$ideal = null;
			
			if( $inputCount > 0 ) {
				$input = new BasicMLData( $inputCount );
				EngineArray\arrayCopy( $pair->getInputArray(), $input->getData() );
			}
			
			if( $idealCount > 0 ) {
				$ideal = new BasicMLData( $idealCount );
				EngineArray\arrayCopy( $pair->getIdealArray(), $ideal->getData() );
			}
			
			$this->add( new BasicMLDataPair( $input, $ideal ) );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function add( $inputData, MLData $idealData = null ) {
		if( $inputData instanceof MLData ) {
			$pair = new BasicMLDataPair( $inputData, $idealData );
			array_push( $this->data, $pair );
		}
		else if( $inputData instanceof MLDataPair ) {
			array_push( $this->data, $inputData );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function __clone() {
		// TODO(katrina) return ObjectCloner.deepCopy(this);
	}

	/**
	 * {@inheritDoc}
	 */
	public function close() {
		// nothing to close
	}

	/**
	 * Get the data held by this container.
	 *
	 * @return MLDataPair[] the data
	 */
	public function &getData() {
		return $this->data;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIdealSize() {
		if( count( $this->data ) == 0 ) {
			return 0;
		}
		$first = $this->data[0];
		if( $first->getIdeal() == null ) {
			return 0;
		}
		
		return $first->getIdeal()->size();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getInputSize() {
		if( count( $this->data ) == 0 ) {
			return 0;
		}
		$first = $this->data[0];
		return $first->getInput()->size();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRecord( $index, MLDataPair $pair ) {
		$source = $this->data[index];
		$pair->setInputArray( $source->getInputArray() );
		if( $pair->getIdealArray() != null ) {
			$pair->setIdealArray( $source->getIdealArray() );
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRecordCount() {
		return $this->data->size();
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSupervised() {
		if( count( $this->data ) == 0 ) {
			return false;
		}
		return $this->data[0]->isSupervised();
	}

	/**
	 * {@inheritDoc}
	 */
	public function iterator() {
		$result = new BasicMLIterator( $this->data );
		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function openAdditional() {
		return copy( $this->data );
	}

	/**
	 *
	 * @param
	 *        	theData
	 *        	the data to set
	 */
	public function setData( array &$theData ) {
		$this->data = &$theData;
	}

	/**
	 * Concert the data set to a list.
	 *
	 * @param
	 *        	theSet The data set to convert.
	 * @return The list.
	 */
	public function toList( MLDataSet $theSet ) {
		$list = array();
		foreach( $theSet as $pair ) {
			array_push( $list, $pair );
		}
		return $list;
	}

	/**
	 * {@inheritDoc}
	 */
	public function size() {
		return $this->getRecordCount();
	}

	/**
	 * {@inheritDoc}
	 */
	public function get( $index ) {
		return $this->data[index];
	}
}