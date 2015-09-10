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

/**
 * Defines a Machine Learning Method that holds properties.
 */
interface MLProperties extends MLMethod {

	/**
	 *
	 * @return array(string,string) A map of all properties.
	 */
	public function getProperties();

	/**
	 * Get the specified property as a double.
	 *
	 * @param
	 *        	string name
	 *        	The name of the property.
	 * @return double The property as a double.
	 */
	public function getPropertyDouble( $name );

	/**
	 * Get the specified property as a long.
	 *
	 * @param
	 *        	string name
	 *        	The name of the specified property.
	 * @return long The value of the specified property.
	 */
	public function getPropertyLong( $name );

	/**
	 * Get the specified property as a string.
	 *
	 * @param
	 *        	string name
	 *        	The name of the property.
	 * @return string The value of the property.
	 */
	public function getPropertyString( $name );

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
	public function setProperty( $name, $d );

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
	public function setProperty( $name, $l );

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
	public function setProperty( $name, $value );

	/**
	 * Update any objeccts when a property changes.
	 */
	public function updateProperties();
}