<?php

namespace SmartOSC\MultiCoupon\Model;

use Magento\Quote\Model\Quote\Address;

class Validator extends \Magento\SalesRule\Model\Validator
{

    protected function _getRules(Address $address = null)
    {
        $addressId = $this->getAddressId($address);
        $aryCoupon = explode(',', $this->getCouponCode());
        $key = $this->getWebsiteId() . '_'
            . $this->getCustomerGroupId() . '_'
            . $this->getCouponCode() . '_'
            . $addressId;
        if (!isset($this->_rules[$key])) {
            $this->_rules[$key] = $this->_collectionFactory->create()
                ->setValidationFilter(
                    $this->getWebsiteId(),
                    $this->getCustomerGroupId(),
                    $aryCoupon,
                    null,
                    $address
                )
                ->addFieldToFilter('is_active', 1)
                ->load();
        }
        return $this->_rules[$key];
    }
}
