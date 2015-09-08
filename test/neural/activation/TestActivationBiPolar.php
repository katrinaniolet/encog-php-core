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
namespace Test\Neural\Activation;

use \Encog\Engine\Network\Activation\ActivationBiPolar;

require_once ("Engine/Network/Activation/ActivationBiPolar.php");
class TestActivationBiPolar extends \PHPUnit_Framework_TestCase {

	public function testBiPolar() {
		$activation = new ActivationBiPolar();
		$this->assertTrue( $activation->hasDerivative() );
		
		$clone = clone ($activation);
		$this->assertNotNull( $clone );
		
		$input = [ 
				0.5,
				- 0.5 ];
		
		$activation->activationFunction( $input, 0, count($input) );
		
		$this->assertEquals( 1.0, $input[0], 0.1 );
		$this->assertEquals( - 1.0, $input[1], 0.1 );
	}
}