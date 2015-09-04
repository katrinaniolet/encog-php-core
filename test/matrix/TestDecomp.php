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
use \Encog\MathUtil\Matrices\decomposition\CholeskyDecomposition;

require_once ("MathUtil/Matrices/Matrix.php");
require_once ("MathUtil/Matrices/MatrixMath.php");
require_once ("MathUtil/Matrices/decompisition/CholeskyDecomposition.php");
class TestDecomp extends \PHPUnit_Framework_TestCase {

	public function testCholesky() {
		$m1 = [ 
				[ 
						1,
						0,
						0,
						0 ],
				[ 
						0,
						1,
						0,
						0 ],
				[ 
						0,
						0,
						1,
						0 ],
				[ 
						0,
						0,
						0,
						1 ] ];
		$matrix1 = Matrix::matrixFromDoubles( $m1 );
		
		$m2 = [ 
				[ 
						17,
						18,
						19,
						20 ],
				[ 
						21,
						22,
						23,
						24 ],
				[ 
						25,
						27,
						28,
						29 ],
				[ 
						37,
						33,
						31,
						30 ] ];
		$matrix2 = Matrix::matrixFromDoubles( $m2 );
		
		$c = new CholeskyDecomposition( $matrix1 );
		$c->solve( $matrix2 );
		
		$mx = $c->getL();
		
		$this->assertEquals( 1.0, $mx->get( 0, 0 ) );
		$this->assertEquals( 1.0, $mx->get( 1, 1 ) );
		$this->assertEquals( 1.0, $mx->get( 2, 2 ) );
		$this->assertEquals( 4, $mx->getRows() );
		$this->assertEquals( 4, $mx->getCols() );
	}
}