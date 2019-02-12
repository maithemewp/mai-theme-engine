/**
 * Body scroll lock.
 *
 * @link https://github.com/willmcpo/body-scroll-lock
 *
 * @version 2.6.1
 */
!function(e,t){if("function"==typeof define&&define.amd)define(["exports"],t);else if("undefined"!=typeof exports)t(exports);else{var o={};t(o),e.bodyScrollLock=o}}(this,function(exports){"use strict";function r(e){if(Array.isArray(e)){for(var t=0,o=Array(e.length);t<e.length;t++)o[t]=e[t];return o}return Array.from(e)}Object.defineProperty(exports,"__esModule",{value:!0});var l=!1;if("undefined"!=typeof window){var e={get passive(){l=!0}};window.addEventListener("testPassive",null,e),window.removeEventListener("testPassive",null,e)}var d="undefined"!=typeof window&&window.navigator&&window.navigator.platform&&/iP(ad|hone|od)/.test(window.navigator.platform),c=[],u=!1,a=-1,s=void 0,v=void 0,f=function(t){return c.some(function(e){return!(!e.options.allowTouchMove||!e.options.allowTouchMove(t))})},m=function(e){var t=e||window.event;return!!f(t.target)||(1<t.touches.length||(t.preventDefault&&t.preventDefault(),!1))},o=function(){setTimeout(function(){void 0!==v&&(document.body.style.paddingRight=v,v=void 0),void 0!==s&&(document.body.style.overflow=s,s=void 0)})};exports.disableBodyScroll=function(i,e){if(d){if(!i)return void console.error("disableBodyScroll unsuccessful - targetElement must be provided when calling disableBodyScroll on IOS devices.");if(i&&!c.some(function(e){return e.targetElement===i})){var t={targetElement:i,options:e||{}};c=[].concat(r(c),[t]),i.ontouchstart=function(e){1===e.targetTouches.length&&(a=e.targetTouches[0].clientY)},i.ontouchmove=function(e){var t,o,n,r;1===e.targetTouches.length&&(o=i,r=(t=e).targetTouches[0].clientY-a,!f(t.target)&&(o&&0===o.scrollTop&&0<r?m(t):(n=o)&&n.scrollHeight-n.scrollTop<=n.clientHeight&&r<0?m(t):t.stopPropagation()))},u||(document.addEventListener("touchmove",m,l?{passive:!1}:void 0),u=!0)}}else{n=e,setTimeout(function(){if(void 0===v){var e=!!n&&!0===n.reserveScrollBarGap,t=window.innerWidth-document.documentElement.clientWidth;e&&0<t&&(v=document.body.style.paddingRight,document.body.style.paddingRight=t+"px")}void 0===s&&(s=document.body.style.overflow,document.body.style.overflow="hidden")});var o={targetElement:i,options:e||{}};c=[].concat(r(c),[o])}var n},exports.clearAllBodyScrollLocks=function(){d?(c.forEach(function(e){e.targetElement.ontouchstart=null,e.targetElement.ontouchmove=null}),u&&(document.removeEventListener("touchmove",m,l?{passive:!1}:void 0),u=!1),c=[],a=-1):(o(),c=[])},exports.enableBodyScroll=function(t){if(d){if(!t)return void console.error("enableBodyScroll unsuccessful - targetElement must be provided when calling enableBodyScroll on IOS devices.");t.ontouchstart=null,t.ontouchmove=null,c=c.filter(function(e){return e.targetElement!==t}),u&&0===c.length&&(document.removeEventListener("touchmove",m,l?{passive:!1}:void 0),u=!1)}else 1===c.length&&c[0].targetElement===t?(o(),c=[]):c=c.filter(function(e){return e.targetElement!==t})}});

/**
 * ScrollMagic (c) 2018 Jan Paepke (@janpaepke)
 *
 * @link http://scrollmagic.io
 *
 * @version 2.0.6
 */
