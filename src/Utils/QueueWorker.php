<?php

namespace  App\Utils;




use App\Utils\CoreDataService;

/**
* This class will used to create background queue
*/

class QueueWorker extends CoreDataService
{
    const SHOULD_SEND_SMS = false;

    public function __construct() {
		parent::__construct();
	}
    /**
    * Get list of queue from database and process one by one
    *
    */
    public function processQueue( )
    {
        $add_to_queue_sql = 'SELECT id,Api_Link FROM background_queue';
        $queue_list = $this->executeSQL($add_to_queue_sql);
        // Logger::log($queue_list);
        foreach ($queue_list as $index => $queue_detail) {
            //execute API
            $this->executeAPI($queue_detail['Api_Link']);
            //remove from queue
            $this->removeFromQueue($queue_detail['id']);
        }
    }
    //  [[  PRIVATE FUNCTIONS  ]]
    /**
    * Remove queue from list, will remove queue entry from database table
    * @param $queue_id
    */
    private function removeFromQueue($queue_id)
    {
        $remove_from_queue_sql = 'DELETE FROM background_queue WHERE id=:queue_id';

        return $this->performDBUpdate($remove_from_queue_sql,['queue_id' => $queue_id]);
    }

     /**
    *  Call API
    *
    *  @param $api_url
    *
    * @return API response
    */
    private  function executeAPI( $api_url )
    {
         if (self::SHOULD_SEND_SMS){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$api_url);
            curl_setopt ( $ch ,CURLOPT_RETURNTRANSFER,1 );
            $output = curl_exec($ch);
            curl_close($ch);
            //Logger::log($api_url);
            return $output;
         }else{
             return null;
         }
    }

}
