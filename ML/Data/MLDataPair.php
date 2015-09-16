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

/**
 * Training data is stored in two ways, depending on if the data is for
 * supervised, or unsupervised training.
 *
 * For unsupervised training just an input value is provided, and the ideal
 * output values are null.
 *
 * For supervised training both input and the expected ideal outputs are
 * provided.
 *
 * This interface abstracts classes that provide a holder for both of these two
 * data items.
 */
interface MLDataPair extends CentroidFactory {

	/**
	 *
	 * @return double[] The ideal data that the machine learning method should produce
	 *         for the specified input.
	 */
	public function getIdealArray();

	/**
	 *
	 * @return double[] The input that the neural network
	 */
	public function getInputArray();

	/**
	 * Set the ideal data, the desired output.
	 *
	 * @param
	 *        	double[] data
	 *        	The ideal data.
	 */
	public function setIdealArray( array $data );

	/**
	 * Set the input.
	 *
	 * @param
	 *        	double[] data
	 *        	The input.
	 */
	public function setInputArray( array $data );

	/**
	 *
	 * @return boolean True if this training pair is supervised. That is, it has both
	 *         input and ideal data.
	 */
	public function isSupervised();

	/**
	 *
	 * @return MLData The ideal data that the neural network should produce for the
	 *         specified input.
	 */
	public function getIdeal();

	/**
	 *
	 * @return MLData The input that the neural network
	 */
	public function getInput();

	/**
	 * Get the significance, 1.0 is neutral.
	 * 
	 * @return double The significance.
	 */
	public function getSignificance();

	/**
	 * Set the significance, 1.0 is neutral.
	 * 
	 * @param
	 *        	double s The significance.
	 */
	public function setSignificance( $s );
}