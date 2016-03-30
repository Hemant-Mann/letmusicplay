(function(i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function() {
        (i[r].q = i[r].q || []).push(arguments)
    }, i[r].l = 1 * new Date();
    a = s.createElement(o),
        m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

ga('create', 'UA-75681914-3', 'auto');
ga('send', 'pageview');

$(document).on('pagecreate', '#myPage', function() {
    $('#autocomplete').on('filterablebeforefilter', function(e, data) {
        var $ul = $(this),
            $input = $(data.input),
            value = $input.val(),
            html = "";

        $ul.html("");
        if (value && value.length > 2) {
            $ul.html("<li><div class='ui-loader'><span class='ui-icon ui-icon-loading'></span></div></li>");
            $ul.listview('refresh');

            Request.get({
                action: 'music/search',
                data: {
                    q: $input.val()
                }
            }, function(response) {
                $.each(response, function(i, val) {
                    html += "<li>" + val + "</li>";
                });
                $ul.html(html);
                $ul.listview('refresh');
                $ul.trigger('updatelayout');
            });
        }
    });

});

$(document).ready(function() {
    $('.downloadThis').bind('click', function (e, ui) {
        e.preventDefault();
        var self = $(this),
            html = self.html();

        self.html('Please wait while we downloading your file');
        Request.get({
            action: self.attr('href')
        }, function (data) {
            if (data == "success") {
                self.html("Download Done! Try Again?");
                window.location.href = self.attr('href');
            } else {
                self.html(html);
                $('#downloadError').popup('open');
            }
        });
    });

    var loc = window.location.pathname;
    if (loc.match('/music/view')) {
        var s = loc.split("/"),
            title = s[3];

        $('title').html(title);
    }
});

$(document).on('pagecreate', function () {
    function scale(width, height, padding, border) {
        var scrWidth = $(window).width() - 30,
            scrHeight = $(window).height() - 20,
            ifrPadding = 2 * padding,
            ifrBorder = 2 * border,
            ifrWidth = width + ifrPadding + ifrBorder,
            ifrHeight = height + ifrPadding + ifrBorder,
        h, w;

        if (ifrWidth < scrWidth && ifrHeight < scrHeight) {
            w = ifrWidth;
            h = ifrHeight;
        } else if ((ifrWidth / scrWidth) > (ifrHeight / scrHeight)) {
            w = scrWidth;
            h = (scrWidth / ifrWidth) * ifrHeight;
        } else {
            h = scrHeight;
            w = (scrHeight / ifrHeight) * ifrWidth;
        }

        return {
            'width': w - (ifrPadding + ifrBorder),
            'height': h - (ifrPadding + ifrBorder)
        };
    };

    $('.ui-popup iframe').attr('width', 0).attr('height', 'auto');
    $('#popupVideo').on({
        popupbeforeposition: function () {
            var size = scale(497, 298, 15, 1),
                w = size.width,
                h = size.height;

            $('#popupVideo iframe').attr('width', w).attr('height', h);
        },
        popupafterclose: function () {
            $('#popupVideo iframe').attr('width', 0).attr('height', 0);
        }
    });
});
