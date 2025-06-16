<?php

namespace  App\Utils;



use App\Utils\CoreDataService;

/**
* This class will used to create background queue
*/

class Queue extends CoreDataService 
{    
    public function __construct() {
		parent::__construct();
	}

    public function addToQueue( $queue_data )
    {
        $add_to_queue_sql = 'INSERT INTO background_queue(id, Api_Link) VALUES (NULL,:api_link)';

        return $this->performDBUpdate($add_to_queue_sql,['api_link' => $queue_data]);
    }
}