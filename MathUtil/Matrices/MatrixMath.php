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
namespace Encog\MathUtil\Matrices\MatrixMath;

use Encog\MathUtil\Matrices\Matrix;
use Encog\MathUtil\Matrices\MatrixError;
use Encog\MathUtil\Matrices\decomposition\LUDecomposition;

require_once ("MathUtil/Matrices/Matrix.php");
require_once ("MathUtil/Matrices/MatrixError.php");

/**
 * This class can perform many different mathematical operations on matrixes.
 * The matrixes passed in will not be modified, rather a new matrix, with the
 * operation performed, will be returned.
 */

/**
 * Add two matrixes.
 *
 * @param
 *        	a
 *        	The first matrix to add.
 * @param
 *        	b
 *        	The second matrix to add.
 * @return A new matrix of the two added.
 */
function add( Matrix $a, Matrix $b ) {
	if( $a->getRows() != $b->getRows() ) {
		throw new MatrixError( "To add the matrices they must have the same number of " + "rows and columns.  Matrix a has " + $a->getRows() + " rows and matrix b has " + $b->getRows() + " rows." );
	}
	
	if( $a->getCols() != $b->getCols() ) {
		throw new MatrixError( "To add the matrices they must have the same number " + "of rows and columns.  Matrix a has " + $a->getCols() + " cols and matrix b has " + $b->getCols() + " cols." );
	}
	
	$aa = $a->getData();
	$bb = $b->getData();
	$result = array();
	
	for( $resultRow = 0; $resultRow < $a->getRows(); ++$resultRow ) {
		for( $resultCol = 0; $resultCol < $a->getCols(); ++$resultCol ) {
			$result[$resultRow][$resultCol] = $aa[$resultRow][$resultCol] + $bb[$resultRow][$resultCol];
		}
	}
	
	return Matrix::matrixFromDoubles( $result );
}

/**
 * Copy from one matrix to another.
 *
 * @param
 *        	source
 *        	The source matrix for the copy.
 * @param
 *        	target
 *        	The target matrix for the copy.
 */
function copy( Matrix $source, Matrix &$target ) {
	$s = $source->getData();
	$t = $target->getData();
	for( $row = 0; $row < $source->getRows(); ++$row ) {
		for( $col = 0; $col < $source->getCols(); ++$col ) {
			$t[$row][$col] = $s[$row][$col];
		}
	}
	$target = Matrix::matrixFromDoubles( $t );
}

/**
 * Delete one column from the matrix.
 * Does not actually touch the source
 * matrix, rather a new matrix with the column deleted is returned.
 *
 * @param
 *        	matrix
 *        	The matrix.
 * @param
 *        	deleted
 *        	The column to delete.
 * @return A matrix with the column deleted.
 */
function deleteCol( Matrix $matrix, $deleted ) {
	if( $deleted >= $matrix->getCols() ) {
		throw new MatrixError( "Can't delete column " + $deleted + " from matrix, it only has " + $matrix->getCols() + " columns." );
	}
	$newMatrix = array();
	
	$d = $matrix->getData();
	
	for( $row = 0; $row < $matrix->getRows(); ++$row ) {
		$targetCol = 0;
		
		for( $col = 0; $col < $matrix->getCols(); ++$col ) {
			if( $col != $deleted ) {
				$newMatrix[$row][$targetCol] = $d[$row][$col];
				++$targetCol;
			}
		}
	}
	return Matrix::matrixFromDoubles( $newMatrix );
}

/**
 * Delete a row from the matrix.
 * Does not actually touch the matrix, rather
 * returns a new matrix.
 *
 * @param
 *        	matrix
 *        	The matrix.
 * @param
 *        	deleted
 *        	Which row to delete.
 * @return A new matrix with the specified row deleted.
 */
