define([
    'jquery',
    'mage/url',
    'mage/cookies',
    'mage/translate',
    'jquery-ui-modules/widget',
    'jquery-ui-modules/effect-fade'
], function($, url) {
    'use strict';

    $.widget('magesuite.pwaNotification', {
        options: {
            beforeTarget: '',
            afterTarget: '',
            containerSelector: '.cs-container--pwa-notification-panel',
            panelSelector: '.cs-pwa-notification-panel',
            panelRequestClass: 'cs-pwa-notification-panel--request',
            panelBrowserClass: 'cs-pwa-notification-panel--browser',
            panelEnabledClass: 'cs-pwa-notification-panel--enabled',
            messageSelector: '.cs-pwa-notification-panel__message',
            messageRequestClass: 'cs-pwa-notification-panel__message--request',
            messageBrowserClass: 'cs-pwa-notification-panel__message--browser',
            messageEnabledClass: 'cs-pwa-notification-panel__message--enabled',
            acceptSelector: '.cs-pwa-notification-panel__button--accept',
            declineSelector: '.cs-pwa-notification-panel__button--decline',
            closeSelector: '.cs-pwa-notification-panel__close',
            applicationServerKey: '',
            notificationType: '',
            notificationEnableDescription: '',
            notificationSubscribedDescription: '',
            notificationDescriptionSelector:
                '.cs-pwa-notification-panel__message-description',
            notificationClosingTimeout: ''
        },
        _create: function() {
            var options = this.options,
                $container;

            if (!this._isPushNotificationSupported()) {
                return;
            }

            if (!this._shouldDisplayPanel()) {
                return;
            }

            $container = $(options.containerSelector);
            if (options.beforeTarget) {
                $(options.beforeTarget).before($container);
            } else {
                $(options.afterTarget).after($container);
            }

            this._registerButtonEvents();

            var $requestMessageSelector = $(
                '.' + this.options.messageRequestClass
            ).find(this.options.notificationDescriptionSelector);

            this._adjustMessageText(
                $requestMessageSelector,
                this.options.notificationEnableDescription
            );

            this.show();
        },

        show: function() {
            this.element.slideDown();
        },

        hide: function() {
            this.element.slideUp();
        },

        _adjustMessageText: function(notificationDescriptionSelector, message) {
            notificationDescriptionSelector.html($.mage.__(message));
        },

        _isPushNotificationSupported: function() {
            if (!this.options.applicationServerKey) {
                throw new Error(
                    'Cannot initialize notification panel, "applicationServerKey" option is not provided.'
                );
            }

            if (!this.options.notificationType) {
                throw new Error(
                    'Cannot initialize notification panel, "notificationType" option is not provided.'
                );
            }

            return 'serviceWorker' in navigator && 'PushManager' in window;
        },

        _shouldDisplayPanel: function() {
            return (
                'Notification' in window &&
                Notification.permission !== 'granted'
            );
        },

        _registerButtonEvents: function() {
            var events = {};

            events[
                'click ' + this.options.acceptSelector
            ] = this._onAcceptClick;
            events[
                'click ' + this.options.declineSelector
            ] = this._onDeclineClick;
            events['click ' + this.options.closeSelector] = this._onCloseClick;

            this._on(this.element, events);
        },

        _onAcceptClick: function() {
            this._requestPermission();
        },
        _whenPending: function() {
            this.element.removeClass(this.options.panelRequestClass);
            this.element.addClass(this.options.panelBrowserClass);
        },
        _whenAccepted: function() {
            this.element.removeClass(this.options.panelBrowserClass);
            this.element.addClass(this.options.panelEnabledClass);

            var $acceptedNotificationMessageSelector = $(
                '.' + this.options.messageEnabledClass
            ).find(this.options.notificationDescriptionSelector);

            this._adjustMessageText(
                $acceptedNotificationMessageSelector,
                this.options.notificationSubscribedDescription
            );

            setTimeout(
                function() {
                    this.hide();
                }.bind(this),
                parseInt(this.options.notificationClosingTimeout, 10)
            );
        },
        _onDeclineClick: function() {
            this.hide();
        },
        _onCloseClick: function() {
            this.hide();
        },

        _requestPermission: function() {
            var applicationServerKey = this._urlBase64ToUint8Array(
                this.options.applicationServerKey
            );

            this._whenPending();

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
                        this._whenAccepted();

                        // some FE dev please refactor below
                        var payload = JSON.parse(JSON.stringify(pushSubscription));
                        payload['permissions'] = ['order_status_notification'];

                        $.post({
                            url: url.build('rest/V1/pwa/device_information'),
                            data: JSON.stringify(payload),
                            contentType: 'application/json'
                        }).then(function(deviceIdentifier) {
                            var oneYearInSeconds = 365 * 24 * 60 * 60 * 1000;
                            var expire = new Date();

                            expire.setTime(expire.getTime() + oneYearInSeconds);

                            $.cookie('pwa_device_identifier', deviceIdentifier, {expires: expire});
                        });
                    }.bind(this)
                )
                .catch(this._onDeclineClick.bind(this));
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

    return $.magesuite.pwaNotification;
});
