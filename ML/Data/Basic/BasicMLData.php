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

use \Encog\ML\Data\MLData;
use Encog\Util\KMmeans\Centroid;

/**
 * Basic implementation of the MLData interface that stores the data in an
 * array.
 */
class BasicMLData implements MLData {
	
	/**
	 * The data held by this object.
	 * 
	 * @var double[] data
	 */
	private $data = array();

	/**
	 * Construct this object with the specified data.
	 *
	 * @param
	 *        	d
	 *        	The data to construct this object with.
	 */
	/**
	 * Construct this object with blank data and a specified size.
	 *
	 * @param
	 *        	size
	 *        	The amount of data to store.
	 */
	/**
	 * Construct a new BasicMLData object from an existing one.
	 * This makes a
	 * copy of an array.
	 *
	 * @param
	 *        	d
	 *        	The object to be copied.
	 */
	// TODO(katrina) documentation, merged methods
	public function __construct( $d ) {
		if( is_array( $d ) ) {
			$this->data = $d;
		}
		else if( is_numeric( $d ) ) {
			$this->data = array_fill( 0, $d, 0.0 );
		}
		else if( is_object( $d ) && $d instanceof MLData ) {
			$this->data = $d->getData();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function add( $index, $value ) {
		$this->data[$index] += $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function clear() {
		for( $i = 0; $i < count( $this->data ); ++$i ) {
			$this->data[$i] = 0;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function __clone() {
		return new BasicMLData( $this );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getData( $index = -1 ) {
		if( $index < 0 )
			return $this->data;
		return $this->data[$index];
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDataFromArray( array $theData ) {
		$this->data = $theData;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setData( $index, $d ) {
		$this->data[$index] = $d;
	}

	/**
	 * {@inheritDoc}
	 */
	public function size() {
		return count( $this->data );
	}

	/**
	 * {@inheritDoc}
	 */
	public function toString() {
		$builder = "[";
		$builder .= $this->getClass()->getSimpleName();
		$builder .= ":";
		for( $i = 0; $i < count( $this->data ); ++$i ) {
			if( $i != 0 ) {
				$builder .= ',';
			}
			$builder .= $this->data[$i];
		}
		$builder .= "]";
		return $builder;
	}

	/**
	 * {@inheritDoc}
	 */
	public function createCentroid() {
		return new BasicMLDataCentroid( $this );
	}

	/**
	 * Add one data element to another.
	 * This does not modify the object.
	 * 
	 * @param
	 *        	MLData o The other data element
	 * @return MLData The result.
	 */
	public function plus( MLData $o ) {
		if( $this->size() != $o->size() )
			throw new IllegalArgumentException();
		
		$result = new BasicMLData( $this->size() );
		for( $i = 0; $i < $this->size(); ++$i )
			$result->setData( $i, $this->getData( $i ) + $o->getData( $i ) );
		
		return $result;
	}

	/**
	 * Multiply one data element with another.
	 * This does not modify the object.
	 * 
	 * @param
	 *        	double d The other data element
	 * @return MLData The result.
	 */
	public function times( $d ) {
		$result = new BasicMLData( $this->size() );
		
		for( $i = 0; $i < $this->size(); ++$i )
			$result->setData( $i, $this->getData( $i ) * $d );
		
		return $result;
	}

	/**
	 * Subtract one data element from another.
	 * This does not modify the object.
	 * 
	 * @param
	 *        	MLData o The other data element
	 * @return MLData The result.
	 */
	public function minus( MLData $o ) {
		if( $this->size() != $o->size() )
			throw new IllegalArgumentException();
		
		$result = new BasicMLData( $this->size() );
		for( $i = 0; $i < $this->size(); ++$i )
			$result->setData( $i, $this->getData( $i ) - $o->getData( $i ) );
		
		return $result;
	}

	/**
	 * Apply a thresholding function to the data elements.
	 * This does not modify the object.
	 * 
	 * @param
	 *        	double thesdholdValue the value to which elements are compared
	 * @param
	 *        	double lowValue the value to use if <= threshold
	 * @param
	 *        	double highValue the value to use if > threshold
	 * @return MLData The result.
	 */
	public function threshold( $thresholdValue, $lowValue, $highValue ) {
		$result = new BasicMLData( $size() );
		for( $i = 0; $i < $this->size(); ++$i )
			if( $this->getData( $i ) > $thresholdValue )
				$result->setData( $i, $highValue );
			else
				$result->setData( $i, $lowValue );
		return $result;
	}
}