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

require_once("MathUtil/Matrices/Matrix.php");
require_once("MathUtil/Matrices/MatrixMath.php");

use Encog\MathUtil\Matrices;
use Encog\MathUtil\Matrices\Matrix;
use Encog\MathUtil\Matrices\MatrixError;
use Encog\MathUtil\Matrices\MatrixMath;

class TestMatrix extends PHPUnit_Framework_TestCase
{

	public function testRowsAndCols() {
		$matrix = new Matrix( 6, 3 );
		$this->assertEquals( $matrix->getRows(), 6 );
		$this->assertEquals( $matrix->getCols(), 3 );

		$matrix->set( 1, 2, 1.5 );
		$this->assertEquals( $matrix->get( 1, 2 ) , 1.5 );
	}

	public function testRowAndColRangeUnder() {
		$matrix = new Matrix( 6, 3 );

		// make sure set registers error on under-bound row
		try {
			$matrix->set( -1, 0, 1 );
			$this->assertTrue( false ); // should have thrown an exception
		}
		catch( MatrixError $e )
		{
		}

		// make sure set registers error on under-bound col
		try {
			$matrix->set( 0, -1, 1 );
			$this->assertTrue(false); // should have thrown an exception
		}
		catch( MatrixError $e )
		{
		}

		// make sure get registers error on under-bound row
		try {
			$matrix->get( -1, 0 );
			$this->assertTrue( false ); // should have thrown an exception
		}
		catch( MatrixError $e )
		{
		}

		// make sure set registers error on under-bound col
		try {
			$matrix->get( 0, -1 );
			$this->assertTrue( false ); // should have thrown an exception
		}
		catch( MatrixError $e )
		{
		}
	}

	public function testRowAndColRangeOver()
	{
		$matrix = new Matrix( 6, 3 );

		// make sure set registers error on under-bound row
		try {
			$matrix->set( 6, 0, 1 );
			$this->assertTrue( false ); // should have thrown an exception
		}
		catch( MatrixError $e )
		{
		}

		// make sure set registers error on under-bound col
		try {
			$matrix->set( 0, 3, 1 );
			$this->assertTrue( false ); // should have thrown an exception
		}
		catch( MatrixError $e )
		{
		}

		// make sure get registers error on under-bound row
		try {
			$matrix->get( 6, 0 );
			$this->assertTrue( false ); // should have thrown an exception
		}
		catch( MatrixError $e )
		{
		}

		// make sure set registers error on under-bound col
		try {
			$matrix->get( 0, 3 );
			$this->assertTrue( false ); // should have thrown an exception
		}
		catch( MatrixError $e )
		{
		}
	}

	public function testMatrixConstruct()
	{
		$m = [
			[1,2,3,4],
			[5,6,7,8],
			[9,10,11,12],
			[13,14,15,16] ];
			$matrix = Matrix::matrixFromDoubles( $m );
			$this->assertEquals( $matrix->getRows(), 4 );
			$this->assertEquals( $matrix->getCols(), 4 );
	}

	public function testMatrixEquals()
	{
		$m1 = [ [1,2],
			[3,4] ];

		$m2= [ [0,2],
			[3,4] ];

		$matrix1 = Matrix::matrixFromDoubles( $m1 );
		$matrix2 = Matrix::matrixFromDoubles( $m2 );

		$this->assertFalse( $matrix1->equals($matrix2) );

		$matrix2 = clone( $matrix1 );

		$this->assertTrue( $matrix1->equals($matrix2) );
	}

	public function testMatrixEqualsPrecision()
	{
		$m1 = [
			[1.1234,2.123],
			[3.123,4.123] ];

		$m2 = [
			[1.123,2.123],
			[3.123,4.123] ];

		$matrix1 = Matrix::matrixFromDoubles( $m1 );
		$matrix2 = Matrix::matrixFromDoubles( $m2 );

		$this->assertTrue($matrix1->equals($matrix2,3));
		$this->assertFalse($matrix1->equals($matrix2,4));

		$m3 = [
			[1.1,2.1],
			[3.1,4.1] ];

		$m4 = [
			[1.2,2.1],
			[3.1,4.1] ];

		$matrix3 = Matrix::matrixFromDoubles( $m3 );
		$matrix4 = Matrix::matrixFromDoubles( $m4 );
		$this->assertTrue($matrix3->equals($matrix4,0));
		$this->assertFalse($matrix3->equals($matrix4,1));

		try
		{
			$matrix3->equals($matrix4,-1);
			$this->assertTrue( false );
		}
		catch( MatrixError $e )
		{
		}

		try
		{
			$matrix3->equals($matrix4,19);
			$this->assertTrue( false );
		}
		catch( MatrixError $e )
		{
		}

	}

