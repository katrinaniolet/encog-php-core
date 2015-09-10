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
namespace Encog\Neural\Data\BiPolar;

use \Encog\ML\Data\Specific\BiPolarNeuralData;
use Encog\ML\Data\MLDataError;

require_once("ML\Data\Specific\BiPolarNeuralData.php");

class TestBiPolarNeuralData extends \PHPUnit_Framework_TestCase {
	public function testConstruct()
	{
		$d = [ true, false ];
		$data = BiPolarNeuralData::fromBooleanArray($d);
		$this->assertEquals("[T,F]",$data->toString());
		$this->assertEquals(1,$data->getData(0),0.5);
		$this->assertEquals(-1,$data->getData(1),0.5);
		$this->assertEquals(true, $data->getBoolean(0));
		$this->assertEquals(false, $data->getBoolean(1));
		$this->assertEquals(count($data->getData()),2);
	}

	public function testClone()
	{
		$d = [ true, false ];
		$data2 = BiPolarNeuralData::fromBooleanArray($d);
		$data = clone($data2);
		$this->assertEquals("[T,F]",$data->toString());
		$this->assertEquals(1,$data->getData(0),0.5);
		$this->assertEquals(-1,$data->getData(1),0.5);
		$this->assertEquals(true, $data->getBoolean(0));
		$this->assertEquals(false, $data->getBoolean(1));
		$this->assertEquals(count($data->getData()),2);
	}

	public function testError()
	{
		$data = new BiPolarNeuralData(2);
		$this->assertEquals(2, $data->size());

		try
		{
			$data->add(0, 0);
			$this->assertTrue(false);
		}
		catch(MLDataError $e)
		{
		}
	}

	public function testClear()
	{
		$d = [1,1];
		$data = new BiPolarNeuralData(2);
		$data->setDataFromArray($d);
		$data->clear();
		$this->assertEquals(-1,$data->getData(0),0.5);
		$data->setData(0,true);
		$this->assertEquals(true,$data->getBoolean(0));
	}
}