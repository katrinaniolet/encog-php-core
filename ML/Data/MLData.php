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
namespace Encog\ML\Data;

use \Encog\Util\KMeans\CentroidFactory;

require_once ("Util/KMeans/CentroidFactory.php");

/**
 * Defines an array of data.
 * This is an array of double values that could be
 * used either for input data, actual output data or ideal output data.
 *
 * @author jheaton
 */
interface MLData extends CentroidFactory {

	/**
	 * Add a value to the specified index.
	 *
	 * @param
	 *        	int index
	 *        	The index to add to.
	 * @param
	 *        	double value
	 *        	The value to add.
	 */
	public function add( $index, $value );

	/**
	 * Clear any data to zero.
	 */
	public function clear();

	/**
	 * Clone this object.
	 *
	 * @return MLData A cloned version of this object.
	 */
	public function __clone();

	/**
	 * Get the element specified index value.
	 *
	 * @param
	 *        	int index The index to read, or -1 to get all data (the default)
	 * @return double The value at the specified inedx.
	 */
	public function getData( $index = -1 );

	/**
	 * Set all of the data as an array of doubles.
	 *
	 * @param
	 *        	array data
	 *        	An array of values.
	 */
	public function setDataFromArray( array $data );

	/**
	 * Set the specified element.
	 *
	 * @param
	 *        	int index
	 *        	The index to set.
	 * @param
	 *        	double d
	 *        	The data for the specified element.
	 */
	public function setData( $index, $d );

	/**
	 *
	 * @return int How many elements are stored in this object.
	 */
	public function size();
}
