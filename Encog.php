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

namespace Encog;
/**
 * The default encoding used by Encog.
 */
const DEFAULT_ENCODING = "UTF-8";

/**
 * The current engog version, this should be read from the properties.
 */
const VERSION = "3.4.0";

/**
 * The current engog version, this should be read from the properties.
 */
const COPYRIGHT = "Copyright 2014 by Heaton Research, Inc.";

/**
 * The current engog version, this should be read from the properties.
 */
const LICENSE = "Open Source under the Apache License";

/**
 * The current engog file version, this should be read from the properties.
 */
const FILE_VERSION = "1";

/**
 * The default precision to use for compares.
 */
const DEFAULT_PRECISION = 9;

/**
 * Default point at which two doubles are equal.
 */
const DEFAULT_DOUBLE_EQUAL = 0.0000000000001;

/**
 * The version of the Encog JAR we are working with. Given in the form
 * x.x.x.
 */
const ENCOG_VERSION = "encog.version";

/**
 * The encog file version. This determines of an encog file can be read.
 * This is simply an integer, that started with zero and is incremented each
 * time the format of the encog data file changes.
 */
const ENCOG_FILE_VERSION = "encog.file.version";