!function(e,t){"function"==typeof define&&define.amd?define(t):"object"==typeof exports?module.exports=t():e.ScrollMagic=t()}(this,function(){"use strict";var e=function(){};e.version="2.0.6",window.addEventListener("mousewheel",function(){});var t="data-scrollmagic-pin-spacer";e.Controller=function(r){var o,s,a="ScrollMagic.Controller",l="FORWARD",c="REVERSE",f="PAUSED",u=n.defaults,d=this,h=i.extend({},u,r),g=[],p=!1,v=0,m=f,w=!0,y=0,S=!0,b=function(){for(var e in h)u.hasOwnProperty(e)||delete h[e];if(h.container=i.get.elements(h.container)[0],!h.container)throw a+" init failed.";w=h.container===window||h.container===document.body||!document.body.contains(h.container),w&&(h.container=window),y=z(),h.container.addEventListener("resize",T),h.container.addEventListener("scroll",T);var t=parseInt(h.refreshInterval,10);h.refreshInterval=i.type.Number(t)?t:u.refreshInterval,E()},E=function(){h.refreshInterval>0&&(s=window.setTimeout(A,h.refreshInterval))},x=function(){return h.vertical?i.get.scrollTop(h.container):i.get.scrollLeft(h.container)},z=function(){return h.vertical?i.get.height(h.container):i.get.width(h.container)},C=this._setScrollPos=function(e){h.vertical?w?window.scrollTo(i.get.scrollLeft(),e):h.container.scrollTop=e:w?window.scrollTo(e,i.get.scrollTop()):h.container.scrollLeft=e},F=function(){if(S&&p){var e=i.type.Array(p)?p:g.slice(0);p=!1;var t=v;v=d.scrollPos();var n=v-t;0!==n&&(m=n>0?l:c),m===c&&e.reverse(),e.forEach(function(e){e.update(!0)})}},L=function(){o=i.rAF(F)},T=function(e){"resize"==e.type&&(y=z(),m=f),p!==!0&&(p=!0,L())},A=function(){if(!w&&y!=z()){var e;try{e=new Event("resize",{bubbles:!1,cancelable:!1})}catch(t){e=document.createEvent("Event"),e.initEvent("resize",!1,!1)}h.container.dispatchEvent(e)}g.forEach(function(e){e.refresh()}),E()};this._options=h;var N=function(e){if(e.length<=1)return e;var t=e.slice(0);return t.sort(function(e,t){return e.scrollOffset()>t.scrollOffset()?1:-1}),t};return this.addScene=function(t){if(i.type.Array(t))t.forEach(function(e){d.addScene(e)});else if(t instanceof e.Scene)if(t.controller()!==d)t.addTo(d);else if(g.indexOf(t)<0){g.push(t),g=N(g),t.on("shift.controller_sort",function(){g=N(g)});for(var n in h.globalSceneOptions)t[n]&&t[n].call(t,h.globalSceneOptions[n])}return d},this.removeScene=function(e){if(i.type.Array(e))e.forEach(function(e){d.removeScene(e)});else{var t=g.indexOf(e);t>-1&&(e.off("shift.controller_sort"),g.splice(t,1),e.remove())}return d},this.updateScene=function(t,n){return i.type.Array(t)?t.forEach(function(e){d.updateScene(e,n)}):n?t.update(!0):p!==!0&&t instanceof e.Scene&&(p=p||[],-1==p.indexOf(t)&&p.push(t),p=N(p),L()),d},this.update=function(e){return T({type:"resize"}),e&&F(),d},this.scrollTo=function(n,r){if(i.type.Number(n))C.call(h.container,n,r);else if(n instanceof e.Scene)n.controller()===d&&d.scrollTo(n.scrollOffset(),r);else if(i.type.Function(n))C=n;else{var o=i.get.elements(n)[0];if(o){for(;o.parentNode.hasAttribute(t);)o=o.parentNode;var s=h.vertical?"top":"left",a=i.get.offset(h.container),l=i.get.offset(o);w||(a[s]-=d.scrollPos()),d.scrollTo(l[s]-a[s],r)}}return d},this.scrollPos=function(e){return arguments.length?(i.type.Function(e)&&(x=e),d):x.call(d)},this.info=function(e){var t={size:y,vertical:h.vertical,scrollPos:v,scrollDirection:m,container:h.container,isDocument:w};return arguments.length?void 0!==t[e]?t[e]:void 0:t},this.loglevel=function(){return d},this.enabled=function(e){return arguments.length?(S!=e&&(S=!!e,d.updateScene(g,!0)),d):S},this.destroy=function(e){window.clearTimeout(s);for(var t=g.length;t--;)g[t].destroy(e);return h.container.removeEventListener("resize",T),h.container.removeEventListener("scroll",T),i.cAF(o),null},b(),d};var n={defaults:{container:window,vertical:!0,globalSceneOptions:{},loglevel:2,refreshInterval:100}};e.Controller.addOption=function(e,t){n.defaults[e]=t},e.Controller.extend=function(t){var n=this;e.Controller=function(){return n.apply(this,arguments),this.$super=i.extend({},this),t.apply(this,arguments)||this},i.extend(e.Controller,n),e.Controller.prototype=n.prototype,e.Controller.prototype.constructor=e.Controller},e.Scene=function(n){var o,s,a="BEFORE",l="DURING",c="AFTER",f=r.defaults,u=this,d=i.extend({},f,n),h=a,g=0,p={start:0,end:0},v=0,m=!0,w=function(){for(var e in d)f.hasOwnProperty(e)||delete d[e];for(var t in f)L(t);C()},y={};this.on=function(e,t){return i.type.Function(t)&&(e=e.trim().split(" "),e.forEach(function(e){var n=e.split("."),r=n[0],i=n[1];"*"!=r&&(y[r]||(y[r]=[]),y[r].push({namespace:i||"",callback:t}))})),u},this.off=function(e,t){return e?(e=e.trim().split(" "),e.forEach(function(e){var n=e.split("."),r=n[0],i=n[1]||"",o="*"===r?Object.keys(y):[r];o.forEach(function(e){for(var n=y[e]||[],r=n.length;r--;){var o=n[r];!o||i!==o.namespace&&"*"!==i||t&&t!=o.callback||n.splice(r,1)}n.length||delete y[e]})}),u):u},this.trigger=function(t,n){if(t){var r=t.trim().split("."),i=r[0],o=r[1],s=y[i];s&&s.forEach(function(t){o&&o!==t.namespace||t.callback.call(u,new e.Event(i,t.namespace,u,n))})}return u},u.on("change.internal",function(e){"loglevel"!==e.what&&"tweenChanges"!==e.what&&("triggerElement"===e.what?E():"reverse"===e.what&&u.update())}).on("shift.internal",function(){S(),u.update()}),this.addTo=function(t){return t instanceof e.Controller&&s!=t&&(s&&s.removeScene(u),s=t,C(),b(!0),E(!0),S(),s.info("container").addEventListener("resize",x),t.addScene(u),u.trigger("add",{controller:s}),u.update()),u},this.enabled=function(e){return arguments.length?(m!=e&&(m=!!e,u.update(!0)),u):m},this.remove=function(){if(s){s.info("container").removeEventListener("resize",x);var e=s;s=void 0,e.removeScene(u),u.trigger("remove")}return u},this.destroy=function(e){return u.trigger("destroy",{reset:e}),u.remove(),u.off("*.*"),null},this.update=function(e){if(s)if(e)if(s.enabled()&&m){var t,n=s.info("scrollPos");t=d.duration>0?(n-p.start)/(p.end-p.start):n>=p.start?1:0,u.trigger("update",{startPos:p.start,endPos:p.end,scrollPos:n}),u.progress(t)}else T&&h===l&&N(!0);else s.updateScene(u,!1);return u},this.refresh=function(){return b(),E(),u},this.progress=function(e){if(arguments.length){var t=!1,n=h,r=s?s.info("scrollDirection"):"PAUSED",i=d.reverse||e>=g;if(0===d.duration?(t=g!=e,g=1>e&&i?0:1,h=0===g?a:l):0>e&&h!==a&&i?(g=0,h=a,t=!0):e>=0&&1>e&&i?(g=e,h=l,t=!0):e>=1&&h!==c?(g=1,h=c,t=!0):h!==l||i||N(),t){var o={progress:g,state:h,scrollDirection:r},f=h!=n,p=function(e){u.trigger(e,o)};f&&n!==l&&(p("enter"),p(n===a?"start":"end")),p("progress"),f&&h!==l&&(p(h===a?"start":"end"),p("leave"))}return u}return g};var S=function(){p={start:v+d.offset},s&&d.triggerElement&&(p.start-=s.info("size")*d.triggerHook),p.end=p.start+d.duration},b=function(e){if(o){var t="duration";F(t,o.call(u))&&!e&&(u.trigger("change",{what:t,newval:d[t]}),u.trigger("shift",{reason:t}))}},E=function(e){var n=0,r=d.triggerElement;if(s&&(r||v>0)){if(r)if(r.parentNode){for(var o=s.info(),a=i.get.offset(o.container),l=o.vertical?"top":"left";r.parentNode.hasAttribute(t);)r=r.parentNode;var c=i.get.offset(r);o.isDocument||(a[l]-=s.scrollPos()),n=c[l]-a[l]}else u.triggerElement(void 0);var f=n!=v;v=n,f&&!e&&u.trigger("shift",{reason:"triggerElementPosition"})}},x=function(){d.triggerHook>0&&u.trigger("shift",{reason:"containerResize"})},z=i.extend(r.validate,{duration:function(e){if(i.type.String(e)&&e.match(/^(\.|\d)*\d+%$/)){var t=parseFloat(e)/100;e=function(){return s?s.info("size")*t:0}}if(i.type.Function(e)){o=e;try{e=parseFloat(o())}catch(n){e=-1}}if(e=parseFloat(e),!i.type.Number(e)||0>e)throw o?(o=void 0,0):0;return e}}),C=function(e){e=arguments.length?[e]:Object.keys(z),e.forEach(function(e){var t;if(z[e])try{t=z[e](d[e])}catch(n){t=f[e]}finally{d[e]=t}})},F=function(e,t){var n=!1,r=d[e];return d[e]!=t&&(d[e]=t,C(e),n=r!=d[e]),n},L=function(e){u[e]||(u[e]=function(t){return arguments.length?("duration"===e&&(o=void 0),F(e,t)&&(u.trigger("change",{what:e,newval:d[e]}),r.shifts.indexOf(e)>-1&&u.trigger("shift",{reason:e})),u):d[e]})};this.controller=function(){return s},this.state=function(){return h},this.scrollOffset=function(){return p.start},this.triggerPosition=function(){var e=d.offset;return s&&(e+=d.triggerElement?v:s.info("size")*u.triggerHook()),e};var T,A;u.on("shift.internal",function(e){var t="duration"===e.reason;(h===c&&t||h===l&&0===d.duration)&&N(),t&&O()}).on("progress.internal",function(){N()}).on("add.internal",function(){O()}).on("destroy.internal",function(e){u.removePin(e.reset)});var N=function(e){if(T&&s){var t=s.info(),n=A.spacer.firstChild;if(e||h!==l){var r={position:A.inFlow?"relative":"absolute",top:0,left:0},o=i.css(n,"position")!=r.position;A.pushFollowers?d.duration>0&&(h===c&&0===parseFloat(i.css(A.spacer,"padding-top"))?o=!0:h===a&&0===parseFloat(i.css(A.spacer,"padding-bottom"))&&(o=!0)):r[t.vertical?"top":"left"]=d.duration*g,i.css(n,r),o&&O()}else{"fixed"!=i.css(n,"position")&&(i.css(n,{position:"fixed"}),O());var f=i.get.offset(A.spacer,!0),u=d.reverse||0===d.duration?t.scrollPos-p.start:Math.round(g*d.duration*10)/10;f[t.vertical?"top":"left"]+=u,i.css(A.spacer.firstChild,{top:f.top,left:f.left})}}},O=function(){if(T&&s&&A.inFlow){var e=h===l,t=s.info("vertical"),n=A.spacer.firstChild,r=i.isMarginCollapseType(i.css(A.spacer,"display")),o={};A.relSize.width||A.relSize.autoFullWidth?e?i.css(T,{width:i.get.width(A.spacer)}):i.css(T,{width:"100%"}):(o["min-width"]=i.get.width(t?T:n,!0,!0),o.width=e?o["min-width"]:"auto"),A.relSize.height?e?i.css(T,{height:i.get.height(A.spacer)-(A.pushFollowers?d.duration:0)}):i.css(T,{height:"100%"}):(o["min-height"]=i.get.height(t?n:T,!0,!r),o.height=e?o["min-height"]:"auto"),A.pushFollowers&&(o["padding"+(t?"Top":"Left")]=d.duration*g,o["padding"+(t?"Bottom":"Right")]=d.duration*(1-g)),i.css(A.spacer,o)}},_=function(){s&&T&&h===l&&!s.info("isDocument")&&N()},P=function(){s&&T&&h===l&&((A.relSize.width||A.relSize.autoFullWidth)&&i.get.width(window)!=i.get.width(A.spacer.parentNode)||A.relSize.height&&i.get.height(window)!=i.get.height(A.spacer.parentNode))&&O()},D=function(e){s&&T&&h===l&&!s.info("isDocument")&&(e.preventDefault(),s._setScrollPos(s.info("scrollPos")-((e.wheelDelta||e[s.info("vertical")?"wheelDeltaY":"wheelDeltaX"])/3||30*-e.detail)))};this.setPin=function(e,n){var r={pushFollowers:!0,spacerClass:"scrollmagic-pin-spacer"};if(n=i.extend({},r,n),e=i.get.elements(e)[0],!e)return u;if("fixed"===i.css(e,"position"))return u;if(T){if(T===e)return u;u.removePin()}T=e;var o=T.parentNode.style.display,s=["top","left","bottom","right","margin","marginLeft","marginRight","marginTop","marginBottom"];T.parentNode.style.display="none";var a="absolute"!=i.css(T,"position"),l=i.css(T,s.concat(["display"])),c=i.css(T,["width","height"]);T.parentNode.style.display=o,!a&&n.pushFollowers&&(n.pushFollowers=!1);var f=T.parentNode.insertBefore(document.createElement("div"),T),d=i.extend(l,{position:a?"relative":"absolute",boxSizing:"content-box",mozBoxSizing:"content-box",webkitBoxSizing:"content-box"});if(a||i.extend(d,i.css(T,["width","height"])),i.css(f,d),f.setAttribute(t,""),i.addClass(f,n.spacerClass),A={spacer:f,relSize:{width:"%"===c.width.slice(-1),height:"%"===c.height.slice(-1),autoFullWidth:"auto"===c.width&&a&&i.isMarginCollapseType(l.display)},pushFollowers:n.pushFollowers,inFlow:a},!T.___origStyle){T.___origStyle={};var h=T.style,g=s.concat(["width","height","position","boxSizing","mozBoxSizing","webkitBoxSizing"]);g.forEach(function(e){T.___origStyle[e]=h[e]||""})}return A.relSize.width&&i.css(f,{width:c.width}),A.relSize.height&&i.css(f,{height:c.height}),f.appendChild(T),i.css(T,{position:a?"relative":"absolute",margin:"auto",top:"auto",left:"auto",bottom:"auto",right:"auto"}),(A.relSize.width||A.relSize.autoFullWidth)&&i.css(T,{boxSizing:"border-box",mozBoxSizing:"border-box",webkitBoxSizing:"border-box"}),window.addEventListener("scroll",_),window.addEventListener("resize",_),window.addEventListener("resize",P),T.addEventListener("mousewheel",D),T.addEventListener("DOMMouseScroll",D),N(),u},this.removePin=function(e){if(T){if(h===l&&N(!0),e||!s){var n=A.spacer.firstChild;if(n.hasAttribute(t)){var r=A.spacer.style,o=["margin","marginLeft","marginRight","marginTop","marginBottom"],a={};o.forEach(function(e){a[e]=r[e]||""}),i.css(n,a)}A.spacer.parentNode.insertBefore(n,A.spacer),A.spacer.parentNode.removeChild(A.spacer),T.parentNode.hasAttribute(t)||(i.css(T,T.___origStyle),delete T.___origStyle)}window.removeEventListener("scroll",_),window.removeEventListener("resize",_),window.removeEventListener("resize",P),T.removeEventListener("mousewheel",D),T.removeEventListener("DOMMouseScroll",D),T=void 0}return u};var R,k=[];return u.on("destroy.internal",function(e){u.removeClassToggle(e.reset)}),this.setClassToggle=function(e,t){var n=i.get.elements(e);return 0!==n.length&&i.type.String(t)?(k.length>0&&u.removeClassToggle(),R=t,k=n,u.on("enter.internal_class leave.internal_class",function(e){var t="enter"===e.type?i.addClass:i.removeClass;k.forEach(function(e){t(e,R)})}),u):u},this.removeClassToggle=function(e){return e&&k.forEach(function(e){i.removeClass(e,R)}),u.off("start.internal_class end.internal_class"),R=void 0,k=[],u},w(),u};var r={defaults:{duration:0,offset:0,triggerElement:void 0,triggerHook:.5,reverse:!0,loglevel:2},validate:{offset:function(e){if(e=parseFloat(e),!i.type.Number(e))throw 0;return e},triggerElement:function(e){if(e=e||void 0){var t=i.get.elements(e)[0];if(!t||!t.parentNode)throw 0;e=t}return e},triggerHook:function(e){var t={onCenter:.5,onEnter:1,onLeave:0};if(i.type.Number(e))e=Math.max(0,Math.min(parseFloat(e),1));else{if(!(e in t))throw 0;e=t[e]}return e},reverse:function(e){return!!e}},shifts:["duration","offset","triggerHook"]};e.Scene.addOption=function(e,t,n,i){e in r.defaults||(r.defaults[e]=t,r.validate[e]=n,i&&r.shifts.push(e))},e.Scene.extend=function(t){var n=this;e.Scene=function(){return n.apply(this,arguments),this.$super=i.extend({},this),t.apply(this,arguments)||this},i.extend(e.Scene,n),e.Scene.prototype=n.prototype,e.Scene.prototype.constructor=e.Scene},e.Event=function(e,t,n,r){r=r||{};for(var i in r)this[i]=r[i];return this.type=e,this.target=this.currentTarget=n,this.namespace=t||"",this.timeStamp=this.timestamp=Date.now(),this};var i=e._util=function(e){var t,n={},r=function(e){return parseFloat(e)||0},i=function(t){return t.currentStyle?t.currentStyle:e.getComputedStyle(t)},o=function(t,n,o,s){if(n=n===document?e:n,n===e)s=!1;else if(!u.DomElement(n))return 0;t=t.charAt(0).toUpperCase()+t.substr(1).toLowerCase();var a=(o?n["offset"+t]||n["outer"+t]:n["client"+t]||n["inner"+t])||0;if(o&&s){var l=i(n);a+="Height"===t?r(l.marginTop)+r(l.marginBottom):r(l.marginLeft)+r(l.marginRight)}return a},s=function(e){return e.replace(/^[^a-z]+([a-z])/g,"$1").replace(/-([a-z])/g,function(e){return e[1].toUpperCase()})};n.extend=function(e){for(e=e||{},t=1;t<arguments.length;t++)if(arguments[t])for(var n in arguments[t])arguments[t].hasOwnProperty(n)&&(e[n]=arguments[t][n]);return e},n.isMarginCollapseType=function(e){return["block","flex","list-item","table","-webkit-box"].indexOf(e)>-1};var a=0,l=["ms","moz","webkit","o"],c=e.requestAnimationFrame,f=e.cancelAnimationFrame;for(t=0;!c&&t<l.length;++t)c=e[l[t]+"RequestAnimationFrame"],f=e[l[t]+"CancelAnimationFrame"]||e[l[t]+"CancelRequestAnimationFrame"];c||(c=function(t){var n=(new Date).getTime(),r=Math.max(0,16-(n-a)),i=e.setTimeout(function(){t(n+r)},r);return a=n+r,i}),f||(f=function(t){e.clearTimeout(t)}),n.rAF=c.bind(e),n.cAF=f.bind(e);var u=n.type=function(e){return Object.prototype.toString.call(e).replace(/^\[object (.+)\]$/,"$1").toLowerCase()};u.String=function(e){return"string"===u(e)},u.Function=function(e){return"function"===u(e)},u.Array=function(e){return Array.isArray(e)},u.Number=function(e){return!u.Array(e)&&e-parseFloat(e)+1>=0},u.DomElement=function(e){return"object"==typeof HTMLElement?e instanceof HTMLElement:e&&"object"==typeof e&&null!==e&&1===e.nodeType&&"string"==typeof e.nodeName};var d=n.get={};return d.elements=function(t){var n=[];if(u.String(t))try{t=document.querySelectorAll(t)}catch(r){return n}if("nodelist"===u(t)||u.Array(t))for(var i=0,o=n.length=t.length;o>i;i++){var s=t[i];n[i]=u.DomElement(s)?s:d.elements(s)}else(u.DomElement(t)||t===document||t===e)&&(n=[t]);return n},d.scrollTop=function(t){return t&&"number"==typeof t.scrollTop?t.scrollTop:e.pageYOffset||0},d.scrollLeft=function(t){return t&&"number"==typeof t.scrollLeft?t.scrollLeft:e.pageXOffset||0},d.width=function(e,t,n){return o("width",e,t,n)},d.height=function(e,t,n){return o("height",e,t,n)},d.offset=function(e,t){var n={top:0,left:0};if(e&&e.getBoundingClientRect){var r=e.getBoundingClientRect();n.top=r.top,n.left=r.left,t||(n.top+=d.scrollTop(),n.left+=d.scrollLeft())}return n},n.addClass=function(e,t){t&&(e.classList?e.classList.add(t):e.className+=" "+t)},n.removeClass=function(e,t){t&&(e.classList?e.classList.remove(t):e.className=e.className.replace(RegExp("(^|\\b)"+t.split(" ").join("|")+"(\\b|$)","gi")," "))},n.css=function(e,t){if(u.String(t))return i(e)[s(t)];if(u.Array(t)){var n={},r=i(e);return t.forEach(function(e){n[e]=r[s(e)]}),n}for(var o in t){var a=t[o];a==parseFloat(a)&&(a+="px"),e.style[s(o)]=a}},n}(window||{});return e});

