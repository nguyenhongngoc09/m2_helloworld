<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 15/07/2018
 * Time: 02:43
 */

namespace SmartOSC\Helloworld\Model\ResourceModel;


class Staff extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('smartosc_staff', 'id');
    }
}