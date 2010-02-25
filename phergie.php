#!/usr/bin/env php
<?php
/**
 * Phergie 
 *
 * PHP version 5
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://phergie.org/license
 *
 * @category  Phergie
 * @package   Phergie
 * @author    Phergie Development Team <team@phergie.org>
 * @copyright 2008-2010 Phergie Development Team (http://phergie.org)
 * @license   http://phergie.org/license New BSD License
 * @link      http://pear.phergie.org/package/Phergie
 */

/**
 * @see Phergie_Autoload
 */
require 'Phergie/Autoload.php';
Phergie_Autoload::registerAutoloader();

$bot = new Phergie_Bot;

if ($argc > 0) {

    foreach ($argv as $file) {

		// Check to make sure Phergie won't try and include this file again
		if (strpos($file, 'phergie.php') === false) {

			// Check to see if an instance of Phergie_Config has been created
			// yet. We only create one when a valid file has been found to stop
			// use ending up with an empty config object.
			if (!isset($config)) {
				$config = new Phergie_Config;
			}
			$config->read($file);
		}
    }

	if (isset($config)) {
		$bot->setConfig($config);
	}
}

$bot->run();