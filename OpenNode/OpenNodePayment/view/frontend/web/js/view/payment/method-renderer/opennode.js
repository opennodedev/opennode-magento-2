define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default'
    ],
    function ($, Component) {
        'use strict';
 
        return Component.extend({
            defaults: {
                redirectAfterPlaceOrder: false,
                template: 'OpenNode_OpenNodePayment/payment/opennode'
            },

            initObservable: function () {

                this._super()
                return this;
            },

            getCode: function() {
                return window.checkoutConfig.payment.opennode.code;
            },

            getData: function() {
                return {
                    'method': this.item.method
                };
            },

            afterPlaceOrder: function () {
                $.mage.redirect(window.checkoutConfig.payment.opennode.redirectUrl);
                return false;
            }
        });
    }
);
