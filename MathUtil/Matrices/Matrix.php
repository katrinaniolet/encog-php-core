<?php
/*
 * Encog(tm) Core v3.3 - PHP Version
 * https://github.com/katrinaniolet/encog-php-core
 * 
 * http://www.heatonresearch.com/encog/
 * https://github.com/encog/encog-java-core

 * Copyright 2008-2014 Heaton Research, Inc.
 * PHP port by Katrina Niolet <katrina@kf5utn.net>
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

namespace Encog\MathUtil\Matrices;

require_once("Encog.php");
require_once("MathUtil\Matrices\MatrixError.php");

/**
 * This class implements a mathematical matrix. Matrix math is very important to
 * neural network processing. Many of the neural network classes make use of the
 * matrix classes in this package.
 */
class Matrix {

	/**
	 * Turn an array of doubles into a column matrix.
	 * 
	 * @param input
	 *            A double array.
	 * @return A column matrix.
	 */
	public static function createColumnMatrix( array $input ) {
		$d = array();
		for( $row = 0; $row < count($input); ++$row ) {
			$d[$row][0] = $input[$row];
		}
		return Matrix::matrixFromDoubles( $d );
	}

	/**
	 * Turn an array of doubles into a row matrix.
	 * 
	 * @param input
	 *            A double array.
	 * @return A row matrix.
	 */
	public static function createRowMatrix( array $input ) {
		$d = array();
		for( $col = 0; $col < count($input); ++$col ) {
			$d[0][$col] = $input[$col];
		}
		return Matrix::matrixFromDoubles( $d );
	}

	/**
	 * The matrix data.
	 */
	private $matrix = array();

	/**
	 * Construct a bipolar matrix from an array of booleans.
	 * 
	 * @param sourceMatrix
	 *            The booleans to create the matrix from.
	 */
	public static function matrixFromBooleans( array $sourceMatrix ) {
		$out = new Matrix( count($sourceMatrix), count($sourceMatrix[0] ) );
		for( $r = 0; $r < $out->getRows(); ++$r) {
			for( $c = 0; $c < $out->getCols(); ++$c) {
				if( $sourceMatrix[$r][$c] ) {
					$out->set( $r, $c, 1 );
				} else {
					$out->set( $r, $c, -1 );
				}
			}
		}
		return $out;
	}

	/**
	 * Create a matrix from an array of doubles.
	 * 
	 * @param sourceMatrix
	 *            An array of doubles.
	 */
	public static function matrixFromDoubles( array $sourceMatrix ) {
		$out = new Matrix( count($sourceMatrix), count($sourceMatrix[0] ) );
		for( $r = 0; $r < $out->getRows(); ++$r) {
			for( $c = 0; $c < $out->getCols(); ++$c) {
				$out->set( $r, $c, $sourceMatrix[$r][$c] );
			}
		}
		return $out;
	}

	/**
	 * Create a blank array with the specified number of rows and columns.
	 * 
	 * @param rows
	 *            How many rows in the matrix.
	 * @param cols
	 *            How many columns in the matrix.
	 */
	public function __construct( $rows = 0, $cols = 0 ) {
		$this->matrix = array();
		for( $r=0; $r<$rows; ++$r ) {
			$this->matrix[$r] = array();
			for( $c=0; $c<$cols; ++$c ) {
				$this->matrix[$r][$c] = 0.0;
			}
			
		}
	}

	/**
	 * Add a value to one cell in the matrix.
	 * 
	 * @param row
	 *            The row to add to.
	 * @param col
	 *            The column to add to.
	 * @param value
	 *            The value to add to the matrix.
	 */
	public function add( $row, $col, $value ) {
		$this->validate( $row, $col );
		$newValue = $this->matrix[$row][$col] + $value;
		$this->set( $row, $col, $newValue );
	}

	/**
	 * Add the specified matrix to this matrix. This will modify the matrix to
	 * hold the result of the addition.
	 * 
	 * @param theMatrix
	 *            The matrix to add.
	 */
	public function addMatrix( Matrix $theMatrix ) {
		$source = theMatrix.getData();

		for( $row = 0; $row < getRows(); ++$row ) {
			for( $col = 0; $col < getCols(); ++$col ) {
				$this->matrix[$row][$col] += $source[$row][$col];
			}
		}
	}

	/**
	 * Set all rows and columns to zero.
	 */
	public function clear() {
		for( $r = 0; $r < $this->getRows(); ++$r ) {
			for( $c = 0; $c < $this->getCols(); ++$c ) {
				$this->matrix[$r][$c] = 0;
			}
		}
	}

