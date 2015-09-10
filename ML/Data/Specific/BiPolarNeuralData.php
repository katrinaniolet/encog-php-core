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
namespace Encog\ML\Data\Specific;

use \Encog\MathUtil\Matrices\BiPolarUtil;
use \Encog\ML\Data\MLData;
use \Encog\ML\Data\MLDataError;

require_once ("MathUtil\Matrices\BiPolarUtil.php");
require_once ("ML\Data\MLData.php");
require_once ("ML\Data\MLDataError.php");

/**
 * A NeuralData implementation designed to work with bipolar data.
 * Bipolar data
 * contains two values. True is stored as 1, and false is stored as -1.
 *
 * @author jheaton
 *        
 */
class BiPolarNeuralData implements MLData {
	
	/**
	 * The data held by this object.
	 * 
	 * @var boolean[] $data
	 */
	private $data = array();

	/**
	 * Construct this object with the specified data.
	 *
	 * @param boolean[] $d
	 *        	The data to create this object with.
	 */
	public static function fromBooleanArray( array $d ) {
		$ret = new BiPolarNeuralData( count( $d ) );
		$ret->setDataFromArray( $d );
		return $ret;
	}

	/**
	 * Construct a data object with the specified size.
	 *
	 * @param
	 *        	int size
	 *        	The size of this data object.
	 */
	public function __construct( $size ) {
		$this->data = array_fill( 0, $size, false );
	}

	/**
	 * This will throw an error, as "add" is not supported for bipolar.
	 *
	 * @param
	 *        	int index
	 *        	Not used.
	 * @param
	 *        	double value
	 *        	Not used.
	 */
	public function add( $index, $value ) {
		throw new MLDataError( "Add is not supported for bipolar data." );
	}

	/**
	 * Set all data to false.
	 */
	public function clear() {
		for( $i = 0; $i < count( $this->data ); ++$i ) {
			$this->data[$i] = false;
		}
	}

	/**
	 *
	 * @return MLData A cloned copy of this object.
	 */
	public function __clone() {
		$result = new BiPolarNeuralData( $this->size() );
		$result->setDataFromArray( $this->data );
		return $result;
	}

	/**
	 * Get the specified data item as a boolean.
	 *
	 * @param int $i
	 *        	The index to read.
	 * @return boolean The specified data item's value.
	 */
	public function getBoolean( $i ) {
		return $this->data[$i];
	}

	/**
	 * Get the data held by the index.
	 *
	 * @param int $index
	 *        	The index to read or -1 to get the
	 *        	index as an array of doubles.
	 * @return double[] Return the data held at the specified index.
	 */
	public function getData( $index = -1 ) {
		if( $index >= 0 )
			return BiPolarUtil\bipolar2double( $this->data[$index] );
		return BiPolarUtil\bipolar2double( $this->data );
	}

	/**
	 * Store the array.
	 *
	 * @param array $theData
	 *        	The data to store.
	 */
	public function setDataFromArray( array $theData ) {
		if( isset( $theData[0] ) && is_bool( $theData[0] ) )
			$this->data = $theData;
		else if( isset( $theData[0] ) && is_numeric( $theData[0] ) )
			$this->data = BiPolarUtil\double2bipolar( $theData );
		else
			throw new MLDataError( "Unsupported array type, should be an array of doubles or booleans." );
	}

	/**
	 * Set the specified index of this object as a boolean.
	 * This value will be
	 * converted into bipolar.
	 *
	 * @param int $index
	 *        	The index to set.
	 * @param boolean $value
	 *        	The value to set.
	 */
	public function setData( $index, $value ) {
		if( is_bool( $value ) )
			$this->data[$index] = $value;
		else if( is_number( $value ) )
			$this->data[$index] = BiPolarUtil\double2bipolar( $value );
		else
			throw new MLDataError( "Unsupported value type, must be a double or a boolean." );
	}

	/**
	 * Get the size of this data object.
	 *
	 * @return int The size of this data object.
	 */
	public function size() {
		return count( $this->data );
	}

	/**
	 * {@inheritDoc}
	 * return string
	 */
	public function toString() {
		$result = "";
		$result .= '[';
		for( $i = 0; $i < $this->size(); ++$i ) {
			if( $this->getData( $i ) > 0 ) {
				$result .= 'T';
			}
			else {
				$result .= 'F';
			}
			if( $i != $this->size() - 1 ) {
				$result .= ',';
			}
		}
		$result .= ']';
		return $result;
	}

	/**
	 * Not supported.
	 * 
	 * @return null
	 */
	public function createCentroid() {
		return null;
	}
}