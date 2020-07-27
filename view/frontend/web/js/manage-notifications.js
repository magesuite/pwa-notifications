define([
    'jquery',
], function ($) {
    'use strict';

    function makeRequest(manageUrl, data) {
        $('body').trigger('processStart');

        $.ajax({
            url: manageUrl,
            data: data,
            dataType: 'json',

            /**
             * Success callback.
             */
            success: function () {
                //success action
            },

            /**
             * Complete callback.
             */
            complete: function () {
                $('body').trigger('processStop');
            }
        });
    }

        /**
         * Initializes event listener to send ajaz request when checkbox state changes
         *
         * @param {Object} config - Optional configuration
         * @param {HTMLElement} el - DOM element.
         */
        function init(config, el) {
            $('.form-pwanotifications-manage input[type="checkbox"]').on('change', function () {
                var item = $(this)[0];
                var data = {[item.name]: item.checked};
                makeRequest(config.manageUrl, data);

            });        
        }

        return init;
});
