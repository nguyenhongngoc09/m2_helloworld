<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SmartOSC\MultiCoupon\Controller\Cart;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CouponPost extends \Magento\Checkout\Controller\Cart
{
    /**
     * Sales quote repository
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
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->couponFactory = $couponFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $isRemoveCoupon = $this->getRequest()->getParam('remove') == 1 ? true : false;
        $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);

        $postCoupon = trim($this->getRequest()->getParam('coupon_code'));

        $cartQuote = $this->cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode();

        if ($oldCouponCode !== null) {
            $aryCoupons = explode(',', $oldCouponCode);
        } else {
            $aryCoupons = [];
        }

        if ($isRemoveCoupon) {
            $removeCouponCode = trim($this->getRequest()->getParam('remove_coupon_code'));

            $idxCouponRemove = array_search($removeCouponCode, $aryCoupons);
            unset($aryCoupons[$idxCouponRemove]);

        } else {
            if (array_search($postCoupon, $aryCoupons) !== false) {
                $this->messageManager->addErrorMessage(
                    __(
                        'The coupon code "%1" is exist.',
                        $escaper->escapeHtml($postCoupon)
                    )
                );
                return $this->_goBack();
            } else {
                $coupon = $this->couponFactory->create();
                $coupon->load($postCoupon, 'code');

                $isCouponValid = $coupon->getId() ? true : false;

                array_push($aryCoupons, $postCoupon);
            }
        }

        $couponCode = implode($aryCoupons, ',');

        try {
            $codeLength = strlen($couponCode);
            if (!$codeLength && !strlen($oldCouponCode)) {
                return $this->_goBack();
            }

            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                $this->quoteRepository->save($cartQuote);
            }

            if ($codeLength && !$isRemoveCoupon) {

                if (!$itemsCount) {
                    if ($isCodeLengthValid && $isCouponValid) {
                        $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
                        $this->messageManager->addSuccess(
                            __(
                                'You used coupon code "%1".',
                                $escaper->escapeHtml($postCoupon)
                            )
                        );
                    } else {
                        $this->messageManager->addError(
                            __(
                                'The coupon code "%1" is not valid.',
                                $escaper->escapeHtml($postCoupon)
                            )
                        );
                    }
                } else {
                    if ($isCodeLengthValid && $isCouponValid && $couponCode == $cartQuote->getCouponCode()) {
                        $this->messageManager->addSuccess(
                            __(
                                'You used coupon code "%1".',
                                $escaper->escapeHtml($postCoupon)
                            )
                        );
                    } else {
                        $this->messageManager->addError(
                            __(
                                'The coupon code "%1" is not valid.',
                                $escaper->escapeHtml($postCoupon)
                            )
                        );
                    }
                }
            } else {
                $this->messageManager->addSuccess(__('You canceled the coupon code "'. $removeCouponCode.'".'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We cannot apply the coupon code.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }

        return $this->_goBack();
    }
}