/*! ScrollMagic v2.0.6 | (c) 2018 Jan Paepke (@janpaepke) | license & info: http://scrollmagic.io */
/* https://raw.githubusercontent.com/janpaepke/ScrollMagic/master/scrollmagic/minified/plugins/debug.addIndicators.min.js */
// !function(e,r){"function"==typeof define&&define.amd?define(["ScrollMagic"],r):r("object"==typeof exports?require("scrollmagic"):e.ScrollMagic||e.jQuery&&e.jQuery.ScrollMagic)}(this,function(e){"use strict";var r="0.85em",t="9999",i=15,o=e._util,n=0;e.Scene.extend(function(){var e,r=this;r.addIndicators=function(t){if(!e){var i={name:"",indent:0,parent:void 0,colorStart:"green",colorEnd:"red",colorTrigger:"blue"};t=o.extend({},i,t),n++,e=new s(r,t),r.on("add.plugin_addIndicators",e.add),r.on("remove.plugin_addIndicators",e.remove),r.on("destroy.plugin_addIndicators",r.removeIndicators),r.controller()&&e.add()}return r},r.removeIndicators=function(){return e&&(e.remove(),this.off("*.plugin_addIndicators"),e=void 0),r}}),e.Controller.addOption("addIndicators",!1),e.Controller.extend(function(){var r=this,t=r.info(),n=t.container,s=t.isDocument,d=t.vertical,a={groups:[]};this._indicators=a;var g=function(){a.updateBoundsPositions()},p=function(){a.updateTriggerGroupPositions()};return n.addEventListener("resize",p),s||(window.addEventListener("resize",p),window.addEventListener("scroll",p)),n.addEventListener("resize",g),n.addEventListener("scroll",g),this._indicators.updateBoundsPositions=function(e){for(var r,t,s,g=e?[o.extend({},e.triggerGroup,{members:[e]})]:a.groups,p=g.length,u={},c=d?"left":"top",l=d?"width":"height",f=d?o.get.scrollLeft(n)+o.get.width(n)-i:o.get.scrollTop(n)+o.get.height(n)-i;p--;)for(s=g[p],r=s.members.length,t=o.get[l](s.element.firstChild);r--;)u[c]=f-t,o.css(s.members[r].bounds,u)},this._indicators.updateTriggerGroupPositions=function(e){for(var t,g,p,u,c,l=e?[e]:a.groups,f=l.length,m=s?document.body:n,h=s?{top:0,left:0}:o.get.offset(m,!0),v=d?o.get.width(n)-i:o.get.height(n)-i,b=d?"width":"height",G=d?"Y":"X";f--;)t=l[f],g=t.element,p=t.triggerHook*r.info("size"),u=o.get[b](g.firstChild.firstChild),c=p>u?"translate"+G+"(-100%)":"",o.css(g,{top:h.top+(d?p:v-t.members[0].options.indent),left:h.left+(d?v-t.members[0].options.indent:p)}),o.css(g.firstChild.firstChild,{"-ms-transform":c,"-webkit-transform":c,transform:c})},this._indicators.updateTriggerGroupLabel=function(e){var r="trigger"+(e.members.length>1?"":" "+e.members[0].options.name),t=e.element.firstChild.firstChild,i=t.textContent!==r;i&&(t.textContent=r,d&&a.updateBoundsPositions())},this.addScene=function(t){this._options.addIndicators&&t instanceof e.Scene&&t.controller()===r&&t.addIndicators(),this.$super.addScene.apply(this,arguments)},this.destroy=function(){n.removeEventListener("resize",p),s||(window.removeEventListener("resize",p),window.removeEventListener("scroll",p)),n.removeEventListener("resize",g),n.removeEventListener("scroll",g),this.$super.destroy.apply(this,arguments)},r});var s=function(e,r){var t,i,s=this,a=d.bounds(),g=d.start(r.colorStart),p=d.end(r.colorEnd),u=r.parent&&o.get.elements(r.parent)[0];r.name=r.name||n,g.firstChild.textContent+=" "+r.name,p.textContent+=" "+r.name,a.appendChild(g),a.appendChild(p),s.options=r,s.bounds=a,s.triggerGroup=void 0,this.add=function(){i=e.controller(),t=i.info("vertical");var r=i.info("isDocument");u||(u=r?document.body:i.info("container")),r||"static"!==o.css(u,"position")||o.css(u,{position:"relative"}),e.on("change.plugin_addIndicators",l),e.on("shift.plugin_addIndicators",c),G(),h(),setTimeout(function(){i._indicators.updateBoundsPositions(s)},0)},this.remove=function(){if(s.triggerGroup){if(e.off("change.plugin_addIndicators",l),e.off("shift.plugin_addIndicators",c),s.triggerGroup.members.length>1){var r=s.triggerGroup;r.members.splice(r.members.indexOf(s),1),i._indicators.updateTriggerGroupLabel(r),i._indicators.updateTriggerGroupPositions(r),s.triggerGroup=void 0}else b();m()}};var c=function(){h()},l=function(e){"triggerHook"===e.what&&G()},f=function(){var e=i.info("vertical");o.css(g.firstChild,{"border-bottom-width":e?1:0,"border-right-width":e?0:1,bottom:e?-1:r.indent,right:e?r.indent:-1,padding:e?"0 8px":"2px 4px"}),o.css(p,{"border-top-width":e?1:0,"border-left-width":e?0:1,top:e?"100%":"",right:e?r.indent:"",bottom:e?"":r.indent,left:e?"":"100%",padding:e?"0 8px":"2px 4px"}),u.appendChild(a)},m=function(){a.parentNode.removeChild(a)},h=function(){a.parentNode!==u&&f();var r={};r[t?"top":"left"]=e.triggerPosition(),r[t?"height":"width"]=e.duration(),o.css(a,r),o.css(p,{display:e.duration()>0?"":"none"})},v=function(){var n=d.trigger(r.colorTrigger),a={};a[t?"right":"bottom"]=0,a[t?"border-top-width":"border-left-width"]=1,o.css(n.firstChild,a),o.css(n.firstChild.firstChild,{padding:t?"0 8px 3px 8px":"3px 4px"}),document.body.appendChild(n);var g={triggerHook:e.triggerHook(),element:n,members:[s]};i._indicators.groups.push(g),s.triggerGroup=g,i._indicators.updateTriggerGroupLabel(g),i._indicators.updateTriggerGroupPositions(g)},b=function(){i._indicators.groups.splice(i._indicators.groups.indexOf(s.triggerGroup),1),s.triggerGroup.element.parentNode.removeChild(s.triggerGroup.element),s.triggerGroup=void 0},G=function(){var r=e.triggerHook(),t=1e-4;if(!(s.triggerGroup&&Math.abs(s.triggerGroup.triggerHook-r)<t)){for(var o,n=i._indicators.groups,d=n.length;d--;)if(o=n[d],Math.abs(o.triggerHook-r)<t)return s.triggerGroup&&(1===s.triggerGroup.members.length?b():(s.triggerGroup.members.splice(s.triggerGroup.members.indexOf(s),1),i._indicators.updateTriggerGroupLabel(s.triggerGroup),i._indicators.updateTriggerGroupPositions(s.triggerGroup))),o.members.push(s),s.triggerGroup=o,void i._indicators.updateTriggerGroupLabel(o);if(s.triggerGroup){if(1===s.triggerGroup.members.length)return s.triggerGroup.triggerHook=r,void i._indicators.updateTriggerGroupPositions(s.triggerGroup);s.triggerGroup.members.splice(s.triggerGroup.members.indexOf(s),1),i._indicators.updateTriggerGroupLabel(s.triggerGroup),i._indicators.updateTriggerGroupPositions(s.triggerGroup),s.triggerGroup=void 0}v()}}},d={start:function(e){var r=document.createElement("div");r.textContent="start",o.css(r,{position:"absolute",overflow:"visible","border-width":0,"border-style":"solid",color:e,"border-color":e});var t=document.createElement("div");return o.css(t,{position:"absolute",overflow:"visible",width:0,height:0}),t.appendChild(r),t},end:function(e){var r=document.createElement("div");return r.textContent="end",o.css(r,{position:"absolute",overflow:"visible","border-width":0,"border-style":"solid",color:e,"border-color":e}),r},bounds:function(){var e=document.createElement("div");return o.css(e,{position:"absolute",overflow:"visible","white-space":"nowrap","pointer-events":"none","font-size":r}),e.style.zIndex=t,e},trigger:function(e){var i=document.createElement("div");i.textContent="trigger",o.css(i,{position:"relative"});var n=document.createElement("div");o.css(n,{position:"absolute",overflow:"visible","border-width":0,"border-style":"solid",color:e,"border-color":e}),n.appendChild(i);var s=document.createElement("div");return o.css(s,{position:"fixed",overflow:"visible","white-space":"nowrap","pointer-events":"none","font-size":r}),s.style.zIndex=t,s.appendChild(n),s}}});

