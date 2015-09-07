<?php
/*
 * Encog(tm) Core v3.3 - PHP Version
 * https://github.com/katrinaniolet/encog-php-core
 *
 * http://www.heatonresearch.com/encog/
 * https://github.com/encog/encog-java-core

 * Copyright 2008-2014 Heaton Research, Inc.
 * PHP port by Katrina Niolet <katria@kf5utn.net>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
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

namespace Test\Matrix;

use \Encog\MathUtil\Matrices\BiPolarUtil;
use \Encog\MathUtil\Matrices\Matrix;

require_once("MathUtil/Matrices/Matrix.php");
require_once("MathUtil/Matrices/BiPolarUtil.php");

class TestBiPolarUtil extends \PHPUnit_Framework_TestCase {

	public function testBipolar2double()
	{
		// test a 1x4
		$booleanData1 = [ true, false, true, false ];
		$checkData1 = [ 1, -1, 1, -1 ];
		$matrix1 = Matrix::createRowMatrix( BiPolarUtil\bipolar2double( $booleanData1 ) );
		$checkMatrix1 = Matrix::createRowMatrix( $checkData1 );
		$this->assertTrue( $matrix1->equals( $checkMatrix1 ) );

		// test a 2x2
		$booleanData2 = [ [ true, false], [false, true ] ];
		$checkData2 = [ [1, -1], [-1, 1] ];
		$matrix2 = Matrix::matrixFromDoubles(BiPolarUtil\bipolar2double( $booleanData2 ) );
		$checkMatrix2 = Matrix::matrixFromDoubles( $checkData2 );
		$this->assertTrue( $matrix2->equals( $checkMatrix2 ) );
	}

	public function testDouble2bipolar()
	{
		// test a 1x4
		$doubleData1 = [ 1, -1, 1, -1 ];
		$checkData1 = [ true, false, true, false ];
		$result1 = BiPolarUtil\double2bipolar( $doubleData1 );
		for( $i=0; $i<count($checkData1); ++$i )
		{
			$this->assertEquals( $checkData1[$i], $result1[$i] );
		}

		// test a 2x2
		$doubleData2 = [ [1,-1], [-1,1] ];
		$checkData2 = [ [true, false], [false, true] ];
		$result2 = BiPolarUtil\double2bipolar( $doubleData2 );

		for( $r = 0; $r<count($doubleData2); ++$r )
		{
			for($c = 0; $c<count($doubleData2[0]);++$c)
			{
				$this->assertEquals($result2[$r][$c], $checkData2[$r][$c]);
			}
		}

	}

	public function testBinary()
	{
		$this->assertEquals( 0.0, BiPolarUtil\normalizeBinary(-1) );
		$this->assertEquals( 1.0, BiPolarUtil\normalizeBinary(2) );
		$this->assertEquals( 1.0, BiPolarUtil\toBinary(1) );
		$this->assertEquals( -1.0, BiPolarUtil\toBiPolar(0) );
		$this->assertEquals( 1.0, BiPolarUtil\toNormalizedBinary(10) );
	}
}