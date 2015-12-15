<?php

class Database{
	
	private static $INSTANCE = null;
	
	private $dbConnection;
	private $sqlStatements;
	private $sqlCache;
	
	/**
	 * Constructor
	 */
	private function __construct(){
		$this->dbConnection = new mysqli( Config::$dbHost, Config::$dbUser, Config::$dbPassword, Config::$dbDatabase );
		
		if( $this->dbConnection->connect_error )
			Log::log( 'error', "Could not connect to database" );
	}
	
	/**
	 * Executes a SQL command.
	 * It creates a prepared sql request or uses an already prepared statement (depending on identiier).
	 * It can also use a cached result, instead of executing a new query.
	 * @param string $identifier		Name of operation (as unique identifier)
	 * @param string $sql				SQL Command
	 * @param string $parameterTypes	Parameter types for sql command (s = string, i = integer, d = double)
	 * @param array $parameters			Array of parameters to be binded to SQL satement
	 * @param string $useCache			If true a cached result is returned, if available.
	 */
	public function sql( $identifier, $sql, $parameterTypes='', $parameters=[], $useCache=true ){
		
		if( is_string($identifier) ){
		
			// check if to use cached result
			if( $useCache && isset($this->sqlCache[$identifier]) ){
				return $this->sqlCache[$identifier];
				
			} else if( is_string($sql)
					&& is_string($parameterTypes)
					&& is_array($parameters) ) {
				
				// check if to create a new prepared statement
				if( !isset($this->sqlStatements[$identifier]) || $this->sqlStatements[$identifier] == null )
					$this->sqlStatements[$identifier] = $this->dbConnection->prepare( $sql );
				$vStatement = & $this->sqlStatements[$identifier];
				
				// check if statement prepared
				if( $vStatement == false ){
					Log::log( 'error', "Cannot prepare statement ".$sql );
					return false;
				}
				
				// create array for parameters
				$vParams = array();
				$vParams[] = & $parameterTypes;
				for( $i=0; $i<strlen($parameterTypes); $i++ ){
					$vParams[] = & $parameters[$i];
				}
				
				// bind parameter
				if( count($parameters) > 0 )
					call_user_func_array( array($vStatement, 'bind_param'), $vParams );
				
				// check if to log sql query
				if( Config::$sqlLogEnabled ){
					echo "<hr />".$sql."<br />";
					var_dump( $parameters );
				}
				
				// execute statement
				if( !$vStatement->execute() )
					return false;
	
				// get result
				$vResult = array();
				if( method_exists($vStatement, 'get_result') ){
					
					// try native driver
					if( ($vGetResult = $vStatement->get_result()) )
						 $vResult = $vGetResult->fetch_all( MYSQLI_ASSOC );
					
				} else {
					
					// fallback to bind result data					
					// create variables to bind
					$row = array();
					$vMetaData = $vStatement->result_metadata();
					while( ($field = $vMetaData->fetch_field()) ){
						$var = $field->name;
						$$var = null;
						$row[$field->name] = & $$var;
					}
					
					// bind result variables
					call_user_func_array( array($vStatement, 'bind_result'), $row );
					
					// fetch results
					while( $vStatement->fetch() ){
						array_push( $vResult, $row );
					}
					
				}
				
				// check if last query has a result
				if( count($vResult) == 0 ){
					
					// check if to return last inserted id
					if( $this->dbConnection->insert_id > 0 )
						return $this->dbConnection->insert_id;
					
				} elseif( $useCache ) {
					// update cache
					$this->sqlCache[$identifier] = $vResult;
				}
					
				return $vResult;
				
			}
			
		}
		
	}
	
	/**
	 * Gets table name for database table.
	 * @param string $table	Table name
	 * @return string	Table name with database tables prefix.
	 */
	public static function getTableName($table){
		return 	Config::$dbPrefix.$table;
	}
	
	/**
	 * @return Instance of Database class.
	 */
	public static function getInstance(){
		if( Database::$INSTANCE == null ){
			Database::$INSTANCE = new Database();
		}
		
		return Database::$INSTANCE;
	}
	
}

?>
