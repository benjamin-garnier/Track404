<?php

namespace Ethos\Track404\Model\Plugin;

use Magento\Cron\Exception;
use Magento\Framework\Model\AbstractModel;

/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 05/07/2017
 * Time: 15:42
 */
class before404 extends AbstractModel
{


    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;


    /**
     * @var \Ethos\Track404\Helper\Logs
     */
    protected $logs;

    /**
     * Init plugin
     * @param \Ethos\Track404\Helper\Logs $logs
     */
    public function __construct(\Ethos\Track404\Helper\Logs $logs)
    {
        $this->logs = $logs;
    }

    function beforeExecute()
    {
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $ref = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:"none";
        $this->logs->info("404 interceptor! : $actual_link   //  $ref");

        try {
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('ethos_url_noroute'); //gives table name with prefix

            //Select Data from table
            $sql = "Select * FROM " . $tableName . " Where url='$actual_link'";
            $result = $connection->fetchAll($sql); // gives associated array, table fields as key in array.
            $now = date('Y-m-d H:m:s');

            if ($result != null) {
                //Update Data into table  UPDATE 404
                $count = $result[0]['count'] + 1;
                $sql = "Update " . $tableName . " Set date_last_time ='$now', count=$count, referer='$ref'  Where url = '$actual_link'";
                $connection->query($sql);
            } else {
                //Insert Data into table  NEW 404
                $sql = "Insert Into " . $tableName . " (url, `count`, date_first_time, date_last_time, referer) Values ('$actual_link',1,'$now','$now', '$ref')";
                $connection->query($sql);
                $this->logs->info("action 404 execute query");
            }
            //Delete Data from table
            //  $sql = "Delete FROM " . $tableName . " Where emp_id = 10";
            //  $connection->query($sql);

        } catch (Exception $e) {
            $this->logs->info("action 404 error : $e");
        }
    }
}