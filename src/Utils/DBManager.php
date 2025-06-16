<?php

namespace App\Utils;


use App\Utils\Logger;
use \PDO;
use \PDOException;

/**
 * This class will be used to perform database query like
 * SELECT,INSERT,UPDATE and DELETE
 */
class DBManager
{
		//select multiple row
		const SELECT_MULTIPLE_ROW = "select_multiple_more";
		//select single row
		const SELECT_SINGLE_ROW = "select_single_more";
		//perform insert or update query
		const UPDATE_DATA = "update_data";
		private $logger = NULL;
		/**
		 * database connection istance
		 * @var [type]
		 */
		private $dbConnection;

		public function __construct(string $dsn,
        string $dbUser,
        string $dbPassword)
		{
			$this->logger = get_logger();	
			 try{

				$this->dbConnection = new PDO($dsn, $dbUser, $dbPassword,array(
					PDO::ATTR_PERSISTENT => true,
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
				));

			} catch (PDOException $pdoExec){
					$this->logger->error("Unable to connecti with databbase ".$dsn." due to:");
					$this->logger->error($pdoExec);
			}

		}

		/**
		 * Perform Insert update in batch to improve performance
		 * @param string $sql_query
		 * @param array $parameters
		 */
		public function executeBatchUpdate($sql_query, ?array $parameters = NULL)
		{
			$result_set = FALSE;
			//prepare query
			$statement = $this->dbConnection->prepare($sql_query);
			if(!is_null($parameters)){
				//To support multiple inserts or update i have added this logic
				// it is expected that $parameter_value is always array
				foreach($parameters as $param_key => $parameter_value){

					foreach($parameter_value as $array_param_key => $array_parameter_value){
						$statement->bindValue($array_param_key,$array_parameter_value);
					}
					//perform execute query
					try {

						$result_set = $statement->execute();

					}catch(PDOException $exec) {

						$this->logger->error("Error occured due to:");
						$this->logger->error($exec);

						return FALSE;
					}
				}
			}


			return $result_set;
		}

		/**
		 * Execute select SQL Query
		 * @param  string $sql_query
		 * @param  array $parameters
		 * @param  bool $get_single_row
		 * @return array $result_set
		 */
		public function executeQuery($query_type, $sql_query, ?array $parameters = NULL)
		{
				$result_set = FALSE;
				//prepare query
				 $statement = $this->dbConnection->prepare($sql_query);
				//bind parameters
				if(!is_null($parameters)){
					foreach($parameters as $param_key => $parameter_value){
						$statement->bindValue($param_key,$parameter_value);

					}
				}

				try {

					$result_set = $statement->execute();

				}catch(PDOException $exec) {

					$this->logger->error("DBManager:Error occured due to:");
					$this->logger->error($exec);
					if($exec->getCode() == 23000){
						$this->logger->error($exec->errorInfo[2]);
						return $exec->errorInfo[2];
					}
					return FALSE;
				}
				//This swithc will be used only in select query
				switch($query_type)
				{
					case self::SELECT_SINGLE_ROW:
								$result_set = $statement->fetch(PDO::FETCH_ASSOC);
								break;
				  case self::SELECT_MULTIPLE_ROW:
								$result_set = $statement->fetchAll(PDO::FETCH_ASSOC);
								break;
				}

				return $result_set;
		}
		/**
		 * Get scalar value like
		 * 1. Count of Data
		 * 2. MAX id of data
		 *
		 * @param  [type] $sql_query  [description]
		 * @param  [type] $parameters [description]
		 * @return [type]             [description]
		 */
		public function getScalarValue($sql_query,$column_name,$parameters = NULL)
		{
				$resultant_row = $this->executeQuery(self::SELECT_SINGLE_ROW, $sql_query, $parameters);

				if(!is_null($resultant_row[$column_name])){
					return $resultant_row[$column_name];
				}
				return NULL;
		}

		/**
		 * get id of data inserted last time
		 * @return int
		 */
		public function lastInsertedId()
		{
			return $this->dbConnection->lastInsertId();
		}
		/**
		 * Start transaction
		 * @return void
		 */
		public function beginTransaction()
		{
				$this->dbConnection->beginTransaction();
		}

		/**
		 * Commit transaction
		 * @return void
		 */
		public function commitTransaction()
		{
			$this->dbConnection->commit();
		}

		/**
		 * Rollback transaction
		 * @return void
		 */
		public function rollBackTransaction()
		{
			$this->dbConnection->rollback();
		}

}// end of class