/**
 * Header shrink and reveal.
 * Everything here rebuilt with ScrollMagic for v1.8.0.
 *
 * @since    1.4.0
 * @since    1.8.0
 */
( function( document, $, undefined ) {

	var $window   = $(window);
	var $html     = $( 'html' );
	var $body     = $( 'body' );
	var $header   = $( '.site-header' );
	var $logoLink = $( '.custom-logo-link' );
	var hasShrink = ( $logoLink.length > 0 ) && $body.hasClass( 'has-shrink-header' );
	var	hasReveal = $body.hasClass( 'has-reveal-header' );

	// Setup ScrollMagic controller.
	var controller = new ScrollMagic.Controller();

	// Scroll class.
	var scrollScene = new ScrollMagic.Scene({
		triggerElement: '#header-trigger',
		triggerHook: 0,
		offset: - parseInt( $html.css( 'marginTop' ) ), // Start when .site-header hits top, accounting for admin-bar.
		duration: '2',
	})
	.on( 'enter', function(e) {
		$body.removeClass( 'scroll' );
		// Resets when fast jumps to top of window.
		if ( hasShrink ) {
			$logoLink.stop(true,false).css({
				'maxWidth' : '',
				'marginTop' : '',
				'marginBottom' : '',
			});
		}
		if ( hasReveal && $header.hasClass( 'conceal-header' ) ) {
			$header.addClass( 'reveal-header' ).removeClass( 'conceal-header' );
		}
	})
	.on( 'leave', function(e) {
		$body.addClass( 'scroll' );
	})
	// .addIndicators()
	.addTo(controller);

	// Bail if nothing we need.
	if ( ! ( hasShrink || hasReveal ) ) {
		return;
	}

	// .2 and .8 must equal 1. This will return a value incremented from 100% down to 80% of the initialValue.
	// var newValue = ( .2 + ( .8 * ( 1 - e.progress ) ) ) * initialValue;

	// This will progress a value incremended from an initialValue to an endValue.
	// var newValue = initialValue - ( ( initialValue - endValue ) * e.progress )

	if ( hasShrink ) {

		var shrinkDirections = [];
		var shrinkProgresses = [];
		var logoWidth        = maiVars.logoWidth ? maiVars.logoWidth : $logoLink.outerWidth();
		var shrunkLogoWidth  = Math.round( logoWidth * .7 );
		var logoMarginTop    = parseInt( $logoLink.css( 'marginTop' ) );
		var logoMarginBot    = parseInt( $logoLink.css( 'marginBottom' ) );
		var currentLogoWidth = logoWidth;
		var currentMarginTop = logoMarginTop;
		var currentMarginBot = logoMarginBot;

		// Shrink Header/Logo.
		var logoScene = new ScrollMagic.Scene({
			triggerElement: '#header-trigger',
			triggerHook: 0,
			offset: - parseInt( $html.css( 'marginTop' ) ), // Start when .site-header hits top, accounting for admin-bar.
			duration: '240',
		})
		.on( 'progress', function(e) {

			// Bail if already small window size. CSS will already be shrunk.
			if ( isSmallWindow() ) {
				return;
			}

			// Store our values for comparing later.
			shrinkDirections = storeItem( shrinkDirections, e.scrollDirection, 2 );
			shrinkProgresses = storeItem( shrinkProgresses, e.progress, 2 );

			// Adjust logo and bail on first scroll.
			if ( 1 === shrinkDirections.length ) {
				adjustLogo( true, e.progress );
				return;
			}

			// Bail if not scrolling the same direction.
			if ( ! sameItems( shrinkDirections ) ) {
				return;
			}

			// Adjust logo.
			adjustLogo( e.scrollDirection, e.progress );
		})
		// .addIndicators()
		.addTo(controller);

		// Update window size and logo width/margin if browser on resize or similar shift.
		logoScene.on( 'shift', function(e) {
			// Reset styles.
			$logoLink.css({
				'maxWidth' : '',
				'marginTop' : '',
				'marginBottom' : '',
			});
			// Set the new margins.
			logoMarginTop = parseInt( $logoLink.css( 'marginTop' ) );
			logoMarginBot = parseInt( $logoLink.css( 'marginBottom' ) );
			if ( isSmallWindow() ) {
				return;
			}
			adjustLogo( true, logoScene.progress() );
			logoScene.refresh();
		});

		// Adust the logo CSS.
		function adjustLogo( scrollDirection, progress ) {

			currentLogoWidth = $logoLink.outerWidth();
			currentMarginTop = parseInt( $logoLink.css( 'marginTop' ) );
			currentMarginBot = parseInt( $logoLink.css( 'marginBottom' ) );

			var newLogoWidth = Math.round( logoWidth - ( ( logoWidth - shrunkLogoWidth ) * progress ) );
			var newMarginTop = Math.round( ( .2 + ( .8 * ( 1 - progress ) ) ) * logoMarginTop );
			var newMarginBot = Math.round( ( .2 + ( .8 * ( 1 - progress ) ) ) * logoMarginBot );
			var doLogoWidth  = shouldAdjust( scrollDirection, currentLogoWidth, newLogoWidth );
			var doMarginTop  = shouldAdjust( scrollDirection, currentMarginTop, newMarginTop );
			var doMarginBot  = shouldAdjust( scrollDirection, currentMarginBot, newMarginBot );

			if ( doLogoWidth || doMarginTop || doMarginBot ) {
				var $css = {};
				if ( doLogoWidth ) {
					$css = $.extend( $css, { maxWidth: + newLogoWidth + 'px' });
				}
				if ( doMarginTop ) {
					$css = $.extend( $css, { marginTop: + newMarginTop + 'px' });
				}
				if ( doMarginBot ) {
					$css = $.extend( $css, { marginBottom: + newMarginBot + 'px' });
				}
				$logoLink.stop(true,true).css( $css );
			}
		}

		// Check if the element should be adjusted via CSS.
		function shouldAdjust( scrollDirection, currentValue, newValue ) {
			if ( true === scrollDirection ) {
				return true;
			}
			return ( ( 'FORWARD' === scrollDirection ) && ( newValue < currentValue ) ) || ( ( 'REVERSE' === scrollDirection ) && ( newValue > currentValue ) );
		}

	}

	if ( hasReveal ) {

		var revealDirections = [];
		// var  = [];
		var revealed         = true; // Header should start showing.
		var concealed        = false;
		var fwdScrollTop     = false;
		var rvsScrollTop     = false;

		// Reveal Header.
		var revealScene = new ScrollMagic.Scene({
			triggerElement: '#header-trigger',
			triggerHook: 0,
			offset: $header.outerHeight(),
			duration: $body.outerHeight(),
		})
		.on( 'progress', function(e) {

			// Bail if mobile menu is activated.
			if ( $body.hasClass( 'mai-menu-activated' ) ) {
				return;
			}

			revealDirections = storeItem( revealDirections, e.scrollDirection, 3 );

			// Reset when changing directions.
			if ( ! sameItems( revealDirections ) ) {
				fwdScrollTop = false;
				rvsScrollTop = false;
			}
			if ( 'FORWARD' === e.scrollDirection ) {
				// Already concealed and still scrolling down.
				if ( concealed ) {
					return;
				}
				// First time scrolling down.
				if ( ! fwdScrollTop ) {
					fwdScrollTop = $window.scrollTop();
				}
				// Scrolling down at least 120px.
				if ( ( $window.scrollTop() - fwdScrollTop ) >= 120 ) {
					$header.addClass( 'conceal-header' ).removeClass( 'reveal-header' );
					concealed    = true;
					revealed     = false;
					rvsScrollTop = false;
				}
			} else if ( 'REVERSE' === e.scrollDirection ) {
				// Already revealed and still scrolling up.
				if ( revealed ) {
					return;
				}
				// First time scxrolling up.
				if ( ! rvsScrollTop ) {
					rvsScrollTop = $window.scrollTop();
				}
				// Scrolling up at least 120px.
				if ( ( rvsScrollTop - $window.scrollTop() ) >= 120 ) {
					$header.addClass( 'reveal-header' ).removeClass( 'conceal-header' );
					revealed     = true;
					concealed    = false;
					fwdScrollTop = false;
				}
			}
		})
		// .addIndicators()
		.addTo(controller);

	}

	// Store items in an array.
	function storeItem( existingItems, newItem, total ) {
		// If we have more than the total.
		if ( existingItems.length > total ) {
			// Set the tempTotal items to 1 less than the actual total.
			var tempTotal = (total - 1);
			existingItems = existingItems.slice(-tempTotal);
		}
		// Add new item to this array.
		existingItems.push( newItem );
		return existingItems;
	}

	// Check if progress has incremented in the same direction.
	function sameItems( currentDirections ) {
		return currentDirections.every( function( v, i, a ) {
			return i === 0 || v === a[i - 1];
		});
	}

	// Check if current window is 768px or smaller.
	function isSmallWindow() {
		return window.matchMedia( '(max-width: 768px)' ).matches;
	}

})( document, jQuery );


