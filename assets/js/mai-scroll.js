!function(t){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=t();else if("function"==typeof define&&define.amd)define([],t);else{("undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this).basicScroll=t()}}(function(){return function u(i,c,s){function f(n,t){if(!c[n]){if(!i[n]){var e="function"==typeof require&&require;if(!t&&e)return e(n,!0);if(a)return a(n,!0);var o=new Error("Cannot find module '"+n+"'");throw o.code="MODULE_NOT_FOUND",o}var r=c[n]={exports:{}};i[n][0].call(r.exports,function(t){return f(i[n][1][t]||t)},r,r.exports,u,i,c,s)}return c[n].exports}for(var a="function"==typeof require&&require,t=0;t<s.length;t++)f(s[t]);return f}({1:[function(t,n,e){"use strict";n.exports=function(t){var n=2.5949095;return(t*=2)<1?t*t*((n+1)*t-n)*.5:.5*((t-=2)*t*((n+1)*t+n)+2)}},{}],2:[function(t,n,e){"use strict";n.exports=function(t){var n=1.70158;return t*t*((n+1)*t-n)}},{}],3:[function(t,n,e){"use strict";n.exports=function(t){var n=1.70158;return--t*t*((n+1)*t+n)+1}},{}],4:[function(t,n,e){"use strict";var o=t("./bounce-out");n.exports=function(t){return t<.5?.5*(1-o(1-2*t)):.5*o(2*t-1)+.5}},{"./bounce-out":6}],5:[function(t,n,e){"use strict";var o=t("./bounce-out");n.exports=function(t){return 1-o(1-t)}},{"./bounce-out":6}],6:[function(t,n,e){"use strict";n.exports=function(t){var n=t*t;return t<4/11?7.5625*n:t<8/11?9.075*n-9.9*t+3.4:t<.9?4356/361*n-35442/1805*t+16061/1805:10.8*t*t-20.52*t+10.72}},{}],7:[function(t,n,e){"use strict";n.exports=function(t){return(t*=2)<1?-.5*(Math.sqrt(1-t*t)-1):.5*(Math.sqrt(1-(t-=2)*t)+1)}},{}],8:[function(t,n,e){"use strict";n.exports=function(t){return 1-Math.sqrt(1-t*t)}},{}],9:[function(t,n,e){"use strict";n.exports=function(t){return Math.sqrt(1- --t*t)}},{}],10:[function(t,n,e){"use strict";n.exports=function(t){return t<.5?4*t*t*t:.5*Math.pow(2*t-2,3)+1}},{}],11:[function(t,n,e){"use strict";n.exports=function(t){return t*t*t}},{}],12:[function(t,n,e){"use strict";n.exports=function(t){var n=t-1;return n*n*n+1}},{}],13:[function(t,n,e){"use strict";n.exports=function(t){return t<.5?.5*Math.sin(13*Math.PI/2*2*t)*Math.pow(2,10*(2*t-1)):.5*Math.sin(-13*Math.PI/2*(2*t-1+1))*Math.pow(2,-10*(2*t-1))+1}},{}],14:[function(t,n,e){"use strict";n.exports=function(t){return Math.sin(13*t*Math.PI/2)*Math.pow(2,10*(t-1))}},{}],15:[function(t,n,e){"use strict";n.exports=function(t){return Math.sin(-13*(t+1)*Math.PI/2)*Math.pow(2,-10*t)+1}},{}],16:[function(t,n,e){"use strict";n.exports=function(t){return 0===t||1===t?t:t<.5?.5*Math.pow(2,20*t-10):-.5*Math.pow(2,10-20*t)+1}},{}],17:[function(t,n,e){"use strict";n.exports=function(t){return 0===t?t:Math.pow(2,10*(t-1))}},{}],18:[function(t,n,e){"use strict";n.exports=function(t){return 1===t?t:1-Math.pow(2,-10*t)}},{}],19:[function(t,n,e){"use strict";n.exports={backInOut:t("./back-in-out"),backIn:t("./back-in"),backOut:t("./back-out"),bounceInOut:t("./bounce-in-out"),bounceIn:t("./bounce-in"),bounceOut:t("./bounce-out"),circInOut:t("./circ-in-out"),circIn:t("./circ-in"),circOut:t("./circ-out"),cubicInOut:t("./cubic-in-out"),cubicIn:t("./cubic-in"),cubicOut:t("./cubic-out"),elasticInOut:t("./elastic-in-out"),elasticIn:t("./elastic-in"),elasticOut:t("./elastic-out"),expoInOut:t("./expo-in-out"),expoIn:t("./expo-in"),expoOut:t("./expo-out"),linear:t("./linear"),quadInOut:t("./quad-in-out"),quadIn:t("./quad-in"),quadOut:t("./quad-out"),quartInOut:t("./quart-in-out"),quartIn:t("./quart-in"),quartOut:t("./quart-out"),quintInOut:t("./quint-in-out"),quintIn:t("./quint-in"),quintOut:t("./quint-out"),sineInOut:t("./sine-in-out"),sineIn:t("./sine-in"),sineOut:t("./sine-out")}},{"./back-in":2,"./back-in-out":1,"./back-out":3,"./bounce-in":5,"./bounce-in-out":4,"./bounce-out":6,"./circ-in":8,"./circ-in-out":7,"./circ-out":9,"./cubic-in":11,"./cubic-in-out":10,"./cubic-out":12,"./elastic-in":14,"./elastic-in-out":13,"./elastic-out":15,"./expo-in":17,"./expo-in-out":16,"./expo-out":18,"./linear":20,"./quad-in":22,"./quad-in-out":21,"./quad-out":23,"./quart-in":25,"./quart-in-out":24,"./quart-out":26,"./quint-in":28,"./quint-in-out":27,"./quint-out":29,"./sine-in":31,"./sine-in-out":30,"./sine-out":32}],20:[function(t,n,e){"use strict";n.exports=function(t){return t}},{}],21:[function(t,n,e){"use strict";n.exports=function(t){return(t/=.5)<1?.5*t*t:-.5*(--t*(t-2)-1)}},{}],22:[function(t,n,e){"use strict";n.exports=function(t){return t*t}},{}],23:[function(t,n,e){"use strict";n.exports=function(t){return-t*(t-2)}},{}],24:[function(t,n,e){"use strict";n.exports=function(t){return t<.5?8*Math.pow(t,4):-8*Math.pow(t-1,4)+1}},{}],25:[function(t,n,e){"use strict";n.exports=function(t){return Math.pow(t,4)}},{}],26:[function(t,n,e){"use strict";n.exports=function(t){return Math.pow(t-1,3)*(1-t)+1}},{}],27:[function(t,n,e){"use strict";n.exports=function(t){return(t*=2)<1?.5*t*t*t*t*t:.5*((t-=2)*t*t*t*t+2)}},{}],28:[function(t,n,e){"use strict";n.exports=function(t){return t*t*t*t*t}},{}],29:[function(t,n,e){"use strict";n.exports=function(t){return--t*t*t*t*t+1}},{}],30:[function(t,n,e){"use strict";n.exports=function(t){return-.5*(Math.cos(Math.PI*t)-1)}},{}],31:[function(t,n,e){"use strict";n.exports=function(t){var n=Math.cos(t*Math.PI*.5);return Math.abs(n)<1e-14?1:1-n}},{}],32:[function(t,n,e){"use strict";n.exports=function(t){return Math.sin(t*Math.PI/2)}},{}],33:[function(t,n,e){"use strict";n.exports=function(t,n){n||(n=[0,""]),t=String(t);var e=parseFloat(t,10);return n[0]=e,n[1]=t.match(/[\d.\-\+]*\s*(.*)/)[1]||"",n}},{}],34:[function(t,n,e){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.create=void 0;var u="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},o=r(t("parse-unit")),i=r(t("eases"));function r(t){return t&&t.__esModule?t:{default:t}}var c,s,f,a=[],p="undefined"!=typeof window,l=function(){return(document.scrollingElement||document.documentElement).scrollTop},d=function(t){return!1===isNaN((0,o.default)(t)[0])},m=function(t){var n=(0,o.default)(t);return{value:n[0],unit:n[1]}},b=function(t){return null!==String(t).match(/^[a-z]+-[a-z]+$/)},h=function(t,n){var e=2<arguments.length&&void 0!==arguments[2]?arguments[2]:l(),o=3<arguments.length&&void 0!==arguments[3]?arguments[3]:window.innerHeight||window.outerHeight,r=n.getBoundingClientRect(),u=t.match(/^[a-z]+/)[0],i=t.match(/[a-z]+$/)[0],c=0;return"top"===i&&(c-=0),"middle"===i&&(c-=o/2),"bottom"===i&&(c-=o),"top"===u&&(c+=r.top+e),"middle"===u&&(c+=r.top+e+r.height/2),"bottom"===u&&(c+=r.top+e+r.height),c+"px"},w=function(t){var n,e,o=1<arguments.length&&void 0!==arguments[1]?arguments[1]:l(),s=t.getData(),r=s.to.value-s.from.value,u=(o-s.from.value)/(r/100),f=Math.min(Math.max(u,0),100),i=(n=s.direct,e={global:document.documentElement,elem:s.elem,direct:s.direct},!0===n?e.elem:n instanceof HTMLElement==1?e.direct:e.global),c=Object.keys(s.props).reduce(function(t,n){var e=s.props[n],o=e.from.unit||e.to.unit,r=e.from.value-e.to.value,u=e.timing(f/100),i=e.from.value-r*u,c=Math.round(1e4*i)/1e4;return t[n]=c+o,t},{}),a=u<0||100<u;return!0===(0<=u&&u<=100)&&s.inside(t,u,c),!0===a&&s.outside(t,u,c),{elem:i,props:c}},v=function(o,r){Object.keys(r).forEach(function(t){return n=o,e={key:t,value:r[t]},void n.style.setProperty(e.key,e.value);var n,e})};e.create=function(t){var n=null,e=!1,o={isActive:function(){return e},getData:function(){return n},calculate:function(){n=function(){var o=0<arguments.length&&void 0!==arguments[0]?arguments[0]:{};if(null==(o=Object.assign({},o)).inside&&(o.inside=function(){}),null==o.outside&&(o.outside=function(){}),null==o.direct&&(o.direct=!1),null==o.track&&(o.track=!0),null==o.props&&(o.props={}),null==o.from)throw new Error("Missing property `from`");if(null==o.to)throw new Error("Missing property `to`");if("function"!=typeof o.inside)throw new Error("Property `inside` must be undefined or a function");if("function"!=typeof o.outside)throw new Error("Property `outside` must be undefined or a function");if("boolean"!=typeof o.direct&&o.direct instanceof HTMLElement==0)throw new Error("Property `direct` must be undefined, a boolean or a DOM element/node");if(!0===o.direct&&null==o.elem)throw new Error("Property `elem` is required when `direct` is true");if("boolean"!=typeof o.track)throw new Error("Property `track` must be undefined or a boolean");if("object"!==u(o.props))throw new Error("Property `props` must be undefined or an object");if(null==o.elem){if(!1===d(o.from))throw new Error("Property `from` must be a absolute value when no `elem` has been provided");if(!1===d(o.to))throw new Error("Property `to` must be a absolute value when no `elem` has been provided")}else!0===b(o.from)&&(o.from=h(o.from,o.elem)),!0===b(o.to)&&(o.to=h(o.to,o.elem));return o.from=m(o.from),o.to=m(o.to),o.props=Object.keys(o.props).reduce(function(t,n){var e=Object.assign({},o.props[n]);if(!1===d(e.from))throw new Error("Property `from` of prop must be a absolute value");if(!1===d(e.to))throw new Error("Property `from` of prop must be a absolute value");if(e.from=m(e.from),e.to=m(e.to),null==e.timing&&(e.timing=i.default.linear),"string"!=typeof e.timing&&"function"!=typeof e.timing)throw new Error("Property `timing` of prop must be undefined, a string or a function");if("string"==typeof e.timing&&null==i.default[e.timing])throw new Error("Unknown timing for property `timing` of prop");return"string"==typeof e.timing&&(e.timing=i.default[e.timing]),t[n]=e,t},{}),o}(t)},update:function(){var t=w(o),n=t.elem,e=t.props;return v(n,e),e},start:function(){e=!0},stop:function(){e=!1},destroy:function(){a[r]=void 0}},r=a.push(o)-1;return o.calculate(),o};!0===p?(!function t(n,e){var o=function(){requestAnimationFrame(function(){return t(n,e)})},r=a.filter(function(t){return null!=t&&t.isActive()});if(0===r.length)return o();var u=l();if(e===u)return o();e=u,r.map(function(t){return w(t,u)}).forEach(function(t){var n=t.elem,e=t.props;return v(n,e)}),o()}(),window.addEventListener("resize",(c=function(){a.filter(function(t){return null!=t&&t.getData().track}).forEach(function(t){t.calculate(),t.update()})},s=50,f=null,function(){for(var t=arguments.length,n=Array(t),e=0;e<t;e++)n[e]=arguments[e];clearTimeout(f),f=setTimeout(function(){return c.apply(void 0,n)},s)}))):console.warn("basicScroll is not executing because you are using it in an environment without a `window` object")},{eases:19,"parse-unit":33}]},{},[34])(34)});

