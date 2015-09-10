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

/**
 * An interface designed to abstract classes that store machine learning data.
 * This interface is designed to provide EngineDataSet objects. These can be
 * used to train machine learning methods using both supervised and unsupervised
 * training.
 *
 * Some implementations of this interface are memory based. That is they store
 * the entire contents of the dataset in memory.
 *
 * Other implementations of this interface are not memory based. These
 * implementations read in data as it is needed. This allows very large datasets
 * to be used. Typically the add methods are not supported on non-memory based
 * datasets.
 */
interface MLDataSet {

	/**
	 *
	 * @return int The size of the ideal data.
	 */
	public function getIdealSize();

	/**
	 *
	 * @return int The size of the input data.
	 */
	public function getInputSize();

	/**
	 *
	 * @return bool True if this is a supervised training set.
	 */
	public function isSupervised();

	/**
	 * Determine the total number of records in the set.
	 *
	 * @return long The total number of records in the set.
	 */
	public function getRecordCount();

	/**
	 * Read an individual record, specified by index, in random order.
	 *
	 * @param
	 *        	long index
	 *        	The index to read.
	 * @param
	 *        	MLDataPair pair
	 *        	The pair that the record will be copied into.
	 */
	public function getRecord( $index, MLDataPair $pair );

	/**
	 * Opens an additional instance of this dataset.
	 *
	 * @return MLDataSet The new instance.
	 */
	public function openAdditional();

	/**
	 * Add a set of input and ideal data to the dataset.
	 * This is used with
	 * supervised training, as ideal output is provided. Note: not all
	 * implementations support the add methods.
	 *
	 * if $idealData is null this is used with unsuporvised //TODO(katrin) documentation
	 *
	 * @param
	 *        	mixed inputData
	 *        	Input data.
	 * @param
	 *        	MLData idealData
	 *        	Ideal data.
	 */
	public function add( $inputData, MLData $idealData = null );

	/**
	 * Close this datasource and release any resources obtained by it, including
	 * any iterators created.
	 */
	public function close();

	/**
	 *
	 * @return int
	 */
	public function size();

	/**
	 *
	 * @param int $index        	
	 * @return MLDataPair
	 */
	public function get( $index );
}