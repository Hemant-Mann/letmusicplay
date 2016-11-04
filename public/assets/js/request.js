/**
 * Request Library a wrapper around jQuery Ajax
 * @param  {Object} window The Global window object
 * @param  {function} $      jQuery function
 * @return {Object}        A new object of the library
 * @author  Hemant Mann http://github.com/Hemant-Mann
 */
(function (window, $) {
    var Request = (function () {
        function Request() {
            this.api = window.location.origin; // Api EndPoint
            this.extension = '.json';

            this.entityMap = {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': '&quot;',
                "'": '&#39;',
                "/": '&#x2F;'
            };

            this.escapeHtml = function escapeHtml(string) {
                var self = this;
                return String(string).replace(/[&<>"'\/]/g, function (s) {
                    return self.entityMap[s];
                });
            };

            $.ajaxSetup({
                headers: {'X-JSON-Api': 'SwiftMVC'}
            }); 
        }

        Request.prototype = {
            get: function (opts, callback) {
                this._request(opts, 'GET', callback);
            },
            post: function (opts, callback) {
                this._request(opts, 'POST', callback);
            },
            delete: function (opts, callback) {
                this._request(opts, 'DELETE', callback);
            },
            _clean: function (entity) {
                if (!entity || entity.length === 0) {
                    return "";
                }
                return entity.replace(/\./g, '');
            },
            _request: function (opts, type, callback) {
                var link = this._getLink(opts.url),
                    self = this;

                $.ajax({
                    url: link,
                    type: type,
                    data: opts.data || {},
                }).done(function (data) {
                    callback.call(self, null, data);
                }).fail(function (err) {
                    callback.call(self, err || "error", {});
                });
            },
            _getLink: function (uri) {
                var parser = this._urlParser(uri);
                var link = parser.protocol + '//' + parser.host + parser.pathname + this.extension + parser.search;
                return link;
            },
            _urlParser: function (uri) {
                var obj = {};
                if (!uri) return obj;

                var parser = document.createElement('a');
                parser.href = uri;

                var properties = ['protocol', 'hostname', 'port', 'pathname', 'search', 'hash', 'host'];
                
                properties.forEach(function (prop) {
                    obj[prop] = parser[prop];
                });

                return obj;
            }
        };
        return Request;
    }());
    // Because "window.Request" is already taken
    window.request = new Request();
}(window, jQuery));