const html            = document.querySelector( 'html' );
const body            = document.querySelector( 'body' );
const siteHeader      = document.querySelector( '.site-header' );
const logoWidth       = maiScroll.logoWidth;
const shrunkLogoWidth = Math.round( logoWidth * .7 );

var htmlStyle       = window.getComputedStyle( html );
var siteHeaderStyle = window.getComputedStyle( siteHeader );
// var from            = siteHeader.offsetTop + parseInt( htmlStyle.marginTop );
var from            = parseInt( htmlStyle.marginTop );
var to              = from + 200;

// console.log( body.scrollHeight );

// basicScroll.create({
// 	elem: document.querySelector( '.custom-logo-link' ),
// 	from: '46',
// 	to: '246',
// 	direct: true,
// 	props: {
// 		'--logo-width': {
// 			from: logoWidth + 'px',
// 			to: shrunkLogoWidth + 'px',
// 		}
// 	}
// })
// .start();

// var headerAnimating = false;
var afterHeader =  false;

basicScroll.create({
	elem: document.querySelector( '.site-header' ),
	from: from,
	to: to,
	// direct: true,
	props: {
		'--logo-width': {
			from: logoWidth + 'px',
			to: shrunkLogoWidth + 'px',
		},
		'--logo-margin-top': {
			from: '24px',
			to: '4px',
		},
		'--logo-margin-bottom': {
			from: '24px',
			to: '4px',
		}
	},
	inside: (instance, percentage, props) => {

		if ( afterHeader ) {
			afterHeader = false;
		}

		// console.log( 'inside' );

		// if ( ! headerAnimating ) {
		// 	headerAnimating = true;
		// }
	},
	outside: (instance, percentage, props) => {

		if ( percentage <= 0 ) {
			if ( afterHeader ) {
				afterHeader = false;
			}
		} else {
			if ( ! afterHeader ) {
				afterHeader = true;
			}
		}

		// console.log( afterHeader );

		// if ( percentage <= 0 ) {
			// if ( ! headerAnimating ) {
				// headerAnimating = true;
			// }
			// return;
		// }
		// console.log( percentage );
		// console.log( 'outside' );

		// if ( headerAnimating ) {
		// 	headerAnimating = false;
		// }
	}
})
.start();