	public function testMatrixMultiply()
	{
		$a = [
			[1,0,2],
			[-1,3,1]
		];

		$b = [
			[3,1],
			[2,1],
			[1,0]
		];

		$c = [
			[5,1],
			[4,2]
		];

		$matrixA = Matrix::matrixFromDoubles( $a );
		$matrixB = Matrix::matrixFromDoubles( $b );
		$matrixC = Matrix::matrixFromDoubles( $c );
		
		$result = clone($matrixA);
		$result = MatrixMath\multiply( $matrixA, $matrixB );

		$this->assertTrue( $result->equals( $matrixC ) );

		$a2 = [
			[1,2,3,4],
			[5,6,7,8]
		];

		$b2 = [
			[1,2,3],
			[4,5,6],
			[7,8,9],
			[10,11,12]
		];

		$c2 = [
			[70,80,90],
			[158,184,210]
		];

		$matrixA = Matrix::matrixFromDoubles( $a2 );
		$matrixB = Matrix::matrixFromDoubles( $b2 );
		$matrixC = Matrix::matrixFromDoubles( $c2 );
		
		$result = MatrixMath\multiply( $matrixA, $matrixB );
		$this->assertTrue( $result->equals( $matrixC ) );

		$result = clone( $matrixB );
		try
		{
			MatrixMath\multiply( $matrixB, $matrixA );
			$this->assertTrue( false );
		}
		catch( MatrixError $e )
		{
		}
	}

	public function testBoolean()
	{
		$matrixDataBoolean = [
			[true,false],
			[false,true]
		];

		$matrixDataDouble = [
			[1.0,-1.0],
			[-1.0,1.0],
		];

		$matrixBoolean = Matrix::matrixFromBooleans( $matrixDataBoolean );
		$matrixDouble = Matrix::matrixFromDoubles( $matrixDataDouble );

		$this->assertTrue( $matrixBoolean->equals( $matrixDouble ) );
	}

	public function testGetRow()
	{
		$matrixData1 = [
			[1.0,2.0],
			[3.0,4.0]
		];
		
		$matrixData2 = [
			[3.0,4.0]
		];

		$matrix1 = Matrix::matrixFromDoubles( $matrixData1 );
		$matrix2 = Matrix::matrixFromDoubles( $matrixData2 );

		$matrixRow = $matrix1->getRow( 1 );
		$this->assertTrue( $matrixRow->equals( $matrix2 ) );

		try
		{
			$matrix1->getRow( 3 );
			$this->assertTrue( false );
		}
		catch( MatrixError $e )
		{
			$this->assertTrue( true );
		}
	}

	public function testGetCol()
	{
		$matrixData1 = [
			[1.0,2.0],
			[3.0,4.0]
		];
		$matrixData2 = [
			[2.0],
			[4.0]
		];

		$matrix1 = Matrix::matrixFromDoubles( $matrixData1 );
		$matrix2 = Matrix::matrixFromDoubles( $matrixData2 );

		$matrixCol = $matrix1->getCol( 1 );
		$this->assertTrue( $matrixCol->equals( $matrix2 ) );

		try
		{
			$matrix1->getCol( 3 );
			$this->assertTrue( false );
		}
		catch( MatrixError $e )
		{
			$this->assertTrue( true );
		}
	}

	public function testZero()
	{
		$doubleData = [ [0,0], [0,0] ];
		$matrix = Matrix::matrixFromDoubles( $doubleData );
		$this->assertTrue( $matrix->isZero() );
	}

	public function testSum()
	{
		$doubleData = [ [1,2], [3,4] ];
		$matrix = Matrix::matrixFromDoubles( $doubleData );
		$this->assertEquals( intval( $matrix->sum() ), 1+2+3+4 );
	}

