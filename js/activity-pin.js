

jQuery(document).ready(function($) {
    jQuery(document).on('click', '.sa-button-pin', function () {
        var button = $(this);
        var data = {
            'action': 'pin_activity',
            'nonces' : button.attr('data-post-nonces'),
            'id' :button.attr('data-post-id')
         };
        jQuery.post("/wp-admin/admin-ajax.php", data, function (response) {
            //alert('Got this from the server: ' + response);
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
        jQuery.post("/wp-admin/admin-ajax.php", data, function (response) {
            //alert('Got this from the server: ' + response);
        });
        return false;
    });
});



