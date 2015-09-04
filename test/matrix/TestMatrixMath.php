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
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * For more information on Heaton Research copyrights, licenses
 * and trademarks visit:
 * http://www.heatonresearch.com/copyright
 */
namespace Test\Encog\matrix;

use \Encog\MathUtil\Matrices;
use \Encog\MathUtil\Matrices\Matrix;
use \Encog\MathUtil\Matrices\MatrixError;
use \Encog\MathUtil\Matrices\MatrixMath;

require_once ("MathUtil/Matrices/Matrix.php");
require_once ("MathUtil/Matrices/MatrixMath.php");
class TestMatrixMath extends \PHPUnit_Framework_TestCase {
	public function testInverse() {
		$matrixData1 = [ 
				[ 
						1,
						2,
						3,
						4 ] ];
		$matrixData2 = [ 
				[ 
						1 ],
				[ 
						2 ],
				[ 
						3 ],
				[ 
						4 ] ];
		
		$matrix1 = Matrix::matrixFromDoubles( $matrixData1 );
		$checkMatrix = Matrix::matrixFromDoubles( $matrixData2 );
		
		$matrix2 = MatrixMath\transpose( $matrix1 );
		
		$this->assertTrue( $matrix2->equals( $checkMatrix ) );
	}
	
	public function testDotProduct() {
		$matrixData1 = [ 
				[ 
						1,
						2,
						3,
						4 ] ];
		$matrixData2 = [ 
				[ 
						5 ],
				[ 
						6 ],
				[ 
						7 ],
				[ 
						8 ] ];
		
		$matrix1 = Matrix::matrixFromDoubles( $matrixData1 );
		$matrix2 = Matrix::matrixFromDoubles( $matrixData2 );
		
		$dotProduct = MatrixMath\dotProduct( $matrix1, $matrix2 );
		
		$this->assertEquals( $dotProduct, 70.0 );
		
		// test dot product errors
		$nonVectorData = [ 
				[ 
						1.0,
						2.0 ],
				[ 
						3.0,
						4.0 ] ];
		$differentLengthData = [ 
				[ 
						1.0 ] ];
		$nonVector = Matrix::matrixFromDoubles( $nonVectorData );
		$differentLength = Matrix::matrixFromDoubles( $differentLengthData );
		
		try {
			MatrixMath\dotProduct( $matrix1, $nonVector );
			$this->assertTrue( false );
		}
		catch( MatrixError $e ) {}
		
		try {
			MatrixMath\dotProduct( $nonVector, $matrix2 );
			$this->assertTrue( false );
		}
		catch( MatrixError $e ) {}
		
		try {
			MatrixMath\dotProduct( $matrix1, $differentLength );
			$this->assertTrue( false );
		}
		catch( MatrixError $e ) {}
	}
	
	/*
	 * public void testMultiply() throws Throwable
	 * {
	 * double matrixData1[][] = {{1,4},
	 * {2,5},
	 * {3,6}
	 * };
	 * double matrixData2[][] = {{7,8,9},
	 * {10,11,12}};
	 *
	 *
	 * double matrixData3[][] = {{47,52,57},
	 * {64,71,78},
	 * {81,90,99}
	 * };
	 *
	 * Matrix matrix1 = new Matrix(matrixData1);
	 * Matrix matrix2 = new Matrix(matrixData2);
	 *
	 * Matrix matrix3 = new Matrix(matrixData3);
	 *
	 * Matrix result = MatrixMath.multiply(matrix1,matrix2);
	 *
	 * TestCase.assertTrue(result.equals(matrix3));
	 * }
	 */
	
	/*
	 * public static void testVerifySame()
	 * {
	 * double dataBase[][] = {{1.0,2.0},{3.0,4.0}};
	 * double dataTooManyRows[][] = {{1.0,2.0},{3.0,4.0},{5.0,6.0}};
	 * double dataTooManyCols[][] = {{1.0,2.0,3.0},{4.0,5.0,6.0}};
	 * Matrix base = new Matrix(dataBase);
	 * Matrix tooManyRows = new Matrix(dataTooManyRows);
	 * Matrix tooManyCols = new Matrix(dataTooManyCols);
	 * MatrixMath.add(base, base);
	 * try
	 * {
	 * MatrixMath.add(base, tooManyRows);
	 * TestCase.assertFalse(true);
	 * }
	 * catch(MatrixError e)
	 * {
	 * }
	 * try
	 * {
	 * MatrixMath.add(base, tooManyCols);
	 * TestCase.assertFalse(true);
	 * }
	 * catch(MatrixError e)
	 * {
	 * }
	 * }
	 */
	
	/*
	 * public void testDivide() throws Throwable
	 * {
	 * double data[][] = {{2.0,4.0},{6.0,8.0}};
	 * Matrix matrix = new Matrix(data);
	 * Matrix result = MatrixMath.divide(matrix, 2.0);
	 * TestCase.assertEquals(1.0, result.get(0,0));
	 * }
	 */
	
	/*
	 * public void testIdentity() throws Throwable
	 * {
	 * try
	 * {
	 * MatrixMath.identity(0);
	 * TestCase.assertTrue(false);
	 * }
	 * catch(MatrixError e)
	 * {
	 *
	 * }
	 *
	 * double checkData[][] = {{1,0},{0,1}};
	 * Matrix check = new Matrix(checkData);
	 * Matrix matrix = MatrixMath.identity(2);
	 * TestCase.assertTrue(check.equals(matrix));
	 * }
	 */
	
	/*
	 * public void testMultiplyScalar() throws Throwable
	 * {
	 * double data[][] = {{2.0,4.0},{6.0,8.0}};
	 * Matrix matrix = new Matrix(data);
	 * Matrix result = MatrixMath.multiply(matrix, 2.0);
	 * TestCase.assertEquals(4.0, result.get(0,0));
	 * }
	 */
	
	/*
	 * public void testDeleteRow() throws Throwable
	 * {
	 * double origData[][] = {{1.0,2.0},{3.0,4.0}};
	 * double checkData[][] = {{3.0,4.0}};
	 * Matrix orig = new Matrix(origData);
	 * Matrix matrix = MatrixMath.deleteRow(orig, 0);
	 * Matrix check = new Matrix(checkData);
	 * TestCase.assertTrue(check.equals(matrix));
	 *
	 * try
	 * {
	 * MatrixMath.deleteRow(orig, 10);
	 * TestCase.assertTrue(false);
	 * }
	 * catch(MatrixError e)
	 * {
	 * }
	 * }
	 */
	
	/*
	 * public void testDeleteCol() throws Throwable
	 * {
	 * double origData[][] = {{1.0,2.0},{3.0,4.0}};
	 * double checkData[][] = {{2.0},{4.0}};
	 * Matrix orig = new Matrix(origData);
	 * Matrix matrix = MatrixMath.deleteCol(orig, 0);
	 * Matrix check = new Matrix(checkData);
	 * TestCase.assertTrue(check.equals(matrix));
	 *
	 * try
	 * {
	 * MatrixMath.deleteCol(orig, 10);
	 * TestCase.assertTrue(false);
	 * }
	 * catch(MatrixError e)
	 * {
	 * }
	 * }
	 */
	
	/*
	 * public void testCopy()
	 * {
	 * double data[][] = {{1.0,2.0},{3.0,4.0}};
	 * Matrix source = new Matrix(data);
	 * Matrix target = new Matrix(2,2);
	 * MatrixMath.copy(source, target);
	 * TestCase.assertTrue(source.equals(target));
	 * }
	 */
}