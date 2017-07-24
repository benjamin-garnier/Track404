<?php

namespace Ethos\Track404\Model;

use Magento\Cron\Exception;
use Magento\Framework\Model\AbstractModel;

class Url404 extends AbstractModel
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Ethos\Track404\Model\ResourceModel\Url404::class);
    }

}