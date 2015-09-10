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
namespace Encog\MathUtil\Matrices\Decomposition;

use Encog\MathUtil\Matrices\Matrix;
use Encog\MathUtil\Matrices\MatrixError;

require_once ("MathUtil/Matrices/Matrix.php");
require_once ("MathUtil/Matrices/MatrixError.php");

/**
 * Cholesky Decomposition.
 *
 * For a symmetric, positive definite matrix A, the Cholesky decomposition is an
 * lower triangular matrix L so that A = L*L'.
 *
 * If the matrix is not symmetric or positive definite, the constructor returns
 * a partial decomposition and sets an internal flag that may be queried by the
 * isSPD() method.
 *
 * This file based on a class from the public domain JAMA package.
 * http://math.nist.gov/javanumerics/jama/
 */
class CholeskyDecomposition {
	
	/**
	 * Array for internal storage of decomposition.
	 *
	 * @var double[][] $l
	 */
	private $l = array();
	
	/**
	 * Row and column dimension (square matrix).
	 *
	 * @var int $n
	 */
	private $n = 0;
	
	/**
	 * Symmetric and positive definite flag.
	 *
	 * @var bool $isspd
	 */
	private $isspd = false;

	/**
	 * Cholesky algorithm for symmetric and positive definite matrix.
	 *
	 * @param
	 *        	matrix
	 *        	Square, symmetric matrix.
	 */
	public function __construct( Matrix $matrix ) {
		
		// Initialize.
		$a = $matrix->getData();
		$this->n = $matrix->getRows();
		$this->l = new \ArrayObject();
		for($r=0; $r<$this->n; ++$r) {
			$this->l[$r] = new \ArrayObject();
			for($c=0;$c<$this->n;++$c) {
				$this->l[$r][$c] = 0.0;
			}
		}
		$this->isspd = ($matrix->getCols() == $this->n);
		// Main loop.
		for( $j = 0; $j < $this->n; ++$j ) {
			$lrowj = $this->l[$j];
			$d = 0.0;
			for( $k = 0; $k < $j; ++$k ) {
				$lrowk = $this->l[$k];
				$s = 0.0;
				for( $i = 0; $i < $k; ++$i ) {
					$s += $lrowk[$i] * $lrowj[$i];
				}
				$s = ($a[$j][$k] - $s) / $this->l[$k][$k];
				$lrowj[$k] = $s;
				$d = $d + $s * $s;
				$this->isspd = $this->isspd & ($a[$k][$j] == $a[$j][$k]);
			}
			$d = $a[$j][$j] - $d;
			$this->isspd = $this->isspd & ($d > 0.0);
			$this->l[$j][$j] = sqrt( max( $d, 0.0 ) );
			for( $k = $j + 1; $k < $this->n; ++$k ) {
				$this->l[$j][$k] = 0.0;
			}
		}
	}

	/**
	 * Is the matrix symmetric and positive definite?
	 *
	 * @return true if A is symmetric and positive definite.
	 */
	public function isSPD() {
		return $this->isspd;
	}

	/**
	 * Return triangular factor.
	 *
	 * @return L
	 */
	public function getL() {
		return Matrix::matrixFromDoubles( $this->l->getArrayCopy() );
	}

	/**
	 * Solve A*X = B.
	 *
	 * @param
	 *        	b
	 *        	A Matrix with as many rows as A and any number of columns.
	 * @return X so that L*L'*X = b.
	 */
	public function solve( Matrix $b ) {
		if( $b->getRows() != $this->n ) {
			throw new MatrixError( "Matrix row dimensions must agree." );
		}
		if( ! $this->isspd ) {
			throw new RuntimeException( "Matrix is not symmetric positive definite." );
		}
		
		// Copy right hand side.
		$x = $b->getArrayCopy();
		$nx = $b->getCols();
		
		// Solve L*Y = B;
		for( $k = 0; $k < $this->n; ++$k ) {
			for( $j = 0; $j < $nx; ++$j ) {
				for( $i = 0; $i < $k; ++$i ) {
					$x[$k][$j] -= $x[$i][$j] * $this->l[$k][$i];
				}
				$x[$k][$j] /= $this->l[$k][$k];
			}
		}
		
		// Solve L'*X = Y;
		for( $k = $this->n - 1; $k >= 0; $k-- ) {
			for( $j = 0; $j < $nx; ++$j ) {
				for( $i = $k + 1; $i < $this->n; ++$i ) {
					$x[$k][$j] -= $x[$i][$j] * $this->l[$i][$k];
				}
				$x[$k][$j] /= $this->l[$k][$k];
			}
		}
		
		return Matrix::matrixFromDoubles( $x );
	}

	public function getDeterminant() {
		$result = 1.0;
		
		for( $i = 0; $i < $this->n; ++$i )
			$result *= $this->l[$i][$i];
		
		return $result * $result;
	}

	public function inverseCholesky() {
		$li = lowerTriangularInverse( $l );
		$ic = array();
		
		for( $r = 0; $r < $this->n; ++$r )
			for( $c = 0; $c < $this->n; ++$c )
				for( $i = 0; $i < $this->n; ++$i )
					$ic[$r][$c] += $li[$i][$r] * $li[$i][$c];
		
		return Matrix::matrixFromDoubles( $ic );
	}

	private function lowerTriangularInverse( array $m ) {
		$lti = array();
		
		for( $j = 0; $j < count( $m ); ++$j ) {
			if( $m[$j][$j] == 0 )
				throw new IllegalArgumentException( "Error, the matrix is not full rank" );
			
			$lti[$j][$j] = 1. / $m[$j][$j];
			
			for( $i = $j + 1; $i < count( $m ); ++$i ) {
				$sum = 0.;
				
				for( $k = $j; $k < $i; ++$k )
					$sum -= $m[$i][$k] * $lti[$k][$j];
				
				$lti[$i][$j] = $sum / $m[$i][$i];
			}
		}
		
		return $lti;
	}
}