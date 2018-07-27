<?php
/**
 * Created by PhpStorm.
 * User: ngocnh
 * Date: 16/07/2018
 * Time: 15:54
 */
namespace SmartOSC\CustomOptions\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class HelperData extends AbstractHelper
{

    const XML_PATH_CUSTOM_OPTIONS = 'smartosc_custom/';

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    /**
     * @param $code
     * @param null $storeId
     * @return mixed
     */
    public function getSmartCustomConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CUSTOM_OPTIONS .'options/'. $code, $storeId);
    }

    /**
     * @return mixed
     */
    public function getMaxFileUpLoadSize() {
        return $this->getSmartCustomConfig('upload_max_size');
    }

    /**
     * @return mixed
     */
    public function getImageWidthConfig() {
        return $this->getSmartCustomConfig('img_width');
    }

    /**
     * @return mixed
     */
    public function getImageHeightConfig() {
        return $this->getSmartCustomConfig('img_height');
    }

}