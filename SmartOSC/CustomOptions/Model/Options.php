<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 19/07/2018
 * Time: 17:36
 */

namespace SmartOSC\CustomOptions\Model;


class Options extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init("SmartOSC\CustomOptions\Model\ResourceModel\Options");
    }
    
}