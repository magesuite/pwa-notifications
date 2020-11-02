define([
    'jquery',
    'mage/url',
    'domReady!',
    'jquery-ui-modules/widget',
    'mage/cookies'
], function($, url) {
    'use strict';

    $.widget('magesuite.pwaCookieCheck', {
        options: {},

        _create: function() {
            var options = this.options;

            this._checkCookieAndPermission();
        },

        _permissionForNotificationWasGranted: function() {
            return (
                'Notification' in window &&
                Notification.permission === 'granted'
            );
        },

        _checkCookieAndPermission: function() {
            if (
                !$.cookie('pwa_device_identifier') &&
                this._permissionForNotificationWasGranted()
            ) {
                var applicationServerKey = this._urlBase64ToUint8Array(
                    this.options.applicationServerKey
                );

                navigator.serviceWorker
                    .getRegistrations()
                    .then(function(registrations) {
                        for (let registration of registrations) {
                            const subscribeOptions = {
                                userVisibleOnly: true,
                                applicationServerKey: applicationServerKey
                            };

                            return registration.pushManager.subscribe(
                                subscribeOptions
                            );
                        }
                    })
                    .then(
                        function(pushSubscription) {
                            $.get({
                                url: url.build('rest/V1/pwa/device_identifier?endpoint='+pushSubscription.endpoint),
                                contentType: 'application/json'
                            }).then(function(deviceIdentifier) {
                                var oneYearInSeconds = 365 * 24 * 60 * 60 * 1000;
                                var expire = new Date();

                                expire.setTime(expire.getTime() + oneYearInSeconds);

                                $.cookie('pwa_device_identifier', deviceIdentifier, {expires: expire});
                            });
                        }
                    );
            }
        },

        _urlBase64ToUint8Array: function(base64String) {
            var padding = '='.repeat((4 - (base64String.length % 4)) % 4);
            var base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');

            var rawData = window.atob(base64);
            var outputArray = new Uint8Array(rawData.length);

            for (var i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    });

    return $.magesuite.pwaCookieCheck;
});
