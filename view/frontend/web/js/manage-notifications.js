define([
    'jquery',
    'mage/url',
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function ($, url, messageList) {
    $.widget('magesuite.pwaNotificationManage', {
        options: {
            checkboxSelector: 'input[type="checkbox"]',
            manageUrl: ''
        },
        /**
         * Initializes event listener to send ajax request when checkbox state changes
         */
        _create: function () {
            var widget = this;
            this.element.find(this.options.checkboxSelector).on('change', function () {
                var item = $(this)[0];
                widget.makeNotificationChangeRequest(widget.options.manageUrl, item.name, item.checked);
            });
        },
        makeNotificationChangeRequest: function (manageUrl, permissionIdentifier, isChecked) {
            $('body').trigger('processStart');

            if (isChecked) {
                $.post({
                    url: url.build('rest/V1/pwa/permission'),
                    data: JSON.stringify({"permission": permissionIdentifier}),
                    contentType: 'application/json'
                }).then(function () {
                    $('body').trigger('processStop');

                    messageList.addSuccessMessage({
                        message: $.mage.__('Permission settings were saved correctly.')
                    });
                });
            } else {
                $.ajax({
                    url: url.build('rest/V1/pwa/permission/' + permissionIdentifier),
                    type: 'DELETE'
                }).then(function () {
                    $('body').trigger('processStop');

                    messageList.addSuccessMessage({
                        message: $.mage.__('Permission settings were saved correctly.')
                    });
                });
            }

        }
    });
    return $.magesuite.pwaNotificationManage;
});