	public function testRowMatrix()
	{
		$matrixData = [1.0,2.0,3.0,4.0];
		$matrix = Matrix::createRowMatrix( $matrixData );
		$this->assertEquals( $matrix->get(0,0), 1.0 );
		$this->assertEquals( $matrix->get(0,1), 2.0 );
		$this->assertEquals( $matrix->get(0,2), 3.0 );
		$this->assertEquals( $matrix->get(0,3), 4.0 );
	}

	public function testColumnMatrix()
	{
		$matrixData = [1.0,2.0,3.0,4.0];
		$matrix = Matrix::createColumnMatrix( $matrixData );
		$this->assertEquals( $matrix->get(0,0), 1.0 );
		$this->assertEquals( $matrix->get(1,0), 2.0 );
		$this->assertEquals( $matrix->get(2,0), 3.0 );
		$this->assertEquals( $matrix->get(3,0), 4.0 );
	}

	public function testAdd()
	{
		$matrixData = [1.0,2.0,3.0,4.0];
		$matrix = Matrix::createColumnMatrix( $matrixData );
		$matrix->add( 0, 0, 1 );
		$this->assertEquals( $matrix->get(0, 0), 2.0 );
	}

	public function testClear()
	{
		$matrixData = [1.0,2.0,3.0,4.0];
		$matrix = Matrix::createColumnMatrix( $matrixData );
		$matrix->clear();
		$this->assertEquals( $matrix->get(0, 0), 0.0 );
		$this->assertEquals( $matrix->get(1, 0), 0.0 );
		$this->assertEquals( $matrix->get(2, 0), 0.0 );
		$this->assertEquals( $matrix->get(3, 0), 0.0 );
	}

	public function testIsVector()
	{
		$matrixData = [1.0,2.0,3.0,4.0];
		$matrixCol = Matrix::createColumnMatrix( $matrixData );
		$matrixRow = Matrix::createRowMatrix( $matrixData );
		$this->assertTrue( $matrixCol->isVector() );
		$this->assertTrue( $matrixRow->isVector() );
		$matrixData2 = [[1.0,2.0],[3.0,4.0]];
		$matrix = Matrix::matrixFromDoubles( $matrixData2 );
		$this->assertFalse( $matrix->isVector() );
	}

	public function testIsZero()
	{
		$matrixData = [1.0,2.0,3.0,4.0];
		$matrix = Matrix::createColumnMatrix( $matrixData );
		$this->assertFalse( $matrix->isZero() );
		$matrixData2 = [0.0,0.0,0.0,0.0];
		$matrix2 = Matrix::createColumnMatrix( $matrixData2 );
		$this->assertTrue( $matrix2->isZero() );

	}

	public function testPackedArray()
	{
		$matrixData = [[1.0,2.0],[3.0,4.0]];
		$matrix = Matrix::matrixFromDoubles( $matrixData );
		$matrixData2 = $matrix->toPackedArray();
		$this->assertEquals( 4, count($matrixData2) );
		$this->assertEquals( 1.0, $matrix->get(0, 0) );
		$this->assertEquals( 2.0, $matrix->get(0, 1) );
		$this->assertEquals( 3.0, $matrix->get(1, 0) );
		$this->assertEquals( 4.0, $matrix->get(1, 1) );

		$matrix2 = new Matrix( 2, 2 );
		$matrix2->fromPackedArray( $matrixData2, 0 );
		$this->assertTrue( $matrix->equals( $matrix2 ) );
	}

	public function testPackedArray2()
	{
		$data = [1.0,2.0,3.0,4.0];
		$matrix = new Matrix( 1, 4 );
		$matrix->fromPackedArray( $data, 0 );
		$this->assertEquals( 1.0, $matrix->get(0, 0) );
		$this->assertEquals( 2.0, $matrix->get(0, 1) );
		$this->assertEquals( 3.0, $matrix->get(0, 2) );
	}

	public function testSize()
	{
		$data = [[1.0,2.0],[3.0,4.0]];
		$matrix = Matrix::matrixFromDoubles( $data );
		$this->assertEquals( 4, $matrix->size() );
	}

	public function testVectorLength()
	{
		$vectorData = [1.0,2.0,3.0,4.0];
		$vector = Matrix::createRowMatrix( $vectorData );
		$this->assertEquals( 5, intval(MatrixMath\vectorLength($vector)));

		$nonVector = new Matrix( 2, 2 );
		try
		{
			MatrixMath\vectorLength( $nonVector );
			$this->assertTrue( false );
		}
		catch( MatrixError $e )
		{
		}
	}

}