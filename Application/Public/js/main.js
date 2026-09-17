var CKEDITOR_BASEPATH = '/Application/Public/js/ckeditor/',
    minPasswordLength,
    _hUid,
    _hLabel,
    t$ = true,
    f$ = false,
    n$ = null,
    _timer = f$,
    u$ = undefined,
    n_$ = 'none',
    i_$ = 'required_error',
    i$ = 'input',
    r_$ = '.row',
    interval = 30,
    getterLock = f$,
    pTooltip = [f$, n$],
    _chunkUpload = {type: n$, id: n$},
    maxFlows = 3,
    _rtl = f$,
    _uploadProgress = n$
;

$(document).ready(function () {
    _rtl = _nz($('rtl'));
    pTooltip[1] = di(rm(_p('password_tooltip')));
    onlyLatin(_('restore_password_1,#register_password_1,#auctioneer_password_1,#profile_newpass_1,#profile_new_password_1,#profile_retry_password')).keydown(function (e) {
        if (eq(e.keyCode, 32)) {
            return boardSilence(e);
        }
        return true;
    }).keyup(function () {
        var i = _v($(this));
        if (_e(i) && t_(pTooltip[0])) {
            pTooltip[0] = f$;
            rm(_nr($(this), 'div', '.password_tooltip'));
        } else {
            pTooltip[0] = t$;
            $(pTooltip[1]).insertAfter($(this));
            di($(pTooltip[1]).find('.password_notice'), _l(i) < minPass);
            _W($(pTooltip[1]).find('.password_slider'), (_l(i) >= minPass ? 100 : (100 / minPass) * _l(i)) + '%');
            _class($(pTooltip[1]).find('.password_slider'), 'password_slider_high', _l(i) >= minPass && t_(checkPassword($(this))));
        }
    }).blur(function () {
        if (t_(pTooltip[0])) {
            pTooltip[0] = f$;
            rm(_nr($(this), 'div', '.password_tooltip'));
        }
    }).focus(function () {
        $(this).keyup();
    });
    if (!_ud(typeof(localCoockie))) {
        var b = $('body').keyup(function (e) {
            if (t_(e.altKey) && t_(e.shiftKey) && e.key.match(/^[lLдД]{1}$/)) {
                setSourceCookie(localCoockie, eq(getSourceCookie(localCoockie), 1) ? 0 : 1);
                go();
            }
        });
        var i = $('<div class="admin_locale">').append('<div class="admin_action">' + _addIconLanguage() + '</div>'),
            y = n$;
        for (y in adminLocale) if (adminLocale.hasOwnProperty(y)) {
            i.append('<div class="locale_row" _lrow="' + y + '"><label>' + adminLocale[y][0] + '</label><input type="text" value="' + adminLocale[y][1] + '" _lcheck="' + adminLocale[y][1] + '">' + _addIconRefresh(n$, '#bd8a49') + '</div>');
        }
        if (!n_(y)) {
            b.append(i);
            _cl(i.find('svg'), function () {
                var l = $(this).closest('[_lrow]'), i = l.find('input'), t = _v(i), c = _a(i, '_lcheck');
                if (t_(api(translateUrl, {hash: _a(l, '_lrow'), text: t, real: c}).result)) {
                    $(':contains(' + c + ')').each(function () {
                        if (!_nz($(this).closest('.admin_locale'))) {
                            if (is(_h($(this)), c)) {
                                _h($(this), t);
                            }
                            if (is(_a($(this), 'placeholder'), c)) {
                                _a($(this), 'placeholder', c);
                            }
                            if (is(_v($(this)), c)) {
                                _v($(this), c);
                            }
                        }
                    });
                }
            });
            _cl(_p('admin_action'), function () {
                _p('admin_locale').toggleClass('admin_locale_active');
            });
        }
    }
    setTimeout(function () {
        ba('type', 'text').each(function () {
            if (_a($(this), 'completeon') === undefined) {
                _a($(this), 'autocomplete', 'off');
            }
        });
        ba('type', 'password').each(function () {
            _a($(this), 'autocomplete', 'new-password');
        });
        $('[value]').each(function () {
            var i = _v($(this)).match(/<rtl>(.*?)<\/rtl>/);
            if (i) {
                _v(_rc($(this), 'rtl_text'), i[1]);
            }
        });
        $('[_placeholder]').each(function () {
            var i = _a($(this), '_placeholder').match(/<rtl>(.*?)<\/rtl>/);
            if (i) {
                _rc(_a($(this), 'placeholder', i[1]), 'rtl_text');
            } else {
                _a($(this), 'placeholder', _a($(this), '_placeholder'));
            }
            $(this).removeAttr('_placeholder');
        });
    }, 1000);
    _ch(_('tests'), function () {
        localStorage.setItem('ab', _v($(this)));
        api(abUrl, {ab: _v($(this))}, function () {
            go(clearLocation());
        });
    });
    if (t_(_rtl)) {
        $('select,input').each(checkRtl);
    }
});

function checkRtl() {
    var i, y;
    switch ($(this)[0].nodeName.toLowerCase()) {
        case 'input':
            i = _a($(this), 'placeholder');
            if (i !== undefined) {
                _a($(this), 'placeholder', i.replace(/<[\/]{0,1}rtl>/g, ''));
            }
            break;
        case 'select':
            $(this).find('option').each(function () {
                _h($(this), _h($(this)).replace(/<[\/]{0,1}rtl>/g, ''))
            });
            break;
    }
    $(this).css({direction: 'rtl'});
}

function checkPassword(obj) {
    var y, i = _v(obj), c = t$;
    for (y in passSymbols) if (passSymbols.hasOwnProperty(y) && !n_(passSymbols[y]) && t_(c)) {
        c = !!i.match(new RegExp(passSymbols[y]));
    }
    _class($(this), 'required_error', !_nz(_p('password_slider_high')));
    return c;
}

function pb(v) {
    return v.closest(r_$);
}

function _rp(s, a) {
    var i;
    for (i in a) if (a.hasOwnProperty(i)) {
        s = s.replace(new RegExp(i), a[i]);
    }
    return s;
}

function refreshMenuHints() {
    const sidebar = _p('sidebar');
    const z = c_(sidebar, 'sidebar-full') ? ['hint', '_hint'] : ['_hint', 'hint'];
    sidebar.find('[' + z[0] + ']').each(function () {
        _a($(this), z[1], _a($(this), z[0]));
        $(this).removeAttr(z[0]);
    });
}

function checkLeftMenu() {
    const l = _p('sidebar');
    if (_ln(l)) {
        _H(l, 'max-content');
        _OverflowY(l, (l[0].offsetHeight > window.innerHeight));
        _H(l, '100vh');
        setTimeout(function () {
            var i = getSourceCookie('_leftMenu');
            if (!n_(i)) {
                _p('sidebar').scrollTop(i_(i));
            }
        }, 250);
    }
}

