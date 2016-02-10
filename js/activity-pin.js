

jQuery(document).ready(function($) {
    jQuery(document).on('click', '.sa-button-pin', function () {
        var button = $(this);
        var data = {
            'action': 'pin_activity',
            'nonces' : button.attr('data-post-nonces'),
            'id' :button.attr('data-post-id')
         };
        button.addClass( 'loading');

        jQuery.post("/wp-admin/admin-ajax.php", data, function (response) {
            button.removeClass('loading');
            button.removeClass('notpinned');
            button.addClass('pinned');
        });
        return false;
    });

    jQuery(document).on('click', '.sa-button-unpin', function () {
        var button = $(this);
        var data = {
            'action': 'unpin_activity',
            'nonces' : button.attr('data-post-nonces'),
            'id' :button.attr('data-post-id')

        };
        button.addClass( 'loading');
        jQuery.post("/wp-admin/admin-ajax.php", data, function (response) {
            button.removeClass('loading');
            button.removeClass('pinned');
            button.addClass('notpinned');
        });
        return false;
    });
});



