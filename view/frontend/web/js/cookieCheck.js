define([
    'jquery',
    'mage/url',
    'domReady!',
    'jquery-ui-modules/widget'
], function($) {
    'use strict';

    $.widget('magesuite.pwaCookieCheck', {
        options: {},

        _create: function() {
            var options = this.options;

            this._checkCookieAndPermission();
        },

        _shouldDisplayPanel: function() {
            return (
                'Notification' in window &&
                Notification.permission === 'granted'
            );
        },

        _checkCookieAndPermission: function() {
            if (
                !$.cookie('pwa_device_identifier') &&
                this._shouldDisplayPanel()
            ) {
                console.log(
                    'Permission has been granted and pwa cookie is missing'
                );
            }
        }
    });

    return $.magesuite.pwaCookieCheck;
});
