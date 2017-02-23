;
(function ($) {

    ////////////////////////////////////////////////////////////////////////////////
    // Functions
    ////////////////////////////////////////////////////////////////////////////////

    // Markdown type
    $.fn.refreshMarkdown = function () {
        var elems = this;
        if (elems.length === 0) {
            return elems;
        }

        return $(elems).each(function () {
            var elem = $(this);
            var rendered = marked(elem.val(), {sanitize:true});

            // hidden textarea that will contain html
            var html = $('#' + elem.data('html'));
            html.html(rendered);

            if (elem.data('preview')) {
                var preview = $(elem.data('preview'));
                if (preview.length) {
                    preview.html(rendered);

                    // refresh highlighting disabled for performance purposes
                    //if ($.isReady) {
                    //    hljs.initHighlighting.called = false;
                    //    hljs.initHighlighting();
                    //}
                }
            }
            autosize(elem);
        });
    };

    $(document).ready(function () {
        var body = $('body');

        ////////////////////////////////////////////////////////////////////////////////
        // Event subscribers
        ////////////////////////////////////////////////////////////////////////////////

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

        // Markdown type
        body
            .off('change keyup keydown paste cut', 'textarea.markdown')
            .on('change keyup keydown paste cut', 'textarea.markdown', function(e) {
                $(this).refreshMarkdown();
            })
        ;

        ////////////////////////////////////////////////////////////////////////////////
        // On ready
        ////////////////////////////////////////////////////////////////////////////////

        $('div.markdown, span.markdown').each(function() {
            var that = $(this);
            that.html(marked(that.html(), {sanitize:true}));
        });

        $('.dropdown-submenu > a').submenupicker();
        hljs.initHighlightingOnLoad();

        $('textarea.markdown').refreshMarkdown();
    });

})(jQuery);