/**
 * This script adds the accessibility-ready responsive menu.
 * Loosely off https://github.com/copyblogger/responsive-menus.
 *
 * Props @robincornett for some help/code.
 *
 * @version  2.0.0
 */
( function( window, document, $, undefined ) {

	var $maiMenu = $( '.mai-menu' );

	// Bail if no menu.
	if ( ! $maiMenu.length ) {
		return;
	}

	_maiGlobalFunctions();

	// Build toggle buttons.
	var $maiToggle = $( '<button />', {
			'id' : 'mai-toggle',
			'class' : 'mai-toggle',
			'aria-expanded' : false,
			'aria-pressed' : false,
			'role' : 'button'
		}).append( '<span class="screen-reader-text">' + maiVars.mainMenu + '</span><span class="mai-bars"></span></span>' );

	var $maiSubToggle = $( '<button />', {
			'class' : 'sub-menu-toggle',
			'aria-expanded' : false,
			'aria-pressed' : false,
			'role' : 'button'
		}).append( '<span class="screen-reader-text">' + maiVars.subMenu + '</span>' );

	// Set vars.
	var $window        = $(window),
		$body          = $( 'body' ),
		$header        = $( '.site-header' ),
		$headerRow     = $( '.site-header-row' ),
		$maiMenus      = $( '.mai-menu .menu' ),
		$maiSubToggles = $( '.mai-menu .sub-menu-toggle' ),
		$maiSubMenus   = $( '.mai-menu .sub-menu' );

	// Get a target element that you want to persist scrolling for (such as a modal/lightbox/flyout/nav).
	var bodyLockElement = document.querySelector( '#mai-menu' );

	// Add the main nav and sub-menu toggle button.
	_addMenuButtons();

	// Remove classes that may unintentially inherit styling.
	$maiMenus.removeClass( 'nav-header nav-primary nav-secondary' );

	// Toggle triggers.
	$header.on( 'click', '.mai-toggle', _doToggleMenu );
	$maiMenu.on( 'click', '.sub-menu-toggle:not(.sub-sub-menu-toggle)', _doToggleSubMenu );
	$maiMenu.on( 'click', '.sub-menu-toggle.sub-sub-menu-toggle', _doToggleSubSubMenu );
	$maiMenu.on( 'click', 'a[href]', _doAnchorLinkClicked );

	// Resize.
	$window.on( 'load resize orientationchange', function(e) {
		_maybeCloseAll();
		_changeSkipLink();
	});

	/**
	 * Add toggle buttons.
	 */
	function _addMenuButtons() {

		// Add the main mobile nav toggle.
		$headerRow.append( $maiToggle );

		// Bail if no menus in the mobile menu. It could just be widget content.
		if ( 0 == $maiMenus.length ) {
			return;
		}

		// Add the responsive menu class to the menus.
		$.each( $maiMenus, function(e) {
			$(this).addClass( 'mobile-menu' );
		});

		// Add the submenu toggles.
		$maiSubMenus.before( $maiSubToggle );

		$( '.sub-menu .sub-menu-toggle' ).addClass( 'sub-sub-menu-toggle' );
	}

	/**
	 * Action to happen when the main menu button is clicked.
	 */
	function _doToggleMenu() {

		var $this       = $(this),
			hasSideMenu = $body.hasClass( 'has-side-menu' );

		// Remove reveal/conceal classes cause they cause animation issues.
		$header.removeClass( 'conceal-header reveal-header' );

		// Toggle the mobile menu activated.
		$this._toggleActive();

		// Activated body class.
		$body.toggleClass( 'mai-menu-activated' );

		// If we have a side menu.
		if ( hasSideMenu ) {
			// Side menu activated class.
			$body.toggleClass( 'mai-side-menu-activated' );
		}
		// Standard menu.
		else {
			// Standard menu activated class.
			$body.toggleClass( 'mai-standard-menu-activated' );
		}

		// If opening the menu.
		if ( $body.hasClass( 'mai-menu-activated' ) ) {

			// Disable body scroll (stupid iOS) while allowing the menu to scroll.
			bodyScrollLock.disableBodyScroll( bodyLockElement );

			if ( ! hasSideMenu ) {

				// Set max-height as window height minus header height.
				$maiMenu.css( 'max-height', $window.height() - $header.height() + 'px' );

				// Set max-height if window is resized.
				$window.on( 'resize orientationchange', function(e) {
					$maiMenu.css( 'max-height', $window.height() - $header.height() + 'px' );
				});
			}

			// Allow additional keyboard nav.
			$(document).keydown( function(e) {

				// Use switch to easily add new keystrokes.
				switch(e.which) {
					case 27: // esc.
						// Close popup with esc key.
						_closeAll();
						break;
					default: return; // Exit this handler for other keys.
				}

				// Prevent the default action (scroll/move caret).
				e.preventDefault();
			});

		}
		// Closing the menu.
		else {

			if ( ! hasSideMenu ) {
				// Remove inline styles.
				$maiMenu.css( 'max-height', '' );
			}

			_closeAll();
		}

		// On click of close button inside the side menu, close all.
		$header.on( 'click', '.menu-close', function(e){
			_closeAll();
		});

	}

	/**
	 * Action for sub-menu toggles.
	 */
	function _doToggleSubMenu() {
		$(this)._toggleSubMenu();
		$( '.sub-menu-toggle.activated' ).not( $(this) )._closeSubMenu();
	}

	/**
	 * Action for nested sub-menu toggles.
	 */
	function _doToggleSubSubMenu() {
		$(this)._toggleSubMenu();
	}

	function _doAnchorLinkClicked() {
		var href = $(this).attr('href');
		/**
		 * Bail if 1 or less characters.
		 * We don't want to do anything on only # links.
		 * And you shouldn't use those anyway.
		 */
		if ( href.length <= 1 ) {
			return;
		}
		// Bail if f link doesn't start with #.
		if ( ! /^#/.test( href ) ) {
			return;
		}
		_closeAll();
	}

	/**
	 * Modify skip link to match mobile buttons.
	 */
	function _changeSkipLink() {

		// Get vars.
		var $skipLinksUL    = $( '.genesis-skip-link' ),
			$mobileSkipLink = $( '.genesis-skip-link a[href="#mai-toggle"]' ),
			$menuSkipLinks  = $( '.genesis-skip-link a[href*="#genesis-nav"]' );

		// Whether mobile menu toggle is visible.
		var toggleDisplay = _getDisplayValue( $maiToggle );

		// If mai-toggle skip link is not created yet.
		if ( 0 == $mobileSkipLink.length ) {
			$skipLinksUL.prepend( '<li><a href="#mai-toggle" class="screen-reader-shortcut"> ' + maiVars.mainMenu + '</a></li>' );
			$mobileSkipLink = $( '.genesis-skip-link a[href="#mobile-nav"]' );
		}

		// If mai-toggle is not visible.
		if ( 'none' == toggleDisplay ) {
			$mobileSkipLink.addClass( 'skip-link-hidden' );
		}
		// Visible.
		else {
			$mobileSkipLink.removeClass( 'skip-link-hidden' );
		}

		// Manage skip link visibility.
		$.each( $menuSkipLinks, function () {

			var $this = $(this);

			if ( 'none' == toggleDisplay ) {
				$this.removeClass( 'skip-link-hidden' );
			} else {
				$this.addClass( 'skip-link-hidden' );
			}

		});
	}

	/**
	 * Maybe close all the things.
	 */
	function _maybeCloseAll() {

		if ( 'none' !== _getDisplayValue( $maiToggle ) ) {
			return true;
		}

		_closeAll();
	}

	/**
	 * Close all the things.
	 */
	function _closeAll() {

		$body.removeClass( 'mai-menu-activated mai-standard-menu-activated mai-side-menu-activated' )

		$maiToggle._closeElement();
		$( '.sub-menu-toggle.activated' )._closeSubMenu();

		// Re-enable body scroll.
		bodyScrollLock.enableBodyScroll( bodyLockElement );
	}

	/**
	 * Get the display value of an element.
	 */
	function _getDisplayValue( $element ) {
		return $element.css( 'display' );
	}

	$.fn._toggleSubMenu = function(){
		$(this)._toggleActive().next( '.sub-menu' ).slideToggle( 'fast' );
		return $(this);
	};

	$.fn._closeSubMenu = function(){
		$(this)._closeElement().next( '.sub-menu' ).slideUp( 'fast' );
		return $(this);
	};

})( window, document, jQuery );


