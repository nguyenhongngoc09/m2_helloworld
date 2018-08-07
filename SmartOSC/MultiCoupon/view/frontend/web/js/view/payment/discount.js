/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'SmartOSC_MultiCoupon/js/action/set-coupon-code',
    'SmartOSC_MultiCoupon/js/action/cancel-coupon',
    'Magento_SalesRule/js/model/payment/discount-messages',
    'mage/translate',
], function ($, ko, Component, quote, setCouponCodeAction, cancelCouponAction, messageContainer, $t) {
    'use strict';

    var totals = quote.getTotals(),
        couponCode = ko.observable(null),
        couponCodes = ko.observable(null),
        isLoading = ko.observable(false),
        messageApplySuccess = $t('Your coupon was successfully applied.'),
        messageApplyError = $t('Your coupon was apply error.'),
        messageRemoveSuccess = $t('Your coupon was successfully removed.'),
        messageRemoveError = $t('Your coupon was remove error.');

    if (totals()) {
        couponCodes(totals()['coupon_code']);
    }

    return Component.extend({
        defaults: {
            template: 'SmartOSC_MultiCoupon/payment/discount'
        },
        couponCode: couponCode,
        couponCodes: couponCodes,
        isLoading: isLoading,

        /**
         * Coupon code application procedure
         */
        apply: function () {
            if (this.validate()) {
                var oldCodes = couponCodes();
                var submitCodes = '';
                if (oldCodes) {
                    submitCodes = oldCodes.split(',').indexOf(couponCode().trim()) === -1 ?
                        oldCodes + ',' + couponCode().trim() : oldCodes;
                } else {
                    submitCodes = couponCode().trim();
                }

                isLoading(true);
                couponCode('');

                setCouponCodeAction(submitCodes).then(function () {
                    couponCodes(submitCodes);
                    isLoading(false);
                    messageContainer.addSuccessMessage({
                        'message': messageApplySuccess
                    });
                }, function () {
                    isLoading(false);
                    messageContainer.addErrorMessage({
                        'message': messageApplyError
                    });
                });
            }
        },

        removeCoupon: function() {
            var oldCodes = couponCodes();
            var removeCode = '';

            if (oldCodes.indexOf(this) === 0) {
                removeCode = oldCodes.indexOf(',') === -1 ? this : this + ','
            } else {
                removeCode =  ',' + this;
            }

            var newCodes = oldCodes.replace(removeCode, '');

            isLoading(true);

            if (removeCode === this) {
                cancelCouponAction().then(function () {
                    couponCodes('');
                    isLoading(false);
                    messageContainer.addSuccessMessage({
                        'message': messageRemoveSuccess
                    });
                }, function () {
                    isLoading(false);
                    messageContainer.addErrorMessage({
                        'message': messageRemoveError
                    });
                });
            } else {
                setCouponCodeAction(newCodes).then(function () {
                    couponCodes(newCodes);
                    isLoading(false);
                    messageContainer.addSuccessMessage({
                        'message': messageRemoveSuccess
                    });
                }, function () {
                    isLoading(false);
                    messageContainer.addErrorMessage({
                        'message': messageRemoveError
                    });
                });
            }
        },

        /**
         * Cancel using coupon
         */
        cancel: function () {
            if (this.validate()) {
                cancelCouponAction().then(function () {
                    couponCodes('');
                    isLoading(false);
                    messageContainer.addErrorMessage({
                        'message': messageRemoveError
                    });
                }, function () {
                    isLoading(false);
                    messageContainer.addErrorMessage({
                        'message': messageRemoveError
                    });
                });

            }
        },

        /**
         * Coupon form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            var form = '#discount-form';

            return $(form).validation() && $(form).validation('isValid');
        }
    });
});