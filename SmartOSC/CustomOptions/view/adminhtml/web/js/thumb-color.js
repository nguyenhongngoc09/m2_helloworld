define([
    'Magento_Ui/js/form/element/abstract',
    'mageUtils',
    'jquery',
    'jquery/colorpicker/js/colorpicker'
], function (Element, utils, $) {
    'use strict';

    return Element.extend({
        defaults: {
            visible: true,
            label: '',
            error: '',
            uid: utils.uniqueid(),
            disabled: false,
            links: {
                value: '${ $.provider }:${ $.dataScope }'
            }
        },

        initialize: function () {
            this._super();
        },

        initColorPickerCallback: function (element) {
            var self = this;

            if (this.initialValue) {
                $(element).css({'background': this.initialValue});
            }

            $(element).ColorPicker({
                onSubmit: function(hsb, hex, rgb, el) {
                    self.value('#' + hex);
                    $(el).ColorPickerHide();
                    $(el).css({'background': '#' + hex});
                },
                onBeforeShow: function () {
                    $(this).ColorPickerSetColor(this.value);
                }
            }).bind('keyup', function(){
                $(this).ColorPickerSetColor(this.value);
            });
        }
    });
});