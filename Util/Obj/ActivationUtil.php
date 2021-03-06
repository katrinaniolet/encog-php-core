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
namespace Encog\Util\Obj\ActivationUtil;

use \Encog\Engine\Network\Activation\ActivationFunction;
use \Encog\Util\CSV\CSVFormat;
use \Encog\Util\CSV\NumberList;

require_once ("Engine/Network/Activation/ActivationFunction.php");
require_once ("Util/CSV/CSVFormat.php");
require_once ("Util/CSV/NumberList.php");

/**
 *
 * @param string $name        	
 * @param ActivationFunction $af        	
 * @return string
 */
function generateActivationFactory( $name, ActivationFunction $af ) {
	$result = strtoupper( $name );
	
	if( $af->getParams() != null && count( $af->getParams() ) > 0 ) {
		$result .= '[';
		NumberList\toList( CSVFormat::EG_FORMAT, $result, $af->getParams() );
		$result .= ']';
	}
	
	return $result;
}