/**
 * Convert menu items with .search class to a search icon with a fade in search box.
 * Show/hide search box on click, and allow closing by clicking outside of search box.
 *
 * @version  2.0.0
 */
( function( document, $, undefined ) {

	var $navMenu    = $( '.genesis-nav-menu' ),
		$searchItem = $navMenu.children( '.search' );

	// Bail if no search items.
	if ( 0 === $searchItem.length ) {
		return;
	}

	_maiGlobalFunctions();

	$searchItem.html( '<button class="nav-search" aria-expanded="false" aria-pressed="false"><span class="search-icon"></span><span class="screen-reader-text">' + $searchItem.text() + '</span></button>' ).show();

	// Add the search box after the link.
	$searchItem.append( maiVars.searchBox );

	$navMenu.on( 'click', '.nav-search', function(e){

		$searchButton = $(this);

		// If already opened.
		if ( $searchButton.hasClass( 'activated' ) ) {

			$searchButton._searchClose();

		}
		// Closing.
		else {

			// Close other search boxes.
			$( '.nav-search' ).not( $searchButton )._searchClose();

			$searchButton._searchOpen();

			// Close search listener.
			$( 'body' ).mouseup( function(e){
				/**
				 * Bail if:
				 * If click is on our search box container.
				 * If click is on a child of our search box container.
				 */
				if ( $(this).hasClass( 'search-box' ) || ( $searchItem.has( e.target ).length ) ) {
					return;
				}
				$searchButton._searchClose();
			});

			// Close search if esc key pressed.
			$(document).keydown( function(e){

				// Use switch to easily add new keystrokes.
				switch(e.which) {
					case 27: // esc.
					$searchButton._searchClose();
					break;
					// Exit this handler for other keys.
					default: return;
				}
			});

		}

	});

	$.fn._searchOpen = function(){
		var $this = $(this);
		$this._openElement().next( '.search-box' ).fadeIn( 'fast' ).find( 'input[type="search"]' ).focus();
		return $this;
	};

	$.fn._searchClose = function(){
		var $this = $(this);
		$this._closeElement().removeClass( 'activated' ).next( '.search-box' ).fadeOut( 'fast' );
		return $this;
	};

})( document, jQuery );


