/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('mage.discountCodeMultiCoupon', {
        options: {
        },

        /** @inheritdoc */
        _create: function () {
            this.couponCode = $(this.options.couponCodeSelector);
            this.removeCoupon = $(this.options.removeCouponSelector);
            this.removeCouponCode = $(this.options.removeCouponCodeSelector);

            $(this.options.applyButton).on('click', $.proxy(function () {
                this.couponCode.attr('data-validate', '{required:true}');
                this.removeCoupon.attr('value', '0');
                $(this.element).validation().submit();
            }, this));

            $(this.options.cancelButton).on('click', $.proxy(function () {
                this.couponCode.removeAttr('data-validate');
                this.removeCoupon.attr('value', '1');

                var removeCouponCode = $(event.target).parent().parent().find('span.rm-coupon-cd').text();
                this.removeCouponCode.attr('value', removeCouponCode);
                this.element.submit();
            }, this));
        }
    });

    return $.mage.discountCodeMultiCoupon;
});