	/**
	 * Compare to matrixes with the specified level of precision.
	 * 
	 * @param theMatrix
	 *            The other matrix to compare to.
	 * @param precision
	 *            How much precision to use.
	 * @return True if the two matrixes are equal.
	 */
	public function equals( Matrix $theMatrix, $precision = \Encog\DEFAULT_PRECISION ) {

		if( $precision < 0 ) {
			throw new MatrixError("Precision can't be a negative number.");
		}

		$test = pow( 10.0, $precision );
		if( is_infinite($test) || ($test > PHP_INT_MAX ) ) {
			throw new MatrixError("Precision of " + $precision
					+ " decimal places is not supported.");
		}

		$actualPrecision = intval( pow(\Encog\DEFAULT_PRECISION,
				$precision ) );

		$data = $theMatrix->getData();

		for( $r = 0; $r < $this->getRows(); ++$r ) {
			for( $c = 0; $c < $this->getCols(); ++$c ) {
				if(intval( $this->matrix[$r][$c] * $actualPrecision) 
						!= intval( $data[$r][$c] * $actualPrecision)) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Create a matrix from a packed array.
	 * 
	 * @param array
	 *            The packed array.
	 * @param index
	 *            Where to start in the packed array.
	 * @return The new index after this matrix has been read.
	 */
	public function fromPackedArray( array $array, $index ) {
		$i = $index;
		for( $r = 0; $r < $this->getRows(); ++$r ) {
			for( $c = 0; $c < $this->getCols(); ++$c ) {
				$this->matrix[$r][$c] = $array[$i++];
			}
		}

		return $i;
	}

	/**
	 * Read the specified cell in the matrix.
	 * 
	 * @param row
	 *            The row to read.
	 * @param col
	 *            The column to read.
	 * @return The value at the specified row and column.
	 */
	public function get( $row, $col ) {
		$this->validate($row, $col);
		return $this->matrix[$row][$col];
	}

	/**
	 * @return A COPY of this matrix as a 2d array.
	 */
	public function getArrayCopy() {
		return $this->matrix;
	}

	/**
	 * Read one entire column from the matrix as a sub-matrix.
	 * 
	 * @param col
	 *            The column to read.
	 * @return The column as a sub-matrix.
	 */
	public function getCol( $col ) {
		if( $col > $this->getCols() ) {
			throw new MatrixError("Can't get column #" + $col
					+ " because it does not exist.");
		}

		$newMatrix = array();

		for( $row = 0; $row < $this->getRows(); ++$row ) {
			$newMatrix[$row][0] = $this->matrix[$row][$col];
		}

		return $this->matrixFromDoubles( $newMatrix );
	}

	/**
	 * Get the columns in the matrix.
	 * 
	 * @return The number of columns in the matrix.
	 */
	public function getCols() {
		return count( $this->matrix[0] );
	}

	/**
	 * @return Get the 2D matrix array.
	 */
	public function getData() {
		return $this->matrix;
	}

	/**
	 * Get a submatrix.
	 * 
	 * @param i0
	 *            Initial row index.
	 * @param i1
	 *            Final row index.
	 * @param j0
	 *            Initial column index.
	 * @param j1
	 *            Final column index.
	 * @return The specified submatrix.
	 */
	public function getMatrix( $i0, $i1, $j0, $j1 ) {

		$result = new Matrix( $i1 - $i0 + 1, $j1 - $j0 + 1 );
		$b = $result->getData();
		try {
			for( $i = $i0; $i <= $i1; ++$i ) {
				for( $j = $j0; $j <= $j1; ++$j ) {
					$b[$i - $i0][$j - $j0] = $this->matrix[$i][$j];
				}
			}
		} catch ( ArrayIndexOutOfBoundsException $e ) {
			throw new MatrixError("Submatrix indices");
		}
		return $result;
	}

	/**
	 * Get the specified row as a sub-matrix.
	 * 
	 * @param row
	 *            The row to get.
	 * @return A matrix.
	 */
	public function getRow( $row ) {
		if( $row > $this->getRows() ) {
			throw new MatrixError("Can't get row #" + $row
					+ " because it does not exist.");
		}

		$newMatrix = array();

		for( $col = 0; $col < $this->getCols(); ++$col ) {
			$newMatrix[0][$col] = $this->matrix[$row][$col];
		}

		return Matrix::matrixFromDoubles( $newMatrix );
	}

	/**
	 * Get the number of rows in the matrix.
	 * 
	 * @return The number of rows in the matrix.
	 */
	public function getRows() {
		return count( $this->matrix );
	}

	/**
	 * Compute a hash code for this matrix.
	 * 
	 * @return The hash code.
	 */
	public function hashCode() {
		$result = 0;
		for( $r = 0; $r < getRows(); ++$r ) {
			for( $c = 0; $c < getCols(); $c ) {
				$result += $this->matrix[$r][$c];
			}
		}
		return intval( $result % PHP_INT_MAX );
	}

	/**
	 * @return The matrix inverted.
	 */
	public function inverse() {
		return $this->solve( MatrixMath::identity( $this->getRows() ) );
	}

	/**
	 * Determine if the matrix is a vector. A vector is has either a single
	 * number of rows or columns.
	 * 
	 * @return True if this matrix is a vector.
	 */
	public function isVector() {
		if( $this->getRows() == 1 ) {
			return true;
		}
		return $this->getCols() == 1;
	}

	/**
	 * Return true if every value in the matrix is zero.
	 * 
	 * @return True if the matrix is all zeros.
	 */
	public function isZero() {
		for( $row = 0; $row < $this->getRows(); ++$row ) {
			for( $col = 0; $col < $this->getCols(); ++$col ) {
				if( $this->matrix[$row][$col] != 0 ) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Multiply every value in the matrix by the specified value.
	 * 
	 * @param value
	 *            The value to multiply the matrix by.
	 */
	public function multiplyAll( $value ) {

		for( $row = 0; $row < getRows(); ++$row ) {
			for( $col = 0; $col < getCols(); ++$col ) {
				$this->matrix[$row][$col] *= $value;
			}
		}
	}

	/**
	 * Multiply every row by the specified vector.
	 * 
	 * @param vector
	 *            The vector to multiply by.
	 * @param result
	 *            The result to hold the values.
	 */
	public function multiply( array $vector, array &$result) {
		for( $i = 0; $i < getRows(); ++$i ) {
			$result[$i] = 0;
			for( $j = 0; $j < getCols(); ++$j ) {
				$result[$i] += $this->matrix[$i][$j] * $vector[$j];
			}
		}
	}

	/**
	 * Randomize the matrix.
	 * @param min Minimum random value.
	 * @param max Maximum random value.
	 */
	public function randomize( $min,  $max ) {
		for( $row = 0; $row < getRows(); ++$row ) {
			for( $col = 0; $col < getCols(); $col ) {
				$this->matrix[$row][$col] = RangeRandomizer::randomize( $min, $max );
			}
		}

	}

	/**
	 * Set every value in the matrix to the specified value.
	 * 
	 * @param value
	 *            The value to set the matrix to.
	 */
	public function setAll( $value ) {
		for( $row = 0; $row < getRows(); ++$row ) {
			for( $col = 0; $col < getCols(); $col ) {
				$this->matrix[$row][$col] = $value;
			}
		}

	}

	/**
	 * Set an individual cell in the matrix to the specified value.
	 * 
	 * @param row
	 *            The row to set.
	 * @param col
	 *            The column to set.
	 * @param value
	 *            The value to be set.
	 */
	public function set( $row, $col, $value ) {
		$this->validate( $row, $col );
		$this->matrix[$row][$col] = $value;
	}

	/**
	 * Set this matrix's values to that of another matrix.
	 * 
	 * @param theMatrix
	 *            The other matrix.
	 */
	/*public function set( Matrix $theMatrix ) {
		final double[][] source = theMatrix.getData();

		for (int row = 0; row < getRows(); row++) {
			for (int col = 0; col < getCols(); col++) {
				this.matrix[row][col] = source[row][col];
			}
		}
	}*/

	/**
	 * Set a submatrix.
	 * 
	 * @param i0
	 *            Initial row index
	 * @param i1
	 *            Final row index
	 * @param j0
	 *            Initial column index
	 * @param j1
	 *            Final column index
	 * @param x
	 *            A(i0:i1,j0:j1)
	 * 
	 */
	/*public function setMatrix(final int i0, final int i1, final int j0,
			final int j1, final Matrix x) {
		try {
			for (int i = i0; i <= i1; i++) {
				for (int j = j0; j <= j1; j++) {
					this.matrix[i][j] = x.get(i - i0, j - j0);
				}
			}
		} catch (final ArrayIndexOutOfBoundsException e) {
			throw new MatrixError("Submatrix indices");
		}
	}

	/**
	 * Set a submatrix.
	 * 
	 * @param i0
	 *            Initial row index
	 * @param i1
	 *            Final row index
	 * @param c
	 *            Array of column indices.
	 * @param x
	 *            The submatrix.
	 */

	/*public void setMatrix(final int i0, final int i1, final int[] c,
			final Matrix x) {
		try {
			for (int i = i0; i <= i1; i++) {
				for (int j = 0; j < c.length; j++) {
					this.matrix[i][c[j]] = x.get(i - i0, j);
				}
			}
		} catch (final ArrayIndexOutOfBoundsException e) {
			throw new ArrayIndexOutOfBoundsException("Submatrix indices");
		}
	}*/

	/**
	 * Set a submatrix.
	 * 
	 * @param r
	 *            Array of row indices.
	 * @param j0
	 *            Initial column index
	 * @param j1
	 *            Final column index
	 * @param x
	 *            A(r(:),j0:j1)
	 */

	/*public void setMatrix(final int[] r, final int j0, final int j1,
			final Matrix x) {
		try {
			for (int i = 0; i < r.length; i++) {
				for (int j = j0; j <= j1; j++) {
					this.matrix[r[i]][j] = x.get(i, j - j0);
				}
			}
		} catch (final ArrayIndexOutOfBoundsException e) {
			throw new ArrayIndexOutOfBoundsException("Submatrix indices");
		}
	}*/

	/**
	 * Set a submatrix.
	 * 
	 * @param r
	 *            Array of row indices.
	 * @param c
	 *            Array of column indices.
	 * @param x
	 *            The matrix to set.
	 */
	/*public void setMatrix(final int[] r, final int[] c, final Matrix x) {
		try {
			for (int i = 0; i < r.length; i++) {
				for (int j = 0; j < c.length; j++) {
					this.matrix[r[i]][c[j]] = x.get(i, j);
				}
			}
		} catch (final ArrayIndexOutOfBoundsException e) {
			throw new MatrixError("Submatrix indices");
		}
	}*/

	/**
	 * Get the size of the array. This is the number of elements it would take
	 * to store the matrix as a packed array.
	 * 
	 * @return The size of the matrix.
	 */
	public function size() {
		return count($this->matrix[0]) * count($this->matrix);
	}

	/**
	 * Solve A*X = B.
	 * 
	 * @param b
	 *            right hand side.
	 * @return Solution if A is square, least squares solution otherwise.
	 */
	/*public Matrix solve(final Matrix b) {
		if (getRows() == getCols()) {
			return (new LUDecomposition(this)).solve(b);
		} else {
			return (new QRDecomposition(this)).solve(b);
		}
	}*/

	/**
	 * Sum all of the values in the matrix.
	 * 
	 * @return The sum of the matrix.
	 */
	public function sum() {
		$result = 0.0;
		for( $r = 0; $r < $this->getRows(); ++$r ) {
			for( $c = 0; $c < $this->getCols(); ++$c ) {
				$result += $this->matrix[$r][$c];
			}
		}
		return $result;
	}

	/**
	 * Convert the matrix into a packed array.
	 * 
	 * @return The matrix as a packed array.
	 */
	public function toPackedArray() {
		$result = array();

		$index = 0;
		for( $r = 0; $r < $this->getRows(); ++$r ) {
			for( $c = 0; $c < $this->getCols(); ++$c ) {
				$result[$index++] = $this->matrix[$r][$c];
			}
		}

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	/*@Override
	public String toString() {
		final StringBuilder result = new StringBuilder();
		result.append("[Matrix: rows=");
		result.append(getRows());
		result.append(",cols=");
		result.append(getCols());
		result.append("]");
		return result.toString();
	}
	*/
		
	/**
	 * Validate that the specified row and column are within the required
	 * ranges. Otherwise throw a MatrixError exception.
	 * 
	 * @param row
	 *            The row to check.
	 * @param col
	 *            The column to check.
	 */
	private function validate( $row, $col ) {
		if( ( $row >= $this->getRows() ) || ( $row < 0 ) ) {
			$str = "The row:" . $row . " is out of range:"
					. $this->getRows();
			throw new MatrixError( $str );
		}

		if( ( $col >= $this->getCols() ) || ( $col < 0 ) ) {
			$str = "The col:" . $col . " is out of range:"
					. $this->getCols();
			throw new MatrixError( $str );
		}
	}
	
	public function isSquare() {
		return getRows() == getCols();
	}
}