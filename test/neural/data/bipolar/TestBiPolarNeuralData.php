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
namespace Encog\Neural\Data\BiPolar;

use \Encog\ML\Data\Specific\BiPolarNeuralData;

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

/*	public void testClone()
	{
		boolean[] d = { true, false };
		BiPolarNeuralData data2 = new BiPolarNeuralData(d);
		BiPolarNeuralData data = (BiPolarNeuralData)data2.clone();
		Assert.assertEquals("[T,F]",data.toString());
		Assert.assertEquals(1,data.getData(0),0.5);
		Assert.assertEquals(-1,data.getData(1),0.5);
		Assert.assertEquals(true, data.getBoolean(0));
		Assert.assertEquals(false, data.getBoolean(1));
		Assert.assertEquals(data.getData().length,2);
	}*/

	/*public void testError()
	{
		BiPolarNeuralData data = new BiPolarNeuralData(2);
		Assert.assertEquals(2, data.size());

		try
		{
			data.add(0, 0);
			Assert.assertTrue(false);
		}
		catch(Exception e)
		{
		}
	}*/

	/*public void testClear()
	{
		double[] d = {1,1};
		BiPolarNeuralData data = new BiPolarNeuralData(2);
		data.setData(d);
		data.clear();
		Assert.assertEquals(-1,data.getData(0),0.5);
		data.setData(0,true);
		Assert.assertEquals(true,data.getBoolean(0));
	}*/


}