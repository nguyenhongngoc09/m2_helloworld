<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 19/07/2018
 * Time: 17:37
 */

namespace SmartOSC\CustomOptions\Model\ResourceModel;

class Options extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context)
    {
        parent::__construct($context);
    }
    
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('smartosc_custom_options', 'id');
    }
    
}