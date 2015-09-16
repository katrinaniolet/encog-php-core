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
namespace Encog\MathUtil\Randomize\Generate;

require_once ("MathUtil/Randomize/Generate/AbstractBoxMuller.php");

/**
 * The Mersenne twister is a pseudo random number generator developed in 1997 by Makoto Matsumoto and
 * Takuji Nishimura that is based on a matrix linear recurrence over a finite binary field F2.
 *
 * References:
 * http://www.cs.gmu.edu/~sean/research
 * http://en.wikipedia.org/wiki/Mersenne_twister/
 *
 * Makato Matsumoto and Takuji Nishimura, "Mersenne Twister: A 623-Dimensionally Equidistributed Uniform
 * Pseudo-Random Number Generator", ACM Transactions on Modeling and. Computer Simulation,
 * Vol. 8, No. 1, January 1998, pp 3--30.
 */
class MersenneTwisterGenerateRandom extends AbstractBoxMuller {
	const N = 624;
	const M = 397;
	const MATRIX_A = 0x9908b0df;
	const UPPER_MASK = 0x80000000;
	const LOWER_MASK = 0x7fffffff;
	const TEMPERING_MASK_B = 0x9d2c5680;
	const TEMPERING_MASK_C = 0xefc60000;
	private $stateVector = array();
	private $mti = 0;
	private $mag01 = array();

	private function currentTimeMillis() {
		list( $usec, $sec ) = explode( " ", microtime() );
		return round( ((float)$usec + (float)$sec) * 1000 );
	}
	
	// TODO(katrina) check how this works out on 64 bit systems considering
	// the rest of the code
	private function unsignedRightShift( $a, $b ) {
		if( $b == 0 )
			return $a;
		return ($a >> $b) & ~ (1 << (8 * PHP_INT_SIZE - 1) >> ($b - 1));
	}

	public function __construct( $seed = null ) {
		if( is_array( $seed ) )
			$this->setSeedArray( $seed );
		else if( is_numeric( $seed ) )
			$this->setSeedNumber( $seed );
		else
			$this->setSeedNumber( $this->currentTimeMillis() );
	}

	public function setSeedNumber( $seed ) {
		$this->stateVector = array_fill( 0, MersenneTwisterGenerateRandom::N, 0 );
		
		$this->mag01 = array();
		$this->mag01[0] = 0x0;
		$this->mag01[1] = MersenneTwisterGenerateRandom::MATRIX_A;
		
		$this->stateVector[0] = intval( $seed );
		for( $this->mti = 1; $this->mti < MersenneTwisterGenerateRandom::N; ++$this->mti ) {
			$this->stateVector[$this->mti] = (1812433253 * ($this->stateVector[$this->mti - 1] ^ $this->unsignedRightShift( $this->stateVector[$this->mti - 1], 30 )) + $this->mti);
		}
	}

	public function setSeedArray( array $array ) {
		$setSeed( 19650218 );
		$i = 1;
		$j = 0;
		$k = (N > count( $array ) ? MersenneTwisterGenerateRandom::N : count( $array ));
		for(; $k != 0; --$k ) {
			$this->stateVector[$i] = ($this->stateVector[$i] ^ (($this->stateVector[$i - 1] ^ $this->unsignedRightShift( $this->stateVector[$i - 1], 30 )) * 1664525)) + $array[$j] + $j;
			++$i;
			++$j;
			if( $i >= MersenneTwisterGenerateRandom::N ) {
				$this->stateVector[0] = $this->stateVector[MersenneTwisterGenerateRandom::N - 1];
				$i = 1;
			}
			if( $j >= count( $array ) )
				$j = 0;
		}
		for( $k = N - 1; $k != 0; --$k ) {
			$this->stateVector[$i] = ($this->stateVector[$i] ^ (($this->stateVector[$i - 1] ^ $this->unsignedRightShift( $this->stateVector[$i - 1], 30 )) * 1566083941)) - $i;
			++$i;
			if( $i >= MersenneTwisterGenerateRandom::N ) {
				$this->stateVector[0] = $this->stateVector[MersenneTwisterGenerateRandom::N - 1];
				$i = 1;
			}
		}
		$this->stateVector[0] = 0x80000000;
	}

	protected function next( $bits ) {
		$y = 0;
		
		if( $this->mti >= MersenneTwisterGenerateRandom::N ) {
			$kk = 0;
			
			for( $kk = 0; $kk < MersenneTwisterGenerateRandom::N - MersenneTwisterGenerateRandom::M; ++$kk ) {
				$y = ($this->stateVector[$kk] & MersenneTwisterGenerateRandom::UPPER_MASK) | ($this->stateVector[$kk + 1] & MersenneTwisterGenerateRandom::LOWER_MASK);
				$this->stateVector[$kk] = $this->stateVector[$kk + MersenneTwisterGenerateRandom::M] ^ $this->unsignedRightShift( $y, 1 ) ^ $this->mag01[$y & 0x1];
			}
			for(; $kk < MersenneTwisterGenerateRandom::N - 1; ++$kk ) {
				$y = ($this->stateVector[$kk] & MersenneTwisterGenerateRandom::UPPER_MASK) | ($this->stateVector[$kk + 1] & MersenneTwisterGenerateRandom::LOWER_MASK);
				$this->stateVector[$kk] = $this->stateVector[$kk + (MersenneTwisterGenerateRandom::M - MersenneTwisterGenerateRandom::N)] ^ $this->unsignedRightShift( $y, 1 ) ^ $this->mag01[$y & 0x1];
			}
			$y = ($this->stateVector[MersenneTwisterGenerateRandom::N - 1] & MersenneTwisterGenerateRandom::UPPER_MASK) | ($this->stateVector[0] & MersenneTwisterGenerateRandom::LOWER_MASK);
			$this->stateVector[MersenneTwisterGenerateRandom::N - 1] = $this->stateVector[MersenneTwisterGenerateRandom::M - 1] ^ $this->unsignedRightShift( $y, 1 ) ^ $this->mag01[$y & 0x1];
			
			$this->mti = 0;
		}
		
		$y = $this->stateVector[$this->mti++];
		$y ^= $this->unsignedRightShift( $y, 11 );
		$y ^= ($y << 7) & MersenneTwisterGenerateRandom::TEMPERING_MASK_B;
		$y ^= ($y << 15) & MersenneTwisterGenerateRandom::TEMPERING_MASK_C;
		$y ^= $this->unsignedRightShift( $y, 18 );
		
		return $this->unsignedRightShift( $y, (32 - $bits) );
	}

	public function nextDouble() {
		return (intval( $this->next( 26 ) << 27 ) + $this->next( 27 )) / doubleval( 1 << 53 );
	}

	public function nextLong() {
		return (intval( next( 32 ) << 32 )) + next( 32 );
	}

	/**
	 * {@inheritDoc}
	 */
	public function nextBoolean() {
		return $this->nextDouble() > 0.5;
	}

	/**
	 * {@inheritDoc}
	 */
	public function nextFloat() {
		return $this->nextDouble();
	}

	/**
	 * {@inheritDoc}
	 */
	public function nextInt() {
		return $this->nextLong();
	}
}