function deleteRow( Matrix $matrix, $deleted ) {
	if( $deleted >= $matrix->getRows() ) {
		throw new MatrixError( "Can't delete row " + $deleted + " from matrix, it only has " + $matrix->getRows() + " rows." );
	}
	$newMatrix = array();
	$d = $matrix->getData();
	
	$targetRow = 0;
	for( $row = 0; $row < $matrix->getRows(); ++$row ) {
		if( $row != $deleted ) {
			for( $col = 0; $col < $matrix->getCols(); ++$col ) {
				$newMatrix[$targetRow][$col] = $d[$row][$col];
			}
			++$targetRow;
		}
	}
	return Matrix::matrixFromDoubles( $newMatrix );
}

/**
 * Return a matrix with each cell divided by the specified value.
 *
 * @param
 *        	a
 *        	The matrix to divide.
 * @param
 *        	b
 *        	The value to divide by.
 * @return A new matrix with the division performed.
 */
function divide( Matrix $a, $b ) {
	$result = array();
	$d = $a->getData();
	for( $row = 0; $row < $a->getRows(); ++$row ) {
		for( $col = 0; $col < $a->getCols(); ++$col ) {
			$result[$row][$col] = $d[$row][$col] / $b;
		}
	}
	return Matrix::matrixFromDoubles( $result );
}

/**
 * Compute the dot product for the two matrixes.
 * To compute the dot product,
 * both
 *
 * @param
 *        	a
 *        	The first matrix.
 * @param
 *        	b
 *        	The second matrix.
 * @return The dot product.
 */
function dotProduct( Matrix $a, Matrix $b ) {
	if( ! $a->isVector() || ! $b->isVector() ) {
		throw new MatrixError( "To take the dot product, both matrices must be vectors." );
	}
	
	$aArray = $a->getData();
	$bArray = $b->getData();
	
	$aLength = count( $aArray ) == 1 ? count( $aArray[0] ) : count( $aArray );
	$bLength = count( $bArray ) == 1 ? count( $bArray[0] ) : count( $bArray );
	
	if( $aLength != $bLength ) {
		throw new MatrixError( "To take the dot product, both matrices must be of the same length." );
	}
	
	$result = 0;
	if( count( $aArray ) == 1 && count( $bArray ) == 1 ) {
		for( $i = 0; $i < $aLength; ++$i ) {
			$result += $aArray[0][$i] * $bArray[0][$i];
		}
	}
	else if( count( $aArray ) == 1 && count( $bArray[0] ) == 1 ) {
		for( $i = 0; $i < $aLength; ++$i ) {
			$result += $aArray[0][$i] * $bArray[$i][0];
		}
	}
	else if( count( $aArray[0] ) == 1 && count( $bArray ) == 1 ) {
		for( $i = 0; $i < $aLength; ++$i ) {
			$result += $aArray[$i][0] * $bArray[0][$i];
		}
	}
	else if( count( $aArray[0] ) == 1 && count( $bArray[0] ) == 1 ) {
		for( $i = 0; $i < $aLength; ++$i ) {
			$result += $aArray[$i][0] * $bArray[$i][0];
		}
	}
	
	return $result;
}

/**
 * Return an identity matrix of the specified size.
 *
 * @param
 *        	size
 *        	The number of rows and columns to create. An identity matrix
 *        	is always square.
 * @return An identity matrix.
 */
function identity( $size ) {
	if( $size < 1 ) {
		throw new MatrixError( "Identity matrix must be at least of " + "size 1." );
	}
	$result = new Matrix( $size, $size );
	$d = $result->getData();
	for( $i = 0; $i < $size; ++$i ) {
		$d[$i][$i] = 1;
	}
	return Matrix::matrixFromDoubles( $d );
}

/**
 * Return the result of multiplying every cell in the matrix by the
 * specified value.
 *
 * @param
 *        	a
 *        	The first matrix.
 * @param
 *        	b
 *        	The second matrix.
 * @return The result of the multiplication.
 */
function multiplyScalar( Matrix $a, $b ) {
	$result = array();
	$d = $a->getData();
	for( $row = 0; $row < $a->getRows(); ++$row ) {
		for( $col = 0; $col < $a->getCols(); ++$col ) {
			$result[$row][$col] = $d[$row][$col] * $b;
		}
	}
	return Matrix::matrixFromDoubles( $result );
}

