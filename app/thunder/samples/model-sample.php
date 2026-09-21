<?php

/**
 * This file is part of the ThunderPHP Framework.
 * 
 * @package ThunderPHP
 * @author Your Name <youremail@email.com>
 * @version 1.0.0
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

namespace {NAMESPACE};
use \Model\Model;

defined('ROOTPATH') or die("Direct script access denied");

/**
 * {CLASS_NAME} class
 */
class {CLASS_NAME} extends Model
{

    protected static string $table = '{TABLE_NAME}s';
    
    // Only these columns can be inserted
    protected static array $fillableInsert = [
    	'name', 
    	'email', 
    	'password', 
    	'role',
    ];
    
    // Only these columns can be updated
    protected static array $fillableUpdate = [
    	'name', 
    	'email', 
    	'role', 
    	'last_login',
    ];


}