// // var initial          = true; // initial page load.
var scrollClassAdded = false;
// var current          = 0;
var prevPercentage         = 0;
var scrollingDown       = true;
// var revealed         = false;
var concealed        = false;
var hasScrolledSame  = false;
var scrollDowns      = [];
// var scrollDistances  = [];
var compareDistance = false;

basicScroll.create({
	elem: document.querySelector( 'body' ),
	from: 'top-top',
	to: 'bottom-bottom',
	// direct: true,
	props: {
		'--body-scroll': {
			from: 0,
			to: 100,
		},
		'--body-scroll-distance': {
			from: 0,
			to: body.scrollHeight,
		}
	},
	inside: (instance, percentage, props) => {

		if ( ( percentage > 0 ) && ! scrollClassAdded ) {
			body.classList.add( 'scroll' );
			scrollClassAdded = true;
		} else if ( ( percentage <= 0 ) && scrollClassAdded ) {
			body.classList.remove( 'scroll' );
			scrollClassAdded = false;
		}

		if ( ! compareDistance ) {
			compareDistance = props['--body-scroll-distance'];
		}

		// if ( ! ( compareDistance || hasScrolledSame ) ) {
		// 	compareDistance = props['--body-scroll-distance'];
		// }

		// Bail on initial page load.
		// if ( initial ) {
			// initial = false;
		// 	return;
		// }

		// if ( headerAnimating ) {
		// 	return;
		// }


		// Store items.
		scrollingDown  = ( percentage > prevPercentage );
		prevPercentage = percentage;
		scrollDowns     = storeItem( scrollDowns, scrollingDown, 4 );
		// scrollDistances = storeItem( scrollDistances, props['--body-scroll-distance'], 20 );

		// // Check items.
		hasScrolledSame   = sameItems( scrollDowns );
		hasScrolledEnough = scrolledEnough( compareDistance, props['--body-scroll-distance'], scrollingDown );

		if ( ! hasScrolledSame && compareDistance ) {
			compareDistance = false;
		}

		console.log( hasScrolledEnough );

		// console.log( scrolledEnough( compareDistance, props['--body-scroll-distance'], scrollingDown ) );

		// Bail if not scrolling the same dirction/distance.
		// if ( ! hasScrolledSame ) {
		// 	return;
		// }

		// Scrolling down.
		if ( scrollingDown ) {
			// If not concealed and after the header.
			if ( ! concealed && afterHeader ) {
				if ( hasScrolledEnough ) {
					concealHeader();
				}
			}
		}
		// Scrolling up.
		else {
			if ( concealed ) {
				if ( hasScrolledEnough || ! afterHeader ) {
					revealHeader();
				}
			}
		}
	},
	outside: (instance, percentage, props) => {
		if ( concealed ) {
			revealHeader();
		}
	}
})
.start();

