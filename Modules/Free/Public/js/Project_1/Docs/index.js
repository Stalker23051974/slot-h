$(document).ready(function() {
    _('docsSearch').keyup(function() {
        var i =_vt($(this));
        if (_l(i) > 2) {
            api(docUrl, {search: i}, function(e) {
                var i = JSON.parse(e);
                alfaFooter = i.sign;
                if (_nz(i.data.result)) {
                    di(_('nothing'), f$);
                    _('docsNav li:not(#nothing)').each(function() {
                        di($(this), _in(_a($(this).find('a'), 'href').match(/[\d]{1,}/)[0], i.data.result));
                    });
                } else {
                    di(_('nothing'));
                    di(_('docsNav li'), f$);
                }
            });
        } else {
            di(_('nothing'), f$);
            di(_('docsNav li'));
        }
    });
});