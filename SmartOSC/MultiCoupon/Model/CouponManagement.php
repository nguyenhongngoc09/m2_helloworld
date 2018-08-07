<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SmartOSC\MultiCoupon\Model;

use \Magento\Quote\Api\CouponManagementInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Coupon management object.
 */
class CouponManagement implements CouponManagementInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Coupon factory
     *
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * Constructs a coupon read service object.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository Quote repository.
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\SalesRule\Model\CouponFactory $couponFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->couponFactory = $couponFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        return $quote->getCouponCode();
    }

    /**
     * {@inheritdoc}
     */
    public function set($cartId, $couponCode)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $couponCodes = explode(',', trim($couponCode));
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        $couponFactory = $objectManager->get(\Magento\SalesRule\Model\CouponFactory::class);


        $couponCollection = $couponFactory->create()->getCollection();

        $coupons = $couponCollection->addFieldToFilter('code', ['in' => $couponCodes]);

        if ($coupons->count() != count($couponCodes)) {
            throw new NoSuchEntityException(__('Coupon code is not valid'));
        }

        $listCoupons = $coupons->load();

        $customerId = $quote->getCustomerId();

        $usageFactory = $couponFactory = $objectManager->get(\Magento\SalesRule\Model\ResourceModel\Coupon\UsageFactory::class)->create();

        foreach ($listCoupons as $coupon) {
            if ($coupon->getUsageLimit() && $coupon->getTimesUsed() >= $coupon->getUsageLimit()) {
                throw new NoSuchEntityException(__('Coupon code is not valid'));
            }
            if ($customerId && $coupon->getUsagePerCustomer()) {
                $couponUsage = $couponFactory = $objectManager->get(\Magento\Framework\DataObjectFactory::class)->create();
                $usageFactory->loadByCustomerCoupon(
                    $couponUsage,
                    $customerId,
                    $coupon->getId()
                );
                if ($couponUsage->getCouponId() &&
                    $couponUsage->getTimesUsed() >= $coupon->getUsagePerCustomer()
                ) {
                    throw new NoSuchEntityException(__('You used coupon code'));
                }
            }
        }

        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            $quote->setCouponCode($couponCode);
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not apply coupon code'));
        }
        if ($quote->getCouponCode() != $couponCode) {
            throw new NoSuchEntityException(__('Coupon code is not valid'));
        }

        return $couponCode;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            $quote->setCouponCode('');
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete coupon code'));
        }
        if ($quote->getCouponCode() != '') {
            throw new CouldNotDeleteException(__('Could not delete coupon code'));
        }
        return true;
    }
}
