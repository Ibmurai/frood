<?php
/**
 * The Frood test bootstrap file.
 *
 * PHP Version 5
 *
 * This file is part of The Frood framework.
 *
 * @link https://github.com/Ibmurai/frood
 *
 * @copyright Copyright 2011 Jens Riisom Schultz
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 *
 * @category Frood
 * @package  Test
 * @author   Jens Riisom Schultz <ibber_of_crew42@hotmail.com>
 * @since    2011-06-10
 */
require_once dirname(__FILE__).'/../src/Frood.php';

// This basically just sets up the autoloader.
new Frood();
