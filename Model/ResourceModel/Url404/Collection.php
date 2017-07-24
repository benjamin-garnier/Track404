<?php

namespace Ethos\Track404\Model\ResourceModel\Url404;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Contact Resource Model Collection
 *
 * @author      Pierre FAY
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ethos\Track404\Model\Url404', 'Ethos\Track404\Model\ResourceModel\Url404');
    }
}