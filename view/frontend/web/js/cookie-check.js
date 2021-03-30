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
            if (this._permissionForNotificationWasGranted()) {
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
                            var pwaDeviceEndpointFromCookie = $.cookie('pwa_device_endpoint');

                            if(
                                pwaDeviceEndpointFromCookie === undefined
                                || pwaDeviceEndpointFromCookie !== pushSubscription.endpoint
                            ) {
                                var pushSubscriptionData = JSON.parse(JSON.stringify(pushSubscription));

                                $.post({
                                    url: url.build('rest/V1/pwa/device_information'),
                                    data: JSON.stringify({
                                        oldEndpoint: pwaDeviceEndpointFromCookie,
                                        endpoint: pushSubscriptionData.endpoint,
                                        keys: pushSubscriptionData.keys
                                    }),
                                    contentType: 'application/json'
                                }).done(function(deviceEndpoint) {
                                    var oneYearInSeconds = 365 * 24 * 60 * 60 * 1000,
                                        expire = new Date();

                                    expire.setTime(expire.getTime() + oneYearInSeconds);

                                    $.cookie(
                                        'pwa_device_endpoint',
                                        deviceEndpoint,
                                        {expires: expire}
                                    );
                                }).fail(function(response) {
                                    throw new Error(response.responseJSON.message);
                                });
                            }
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
