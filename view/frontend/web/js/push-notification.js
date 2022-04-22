define(['jquery', 'ko', 'uiComponent', 'mage/url', 'mage/cookies'], function (
    $,
    ko,
    Component,
    url
) {
    'use strict';

    return Component.extend({
        defaults: {
            applicationServerKey: '',
            notificationType: '',
            notificationValue: '',
            alwaysAsk: false,
            showPanelIfPermissionAlwaysGranted: false,
            showOnInit: true,
            subscribedCallback: undefined,
        },

        /**
         * Initialize push notification script.
         * First check if panel can be displayed. If not set initial content to empty, hide buttons and set status to the default one (request);
         * Subscribe to the subscriptionStatus observable to update css class of component depending on current settings of notifications in the browser;
         * set this._isRequestedByClient to false (describeed later);
         * Run _monitorPermissionChanges() is `permissions` are supported to catch permissions changes made by user directly in browser settings;
         * Show panel if requested by option.
         */
        initialize: function () {
            this._super();

            this.content = ko.observable({
                header: '',
                description: '',
            });

            if (!this._canDisplayPanel()) {
                this.showActions(false);
                this.subscriptionStatus('request');
                this.canDisplay(false);

                return this;
            }

            this.canDisplay(true);

            this.subscriptionStatus.subscribe(
                function (newStatus) {
                    this.modifier(
                        'cs-push-notification__content--' + newStatus
                    );
                }.bind(this)
            );

            this._isRequestedByClient = false;

            if ('permissions' in navigator) {
                this._monitorPermissionChanges();
            }

            this._setInitialPanelContent();

            if (this.showOnInit) {
                this.showPanel(true);
            }

            $('body').on('bis:modalclosed bis:formclosed', function() {
                this._setInitialPanelContent();
            }.bind(this));

            return this;
        },

        initObservable: function () {
            this._super().observe([
                'canDisplay',
                'showPanel',
                'content',
                'showActions',
                'subscriptionStatus',
                'modifier',
            ]);

            return this;
        },

        /**
         * This method is directly bound to the click event of the "Yes" button.
         * Set this._isRequestedByClient to true and initialize subscription process
         * this._isRequestedByClient informs whether it was user that initializes subscription process. The other option is browser settings that can initialize process because of already granted permissions.
         * There is also new permission post request send
         */
        onAccept: function () {
            this._isRequestedByClient = true;
            this._subscribe(false);

            $.post({
                url: url.build('rest/V1/pwa/permission'),
                data: JSON.stringify({"permission": this.notificationType}),
                contentType: 'application/json'
            })
        },

        /**
         * Hides component
         */
        closePanel: function () {
            this.showPanel(false);
        },

        /**
         * Checks if component can be displayed at all.
         * Component requires applicationServerKey and notificationType to be passed:
         *   - applicationServerKey is generated on the BE side and merged to the js settings in the template
         *   - notificationType is required to know the feature user subscribes to (whether it's order status update or back in stock alert of whatever else.). All possible types are (and must be) declated in di.xml
         * At the end we must check if required APIs are supported by the browser at all.
         */
        _canDisplayPanel: function () {
            if (!this.applicationServerKey) {
                throw new Error(
                    'Cannot initialize notification panel, "applicationServerKey" option is not provided.'
                );
            }

            if (!this.notificationType) {
                throw new Error(
                    'Cannot initialize notification panel, "notificationType" option is not provided.'
                );
            }

            return (
                'Notification' in window &&
                'serviceWorker' in navigator &&
                'PushManager' in window
            );
        },

        /**
         * Fetch and save device identifier to the cookie if doesn't exist yet.
         * Trigger custom event to inject callback if necessary
         * @param {JSON} payload - payload data
         */
        _setDeviceIdentifier: function (payload) {
            var deviceEndpointFromCookie = $.cookie('pwa_device_endpoint');

            if (deviceEndpointFromCookie != null) {
                $('body').trigger('push:subscribed', [
                    this.notificationType,
                    deviceEndpointFromCookie
                ]);

                if (typeof this.subscribedCallback === 'function') {
                    this.subscribedCallback(deviceEndpointFromCookie);
                }
            } else {
                $.post({
                    url: url.build('rest/V1/pwa/device_information'),
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                })
                    .done(function (deviceEndpoint) {
                        var oneYearInSeconds = 365 * 24 * 60 * 60 * 1000,
                            expire = new Date();

                        expire.setTime(expire.getTime() + oneYearInSeconds);

                        $.cookie('pwa_device_endpoint', deviceEndpoint, {
                            expires: expire,
                        });

                        $('body').trigger('push:subscribed', [
                            this.notificationType,
                            deviceEndpointFromCookie
                        ]);

                        if (typeof this.subscribedCallback === 'function') {
                            this.subscribedCallback(deviceEndpoint);
                        }
                    })
                    .fail(function (response) {
                        throw new Error(response.responseJSON.message);
                    });
            }
        },

        /**
         * Actual subscription process.
         * First decode applicationServerKey and initialize "pending for permission" sequence if permission wasn't granted yet.
         * Then fetch all registrations list, set options required by Notifications API and request permission from the browser (if not granted yet).
         * As a result a Promise is returned for futher processing. It's either:
         *   - subscription object if user agreed, or
         *   - error message if user disagreed.
         * Agreed:
         *   - callback variable is created from the options
         *   - payload is created as JSON
         *   - notificationType is assigned to the payload JSON
         *   - _whenGranted status is triggered
         *   - _setDeviceIdentifier is triggered with payload data
         * Disagreed:
         *   - _onRejectedByUser status is triggered
         * @param {boolean} isAlreadyGranted - informs if push notification permission is already granted at this stage
         */
        _subscribe: function (isAlreadyGranted) {
            var applicationServerKey = this._urlBase64ToUint8Array(
                this.applicationServerKey
            );

            if (!isAlreadyGranted) {
                this._whenPending();
            }

            navigator.serviceWorker
                .getRegistrations()
                .then(function (registrations) {
                    for (let registration of registrations) {
                        var subscribeOptions = {
                            userVisibleOnly: true,
                            applicationServerKey: applicationServerKey,
                        };

                        return registration.pushManager.subscribe(
                            subscribeOptions
                        );
                    }
                })
                .then(
                    function (pushSubscription) {
                        var payload = JSON.parse(
                            JSON.stringify(pushSubscription)
                        );

                        payload.permissions = [this.notificationType];
                        this._whenGranted(isAlreadyGranted);
                        this._setDeviceIdentifier(payload);
                    }.bind(this)
                )
                .catch(
                    function (err) {
                        this._onRejectedByUser.bind(this);
                        throw new Error(err);
                    }.bind(this)
                );
        },

        /**
         * Sets a listener that can catch changes in the browser settings in area of push notifications permissions.
         * Reaction is scoped to !this._isRequestedByClient to avoid multiple actions. This event is mostly used on checkout success page to make subscription process possible without refreshing page (when notifications are denied and user changes this setting manually in browser settings, browser will inform him that refresh is needed to apply changes). Refresh on checkout success page ends with redirect to empty basket page so we need to catch this change and subscribe immediately to not loose session
         *  alwaysAsk option informs if subscription can be processed right after user changes permissions or script should just set status to the default one and wait for user reaction (used by back in stock to avoid automatic subscriptions everytime user enters the PDP with product [or it's option] that is out of stock).
         */
        _monitorPermissionChanges: function () {
            navigator.permissions
                .query({
                    name: 'notifications',
                })
                .then(
                    function (permission) {
                        permission.addEventListener(
                            'change',
                            function () {
                                if (this._isRequestedByClient) {
                                    return;
                                }

                                if (permission.state === 'denied') {
                                    this._onRejectedByBrowser();
                                } else if (permission.state === 'granted') {
                                    if (this.alwaysAsk) {
                                        this._request();
                                    } else {
                                        this._subscribe(false);
                                    }
                                } else {
                                    this._request();
                                }
                            }.bind(this)
                        );
                    }.bind(this)
                );
        },

        /**
         * Sets initial content of the request panel.
         * showPanelIfPermissionAlwaysGranted informs if panel should be even shown at all if persmission is already granted at this stage
         * Basically it checks current permission state and adjusts content of the panel.
         */
        _setInitialPanelContent: function () {
            if (Notification.permission === 'granted') {
                if (!this.showPanelIfPermissionAlwaysGranted) {
                    this.showOnInit = false;
                }

                if (this.alwaysAsk) {
                    this._request();
                } else {
                    $.get({
                        url: url.build('rest/V1/pwa/permission'),
                        contentType: 'application/json'
                    }).success(function (acceptedPermissions) {
                        if (acceptedPermissions.includes(this.notificationType)) {
                            this._subscribe(true);
                        } else {
                            this._request();
                        }
                    }.bind(this));
                }
            } else if (Notification.permission === 'denied') {
                this._onRejectedByBrowser();
            } else {
                this._request();
            }
        },

        /**
         * State.
         * Updates set of observables when permission state is 'prompt' (default).
         */
        _request: function () {
            this.content({
                header: this.panelHeaders.request,
                description: this.panelDescriptions.request,
            });
            this.showActions(true);
            this.subscriptionStatus('request');
            this.showPanel(true);
        },

        /**
         * State.
         * Updates set of observables when permission state is between 'prompt' and 'denied/granted'. It's state where we send request to the user and waiting for his decision.
         */
        _whenPending: function () {
            this.content({
                header: this.panelHeaders.pending,
                description: this.panelDescriptions.pending,
            });
            this.showActions(false);
            this.subscriptionStatus('pending');
        },

        /**
         * State.
         * Updates set of observables when permission state is 'granted' (user confirmed he wants to receive push notifications).
         * @param {boolean} isAlreadyGranted - informs if push notification permission was granted by user or automatically by browser (it was already granted before user entered current page). Based on this info we display different content (defined in XML)
         */
        _whenGranted: function (isAlreadyGranted) {
            this.content({
                header: isAlreadyGranted
                    ? this.panelHeaders.alreadyGranted
                    : this.panelHeaders.granted,
                description: isAlreadyGranted
                    ? this.panelDescriptions.alreadyGranted
                    : this.panelDescriptions.granted,
            });
            this.showActions(false);
            this.subscriptionStatus('granted');
        },

        /**
         * State.
         * Updates set of observables when permission state is 'rejtected' by browser settings (request was declined by browser that already knew to decline push notificaitons before user eneter current page).
         */
        _onRejectedByBrowser: function () {
            this.content({
                header: this.panelHeaders.autoReject,
                description: this.panelDescriptions.autoReject,
            });
            this.showActions(false);
            this.subscriptionStatus('rejected');
        },

        /**
         * State.
         * Updates set of observables when permission state is 'rejtected' by user (request was made and user rejected it).
         */
        _onRejectedByUser: function () {
            this.content({
                header: this.panelHeaders.userReject,
                description: this.panelDescriptions.userReject,
            });
            this.showActions(false);
            this.subscriptionStatus('rejected');
        },

        /**
         * Map base64 string to Uint8Array
         * @param {string} base64String - a base64 decoded string
         * @return {array}
         */
        _urlBase64ToUint8Array: function (base64String) {
            // BBB-mPweDyPLQsnE1rJPPpI3jRCx4VLa5aWpInrMjjM8ZjPhdmZTF6-IU5IcUADI7ITFmZLutqmo76UBdf_Pub8
            var padding = '='.repeat((4 - (base64String.length % 4)) % 4),
                base64 = (base64String + padding)
                    .replace(/\-/g, '+')
                    .replace(/_/g, '/'),
                rawData = window.atob(base64),
                outputArray = new Uint8Array(rawData.length);

            for (var i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }

            return outputArray;
        },
    });
});