/**
 * Return the product of the first and second matrix.
 *
 * @param
 *        	a
 *        	The first matrix.
 * @param
 *        	b
 *        	The second matrix.
 * @return The result of the multiplication.
 */
function multiply( Matrix $a, Matrix $b ) {
	if( $b->getRows() != $a->getCols() ) {
		throw new MatrixError( "To use ordinary matrix multiplication the number of " . "columns on the first matrix must match the number of " . "rows on the second." );
	}
	
	$aData = $a->getData();
	$bData = $b->getData();
	
	$x = new Matrix( $a->getRows(), $b->getCols() );
	$c = $x->getData();
	$bcolj = array( 
			$a->getCols() );
	for( $j = 0; $j < $b->getCols(); ++$j ) {
		for( $k = 0; $k < $a->getCols(); ++$k ) {
			$bcolj[$k] = $bData[$k][$j];
		}
		for( $i = 0; $i < $a->getRows(); ++$i ) {
			$arowi = $aData[$i];
			$s = 0;
			for( $k = 0; $k < $a->getCols(); ++$k ) {
				$s += $arowi[$k] * $bcolj[$k];
			}
			$c[$i][$j] = $s;
		}
	}
	return Matrix::matrixFromDoubles( $c );
}

/**
 * Return the results of subtracting one matrix from another.
 *
 * @param
 *        	a
 *        	The first matrix.
 * @param
 *        	b
 *        	The second matrix.
 * @return The results of the subtraction.
 */
/*
 * public static Matrix subtract(final Matrix a, final Matrix b) {
 * if (a.getRows() != b.getRows()) {
 * throw new MatrixError(
 * "To subtract the matrices they must have the same "
 * + "number of rows and columns. Matrix a has "
 * + a.getRows() + " rows and matrix b has "
 * + b.getRows() + " rows.");
 *
 * }
 *
 * if (a.getCols() != b.getCols()) {
 * throw new MatrixError(
 * "To subtract the matrices they must have the same "
 * + "number of rows and columns. Matrix a has "
 * + a.getCols() + " cols and matrix b has "
 * + b.getCols() + " cols.");
 * }
 *
 * final double[][] result = new double[a.getRows()][a.getCols()];
 * final double[][] aa = a.getData();
 * final double[][] bb = b.getData();
 *
 * for (int resultRow = 0; resultRow < a.getRows(); resultRow++) {
 * for (int resultCol = 0; resultCol < a.getCols(); resultCol++) {
 * result[resultRow][resultCol] = aa[resultRow][resultCol]
 * - bb[resultRow][resultCol];
 * }
 * }
 *
 * return new Matrix(result);
 * }
 */

/**
 * Return the transposition of a matrix.
 *
 * @param $input Matrix
 *        	The matrix to transpose.
 * @return Matrix The matrix transposed.
 */
function transpose( Matrix $input ) {
	$transposeMatrix = array();
	
	$d = $input->getData();
	
	for( $r = 0; $r < $input->getRows(); ++$r ) {
		for( $c = 0; $c < $input->getCols(); ++$c ) {
			$transposeMatrix[$c][$r] = $d[$r][$c];
		}
	}
	
	return Matrix::matrixFromDoubles( $transposeMatrix );
}

/**
 * Calculate the length of a vector.
 *
 * @param
 *        	input
 *        	The matrix to calculate the length of.
 *        	
 * @return Vector length.
 */
function vectorLength( Matrix $input ) {
	if( ! $input->isVector() ) {
		throw new MatrixError( "Can only take the vector length of a vector." );
	}
	$v = $input->toPackedArray();
	$rtn = 0.0;
	foreach( $v as $element ) {
		$rtn += pow( $element, 2 );
	}
	return sqrt( $rtn );
}

/*	public static double determinant(Matrix m) {
		return new LUDecomposition(m).det();
	}*/

/*	public static double[] multiply(Matrix a, double[] d) {
		double[] p = new double[a.getRows()];
		double[][] aData = a.getData();

		for (int r = 0; r < a.getRows(); r++)
			for (int i = 0; i < a.getCols(); i++)
				p[r] += aData[r][i] * d[i];

			return p;
	}*/