function concealHeader() {
	siteHeader.classList.remove( 'reveal-header' );
	siteHeader.classList.add( 'conceal-header' );
	concealed = true;
}

function revealHeader() {
	siteHeader.classList.remove( 'conceal-header' );
	siteHeader.classList.add( 'reveal-header' );
	concealed = false;
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
function sameItems( array ) {
	return array.every( function( v, i, a ) {
		return i === 0 || v === a[i - 1];
	});
}

// function scrolledEnoughOG( array, scrollingDown ) {
// 	var distance  = 0;
// 	var firstItem = array[0];
// 	var lastItem  = array[array.length-1];
// 	if ( scrollingDown ) {
// 		distance = ( firstItem - lastItem );
// 	}
// 	distance = ( lastItem - firstItem );
// 	return ( distance > 60 );
// }

function scrolledEnough( firstItem, lastItem, scrollingDown ) {
	if ( ! ( firstItem && lastItem ) ) {
		return false;
	}
	var distance;
	if ( scrollingDown ) {
		distance = ( lastItem - firstItem );
	} else {
		distance = ( firstItem - lastItem );
	}
	return ( distance > 120 );
}

document.querySelectorAll( '.section .bg-image' ).forEach( function(elem) {

	basicScroll.create({
		elem: elem,
		from: 'top-bottom',
		to: 'bottom-top',
		direct: true,
		props: {
			'--translateY': {
				from: '-20%',
				to: '20%',
			}
		},
	})
	.start();

});