function saveHelp() {
    var y = _v(_p('helper').find('textarea')), t;
    if (api(helpUrl, {
            uid: _hUid,
            module: warningPage.module,
            controller: warningPage.controller,
            action: warningPage.action,
            label: _hLabel,
            text: y
        })) {
        _h(_rc($(this), 'btn-success', 'btn-primary'), hsText[1]);
        t = [ba('huid', _hUid), ba('__hf', _hUid)];
        _a(t[_nz(t[0]) ? 0 : 1].removeAttr(_e(y) ? 'huid' : '__hf'), _e(y) ? '__hf' : 'huid', _hUid);
        helps[_hUid] = y;
        setTimeout(function () {
            _h(_rc(_p('helper .btn-success'), 'btn-primary', 'btn-success'), hsText[0]);
            closeHelp();
        }, 1000);
    }
}

function closeHelp() {
    _hUid = n$;
    _hLabel = n$;
    di(_p('helper,.back_opacity'), f$);
}

function addExpandedControl(target, child, hideClass, showClass, selectClass, closure) {
    if (typeof (selectClass)) {
        selectClass = n$;
    }
    if (typeof (closure)) {
        closure = n$;
    }
    _cl(ba(target), function () {
        var i = ba(child, _a($(this), target));
        if (c_($(this), showClass)) {
            _rc($(this), n$, showClass);
            di(_rc(i, n$, selectClass, !n_(selectClass)), f$);
        } else {
            _rc($(this), showClass);
            if (_a(i, 'flexable') !== undefined) {
                _Flex(i);
            } else {
                di(i);
            }
            di(_rc(i, selectClass, n$, !n_(selectClass)));
            if (!n_(closure)) {
                closure($(this));
            }
        }
    });
}

function activateLnkTab(e) {
    if (!c_($(this), 'active') && !c_($(this), '.deactivate')) {
        var z = _rc(_block('form').find('.active'), n$, 'active'),
            i = _block(_a(_rc($(this), 'active'), 'lnk')),
            y = _block(_a(z, 'lnk'));
        if (_a(i, 'flexable') !== undefined) {
            _Flex(i);
        } else {
            di(i);
        }
        if (_a(y, 'flexable') !== undefined) {
            _Display(y, n_$);
        } else {
            di(y, f$);
        }
    }
    return boardSilence(e);
}

function closeControlTooltip() {
    if (_nz(_p('control-tooltip:visible'))) {
        di(_p('control-back,.control-tooltip'), f$);
        _p('control-tooltip tbody').empty();
    }
}

function likeUpAction() {
    var arr = {};
    arr['entity'] = _a($(this), 'le');
    arr['entity_type'] = _a($(this), 'lt');
    var res = api(likeURL, arr);
    if (!is(res, 'not ok')) {
        $(this).siblings('.like_counter').text(i_($(this).siblings('.like_counter').text()) + 1);
    }
    return f$;
}

function getterInit() {
    if (typeof (getterSend) !== 'undefined') {
        if (!_ud(typeof (getterSend))) {
            setInterval(getterFunc, interval * 1000);
        }
        setInterval(function () {
            _p('notify [cnt]').each(function () {
                var i = i_(_a($(this), 'cnt'));
                if (is(i, 1)) {
                    rm($(this));
                } else {
                    _a($(this), 'cnt', (i - 1));
                }
            });
        }, 1000);
        getterFunc();
    }
}

function getterFunc() {
    if (_ud(typeof (getterSend)) || t_(getterLock)) {
        return t$;
    }
    var z = {voice: voiceType}, i, y, p = 1;
    i = api(getterSend, z);
    if (!_ud(typeof(alerts))) {
        for (y in alerts) if (alerts.hasOwnProperty(y)) {
            if (!_ud(typeof (i.alerts[y])) && !_e(i.alerts[y])) {
                $('.notify').append(
                    _p('notify').append(_cl(_Opacity(_addDiv('notify_' + y + (!_ud(typeof (alarmPushes[y])) ? ' alarmPush' : ''), 'cnt="' + interval + '"'), '.7').append('<span class="notify_title">' + alerts[y] + '</span><hr>' + i.alerts[y]), function () {
                        if (!c_($(this), 'alarmPush')) {
                            rm($(this));
                        }
                    })));
                talk(i.alerts[y]);
            }
        }
        /*
         for (y in alerts) if (alerts.hasOwnProperty(y)) {
         if (!_ud(typeof (i.alerts[y])) && !_e(i.alerts[y])) {
         $('.notify').append(
         _p('notify').append(_cl(_Opacity(_addDiv('notify_' + y + (!_ud(typeof (alarmPushes[y])) ? ' alarmPush' : ''), 'cnt="' + interval + '"'), '.7').append('<span class="notify_title">' + alerts[y] + '</span><hr>' + i.alerts[y]), function ()
         {
         if (!c_($(this), 'alarmPush')) {
         rm($(this));
         }
         })));
         }
         }
         */
    }
    _class(_p('header__login_user'), 'alert-message', i.counters.messages > 0);
    if (!localStorage.getItem('ab')) {
        i = api(abUrl, {});
        localStorage.setItem('ab', i.result);
    }
}

function api(url, data, async, before) {
    if (u_(async)) {
        async = f$;
    }
    if (u_(before)) {
        before = function () {
        };
    }
    var res = ajaxCall(url, data, async, before), msg;
    if (f_(async)) {
        msg = $.parseJSON(res.responseText);
        alfaFooter = msg.sign;
        if (is(msg.status, 'error')) {
            alert(msg.error.text + ' (#' + msg.error.code + ')');
            return f$;
        }
        return msg.data;
    }
    return t$;
}

function apiFile(url, data, async) {
    if (async === undefined) {
        async = f$;
    }
    var res = ajaxCallFile(url, data, async), msg;
    if (f_(async)) {
        msg = $.parseJSON(res.responseText);
        alfaFooter = msg.sign;
        if (is(msg.status, 'error')) {
            alert(msg.error.text + ' (#' + msg.error.code + ')');
            return f$;
        }
        return msg.data;
    }
    return t$;
}

function ajaxCallFile(url, data, async) {
    if (async === undefined) {
        async = f$;
    }
    data._sign_ = alfaFooter;
    var i = {
        url: url,
        cache: f$,
        contentType: f$,
        processData: f$,
        data: data,
        method: 'POST'
    };
    if (f_(async)) {
        i.async = f$;
    } else {
        i.success = async;
        i.async = t$;
    }
    return $.ajax(i);
}

