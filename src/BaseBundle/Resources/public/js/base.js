;
$(document).ready(function () {
    var body = $('body');

    // Confirmation messages
    body
        .off('click', '.requires-confirmation')
        .on('click', '.requires-confirmation', function (e) {
            var message = $(this).data('message');
            if (!confirm(message)) {
                e.preventDefault();
            }
        })
    ;

});