/**
 * Set an elements min-height
 * according to the aspect ratio of its' background image.
 *
 * @version  2.1.0
 */
( function( window, document, $, undefined ) {

	// Get all our elements.
	var elements = document.querySelectorAll( '.aspect-ratio' );

	// Bail if no elements.
	if ( 0 === elements.length ) {
		return;
	}

	// Resize after the window is ready. WP Rocket critical CSS needs this to wait, among other things.
	window.addEventListener( 'load', aspectRatio );
	window.addEventListener( 'resize', aspectRatio );
	window.addEventListener( 'orientationchange', aspectRatio );

	// Helper function to loop through the elements and set the aspect ratio.
	function aspectRatio() {
		forEach( elements, function( index, value ) {
			return value.style.minHeight = Math.round( value.offsetWidth / ( value.getAttribute( 'data-aspect-width' ) / value.getAttribute('data-aspect-height') ) ) + 'px';
		});
	}

	// Thanks Todd! @link https://toddmotto.com/ditch-the-array-foreach-call-nodelist-hack/
	var forEach = function( array, callback, scope ) {
		for ( var i = 0; i < array.length; i++ ) {
			// Passes back stuff we need.
			callback.call( scope, i, array[i] );
		}
	};

	// After FacetWP is loaded/refreshed. We needed to get the elements again because of the way FWP re-displays them.
	$( document ).on( 'facetwp-loaded', function() {
		aspectRatio();
	});

})( window, document, jQuery );