function ajaxCall(url, data, async, before) {
    data._sign_ = alfaFooter;
    alfaFooter = f$;
    if (u_(async)) {
        async = f$;
    }
    var i = {
        method: 'POST',
        url: url,
        data: data,
        beforeSend: before
    };
    if (f_(async)) {
        i.async = f$;
    } else {
        i.success = async;
        i.async = t$;
    }
    return $.ajax(i);
}

/**
 * check on control key
 * @param ev
 * @returns {boolean}
 */
function checkControl(ev) {
    var k = ev.keyCode;
    return _in(k, [8, 46, 36, 35, 9, 37, 39, 116]) || (eq(k, 82) && t_(ev.ctrlKey));
}

function onlyLatin(obj) {
    if (typeof(__onlyLatin) !== 'undefined' && t_(__onlyLatin)) {
        obj.keydown(function (e) {
            if (!(checkControl(e) || checkKeyInteger(e) || (e.key !== undefined && e.key.match(/^([a-z \_\-\/,\(\)]{1})$/i)))) {
                return boardSilence(e);
            }
            return true;
        });
    }
    return obj;
}

function _in(v, a) {
    for (var i in a) if (a.hasOwnProperty(i) && eq(v, a[i])) {
        return t$;
    }
    return f$;
}

/**
 * check on numeric key
 * @param e
 * @returns {boolean}
 */
function checkKeyInteger(e) {
    var k = e.keyCode;
    return (k > 47 && k < 58) || (k > 95 && k < 106);
}

function _class(obj, c, condition) {
    if (t_(condition)) {
        return _rc(obj, c);
    } else {
        return _rc(obj, n$, c);
    }
}

function _rc(v, add, remove, condition) {
    if (condition === undefined || t_(condition)) {
        if (remove === undefined) {
            remove = n$;
        } else if (is(typeof (remove), 'string')) {
            remove = [remove];
        }
        if (!n_(add) && is(typeof (add), 'string')) {
            add = [add];
        }
        if (!n_(remove)) {
            for (var i = 0; i < _l(remove); i++) v.removeClass(remove[i]);
        }
        if (!n_(add)) {
            for (i = 0; i < _l(add); i++) v.addClass(add[i]);
        }
    }
    return v;
}

function _p(v) {
    return $('.' + v);
}

function di(v, condition) {
    if (_ud(typeof (condition)) || t_(!!condition)) {
        if (_a($(v), 'flexable') !== undefined) {
            return _Flex($(v));
        }
        if (_a($(v), 'gridble') !== undefined) {
            return _Grid($(v));
        }
        return $(v).show();
    }
    return $(v).hide();
}

function isEmail(v) {
    return !n_(_v(v).match(/^[a-z\d._-]+@[a-z\d._-]+\.[a-z]{2,8}$/i));
}

function isUrl(v) {
    return !n_(_v(v).match(/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[a-zA-Z]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[a-zA-Z]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[a-zA-Z]{2,}|www\.[a-zA-Z0-9]+\.[a-zA-Z]{2,}|[a-zA-Z0-9]+\.[a-zA-Z]{2,})/i));
}

function _e(v) {
    return n_(v) || (is(typeof (v), 'object') ? (v.hasOwnProperty('val') ? is(_vt(v), '') : is(_l(v), 0)) : is(typeof (v), 'string') ? is(v.trim(), '') : is(i_(v), 0));
}

function _nz(obj) {
    return n_(obj) ? t$ : (is(typeof (obj), 'object') ? _l(obj) > 0 : i_(obj) > 0);
}

function isNumeric(v) {
    return !is(typeof (v), 'number');
}

function toggleChart(z) {
    var i = $(z), y = i.is(':visible');
    di(i, !t_(y));
    di(_p('back_opacity'), !t_(y));
    return y;
}

function tg(z) {
    var i = $(z), y = i.is(':visible');
    di(i, !t_(y));
}

function invalid(v) {
    return _rc(v, i_$);
}

function isValid(v) {
    return !c_(v, i_$);
}

function valid(v) {
    return _rc(v, n$, i_$);
}

function convertInTime(v, h) {
    if (_ud(typeof (h))) {
        h = f$;
    }
    var i = i_(v / 3600), y = i_((v - i * 3600) / 60), z = v - i * 3600 - y * 60, u, o, g;
    if (i > 24) {
        u = i_(i / 24);
        i -= u * 24;
        if (u > 30) {
            o = i_(u / 30);
            u -= o * 30;
        }
        if (u > 7) {
            g = i_(u / 7);
            u -= g * 7;
        }
    }
    return (t_(h) ? (o > 0 ? o + calendarText.month : '') + (g > 0 ? g + calendarText.week : '') + (u > 0 ? u + calendarText.day : '') + addZerro(i) + ':' : '') + addZerro(y) + ':' + addZerro(z);
}

function showChart(data, i, y, z) {
    var chart;
    if (i === undefined) {
        var p = api(chartUrl, data).result;
        i = p.data;
        y = p.pattern;
        z = '#chartDiv';
    }
    if (!f_(i)) {
        m = {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: p.pattern,
                    data: [],
                    fill: f$,
                    borderColor: 'rgb(206,161,43)',
                    lineTension: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: t$
                        }
                    }]
                }
            }
        };
        for (d in i[0].dataPoints) if (i[0].dataPoints.hasOwnProperty(d)) {
            m.data.labels.push(i[0].dataPoints[d].label);
            m.data.datasets[0].data.push(i[0].dataPoints[d].y);
        }
        chart = new Chart(document.getElementById('chartBlock').getContext('2d'), m);
        if (!_ud(typeof (z))) {
            toggleChart(z);
        }
        chart.render();
        return t$;
    }
    return f$;
}

function addZerro(v) {
    return (v < 10 ? '0' : '') + v;
}

function convertFromTime(v) {
    var i = _s(v, ':');
    return is(_l(i), 3) ? i_(i[0]) * 3600 + i_(i[1]) * 60 + i_(i[2]) : i_(i[0]) * 60 + i_(i[1]);
}

function getSourceCookie(k, v) {
    var i = document.cookie.match(/([^\s\;]{1,})/g), y = {}, z, p;
    for (z in i) if (i.hasOwnProperty(z)) {
        p = _s(i[z], '=');
        y[p[0]] = p[1];
    }
    if (!_ud(typeof (k))) {
        return !_ud(typeof (y[k])) ? y[k] : (v === undefined ? n$ : v);
    }
    return y;
}

function delSourceCookie(k) {
    setSourceCookie(k, n$);
}

