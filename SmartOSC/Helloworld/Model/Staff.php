<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 15/07/2018
 * Time: 02:40
 */

namespace SmartOSC\Helloworld\Model;


class Staff extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        $this->_init("SmartOSC\Helloworld\Model\ResourceModel\Staff");
    }

}