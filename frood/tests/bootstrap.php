<?php
/**
 * The Frood test bootstrap file
 *
 * PHP Version 5
 *
 * @category Library
 * @package  Frood
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2011-06-10
 */
require_once dirname(__FILE__).'/../src/Frood.php';

// This basically just sets up the autoloader, without booting Xoops.
new Frood(null, false, false);