function setSourceCookie(k, v) {
    var i = getSourceCookie(), z;
    if (!_ud(typeof (k)) && !_ud(typeof (v))) {
        i[k] = v;
    }
    for (z in i) if (i.hasOwnProperty(z)) {
        document.cookie = !n_(i[z]) ? z + '=' + i[z] + '; expire=' + (n_(i[z]) ? '1' : '31536000000') + '; path=/' : z + '=""; expire=0; path=/';
    }
}

function setSourceStorage(k, v) {
    if (is(typeof(v), 'object')) {
        v._timestamp = (new Date()).valueOf();
        checkStorage(k);
        localStorage.setItem(k, JSON.stringify(v));
    } else {
        localStorage.setItem(k, v);
    }
}

function delSourceStorage(i) {
    localStorage.removeItem(i);
}

function getSourceStorage(i) {
    checkStorage(i);
    return localStorage.getItem(i);
}

function checkStorage(k) {
    var i = (new Date()).valueOf() - 3600000, y = localStorage.valueOf(), z, p;
    for (z in y) if (y.hasOwnProperty(z) && !_e(y[z])) {
        if (p = JSON.parse(y[z])) {
            if (z.match(new RegExp(k)) && (is(typeof(p), 'object') && (p._timestamp === undefined || p._timestamp < i))) {
                delSourceStorage(z);
            }
        }
    }
}

function boardSilence(e) {
    e.preventDefault();
    e.stopPropagation();
    return f$;
}

function camelCase(v) {
    var i = _s(v, '-'), y, r = i[0], z = _l(i);
    if (z > 1) {
        for (y = 1; y < z; y++) {
            i[y] = _s(i[y], '');
            i[y][0] = (i[y][0]).toUpperCase();
            r += (i[y]).join('');
        }
    }
    return r;
}

function talk(v) {
    try {
        if (t_(systemVoice) && eq(getSourceCookie(voiceEnabledCookie, 0), 1)) {
            responsiveVoice.speak(v);
        }
    } catch (e) {
    }
}

function checkNumber(e, max) {
    return is(e.keyCode, 9) || (!isNaN(i_(e.key)) && (f_(max) || _vi($(e.target) + e.key) < max)) || (_in(e.keyCode, [36, 35, 37, 39, 8, 46]));
}

var MD5 = function (d) {
    result = M(V(Y(X(d), 8 * _l(d))));
    return result.toLowerCase();
};

function M(d) {
    for (var _, m = "0123456789ABCDEF", f = "", r = 0; r < _l(d); r++) _ = d.charCodeAt(r), f += m.charAt(_ >>> 4 & 15) + m.charAt(15 & _);
    return f
}

function X(d) {
    for (var _ = Array(_l(d) >> 2), m = 0; m < _l(_); m++) _[m] = 0;
    for (m = 0; m < 8 * _l(d); m += 8) _[m >> 5] |= (255 & d.charCodeAt(m / 8)) << m % 32;
    return _
}

function V(d) {
    for (var _ = "", m = 0; m < 32 * _l(d); m += 8) _ += String.fromCharCode(d[m >> 5] >>> m % 32 & 255);
    return _
}

function Y(d, _) {
    d[_ >> 5] |= 128 << _ % 32, d[14 + (_ + 64 >>> 9 << 4)] = _;
    for (var m = 1732584193, f = -271733879, r = -1732584194, i = 271733878, n = 0; n < _l(d); n += 16) {
        var h = m, t = f, g = r, e = i;
        f = md5_ii(f = md5_ii(f = md5_ii(f = md5_ii(f = md5_hh(f = md5_hh(f = md5_hh(f = md5_hh(f = md5_gg(f = md5_gg(f = md5_gg(f = md5_gg(f = md5_ff(f = md5_ff(f = md5_ff(f = md5_ff(f, r = md5_ff(r, i = md5_ff(i, m = md5_ff(m, f, r, i, d[n + 0], 7, -680876936), f, r, d[n + 1], 12, -389564586), m, f, d[n + 2], 17, 606105819), i, m, d[n + 3], 22, -1044525330), r = md5_ff(r, i = md5_ff(i, m = md5_ff(m, f, r, i, d[n + 4], 7, -176418897), f, r, d[n + 5], 12, 1200080426), m, f, d[n + 6], 17, -1473231341), i, m, d[n + 7], 22, -45705983), r = md5_ff(r, i = md5_ff(i, m = md5_ff(m, f, r, i, d[n + 8], 7, 1770035416), f, r, d[n + 9], 12, -1958414417), m, f, d[n + 10], 17, -42063), i, m, d[n + 11], 22, -1990404162), r = md5_ff(r, i = md5_ff(i, m = md5_ff(m, f, r, i, d[n + 12], 7, 1804603682), f, r, d[n + 13], 12, -40341101), m, f, d[n + 14], 17, -1502002290), i, m, d[n + 15], 22, 1236535329), r = md5_gg(r, i = md5_gg(i, m = md5_gg(m, f, r, i, d[n + 1], 5, -165796510), f, r, d[n + 6], 9, -1069501632), m, f, d[n + 11], 14, 643717713), i, m, d[n + 0], 20, -373897302), r = md5_gg(r, i = md5_gg(i, m = md5_gg(m, f, r, i, d[n + 5], 5, -701558691), f, r, d[n + 10], 9, 38016083), m, f, d[n + 15], 14, -660478335), i, m, d[n + 4], 20, -405537848), r = md5_gg(r, i = md5_gg(i, m = md5_gg(m, f, r, i, d[n + 9], 5, 568446438), f, r, d[n + 14], 9, -1019803690), m, f, d[n + 3], 14, -187363961), i, m, d[n + 8], 20, 1163531501), r = md5_gg(r, i = md5_gg(i, m = md5_gg(m, f, r, i, d[n + 13], 5, -1444681467), f, r, d[n + 2], 9, -51403784), m, f, d[n + 7], 14, 1735328473), i, m, d[n + 12], 20, -1926607734), r = md5_hh(r, i = md5_hh(i, m = md5_hh(m, f, r, i, d[n + 5], 4, -378558), f, r, d[n + 8], 11, -2022574463), m, f, d[n + 11], 16, 1839030562), i, m, d[n + 14], 23, -35309556), r = md5_hh(r, i = md5_hh(i, m = md5_hh(m, f, r, i, d[n + 1], 4, -1530992060), f, r, d[n + 4], 11, 1272893353), m, f, d[n + 7], 16, -155497632), i, m, d[n + 10], 23, -1094730640), r = md5_hh(r, i = md5_hh(i, m = md5_hh(m, f, r, i, d[n + 13], 4, 681279174), f, r, d[n + 0], 11, -358537222), m, f, d[n + 3], 16, -722521979), i, m, d[n + 6], 23, 76029189), r = md5_hh(r, i = md5_hh(i, m = md5_hh(m, f, r, i, d[n + 9], 4, -640364487), f, r, d[n + 12], 11, -421815835), m, f, d[n + 15], 16, 530742520), i, m, d[n + 2], 23, -995338651), r = md5_ii(r, i = md5_ii(i, m = md5_ii(m, f, r, i, d[n + 0], 6, -198630844), f, r, d[n + 7], 10, 1126891415), m, f, d[n + 14], 15, -1416354905), i, m, d[n + 5], 21, -57434055), r = md5_ii(r, i = md5_ii(i, m = md5_ii(m, f, r, i, d[n + 12], 6, 1700485571), f, r, d[n + 3], 10, -1894986606), m, f, d[n + 10], 15, -1051523), i, m, d[n + 1], 21, -2054922799), r = md5_ii(r, i = md5_ii(i, m = md5_ii(m, f, r, i, d[n + 8], 6, 1873313359), f, r, d[n + 15], 10, -30611744), m, f, d[n + 6], 15, -1560198380), i, m, d[n + 13], 21, 1309151649), r = md5_ii(r, i = md5_ii(i, m = md5_ii(m, f, r, i, d[n + 4], 6, -145523070), f, r, d[n + 11], 10, -1120210379), m, f, d[n + 2], 15, 718787259), i, m, d[n + 9], 21, -343485551), m = safe_add(m, h), f = safe_add(f, t), r = safe_add(r, g), i = safe_add(i, e)
    }
    return Array(m, f, r, i)
}

