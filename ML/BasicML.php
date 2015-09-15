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
namespace Encog\ML;

use \Encog\Encog;
use \Encog\Util\CSV\CSVFormat;

require_once ("ML/MLProperties.php");

/**
 * A class that provides basic property functionality for the MLProperties
 * interface.
 */
abstract class BasicML implements MLMethod, MLProperties {
	
	/**
	 * Properties about the neural network.
	 * Some NeuralLogic classes require
	 * certain properties to be set.
	 * 
	 * @var string[string]
	 */
	private $properties = array();

	/**
	 *
	 * @return &string[string] A map of all properties.
	 */
	public function &getProperties() {
		return $this->properties;
	}

	/**
	 * Get the specified property as a double.
	 *
	 * @param
	 *        	string name
	 *        	The name of the property.
	 * @return double The property as a double.
	 */
	public function getPropertyDouble( $name ) {
		if(!array_key_exists($name, $this->properties))
				return null;
		return doubleval( $this->properties[$name] );
	}

	/**
	 * Get the specified property as a long.
	 *
	 * @param
	 *        	string name
	 *        	The name of the specified property.
	 * @return long The value of the specified property.
	 */
	public function getPropertyLong( $name ) {
		if(!array_key_exists($name, $this->properties))
				return null;
		return intval( $this->properties[$name] );
	}

	/**
	 * Get the specified property as a string.
	 *
	 * @param
	 *        	string name
	 *        	The name of the property.
	 * @return string The value of the property.
	 */
	public function getPropertyString( $name ) {
		if(!array_key_exists($name, $this->properties))
				return null;
		return $this->properties[$name];
	}

	/**
	 * Set a property as a double.
	 *
	 * @param
	 *        	string name
	 *        	The name of the property.
	 * @param
	 *        	double d
	 *        	The value of the property.
	 */
	public function setProperty( $name, $d ) {
		$this->properties[$name] = strval( CSVFormat::$EG_FORMAT->format( $d, Encog\DEFAULT_PRECISION ) );
		updateProperties();
	}

	/**
	 * Set a property as a long.
	 *
	 * @param
	 *        	string name
	 *        	The name of the property.
	 * @param
	 *        	long l
	 *        	The value of the property.
	 */
	public function setPropertyLong( $name, $l ) {
		$this->properties[$name] = strval( $l );
		$this->updateProperties();
	}

	/**
	 * Set a property as a double.
	 *
	 * @param
	 *        	string name
	 *        	The name of the property.
	 * @param
	 *        	string value
	 *        	The value of the property.
	 */
	public function setPropertyDouble( $name, $value ) {
		$this->properties[$name] =  strval($value);
		$this->updateProperties();
	}

	public abstract function updateProperties();
}