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

namespace Encog\Mathutil\Randomize;

use \Encog\MathUtil\Matrices\Matrix;
use \Encog\MathUtil\Randomize\Generate\GenerateRandom;
use \Encog\ML\MLMethod;

/**
 * Defines the interface for a class that is capable of randomizing the weights
 * and bias values of a neural network.
 */
interface Randomizer {

	/**
	 * Randomize the synapses and bias values in the basic network based on an
	 * array, modify the array. Previous values may be used, or they may be
	 * discarded, depending on the randomizer.
	 *
	 * @param MLMethod network
	 *            A network to randomize.
	 */
	public function randomizeMLMethod(MLMethod $network);

	/**
	 * Starting with the specified number, randomize it to the degree specified
	 * by this randomizer. This could be a totally new random number, or it
	 * could be based on the specified number.
	 *
	 * @param double d
	 *            The number to randomize.
	 * @return double A randomized number.
	*/
	public function randomizeDouble($d);

	/**
	 * Randomize the 2d array based on an array, modify the array. Previous
	 * values may be used, or they may be discarded, depending on the
	 * randomizer.
	 *
	 * @param double[][] d
	 *            An array to randomize.
	*/
	public function randomizeMatrixArray(array &$d);

	/**
	 * Randomize the matrix based on an array, modify the array. Previous values
	 * may be used, or they may be discarded, depending on the randomizer.
	 *
	 * @param Matrix m
	 *            A matrix to randomize.
	*/
	public function randomizeMatrix(Matrix &$m);

	/**
	 * Randomize an array.
	 * @param d The array to randomize.
	 * @param begin The beginning element.
	 * @param size The size of the array.
	*/
	/**
	 * Randomize the array based on an array, modify the array. Previous values
	 * may be used, or they may be discarded, depending on the randomizer.
	 *
	 * @param double[] d
	 *            An array to randomize.
	 */
	//TODO(katrina) documentation, merged methods
	public function randomizeArray(array &$d, $begin = 0, $size = -1);

	/**
	 * Explicitly set the Random source
	 * @param GenerateRandom theRandom
	*/
	public function setRandom(GenerateRandom $theRandom);
	/**
	 * @return GenerateRandom Retrieve the Random generator.
	*/
	public function getRandom();
}