function md5_cmn(d, _, m, f, r, i) {
    return safe_add(bit_rol(safe_add(safe_add(_, d), safe_add(f, i)), r), m)
}

function md5_ff(d, _, m, f, r, i, n) {
    return md5_cmn(_ & m | ~_ & f, d, _, r, i, n)
}

function md5_gg(d, _, m, f, r, i, n) {
    return md5_cmn(_ & f | m & ~f, d, _, r, i, n)
}

function md5_hh(d, _, m, f, r, i, n) {
    return md5_cmn(_ ^ m ^ f, d, _, r, i, n)
}

function md5_ii(d, _, m, f, r, i, n) {
    return md5_cmn(m ^ (_ | ~f), d, _, r, i, n)
}

function safe_add(d, _) {
    var m = (65535 & d) + (65535 & _);
    return (d >> 16) + (_ >> 16) + (m >> 16) << 16 | 65535 & m
}

function bit_rol(d, _) {
    return d << _ | d >>> 32 - _
}

function generatePassCheckReqular(pass) {
    minPasswordLength = pass.minLength;
    var i = '';
    if (t_(pass.decimal)) {
        i += '0-9';
    }
    if (t_(pass.upper)) {
        i += 'A-Z';
    }
    if (t_(pass.lower)) {
        i += 'a-z';
    }
    if (!_e(pass.symbol)) {
        i += pass.symbol;
    }
    return new RegExp('^' + '[' + i + ']{' + pass.minLength + ',}$');
}

function clearLocation() {
    return _rp(_url(), {'[\?\#]+.*?$': ''});
}

function _url() {
    return location.href;
}

function _j(obj, d) {
    return obj.join(d);
}

function _s(obj, d) {
    return obj.split(d);
}

function getSize(v, cell) {
    var i = [], t = 0;
    v = i_(v);
    if (v > 1073741823) {
        if (f_(cell)) {
            t = i_(v / 1073741824);
            i.push(i_(t) + ' Gb');
        } else {
            return _fl(v / 1073741824) + 'Gb';
        }
    }
    v -= t * 1073741824;
    if (v > 1048575) {
        if (f_(cell)) {
            t = i_(v / 1048576);
            i.push(i_(t) + ' Mb');
        } else {
            return _fl(v / 1048576) + 'Mb';
        }
    }
    v -= t * 1048576;
    if (v > 1023) {
        if (f_(cell)) {
            t = i_(v / 1024);
            i.push(i_(t) + ' Kb');
        } else {
            return _fl(v / 1024) + 'Kb';
        }
    }
    v -= t * 1024;
    if (_nz(v)) {
        i.push(v + ' B');
    }
    if (t_(cell)) {
        return v + 'B';
    }
    return _j(i, ', ');
}

function _fl(v) {
    var i = parseFloat(v);
    return parseFloat(isNaN(i) ? 0 : i.toFixed(2));
}

function toggleBtn(obj, v) {
    if (_nz(obj)) {
        _class(_class(obj, t_(obj[0].hasAttribute('btns')) ? 'btn-success' : 'btn-primary', v), 'btn-secondary', f_(v));
    }
    return obj;
}

function _b(v) {
    return f_(v) || t_(v);
}

function _r(v) {
    return Math.random() * (undefined === v ? 1 : v);
}

function RGB2Hex(red, green, blue) {
    var r = i_(red).toString(16), g = i_(green).toString(16), b = i_(blue).toString(16);
    return _((_l(r) < 2 ? '0' : '') + r + (_l(g) < 2 ? '0' : '') + g + (_l(b) < 2 ? '0' : '') + b, t$);
}

function _W(obj, value, condition) {
    if (_ud(typeof (value))) {
        var i = obj.css('width');
        return (is(i, 'auto') || i.match(/\%$/)) ? i : i_(i);
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({width: value + (s_(value).match(/[\d]{1}$/) ? 'px' : '')});
    }
    return obj;
}

function _MinWidth(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({minWidth: value + (s_(value).match(/[\d]{1}$/) ? 'px' : '')});
    }
    return obj;
}

function _H(obj, value, condition) {
    if (_ud(typeof (value))) {
        var i = obj.css('height');
        return (is(i, 'auto') || i.match(/\%$/) || i.match(/\vh$/)) ? i : i_(i);
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({height: value + (s_(value).match(/[\d]{1}$/) ? 'px' : '')});
    }
    return obj;
}

function _BgColor(obj, value, condition) {
    if (_ud(typeof (value))) {
        return obj.css('backgroundColor');
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({backgroundColor: value});
    }
    return obj;
}

function _BgSize(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({backgroundSize: value});
    }
    return obj;
}

function _BgPosition(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({backgroundPosition: value});
    }
    return obj;
}

function _BgImage(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({backgroundImage: 'url(' + value + ')'});
    }
    return obj;
}

function _BgRepeat(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({backgroundRepeat: value});
    }
    return obj;
}

function _Border(obj, value, type, color) {
    obj.css({border: value > 0 ? (value + 'px' + (type !== undefined ? ' ' + type + ' ' + (color !== undefined ? color : 'black') : '')) : 'none'});
    return obj;
}

