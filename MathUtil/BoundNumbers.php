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

/**
 * TODO(katrina) documentation
 * A simple class that prevents numbers from getting either too big or too
 * small.
 */
namespace Encog\MathUtil\BoundNumbers;

/**
 * Too small of a number.
 */
const TOO_SMALL = -1.0E20;

/**
 * Too big of a number.
 */
const TOO_BIG = 1.0E20;

/**
 * Bound the number so that it does not become too big or too small.
 *
 * @param double d
 *            The number to check.
 * @return double The new number. Only changed if it was too big or too small.
 */
function bound($d) {
	if ($d < \Encog\MathUtil\BoundNumbers\TOO_SMALL) {
		return \Encog\MathUtil\BoundNumbers\TOO_SMALL;
	} else if ($d > \Encog\MathUtil\BoundNumbers\TOO_BIG) {
		return \Encog\MathUtil\BoundNumbers\TOO_BIG;
	} else {
		return $d;
	}
}
