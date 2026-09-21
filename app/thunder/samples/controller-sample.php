<?php

use \Core\Session;
use \Core\Request;
use \Core\Database;

$req = new Request;


/* 
* -----------------------------
* Check if its a post request
* -----------------------------
* if($req->method == 'POST')	// alternative way to check
*/
if($req->posted())
{

	$ses = new Session;
	$db = new Database;

	/* 
	* -----------------------------------------------
	* Current User's Data
	* -----------------------------------------------
	* $user = $ses->user();  				// get entire row
	* $user = $ses->user('id') 				// get single user column by key;
	*/

	//$getData = $req->get(); 				// get all GET vars
	$postData = $req->post(); 				// get all post data


	/* 
	* -----------------------------------------------
	* Read or save data to the database
	* -----------------------------------------------
	* $rows = $db->query("select *  from users");
	* $row = $db->get_row("select *  from users"); // get single row;
	* 
	* $id = $db->insert_id; 				// get id of last inserted row;
	* $row_count = $db->affected_rows; 		// get rows affected by update or delete;
	* if($db->has_error) 					// get single row;
	* $error = $db->error; 					// get single row;
	*/	


	/* 
	* -----------------------------------------------
	* Save data to be used in other functions/actions
	* -----------------------------------------------
	* $values = get_value(); 	//get data saved with set_value
	*/
	set_value([
		'key'=> 'value',
		'key2'=> 'value2',
	]);
}