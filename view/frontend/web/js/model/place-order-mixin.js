define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Customer/js/model/customer',
    'ClassyLlama_AvaTax/js/model/address-model'
], function ($, wrapper, customer, addressModel) {
    'use strict';

    return function (placeOrderAction) {

        return wrapper.wrap(placeOrderAction, function (originalAction, serviceUrl, payload, messageContainer) {

            if(!customer.isLoggedIn() && addressModel.validAddress()){
                payload.billingAddress = addressModel.validAddress();
            }

            return originalAction(serviceUrl, payload, messageContainer);
        });
    };
});