function _BorderTop(obj, value, type, color) {
    obj.css({borderTop: value > 0 ? (value + 'px' + (type !== undefined ? ' ' + type + ' ' + (color !== undefined ? color : 'black') : '')) : 'none'});
    return obj;
}

function _BorderRight(obj, value, type, color) {
    obj.css({borderRight: value > 0 ? (value + 'px' + (type !== undefined ? ' ' + type + ' ' + (color !== undefined ? color : 'black') : '')) : 'none'});
    return obj;
}

function _BorderBottom(obj, value, type, color) {
    obj.css({borderBottom: value > 0 ? (value + 'px' + (type !== undefined ? ' ' + type + ' ' + (color !== undefined ? color : 'black') : '')) : 'none'});
    return obj;
}

function _BorderLeft(obj, value, type, color) {
    obj.css({borderLeft: value > 0 ? (value + 'px' + (type !== undefined ? ' ' + type + ' ' + (color !== undefined ? color : 'black') : '')) : 'none'});
    return obj;
}

function _BColor(obj, color) {
    obj.css({borderColor: color !== undefined ? color : 'black'});
    return obj;
}

function _BorderRadius(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({borderRadius: value + (s_(value).match(/[\d]{1}$/) ? 'px' : '')});
    }
    return obj;
}

function _MinHeight(obj, value, condition) {
    if (_ud(typeof (value))) {
        return obj.css('minHeight');
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({minHeight: value + (s_(value).match(/[\d]{1}$/) ? 'px' : '')});
    }
    return obj;
}

function _T(obj, value, condition) {
    if (_ud(typeof (value))) {
        return i_(obj.css('top'));
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({top: value + (s_(value).match(/[\d]{1}$/) ? 'px' : '')});
    }
    return obj;
}

function _L(obj, value, condition) {
    if (_ud(typeof (value))) {
        return i_(obj.css('left'));
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({left: value + (s_(value).match(/[\d]{1}$/) ? 'px' : '')});
    }
    return obj;
}

function _Right(obj, value, condition) {
    if (_ud(typeof (value))) {
        return i_(obj.css('left'));
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({right: value + (s_(value).match(/[\d]{1}$/) ? 'px' : '')});
    }
    return obj;
}

function _Bottom(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({bottom: value + (s_(value).match(/[\d]{1}$/) ? 'px' : '')});
    }
    return obj;
}

function _MaxHeight(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({minHeight: value + (s_(value).match(/[\d]{1}$/) ? 'px' : '')});
    }
    return obj;
}

function _Flex(obj) {
    return _Display(obj, 'flex');
}

function _Grid(obj) {
    return _Display(obj, 'grid');
}

function _Display(obj, value, condition) {
    if (_ud(typeof (value))) {
        return !is(obj.css('display'), n_$);
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({display: value});
    }
    return obj;
}

function _LineHeight(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({lineHeight: value + (s_(value).match(/[\d]{1}$/) ? 'px' : '')});
    }
    return obj;
}

function _TextAlignment(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({textAlign: value});
    }
    return obj;
}

function _Padding(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({padding: value});
    }
    return obj;
}

function _PLeft(obj, value, condition) {
    if (value === undefined) {
        return i_(obj.css('padding-left'));
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({paddingLeft: value});
    }
    return obj;
}

function _PRight(obj, value, condition) {
    if (value === undefined) {
        return i_(obj.css('padding-right'));
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({paddingRight: value});
    }
    return obj;
}

function _PTop(obj, value, condition) {
    if (value === undefined) {
        return i_(obj.css('paddingTop'));
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({paddingTop: value});
    }
    return obj;
}

function _PBottom(obj, value, condition) {
    if (value === undefined) {
        return i_(obj.css('paddingBottom'));
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({paddingBottom: value});
    }
    return obj;
}

function _Margin(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({margin: value});
    }
    return obj;
}

function _MLeft(obj, value, condition) {
    if (_ud(typeof (value))) {
        return i_(obj.css('marginLeft'));
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({marginLeft: value});
    }
    return obj;
}

function _MTop(obj, value, condition) {
    if (_ud(typeof (value))) {
        return i_(obj.css('marginTop'));
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({marginTop: value});
    }
    return obj;
}

function _MBottom(obj, value, condition) {
    if (_ud(typeof (value))) {
        return i_(obj.css('marginBottom'));
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({marginBottom: value});
    }
    return obj;
}

function _Visibility(obj, value, condition) {
    if (_ud(typeof (value))) {
        return obj.is(':visible');
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({visibility: value});
    }
    return obj;
}

function _ZIndex(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({zIndex: value});
    }
    return obj;
}

function _BoxSizing(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({boxSizing: value});
    }
    return obj;
}

function _Position(obj, value, condition) {
    if (_ud(typeof (value))) {
        return obj.css.position;
    }
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({position: value});
    }
    return obj;
}

function _Opacity(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({opacity: value});
    }
    return obj;
}

function _Color(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({color: value});
    }
    return obj;
}

function _FontWeight(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({fontWeight: value});
    }
    return obj;
}

function _TextShadow(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({textShadow: value});
    }
    return obj;
}

function _FontStyle(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({fontStyle: value});
    }
    return obj;
}

function _Fill(obj, value) {
    return obj.css({fill: value});
}

function _FontSize(obj, value, condition) {
    if (_ud(typeof (condition)) || t_(condition)) {
        obj.css({fontSize: value + (s_(value).match(/[\d]{1}$/) ? 'px' : '')});
    }
    return obj;
}

function _OverflowY(obj, condition) {
    return obj.css({overflowY: condition === undefined || t_(condition) ? 'scroll' : 'hidden'});
}

function getId(obj) {
    return _a(obj, 'id') !== undefined ? _a(obj, 'id') : undefined;
}

function getName(obj) {
    return _a(obj, 'name') !== undefined ? _a(obj, 'name') : undefined;
}

function _addDiv(c, attr) {
    return $('<div class="' + c + '"' + (attr !== undefined ? ' ' + attr : '') + '>');
}

function _addInput(type, value, name, id, c) {
    return $('<input type="' + type + '" value=\'' + value + '\'' + (name !== undefined && !n_(name) ? ' name="' + name + '"' : '') + (id !== undefined && !n_(id) ? ' id="' + id + '"' : '') + (c !== undefined && !n_(c) ? ' class="' + c + '"' : '') + '>');
}

function _addImage(src, name, id, c) {
    return $('<img src="' + src + '"' + (name !== undefined && !n_(name) ? ' name="' + name + '"' : '') + (id !== undefined && !n_(id) ? ' id="' + id + '"' : '') + (c !== undefined && !n_(c) ? ' class="' + c + '"' : '') + '>');
}

