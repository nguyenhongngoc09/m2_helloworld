<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 19/07/2018
 * Time: 17:37
 */

namespace SmartOSC\CustomOptions\Model\ResourceModel\Options;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            'SmartOSC\CustomOptions\Model\Options',
            'SmartOSC\CustomOptions\Model\ResourceModel\Options'
        );
    }
    
}