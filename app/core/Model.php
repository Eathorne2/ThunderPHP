<?php

/**
 * This file is part of the ThunderPHP Framework.
 * 
 * @package ThunderPHP
 * @version 1.0.0
 * @author Eathorne Choongo <eathorne2012@yahoo.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

namespace Model;

use \PDO;
use \Core\Database;

defined('ROOTPATH') or die("Direct script access denied");

abstract class Model extends Database
{
        
    // Whitelist of columns allowed for INSERT operations.
    // If empty, all columns are allowed.
    protected array $fillableInsert = [];
    
    // Whitelist of columns allowed for UPDATE operations.
    // If empty, all columns are allowed.
    protected array $fillableUpdate = [];
 
    /**
     * Filter insert data according to $fillableInsert.
     * If $fillableInsert is empty, all columns are allowed.
     */
    protected function filterInsertData(array $data): array
    {
        if (empty($this->fillableInsert)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillableInsert));
    }

    /**
     * Filter update data according to $fillableUpdate.
     * If $fillableUpdate is empty, all columns are allowed.
     */
    protected function filterUpdateData(array $data): array
    {
        if (empty($this->fillableUpdate)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillableUpdate));
    }
 
}