function _addIconApps(classes, color, attrs) {
    return addSvg(_icons.apps, (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconRefresh(classes, color, attrs) {
    return addSvg(_icons.refresh, (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconLeft(classes, color, attrs) {
    return addSvg(_icons.left, 'fa-chevron-left' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconRight(classes, color, attrs) {
    return addSvg(_icons.right, 'fa-chevron-right' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconUp(classes, color, attrs) {
    return addSvg(_icons.up, 'fa-chevron-up' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconDown(classes, color, attrs) {
    return addSvg(_icons.down, 'fa-chevron-down' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconImage(classes, color, attrs) {
    return addSvg(_icons.image, 'fa-file-image' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconBars(classes, color, attrs) {
    return addSvg(_icons.bars, 'fa-bars' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconVideo(classes, color, attrs) {
    return addSvg(_icons.video, 'fa-file-film' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconEdit(classes, color, attrs) {
    return addSvg(_icons.edit, 'fa-edit' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconCopy(classes, color, attrs) {
    return addSvg(_icons.copy, 'fa-copy' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconClone(classes, color, attrs) {
    return addSvg(_icons.duplicate, 'fa-clone' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconAdd(classes, color, attrs) {
    return addSvg(_icons.add, 'fa-plus-circle' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconCheck(classes, color, attrs) {
    return addSvg(_icons.check, 'fa-check' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconCancel(classes, color, attrs) {
    return addSvg(_icons.cancel, 'fa-times' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconEye(classes, color, attrs) {
    return addSvg(_icons.eye, 'fa-eye' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconLanguage(classes, color, attrs) {
    return addSvg(_icons.language, 'fa-language' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconEyeSlash(classes, color, attrs) {
    return addSvg(_icons.eyeSlash, 'fa-eye-slash' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconTrash(classes, color, attrs) {
    return addSvg(_icons.trash, 'fa-ban' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconDotsHorizontal(classes, color, attrs) {
    return addSvg(_icons.dotsHorizontal, 'dots_horizontal' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconDotsVertical(classes, color, attrs) {
    return addSvg(_icons.dotsVertical, 'dots_vertical' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function _addIconAlert(classes, color, attrs) {
    return addSvg(_icons.alert, 'fa-alert' + (classes !== undefined && !n_(classes) ? ' ' + classes : ''), color !== undefined ? color : n$, attrs === undefined ? n$ : attrs);
}

function addSvg(path, c, color, attrs) {
    var i = ['<path class="fa ' + (c !== undefined ? c : '') + '" d="', '"' + (color !== n$ ? ' fill="' + color + '"' : '') + '></path>'];
    return '<svg' + (!is(n$, attrs) ? ' ' + attrs : '') + '>' + i[0] + path.join(i[1] + i[0], path) + i[1] + '</svg>';
}

// Boolean operation
function is(v0, v1) {
    return v0 === v1;
}

function _ud(v0) {
    return is(v0, 'undefined');
}

function eq(v0, v1) {
    return v0 == v1;
}

function n_(v) {
    return is(v, n$);
}

function t_(v) {
    return is(v, t$);
}

function f_(v) {
    return is(v, f$);
}

function u_(v) {
    return is(v, u$);
}

function _checked(obj, v) {
    return v === undefined ? obj.prop('checked') : _a(obj.prop('checked', v), 'checked', 'checked');
}

function _selected(obj, v, multi) {
    if (!_ln(obj)) {
        return n$;
    }
    if (multi === undefined && is(obj[0].localName, 'option')) {
        _a(obj.closest('select').find('option:selected').prop('selected', f$), 'selected', f$);
    }
    return v === undefined ? obj.prop('selected') : _a(obj.prop('selected', v), 'selected', v);
}

function _disabled(obj, v) {
    return v === undefined ? obj.prop('disabled') : obj.prop('disabled', v);
}

/**
 * @param {jQuery} obj
 * @param string closest
 * @param string find
 * @returns {jQuery}
 */
function _nr(obj, closest, find) {
    return obj.closest(closest).find(find);
}

function _click(obj, condition) {
    if (t_(condition)) {
        _cl(obj);
    }
}

function _block(v) {
    return _(v + 'Block');
}

function _chosenUpdate(obj) {
    return obj.trigger('chosen:updated');
}

function _isEnable(obj) {
    return !_isDisable(obj);
}

function _isDisable(obj) {
    return c_(obj, 'btn-secondary');
}

function _empty(obj, condition) {
    return condition === undefined || t_(condition) ? _v(obj, '') : obj;
}

function addOption(obj, k, v) {
    return obj.append('<option value="' + k + '" selected="selected">' + v + '</option>');
}

function svgClick(obj, func) {
    _cl(_svg(_cl(obj, func)), func);
}

// Return integer value if variable is integer
function i_(v) {
    var i = t_(v) ? 1 : (f_(v) ? 0 : parseInt(v));
    return isNaN(i) ? 0 : i;
}

function s_(v) {
    return '' + v;
}

function _v(obj, v) {
    return v === undefined ? obj.val() : obj.val(v);
}

function _h(obj, v) {
    if (v === undefined) {
        return obj.html();
    }
    obj.html(v);
    return obj;
}

// Object length
function _l(obj) {
    return obj.length;
}

function _le(obj) {
    return _e(_l(obj));
}

function _ln(obj) {
    return _nz(_l(obj));
}

function _vi(obj) {
    return i_(_v(obj));
}

function _vt(obj) {
    return _v(obj).trim();
}

function _cl(obj, func, action) {
    if (func !== undefined) {
        obj.click(func);
        return action !== undefined && t_(action) ? obj.click() : obj
    }
    return obj.click();
}

function _ch(obj, func, action) {
    if (func !== undefined) {
        obj.change(func);
        return action !== undefined && t_(action) ? obj.change() : obj
    }
    return obj.change();
}

function _ft(obj) {
    return obj.first();
}

function _lt(obj) {
    return obj.last();
}

function cf(obj) {
    _cl(_ft(obj));
}

function go(addr) {
    if (!f_(addr)) {
        location.href = addr === undefined ? clearLocation() : (t_(addr) ? _url() : addr);
    }
    return t$;
}

function _ck(obj, condition) {
    return t_(condition) ? valid(obj) : invalid(obj);
}

function c_tr(obj, attr, v) {
    return attr === undefined ? obj.closest('tr') : (v === undefined ? pa(obj, 'tr', attr) : _a(obj.closest('tr'), attr, v));
}

function c_(obj, v) {
    return obj.hasClass(v);
}

function c_li(obj, attr, v) {
    return obj.closest('li');
}

function rm(obj) {
    return obj.remove();
}

function _a(obj, attr, v) {
    if (v === undefined) {
        return obj.attr(attr);
    }
    obj.attr(attr, v);
    return obj;
}

function ev(obj) {
    return _e(_v(obj));
}

function cl_(obj) {
    return $.parseJSON(JSON.stringify(obj));
}

function _svg(obj) {
    return obj.closest('svg');
}

function FixTable(table) {
    var inst = this;
    this.table = table;
    $('tr > th', $(this.table)).each(function (index) {
        var t = $(this).html();
        $(this).empty().append(di(_h(_rc($('<div/>'), 'fixtable-fixed'), t), f$)).append(_h(_rc($('<div/>'), 'fixtable-relative'), t));
    });
    this.StyleColumns();
    this.FixColumns();
    _p('contentLine').scroll(function () {
        inst.FixColumns()
    }).resize(function () {
        inst.StyleColumns()
    });
}

FixTable.prototype.StyleColumns = function () {
    var inst = this;
    $('tr > th', $(this.table)).each(function () {
        var i = $('div.fixtable-relative', $(this)), y = $(i).parent('th');
        _PRight(
            _PLeft(
                _PTop(
                    _L(
                        _H(
                            _W($('div.fixtable-fixed', $(this)), $(y).outerWidth(t$) - parseInt($(y).css('border-left-width')) + 'px'),
                            $(y).outerHeight(t$) + 'px'
                        ),
                        $(i).offset().left - i_(_PLeft($(y))) + 'px'
                    ),
                    $(i).offset().top - $(inst.table).offset().top + 'px'
                ),
                _PLeft($(y))
            ),
            _PRight($(y))
        );
    });
};

FixTable.prototype.FixColumns = function () {
    var inst = this, y = $(window).scrollTop(), z = $(inst.table).offset().top,
        i = y < (z + $(inst.table).height() - $(inst.table).find('.fixtable-fixed').outerHeight()) && y > z;
    $('tr > th > div.fixtable-fixed', $(this.table)).each(function () {
        di($(this), i);
    });
};

function ba(a, v) {
    return $('[' + a + (v === undefined ? '' : '="' + v + '"') + ']');
}

function bv(v) {
    return ba('value', v);
}

function pa(p, c, a) {
    return _a(p.closest(a === undefined ? '[' + c + ']' : c), a === undefined ? c : a);
}

function bn(v) {
    return ba('name', v);
}

function bc(v) {
    return _p('' + v);
}

function v_(v) {
    return '[value="' + v + '"]';
}

function _(v, object) {
    return object !== undefined ? '#' + v : $('#' + v);
}

function checkUrlAddress(v) {
    return v.match(/^http[s]{0,1}:\/\//i) || v.match(/^www/i) ? v.match(/^(http[s]{0,1}:\/\/|)(www\.|)[a-z0-9-_]{2,}\.[a-z]{2,4}(:[0-9]{2,5}|)?((\/|\?|\#).*?|)/i) : n$;
}

function setAuctionDate() {
    var el = $(this), q, z;
    $(this).find('[_gmt_pattern]').each(function () {
        q = _a(el, '_gmt').match(/^(.*?)\|(.*?)$/);
        if ($(this).is('[_gmt_pattern_origin]')) {
            z = new Date(i_(q[1]) * 1000);
        } else {
            z = new Date((i_(q[1]) - i_(q[2]) * 3600 + _h(_('timezone .select-selected')).match(/([-\d]{1,})$/)[1] * 3600) * 1000);
            q[2] = _h(_('timezone .select-selected')).match(/([-\d]{1,})$/)[1];
        }
        _h($(this), _a($(this), '_gmt_pattern')
            .replace(/(\|d\|)/g, addZerro(z.getDate()))
            .replace(/(\|M\|)/g, locale.monthes[z.getMonth() + 1])
            .replace(/(\|Y\|)/g, ('' + z.getFullYear()).match(/[\d]{2}$/)[0])
            .replace(/(\|H\|)/g, addZerro(z.getHours()))
            .replace(/(\|w\|)/g, locale.days[is(z.getDay(), 0) ? 6 : z.getDay() - 1])
            .replace(/(\|i\|)/g, addZerro(z.getMinutes()))
            .replace(/(\|day\|)/g, locale.days[eq(z.getDay(), 0) ? 6 : z.getDay() - 1])
            .replace(/(\|t\|)/g, (q[2] > 0 ? '+' : '') + parseInt(q[2]))
            .replace(/(\|y\|)/g, ('' + z.getFullYear()).substr(2, 2))
        );
    });
}

function chunkUpload(object, texts) {
    var file = object[0].files[0], uniq = MD5(file.name + '/' + file.size + '/' + (new Date()).valueOf()), g, w, et,
        data = {
            filename: file.name,
            filesize: file.size,
            entitytype: _chunkUpload.type,
            entityid: _chunkUpload.id,
            filehash: uniq
        }, source, xhr;
    g = api(assemblyUrl, data);
    var i = g.result, flow, z, reader = new FileReader();
    if (navigator && navigator.connection && navigator.connection.downlink) {
        et = [i_((((navigator && navigator.connection && navigator.connection.downlink) ? fileChunkSize / ((navigator.connection.downlink * 1048576) / 8) : 4) + .5) * i.chunks + 1)];
        et.push(i_(et/3600));
        et.push(i_((et[0] - et[1] * 3600)/60));
        et.push(et[0] - et[1] * 3600 - et[2] * 60);
        _h(_p('upload_percents'), texts.estimated + (et[1] > 0 ? et[1]+texts.h+' ' : '') + (et[2] > 0 ? et[2]+texts.m+' ' : '') + (et[3] > 0 ? et[3]+texts.s : ''));
    }
    reader.onload = function (Event) {
        source = Event.target.result;
        if (!f_(i)) {
            w = i.chunks;
	    getterFunc();
            for (flow = 0; flow < w; flow++) {
                var f0 = new Uint8Array(source, flow * fileChunkSize, ((flow + 1) * fileChunkSize) < data.filesize ? fileChunkSize : data.filesize - flow * fileChunkSize),
		d = {
		    filename: file.name,
		    filesize: file.size,
		    entitytype: _chunkUpload.type,
		    entityid: _chunkUpload.id,
		    filehash: uniq,
		    fragmenthash: MD5(f0.join('|')),
		    fragment: flow,
		    merge: 0,
		    content: f0.join('|')
		};
		if (!is(flow, w - 1)) {
		    api(fragmentUrl, d);
                } else {
		    d.merge = 1;
		    api(fragmentUrl, d, function (e) {
			console.log(e);
                        e = JSON.parse(e);
                        if (e.response !== undefined) {
                            uploadCallback(e);
                        }
                    });
                }
            }
        }
    };
    reader.readAsArrayBuffer(file);
}
