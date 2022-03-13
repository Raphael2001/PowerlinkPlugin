function delete_token() {
    (function ($) {
        $.ajax({
            type: "POST",
            url: ajaxurl,
            cache: false,
            data: {
                'action': 'delete_token',
            },
            success: function (response) {
                console.log(response);

            },
            fail: function () {

            },
            complete: function () {
                location.reload();

            }
        });
    })(jQuery);
}

