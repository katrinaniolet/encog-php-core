<?php
/*
 * Encog(tm) Core v3.3 - PHP Version
 * https://github.com/katrinaniolet/encog-php-core
 *
 * http://www.heatonresearch.com/encog/
 * https://github.com/encog/encog-java-core
 *
 * Copyright 2008-2014 Heaton Research, Inc.
 * PHP port by Katrina Niolet <katria@kf5utn.net>
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
 * See the License for the specific language governing pe/**
 * This interface allows various activation functions to be used with the neural
 * network. Activation functions are applied to the output from each layer of a
 * neural network. Activation functions scale the output into the desired range.
 *
 * Methods are provided both to process the activation function, as well as the
 * derivative of the function. Some training algorithms, particularly back
 * propagation, require that it be possible to take the derivative of the
 * activation function.
 *
 * Not all activation functions support derivatives. If you implement an
 * activation function that is not derivable then an exception should be thrown
 * inside of the derivativeFunction method implementation.
 *
 * Non-derivable activation functions are perfectly valid, they simply cannot be
 * used with every training algorithm.
 */
namespace Encog\ML\Data;

use \Encog\Util\KMeans\CentroidFactory;

require_once("Util/KMeans/CentroidFactory.php");

/**
 * Defines an array of data. This is an array of double values that could be
 * used either for input data, actual output data or ideal output data.
 *
 * @author jheaton
 */
interface MLData extends CentroidFactory {

	/**
	 * Add a value to the specified index.
	 *
	 * @param int index
	 *            The index to add to.
	 * @param double value
	 *            The value to add.
	 */
	public function add($index, $value);

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
	 * @return double[] All of the elements as an array.
	*/
	public function getData();

	/**
	 * Get the element specified index value.
	 *
	 * @param int index
	 *            The index to read.
	 * @return double The value at the specified inedx.
	*/
	public function getData($index);

	/**
	 * Set all of the data as an array of doubles.
	 *
	 * @param double[] data
	 *            An array of doubles.
	*/
	public function setData(array $data);

	/**
	 * Set the specified element.
	 *
	 * @param int index
	 *            The index to set.
	 * @param double d
	 *            The data for the specified element.
	*/
	public function setData($index, $d);

	/**
	 * @return int How many elements are stored in this object.
	*/
	public function size();

}