/**
 * Scroll to a div id.
 *
 * Link
 * <a class="scroll-to" href="#element-id">Text</a>
 *
 * Target
 * <div id="element-id">Content</div>
 */
( function ( document, $, undefined ) {

	var $html     = $( 'html' );
	var $body     = $( 'body' );
	var $header   = $( '.site-header' );
	var hasSticky = $body.hasClass( 'has-sticky-header' );

	$body.on( 'click', '.scroll-to', function(e) {
		var target = $( this.getAttribute('href') );
		// Bail if empty link.
		if( ! target.length ) {
			return;
		}
		// Bail if link doesn't start with #.
		if ( ! /^#/.test( $(this).attr( 'href' ) ) ) {
			return;
		}
		e.preventDefault();
		var offset = target.offset().top - parseInt( $html.css( 'marginTop' ) );
		if ( hasSticky ) {
			// Offset adds header height plus a little extra.
			offset = offset - $header.outerHeight() - 16;
		}
		$( 'html, body' ).stop().animate({ scrollTop: offset }, 1000 );
	});

})( document, jQuery );


/**
 * Initialise Superfish with custom arguments.
 *
 * @package Genesis\JS
 * @author StudioPress
 * @license GPL-2.0+
 */
( function( document, $, undefined ) {

	var $superfish = $( '.js-superfish' );

	// Bail if no object.
	if ( ! $superfish.length ) {
		return;
	}

	// Bail if superfish function does not exist.
	if ( 'function' !== typeof $superfish.superfish ) {
		return;
	}

	$superfish.superfish({
		'delay': 1000,
		'speed': 'fast',
		'speedOut': 'slow',
		'disableHI': true,
	});

})( document, jQuery );

/**
 * Build some helper functions.
 *
 * @access  private.
 */
function _maiGlobalFunctions() {

	var $ = jQuery;

	$.fn._toggleActive = function(){
		var $this = $(this);
		$this._toggleArias().toggleClass( 'activated' );
		return $this;
	};

	$.fn._toggleArias = function(){
		var $this = $(this);
		$this.attr({
			'aria-expanded': 'false' === $this.attr( 'aria-expanded' ),
			'aria-pressed': 'false' === $this.attr( 'aria-pressed' ),
		});
		return $this;
	};

	$.fn._openElement = function(){
		var $this = $(this);
		$this.addClass( 'activated' ).attr({
			'aria-expanded': true,
			'aria-pressed': true,
		});
		return $this;
	};

	$.fn._closeElement = function(){
		var $this = $(this);
		$this.removeClass( 'activated' ).attr({
			'aria-expanded': false,
			'aria-pressed': false,
		});
		return $this;
	};

}
