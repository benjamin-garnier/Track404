<?php

namespace Ethos\Track404\Controller\Adminhtml\Admin;

use Magento\Backend\App\Action;

class Index extends \Magento\Backend\App\Action
{
    public function execute()
    {
        /*/
                $url = $this->_objectManager->create('Ethos\Track404\Model\Url404');

                        $url->setUrl("manual entry");
                        $url->setCount(1);
                        $url->setDate_first_time("2017-07-24 00:00:00");
                        $url->setDate_last_time("2017-07-24 00:00:00");
                        $url->setReferer("ME");
                        $url->save();


                try {
                    $collection = $url->getCollection()->addFieldToFilter('url', array('like' => '%%'));

                    foreach ($collection as $entry) {
                        var_dump($entry->getData());
                    }
                } catch (Exception $e) {

                    var_dump($e);
                }

                die('test');
        //*/

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
