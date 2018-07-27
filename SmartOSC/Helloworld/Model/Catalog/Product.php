<?php
/**
 * Created by PhpStorm.
 * User: NgocNH
 * Date: 7/13/2018
 * Time: 1:19 PM
 */

namespace SmartOSC\Helloworld\Model\Catalog;


class Product extends \Magento\Catalog\Model\Product
{
    public function getName()
    {
//        return $this->_getData(self::NAME) . ' + NgocNH test';
        return $this->_getData(self::NAME);
    }
}
