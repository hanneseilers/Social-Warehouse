<?php
	/*
	 * API calls
	 * Set GET parameter function to call api function.
	 */

	session_start();
	include( "db/db.php" );
	
	$OK = "ok";
	$ERR = "err";
	$SEP = ";";
	
	function _updateGroupInfo($id){
		$_SESSION['groupinfo'] = db_getGroupInfo( $id )[0];
	}
	
	/* 
	 * check if login data is valid
	 * group = id
	 * pw = md5 password
	 * @return = <status>;<group-id>
	*/
	if( $_GET['function'] == "checkLogin" ){
		if( db_checkGroupLogin($_GET['group'], $_GET['pw']) ){
			_updateGroupInfo( $_GET['group'] );
			print $OK;
		} else {
			print $ERR;
		}
		
		print $SEP.$_GET['group'];
	}
	
	/*
	 * Logout and destory session 
	 */
	if( $_GET['function'] == "logout" ){
		session_destroy();
	}
	
	/*
	 * Add a new group
	 * name = group name
	 * desc = description
	 * pw = md5 password
	 * @return = <status>;<group-id>
	 */
	if( $_GET['function'] == "addGroup" ){
		$id = db_addGroup( base64_decode($_GET['name']), base64_decode($_GET['desc']), $_GET['pw'] );
		if( $id > 0 )
			print $OK.$SEP.$id;
		else
			print $ERR;
	}
	
	/*
	 * Change group name
	 * name = new group name
	 * @return = <status>
	 */
	if( isset($_SESSION['groupinfo']) && $_GET['function'] == "changeGroupInfo" ){
		if( db_changeGroupInfo($_SESSION['groupinfo']['id'], base64_decode($_GET['name']), base64_decode($_GET['desc']), $_GET['pw']) ){
			_updateGroupInfo( $_SESSION['groupinfo']['id'] );
			print $OK;
		} else {
			print $ERR;
		}
	}
	
?>