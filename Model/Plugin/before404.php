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
    const tableName = 'ethos_track404';

    //max number of rows in the table (too much could slow down the website)
    protected $max_Entry = 1000;
    //minimum number of rows to delete when we cross the limit
    protected $nbToDelete = 200;
    //start deleting rows by date (older than X months will be deleted first)
    protected $deleteOlderThanXMonths = 2;

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
        $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "none";
        $this->logs->info("404 interceptor! : $actual_link   //  $ref");

        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('' . self::tableName . ''); //gives table name with prefix

            //Verifiy that the table is not "full" according to our limit
            $sql = "Select count(*) as count FROM " . $tableName;
            $result = $connection->fetchAll($sql);
            if (intval($result[0]["count"]) >= $this->max_Entry) {
                $this->logs->info("test max Entry true, call cleanTable ");
                $n = 0;
                $date = date("Y-m-d", strtotime(date("Y-m-d") . " + 0 days - $this->deleteOlderThanXMonths months + 0 year"));
                $this->cleanTable($n, $date);
            }

            //Select Data from table
            $sql = "Select * FROM " . $tableName . " Where url='$actual_link'";
            $result = $connection->fetchAll($sql); // gives associated array, table fields as key in array.
            $now = date('Y-m-d');
            //If the url is already in the table, increase its count, else create new row
            if ($result != null) {
                //Update Data into table  UPDATE 404
                $count = $result[0]['count'] + 1;
                $sql = "Update " . $tableName . " Set date_last_time ='$now', count=$count, referer='$ref'  Where url = '$actual_link'";
                $connection->query($sql);
            } else {
                //Insert Data into table  NEW 404
                $sql = "Insert Into " . $tableName . " (url, `count`, date_first_time, date_last_time, referer) Values ('$actual_link',1,'$now','$now', '$ref')";
                $connection->query($sql);
                $this->logs->info("action 404 execute insert");
            }

        } catch (Exception $e) {
            $this->logs->info("action 404 error : $e");
        }
    }

    //Reached maxEntry size for the table, delete nbDeleted entries where now() - date_last_time greatest
    function cleanTable($n, $dateThreshold)
    {
        //add a security, don't want  an infinity loop if something goes wrong
        static $nbBoucle = 15;
        $nbBoucle--;
        if ($nbBoucle <= 0) {
            return;
        }
        //If we deleted enough rows, stop the function
        $count = $n;
        if ($n >= $this->nbToDelete) {
            return null;
        } else {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('' . self::tableName . ''); //gives table name with prefix
            //How many rows will be deleted this time ?
            $sql = "select id from " . $tableName . ' where date_last_time <= "' . $dateThreshold . '"';
            $result = $connection->fetchAll($sql);
            $count += count($result);
            //delete rows. TODO find a way to know how many rows were deleted without making a select query beforehand
            $sql = "Delete from " . $tableName . ' where date_last_time <= "' . $dateThreshold . '"';
            $connection->query($sql);
            //recursive call with updated count of deleted rows, and a more recent date
            $newdate = date("Y-m-d", strtotime($dateThreshold . " + 10 days + 0 months + 0 year"));
            return $this->cleanTable($count, $newdate);
        }
        return null;
    }
}

