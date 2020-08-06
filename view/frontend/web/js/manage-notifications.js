
define([
    'jquery'
], function ($) { 
    $.widget('magesuite.pwaNotificationManage', {
        options: {
            checkboxSelector: 'input[type="checkbox"]',
            manageUrl: ''
        },
        /**
         * Initializes event listener to send ajaz request when checkbox state changes
         */
        _create: function () {
            var widget = this;
            this.element.find(this.options.checkboxSelector).on('change', function () {
                var item = $(this)[0];
                var data = {[item.name]: item.checked};
                widget.makeNotificationChangeRequest(widget.options.manageUrl, data);
            });
        },
        makeNotificationChangeRequest: function(manageUrl, data) {
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
        },
    });
    return $.magesuite.pwaNotificationManage; 
});
