/*!
 * vue-scroll-magnet v0.3.7
 * (c) 2017 Todd Beauchamp <toddbee@gmail.com>
 * 
 */
!function(t, e) {
    "object" == typeof exports && "object" == typeof module ? module.exports = e() : "function" == typeof define && define.amd ? define([], e) : "object" == typeof exports ? exports.VueScrollMagnet = e() : t.VueScrollMagnet = e()
}(this, function() {
    return function(t) {
        function e(i) {
            if (n[i])
                return n[i].exports;
            var o = n[i] = {
                exports: {},
                id: i,
                loaded: !1
            };
            return t[i].call(o.exports, o, o.exports, e),
            o.loaded = !0,
            o.exports
        }
        var n = {};
        return e.m = t,
        e.c = n,
        e.p = "",
        e(0)
    }([function(t, e, n) {
        "use strict";
        var i = n(1)
          , o = n(8);
        t.exports = {
            install: function(t) {
                t.component("scroll-magnet-container", i),
                t.component("scroll-magnet-item", o)
            },
            ScrollMagnetContainer: i,
            ScrollMagnetItem: o
        }
    }
    , function(t, e, n) {
        var i, o;
        n(2),
        i = n(6);
        var r = n(7);
        o = i = i || {},
        "object" != typeof i.default && "function" != typeof i.default || (o = i = i.default),
        "function" == typeof o && (o = o.options),
        o.render = r.render,
        o.staticRenderFns = r.staticRenderFns,
        o._scopeId = "data-v-d9583ad8",
        t.exports = i
    }
    , function(t, e, n) {
        var i = n(3);
        "string" == typeof i && (i = [[t.id, i, ""]]);
        n(5)(i, {});
        i.locals && (t.exports = i.locals)
    }
    , function(t, e, n) {
        e = t.exports = n(4)(),
        e.push([t.id, ".scroll-magnet-container[data-v-d9583ad8]{position:relative}", ""])
    }
    , function(t, e) {
        t.exports = function() {
            var t = [];
            return t.toString = function() {
                for (var t = [], e = 0; e < this.length; e++) {
                    var n = this[e];
                    n[2] ? t.push("@media " + n[2] + "{" + n[1] + "}") : t.push(n[1])
                }
                return t.join("")
            }
            ,
            t.i = function(e, n) {
                "string" == typeof e && (e = [[null, e, ""]]);
                for (var i = {}, o = 0; o < this.length; o++) {
                    var r = this[o][0];
                    "number" == typeof r && (i[r] = !0)
                }
                for (o = 0; o < e.length; o++) {
                    var s = e[o];
                    "number" == typeof s[0] && i[s[0]] || (n && !s[2] ? s[2] = n : n && (s[2] = "(" + s[2] + ") and (" + n + ")"),
                    t.push(s))
                }
            }
            ,
            t
        }
    }
    , function(t, e, n) {
        function i(t, e) {
            for (var n = 0; n < t.length; n++) {
                var i = t[n]
                  , o = u[i.id];
                if (o) {
                    o.refs++;
                    for (var r = 0; r < o.parts.length; r++)
                        o.parts[r](i.parts[r]);
                    for (; r < i.parts.length; r++)
                        o.parts.push(c(i.parts[r], e))
                } else {
                    for (var s = [], r = 0; r < i.parts.length; r++)
                        s.push(c(i.parts[r], e));
                    u[i.id] = {
                        id: i.id,
                        refs: 1,
                        parts: s
                    }
                }
            }
        }
        function o(t) {
            for (var e = [], n = {}, i = 0; i < t.length; i++) {
                var o = t[i]
                  , r = o[0]
                  , s = o[1]
                  , a = o[2]
                  , c = o[3]
                  , l = {
                    css: s,
                    media: a,
                    sourceMap: c
                };
                n[r] ? n[r].parts.push(l) : e.push(n[r] = {
                    id: r,
                    parts: [l]
                })
            }
            return e
        }
        function r(t, e) {
            var n = p()
              , i = m[m.length - 1];
            if ("top" === t.insertAt)
                i ? i.nextSibling ? n.insertBefore(e, i.nextSibling) : n.appendChild(e) : n.insertBefore(e, n.firstChild),
                m.push(e);
            else {
                if ("bottom" !== t.insertAt)
                    throw new Error("Invalid value for parameter 'insertAt'. Must be 'top' or 'bottom'.");
                n.appendChild(e)
            }
        }
        function s(t) {
            t.parentNode.removeChild(t);
            var e = m.indexOf(t);
            e >= 0 && m.splice(e, 1)
        }
        function a(t) {
            var e = document.createElement("style");
            return e.type = "text/css",
            r(t, e),
            e
        }
        function c(t, e) {
            var n, i, o;
            if (e.singleton) {
                var r = v++;
                n = g || (g = a(e)),
                i = l.bind(null, n, r, !1),
                o = l.bind(null, n, r, !0)
            } else
                n = a(e),
                i = d.bind(null, n),
                o = function() {
                    s(n)
                }
                ;
            return i(t),
            function(e) {
                if (e) {
                    if (e.css === t.css && e.media === t.media && e.sourceMap === t.sourceMap)
                        return;
                    i(t = e)
                } else
                    o()
            }
        }
        function l(t, e, n, i) {
            var o = n ? "" : i.css;
            if (t.styleSheet)
                t.styleSheet.cssText = w(e, o);
            else {
                var r = document.createTextNode(o)
                  , s = t.childNodes;
                s[e] && t.removeChild(s[e]),
                s.length ? t.insertBefore(r, s[e]) : t.appendChild(r)
            }
        }
        function d(t, e) {
            var n = e.css
              , i = e.media
              , o = e.sourceMap;
            if (i && t.setAttribute("media", i),
            o && (n += "\n/*# sourceURL=" + o.sources[0] + " */",
            n += "\n/*# sourceMappingURL=data:application/json;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(o)))) + " */"),
            t.styleSheet)
                t.styleSheet.cssText = n;
            else {
                for (; t.firstChild; )
                    t.removeChild(t.firstChild);
                t.appendChild(document.createTextNode(n))
            }
        }
        var u = {}
          , h = function(t) {
            var e;
            return function() {
                return "undefined" == typeof e && (e = t.apply(this, arguments)),
                e
            }
        }
          , f = h(function() {
            return /msie [6-9]\b/.test(window.navigator.userAgent.toLowerCase())
        })
          , p = h(function() {
            return document.head || document.getElementsByTagName("head")[0]
        })
          , g = null
          , v = 0
          , m = [];
        t.exports = function(t, e) {
            e = e || {},
            "undefined" == typeof e.singleton && (e.singleton = f()),
            "undefined" == typeof e.insertAt && (e.insertAt = "bottom");
            var n = o(t);
            return i(n, e),
            function(t) {
                for (var r = [], s = 0; s < n.length; s++) {
                    var a = n[s]
                      , c = u[a.id];
                    c.refs--,
                    r.push(c)
                }
                if (t) {
                    var l = o(t);
                    i(l, e)
                }
                for (var s = 0; s < r.length; s++) {
                    var c = r[s];
                    if (0 === c.refs) {
                        for (var d = 0; d < c.parts.length; d++)
                            c.parts[d]();
                        delete u[c.id]
                    }
                }
            }
        }
        ;
        var w = function() {
            var t = [];
            return function(e, n) {
                return t[e] = n,
                t.filter(Boolean).join("\n")
            }
        }()
    }
    , function(t, e) {
        "use strict";
        Object.defineProperty(e, "__esModule", {
            value: !0
        }),
        e.default = {
            name: "scroll-magnet-container",
            data: function() {
                return {
                    width: 0,
                    height: 0,
                    offsetTop: 0,
                    scrollTop: 0,
                    mutationObserver: null,
                    target: null
                }
            },
            props: {
                boundsElementSelector: {
                    type: String,
                    default: "",
                    required: !1
                }
            },
            created: function() {
                this.attachScroll(),
                this.attachResize()
            },
            mounted: function() {
                if (this.boundsElementSelector) {
                    var t = document.querySelector(this.boundsElementSelector);
                    t && (this.target = t)
                } else
                    this.target = this.$el.parentElement;
                this.getElementPosition(),
                this.attachMutationObserver()
            },
            beforeUpdate: function() {
                var t = this;
                this.$nextTick(function() {
                    t.getScrollPosition(),
                    t.getElementPosition({
                        recalcWidth: !0,
                        recalcHeight: !0
                    })
                })
            },
            destroyed: function() {
                this.detachScroll(),
                this.detachResize(),
                this.detachMutationObserver()
            },
            methods: {
                attachScroll: function() {
                    var t = this;
                    "undefined" != typeof window && (this.scrollListener = window.addEventListener("scroll", function() {
                        t.getScrollPosition(),
                        t.getElementPosition()
                    }));
                },
                detachScroll: function() {
                    "undefined" != typeof window && window.removeEventListener("scroll", this.scrollListener)
                },
                attachResize: function() {
                    var t = this;
                    "undefined" != typeof window && (this.resizeListener = window.addEventListener("resize", function() {
                        setTimeout(function() {
                            t.getScrollPosition(),
                            t.getElementPosition({
                                recalcWidth: !0,
                                recalcHeight: !0
                            })
                        }, 16)
                    }))
                },
                detachResize: function() {
                    "undefined" != typeof window && window.removeEventListener("resize", this.resizeListener)
                },
                attachMutationObserver: function() {
                    var t = this;
                    if ("undefined" != typeof window) {
                        var e = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;
                        if (e) {
                            var n = {
                                attributes: !0
                            };
                            this.mutationObserver = new e(function() {
                                t.getElementPosition({
                                    recalcWidth: !0,
                                    recalcHeight: !0
                                }),
                                t.scrollTop = t.getScrollY() + 1,
                                t.$nextTick(function() {
                                    t.scrollTop = t.getScrollY()
                                })
                            }
                            ),
                            this.target && this.mutationObserver.observe(this.target, n)
                        }
                    }
                },
                detachMutationObserver: function() {
                    this.mutationObserver && this.mutationObserver.disconnect()
                },
                getElementPosition: function(t) {
                    var e = t && t.recalcWidth || !1
                      , n = t && t.recalcHeight || !1;
                    this.offsetTop = this.$el.getBoundingClientRect().top + this.getScrollY(),
                    (!this.width > 0 || e) && (this.width = this.$el && this.$el.clientWidth || 0),
                    (!this.height > 0 || n) && (this.height = this.target && this.target.clientHeight || 0)
                },
                getScrollPosition: function() {
                    this.scrollTop = this.getScrollY(),
                    this.offsetTop = this.$el.getBoundingClientRect().top + this.getScrollY()
                },
                getScrollY: function() {
                    return "undefined" == typeof window ? 0 : window.scrollY || window.pageYOffset
                }
            }
        }
    }
    , function(t, e) {
        t.exports = {
            render: function() {
                var t = this
                  , e = t.$createElement
                  , n = t._self._c || e;
                return n("div", {
                    staticClass: "scroll-magnet-container",
                    style: "height: " + t.height + "px"
                }, [t._t("default")], 2)
            },
            staticRenderFns: []
        }
    }
    , function(t, e, n) {
        var i, o;
        n(9),
        i = n(11);
        var r = n(12);
        o = i = i || {},
        "object" != typeof i.default && "function" != typeof i.default || (o = i = i.default),
        "function" == typeof o && (o = o.options),
        o.render = r.render,
        o.staticRenderFns = r.staticRenderFns,
        o._scopeId = "data-v-1c578f50",
        t.exports = i
    }
    , function(t, e, n) {
        var i = n(10);
        "string" == typeof i && (i = [[t.id, i, ""]]);
        n(5)(i, {});
        i.locals && (t.exports = i.locals)
    }
    , function(t, e, n) {
        e = t.exports = n(4)(),
        e.push([t.id, ".scroll-magnet-item[data-v-1c578f50]{position:relative;width:100%}.is-scrolling[data-v-1c578f50]{position:fixed;top:0}.is-bottomed[data-v-1c578f50]{position:absolute;bottom:0}", ""])
    }
    , function(t, e) {
        "use strict";
        Object.defineProperty(e, "__esModule", {
            value: !0
        }),
        e.default = {
            name: "scroll-magnet-item",
            data: function() {
                return {
                    nearestContainer: void 0,
                    width: 0,
                    height: 0,
                    top: 0,
                    scrollDist: 0,
                    scrollEnd: 0,
                    isBottomed: !1,
                    isScrolling: !1
                }
            },
            props: {
                offsetTopPad: {
                    type: Number,
                    default: 0,
                    required: !1
                }
            },
            created: function() {
                this.nearestContainer = this.getNearestMagnetContainer()
            },
            mounted: function() {
                var t = this;
                this.setMagnetHeight(),
                this.$nextTick(function() {
                    t.setMagnetStatus(t.nearestContainer),
                    t.setMagnetWidth()
                }),
                this.$watch("nearestContainer.width", function() {
                    t.width = t.nearestContainer.width
                }),
                this.$watch("nearestContainer.scrollTop", function() {
                    t.setMagnetStatus(t.nearestContainer)
                })
            },
            methods: {
                getNearestMagnetContainer: function() {
                    return this.checkParentForMatch(this.$parent)
                },
                checkParentForMatch: function(t) {
                    return t && "scroll-magnet-container" === t.$options._componentTag ? t : this.checkParentForMatch(t)
                },
                setMagnetWidth: function() {
                    this.width = this.nearestContainer.width
                },
                setMagnetHeight: function() {
                    var t = this.$el.getBoundingClientRect();
                    this.height = t.height
                },
                setMagnetStatus: function(t) {
                    this.setMagnetHeight(),
                    this.scrollDist = t.scrollTop + this.height,
                    this.scrollEnd = t.offsetTop + (t.height - this.offsetTopPad),
                    this.isWithinHeight = this.scrollDist < this.scrollEnd,
                    this.isScrolling = t.scrollTop + this.offsetTopPad >= t.offsetTop && this.isWithinHeight,
                    this.isBottomed = this.scrollDist >= this.scrollEnd,
                    this.top = this.isBottomed ? "auto" : this.offsetTopPad + "px"
                }
            }
        }
    }
    , function(t, e) {
        t.exports = {
            render: function() {
                var t = this
                  , e = t.$createElement
                  , n = t._self._c || e;
                return n("div", {
                    staticClass: "scroll-magnet-item",
                    class: {
                        "is-scrolling": t.isScrolling,
                        "is-bottomed": t.isBottomed
                    },
                    style: "width: " + (t.width > 0 && t.width) + "px; top: " + t.top
                }, [t._t("default")], 2)
            },
            staticRenderFns: []
        }
    }
    ])
});
