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
 * @package   Phergie_Core
 * @author    Phergie Development Team <team@phergie.org>
 * @copyright 2008-2010 Phergie Development Team (http://phergie.org)
 * @license   http://phergie.org/license New BSD License
 * @link      http://pear.phergie.org/package/Phergie_Core
 */

/**
 * Helper plugin to assist other plugins with string sanitisation.
 *
 * @category Phergie
 * @package  Phergie_Core
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie_Core
 */
class Phergie_Plugin_Helper_String
{
    /**
     * Decodes HTML characters ("&gt;" => ">"), since they're no threat to IRC
     * and removes formatting codes which can effect IRC (bold, colours, etc).
     * 
     * TODO: It removes the colour-code character, but not the following
     *       numerical colour code, meaning it can look a little odd.
     * 
     * @param string $output String to clean.
     * 
     * @return string Cleaned version of the input.
     */
    public static function cleanString($output)
    {
        $output = htmlspecialchars_decode($output);
        $output = str_replace(array('', '', ''), '', $output);
        
        return $output;
    }

}
