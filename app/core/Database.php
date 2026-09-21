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

namespace Core;
use \PDO;
use \PDOException;
use \Model\QueryBuilder;

/* restrict direct access to this file */
defined('ROOTPATH') or die("Direct script access denied");

/**
 * Main Database class containing a single static instance of a PDO connnection
 */
class Database
{
	use QueryBuilder;

	/** Set an id to identify a particular query e.g $db::$query_id = 'my-id' */
	public static $query_id 	= '';
	public $affected_rows 		= 0;
	public $insert_id 			= 0;
	public $error 				= '';
	public $has_error 			= false;

	/** @ignore */
	protected ?PDO $con = null;

	/**
	 * Connect to the database
	 * @ignore 
	 * @return PDO Returns a PDO con object
	 * 
	 */
	protected function connect():PDO
	{

		if ($this->con === null) {

			$VARS['DB_NAME'] 		= DB_NAME;
			$VARS['DB_USER'] 		= DB_USER;
			$VARS['DB_PASSWORD'] 	= DB_PASSWORD;
			$VARS['DB_HOST'] 		= DB_HOST;
			$VARS['DB_DRIVER'] 		= DB_DRIVER;
			$VARS['DB_PORT'] 		= DB_PORT;

			$VARS = do_filter('before_db_connect',$VARS);

			$string = "$VARS[DB_DRIVER]:host=$VARS[DB_HOST];port=$VARS[DB_PORT];dbname=$VARS[DB_NAME]";

			try
			{
				$this->con = new PDO($string,$VARS['DB_USER'],$VARS['DB_PASSWORD']);
				$this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			} catch (PDOException $e) {
				
				die("Failed to connect to database with error " . $e->getMessage());
			}
		}
			
		return $this->con;

	}

	public function getConnection()
	{
		return $this->connect();
	}
 
	/**
	 	 * Runs a query to the database and returns a single result if successful
	 	 * 
	 * @param string $query A query string to be executed as a prepared statement
	 * @param array $data An array of values to replace in the query string
	 * @param string $data_type What data type to return. Can be array or object
	 * @return array A single row as an array or a single row as an object if object is selected as $data_type
	 * @return  bool True or false depending if the query was successful or not
	 * 
	 */
	public function get_row(string $query, array $data = [], string $data_type = 'object'):mixed
	{

		$result = $this->query($query,$data,$data_type);
		if(is_array($result) && count($result) > 0)
		{
			return $result[0];
		}

		return false;
	}

	/**
	 * Runs a query to the database and returns the result if successful
	 * 
	 * @param string $query A query string to be executed as a prepared statement
	 * @param array $data An array of values to replace in the query string
	 * @param string $data_type What data type to return. Can be array or object
	 * @return array An array of objects in case of multiple rows or an array of arrays if array is selected as $data_type
	 * @return  bool True or false depending if the query was successful or not
	 * 
	 */
	public function query(string $query, array $data = [], string $data_type = 'object'):array|bool
	{

		$query = do_filter('before_query_query',$query);
		$data = do_filter('before_query_data',$data);

		$this->error 				= '';
		$this->has_error 			= false;

		$con = $this->connect();

		try
		{
			$stm = $con->prepare($query);

			$result = $stm->execute($data);
			$this->affected_rows 	= $stm->rowCount();
			
			if (stripos(strtolower($query), 'insert') === 0) {
			    $this->insert_id = $con->lastInsertId();
			} else {
			    $this->insert_id = 0;
			}

			if($result)
			{
				if($data_type == 'object'){
					$rows = $stm->fetchAll(PDO::FETCH_OBJ);
				}else{
					$rows = $stm->fetchAll(PDO::FETCH_ASSOC);
				}

			}

		}catch(PDOException $e)
		{
			$this->error 				= $e->getMessage();
			$this->has_error 			= true;
		}


		$arr = [];
		$arr['query'] = $query;
		$arr['data'] = $data;
		$arr['result'] = $rows ?? [];
		$arr['query_id'] = self::$query_id;
		self::$query_id = '';

		$result = do_filter('after_query',$arr);

		if(is_array($result['result']) && count($result['result']) > 0)
		{
			return $result['result'];
		}

		return false;
	}

}