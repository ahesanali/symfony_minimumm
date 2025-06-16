<?php

namespace App\Utils;



use App\Utils\DBManager;
use \Exception;
/**
* This class will used to execute database query using Nemiro\Data\MySql.
*/
class CoreDataService
{
   /**
     * Paginator instance.
     *
     * @var [type]
     */
    protected $paginator = null;

    /**
     * DBManager object
     * @var DBManager
     */
    private $dbManager;
    /**
    * boolean
    * indiacte wheather result set should be paginated or not by defailt false
    * @var boolean
    */
    private $shouldPaginateResult;
    private $logger = NULL;

   public function __construct(DBManager $dbManager)
   {
        $this->logger = get_logger();	
       try {
           # create a new DBManager instance
            $this->dbManager = $dbManager;
            $this->shouldPaginateResult = false;
       } catch (Exception $ex) {
           $this->logger->error('Unable to connect with MySql. Due to '.$ex->getMessage());
       }
   }
   /**
    *  set flag should paginate resulst before execting sql query
    */
    public function shouldPaginateResult($should_paginate_result = false)
    {
        $this->shouldPaginateResult = $should_paginate_result;
        return $this;
    }
    /**
     * Execute sql query using Mysql Client library class.
     *
     * @param string $sql_query
     * @param bool   $sigle_row
     *
     * @return array
     */
    public function executeSQL($sql_query, $parameters= NULL, $sigle_row = false)
    {
        try {
            //append limit string in sql query if paginator is set
            if (!is_null($this->paginator) && $this->shouldPaginateResult) {
                $sql_query = $sql_query.$this->getLimit();
            }

            if ($sigle_row) {
                $result_set = $this->dbManager->executeQuery(DBManager::SELECT_SINGLE_ROW,$sql_query,$parameters);
            } else {
                $result_set = $this->dbManager->executeQuery(DBManager::SELECT_MULTIPLE_ROW,$sql_query,$parameters);
            }

            return $result_set;
        } catch (Exception $ex) {
        	  $this->logger->error("Error occurs due to :".$ex->getMessage());
            return NULL;
        }
    }

    /**
     * Execute insert,update query and get id of updated or inserted record
     * @param  string $update_sql
     * @return int
     */
   public function performDBUpdate($update_sql, array $parameters)
   {
       try {
           $is_executed = $this->dbManager->executeQuery(DBManager::UPDATE_DATA, $update_sql,$parameters);
        
           return $is_executed;
       } catch (Exception $ex) {
       		$this->logger->error("CoreDataServ:Error occured due to :".$ex->getMessage());
        	return FALSE;
       }
   }

   /**
    * To perform insert and update operation in batch use this functions
    * $parameters arguments contains array of arrays
    * example: [[],[],[],[]]
    * @param unknown $update_sql
    * @param array $parameters
    */
   public function performBatchUpdate($update_sql, array $parameters)
   {
   	try {
   		$is_executed = $this->dbManager->executeBatchUpdate( $update_sql,$parameters);

   		return $is_executed;
   	} catch (Exception $ex) {
   		$this->logger->error("Error occurs due to :".$ex->getMessage());
   		return FALSE;
   	}

   }
    /**
     * Get singel value from query. First column of first row.
     * 1. Used in getting count from query.
     * 2. used for max value
     * @param string $sql_query
     *
     * @return int
     */
    public function getSingleValue($sql_query,$column_name)
    {
        try {

            $result_value = $this->dbManager->getScalarValue($sql_query,$column_name);
            return $result_value;
        } catch (Exception $ex) {
            $this->logger->error("Error occurs due to :".$ex->getMessage());
          	return NULL;
        }
    }

    public function getLastInsertedId()
    {
    	return $this->dbManager->lastInsertedId();
    }

    /**
     * Set paginator instance.
     *
     * @param Paginator $paginator
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Get Limit string to apply pagination for mysql query.
     *
     * @return string
     */
    private function getLimit()
    {
        $offset = ($this->paginator->getCurrentPage() - 1) * $this->paginator->getPageSize();
        $limit = ' LIMIT '.$offset.','.$this->paginator->getPageSize();

        return $limit;
    }

   /**
    * Get dropdown option list from sql.
    *
    * @param  string $dropdown_option_sql
    *
    * @return array
    */
   public function getDropDownOptions($dropdown_option_sql)
   {
       $dropdown_options = [];
       $dropdown_option_result = $this->dbManager->executeQuery(DBManager::SELECT_MULTIPLE_ROW, $dropdown_option_sql);
       foreach ($dropdown_option_result as $dropdown_option) {
           $dropdown_option_keys = array_keys($dropdown_option);
           if (count($dropdown_option_keys) > 1) {
               $dropdown_options [ $dropdown_option[$dropdown_option_keys[0]] ] = $dropdown_option[$dropdown_option_keys[1]];
           } else {
               $dropdown_options [] = $dropdown_option[$dropdown_option_keys[0]];
           }
       }

       return $dropdown_options;
   }

   public function beginTxn()
   {
   		$this->dbManager->beginTransaction();
   }
   public function commitTxn()
   {
   		$this->dbManager->commitTransaction();
   }

   public function rollBackTxn()
   {
   		$this->dbManager->rollBackTransaction();
   }
}// end of class
