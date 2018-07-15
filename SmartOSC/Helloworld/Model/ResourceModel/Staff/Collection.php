<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 15/07/2018
 * Time: 02:45
 */

namespace SmartOSC\Helloworld\Model\ResourceModel\Staff;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            'SmartOSC\Helloworld\Model\Staff',
            'SmartOSC\Helloworld\ResourceModel\Staff'
        );
    }

}