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

            request.get({
                action: 'music/search',
                data: {
                    q: $input.val()
                }
            }, function(err, response) {
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
    $(".fbshare").click(function (e) {
        e.preventDefault();
        var self = $(this);
        self.addClass('disabled');
        ouvre("https://www.facebook.com/sharer/sharer.php?u=" + self.attr('href'));
        self.removeClass('disabled');
    });

    $('.downloadThis').bind('click', function (e, ui) {
        e.stopImmediatePropagation();
        e.preventDefault();
        var self = $(this),
            html = self.html();
        self.addClass('disabled');
        self.html('Please wait while we are downloading your file');
        request.get({
            url: self.attr('href')
        }, function (err, data) {
            if (data == "success") {
                self.removeClass('disabled');
                self.html("Clicks here if Download does not starts");
                window.location.href = self.attr('href');
            } else {
                self.html(html);
                $('#downloadError').popup('open');
            }
        });
    });

    $("#showApiKey").bind("click", function(event, ui) {
        $("#apiKey").attr('type', 'text');
    });
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

function ouvre(fichier) {
    ff=window.open(fichier,"popup","width=600px,height=300px,left=50%,top=50%");
}
