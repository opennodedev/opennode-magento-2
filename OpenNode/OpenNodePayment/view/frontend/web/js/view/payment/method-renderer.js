define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'opennode',
                component: 'OpenNode_OpenNodePayment/js/view/payment/method-renderer/opennode'
            }
        );
        return Component.extend({});
    }
);
