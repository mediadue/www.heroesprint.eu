/*!
 * Modernizr v2.8.3
 * www.modernizr.com
 *
 * Copyright (c) Faruk Ates, Paul Irish, Alex Sexton
 * Available under the BSD and MIT licenses: www.modernizr.com/license/
 */

/*
 * Modernizr tests which native CSS3 and HTML5 features are available in
 * the current UA and makes the results available to you in two ways:
 * as properties on a global Modernizr object, and as classes on the
 * <html> element. This information allows you to progressively enhance
 * your pages with a granular level of control over the experience.
 *
 * Modernizr has an optional (not included) conditional resource loader
 * called Modernizr.load(), based on Yepnope.js (yepnopejs.com).
 * To get a build that includes Modernizr.load(), as well as choosing
 * which tests to include, go to www.modernizr.com/download/
 *
 * Authors        Faruk Ates, Paul Irish, Alex Sexton
 * Contributors   Ryan Seddon, Ben Alman
 */

window.Modernizr = (function(window, document, undefined) {
  var version = '2.8.3',
    Modernizr = {},
    /*>>cssclasses*/
    // option for enabling the HTML classes to be added
    enableClasses = true,
    /*>>cssclasses*/

    docElement = document.documentElement,
    /**
         * Create our "modernizr" element that we do most feature tests on.
         */
    mod = 'modernizr',
    modElem = document.createElement(mod),
    mStyle = modElem.style,
    /**
         * Create the input element for various Web Forms feature tests.
         */
    inputElem /*>>inputelem*/ = document.createElement('input') /*>>inputelem*/,
    /*>>smile*/
    smile = ':)',
    /*>>smile*/

    toString = {}.toString,
    // TODO :: make the prefixes more granular
    /*>>prefixes*/
    // List of property values to set for css tests. See ticket #21
    prefixes = ' -webkit- -moz- -o- -ms- '.split(' '),
    /*>>prefixes*/

    /*>>domprefixes*/
    // Following spec is to expose vendor-specific style properties as:
    //   elem.style.WebkitBorderRadius
    // and the following would be incorrect:
    //   elem.style.webkitBorderRadius

    // Webkit ghosts their properties in lowercase but Opera & Moz do not.
    // Microsoft uses a lowercase `ms` instead of the correct `Ms` in IE8+
    //   erik.eae.net/archives/2008/03/10/21.48.10/

    // More here: github.com/Modernizr/Modernizr/issues/issue/21
    omPrefixes = 'Webkit Moz O ms',
    cssomPrefixes = omPrefixes.split(' '),
    domPrefixes = omPrefixes.toLowerCase().split(' '),
    /*>>domprefixes*/

    /*>>ns*/
    ns = { svg: 'http://www.w3.org/2000/svg' },
    /*>>ns*/

    tests = {},
    inputs = {},
    attrs = {},
    classes = [],
    slice = classes.slice,
    featureName, // used in testing loop
    /*>>teststyles*/
    // Inject element with style element and some CSS rules
    injectElementWithStyles = function(rule, callback, nodes, testnames) {
      var style,
        ret,
        node,
        docOverflow,
        div = document.createElement('div'),
        // After page load injecting a fake body doesn't work so check if body exists
        body = document.body,
        // IE6 and 7 won't return offsetWidth or offsetHeight unless it's in the body element, so we fake it.
        fakeBody = body || document.createElement('body');

      if (parseInt(nodes, 10)) {
        // In order not to give false positives we create a node for each test
        // This also allows the method to scale for unspecified uses
        while (nodes--) {
          node = document.createElement('div');
          node.id = testnames ? testnames[nodes] : mod + (nodes + 1);
          div.appendChild(node);
        }
      }

      // <style> elements in IE6-9 are considered 'NoScope' elements and therefore will be removed
      // when injected with innerHTML. To get around this you need to prepend the 'NoScope' element
      // with a 'scoped' element, in our case the soft-hyphen entity as it won't mess with our measurements.
      // msdn.microsoft.com/en-us/library/ms533897%28VS.85%29.aspx
      // Documents served as xml will throw if using &shy; so use xml friendly encoded version. See issue #277
      style = ['&#173;', '<style id="s', mod, '">', rule, '</style>'].join('');
      div.id = mod;
      // IE6 will false positive on some tests due to the style element inside the test div somehow interfering offsetHeight, so insert it into body or fakebody.
      // Opera will act all quirky when injecting elements in documentElement when page is served as xml, needs fakebody too. #270
      (body ? div : fakeBody).innerHTML += style;
      fakeBody.appendChild(div);
      if (!body) {
        //avoid crashing IE8, if background image is used
        fakeBody.style.background = '';
        //Safari 5.13/5.1.4 OSX stops loading if ::-webkit-scrollbar is used and scrollbars are visible
        fakeBody.style.overflow = 'hidden';
        docOverflow = docElement.style.overflow;
        docElement.style.overflow = 'hidden';
        docElement.appendChild(fakeBody);
      }

      ret = callback(div, rule);
      // If this is done after page load we don't want to remove the body so check if body exists
      if (!body) {
        fakeBody.parentNode.removeChild(fakeBody);
        docElement.style.overflow = docOverflow;
      } else {
        div.parentNode.removeChild(div);
      }

      return !!ret;
    },
    /*>>teststyles*/

    /*>>mq*/
    // adapted from matchMedia polyfill
    // by Scott Jehl and Paul Irish
    // gist.github.com/786768
    testMediaQuery = function(mq) {
      var matchMedia = window.matchMedia || window.msMatchMedia;
      if (matchMedia) {
        return (matchMedia(mq) && matchMedia(mq).matches) || false;
      }

      var bool;

      injectElementWithStyles(
        '@media ' + mq + ' { #' + mod + ' { position: absolute; } }',
        function(node) {
          bool =
            (window.getComputedStyle ? getComputedStyle(node, null) : node.currentStyle)[
              'position'
            ] == 'absolute';
        }
      );

      return bool;
    },
    /*>>mq*/

    /*>>hasevent*/
    //
    // isEventSupported determines if a given element supports the given event
    // kangax.github.com/iseventsupported/
    //
    // The following results are known incorrects:
    //   Modernizr.hasEvent("webkitTransitionEnd", elem) // false negative
    //   Modernizr.hasEvent("textInput") // in Webkit. github.com/Modernizr/Modernizr/issues/333
    //   ...
    isEventSupported = (function() {
      var TAGNAMES = {
        select: 'input',
        change: 'input',
        submit: 'form',
        reset: 'form',
        error: 'img',
        load: 'img',
        abort: 'img',
      };

      function isEventSupported(eventName, element) {
        element = element || document.createElement(TAGNAMES[eventName] || 'div');
        eventName = 'on' + eventName;

        // When using `setAttribute`, IE skips "unload", WebKit skips "unload" and "resize", whereas `in` "catches" those
        var isSupported = eventName in element;

        if (!isSupported) {
          // If it has no `setAttribute` (i.e. doesn't implement Node interface), try generic element
          if (!element.setAttribute) {
            element = document.createElement('div');
          }
          if (element.setAttribute && element.removeAttribute) {
            element.setAttribute(eventName, '');
            isSupported = is(element[eventName], 'function');

            // If property was created, "remove it" (by setting value to `undefined`)
            if (!is(element[eventName], 'undefined')) {
              element[eventName] = undefined;
            }
            element.removeAttribute(eventName);
          }
        }

        element = null;
        return isSupported;
      }
      return isEventSupported;
    })(),
    /*>>hasevent*/

    // TODO :: Add flag for hasownprop ? didn't last time

    // hasOwnProperty shim by kangax needed for Safari 2.0 support
    _hasOwnProperty = {}.hasOwnProperty,
    hasOwnProp;

  if (!is(_hasOwnProperty, 'undefined') && !is(_hasOwnProperty.call, 'undefined')) {
    hasOwnProp = function(object, property) {
      return _hasOwnProperty.call(object, property);
    };
  } else {
    hasOwnProp = function(object, property) {
      /* yes, this can give false positives/negatives, but most of the time we don't care about those */
      return property in object && is(object.constructor.prototype[property], 'undefined');
    };
  }

  // Adapted from ES5-shim https://github.com/kriskowal/es5-shim/blob/master/es5-shim.js
  // es5.github.com/#x15.3.4.5

  if (!Function.prototype.bind) {
    Function.prototype.bind = function bind(that) {
      var target = this;

      if (typeof target != 'function') {
        throw new TypeError();
      }

      var args = slice.call(arguments, 1),
        bound = function() {
          if (this instanceof bound) {
            var F = function() {};
            F.prototype = target.prototype;
            var self = new F();

            var result = target.apply(self, args.concat(slice.call(arguments)));
            if (Object(result) === result) {
              return result;
            }
            return self;
          } else {
            return target.apply(that, args.concat(slice.call(arguments)));
          }
        };

      return bound;
    };
  }

  /**
         * setCss applies given styles to the Modernizr DOM node.
         */
  function setCss(str) {
    mStyle.cssText = str;
  }

  /**
         * setCssAll extrapolates all vendor-specific css strings.
         */
  function setCssAll(str1, str2) {
    return setCss(prefixes.join(str1 + ';') + (str2 || ''));
  }

  /**
         * is returns a boolean for if typeof obj is exactly type.
         */
  function is(obj, type) {
    return typeof obj === type;
  }

  /**
         * contains returns a boolean for if substr is found within str.
         */
  function contains(str, substr) {
    return !!~('' + str).indexOf(substr);
  }

  /*>>testprop*/

  // testProps is a generic CSS / DOM property test.

  // In testing support for a given CSS property, it's legit to test:
  //    `elem.style[styleName] !== undefined`
  // If the property is supported it will return an empty string,
  // if unsupported it will return undefined.

  // We'll take advantage of this quick test and skip setting a style
  // on our modernizr element, but instead just testing undefined vs
  // empty string.

  // Because the testing of the CSS property names (with "-", as
  // opposed to the camelCase DOM properties) is non-portable and
  // non-standard but works in WebKit and IE (but not Gecko or Opera),
  // we explicitly reject properties with dashes so that authors
  // developing in WebKit or IE first don't end up with
  // browser-specific content by accident.

  function testProps(props, prefixed) {
    for (var i in props) {
      var prop = props[i];
      if (!contains(prop, '-') && mStyle[prop] !== undefined) {
        return prefixed == 'pfx' ? prop : true;
      }
    }
    return false;
  }
  /*>>testprop*/

  // TODO :: add testDOMProps
  /**
         * testDOMProps is a generic DOM property test; if a browser supports
         *   a certain property, it won't return undefined for it.
         */
  function testDOMProps(props, obj, elem) {
    for (var i in props) {
      var item = obj[props[i]];
      if (item !== undefined) {
        // return the property name as a string
        if (elem === false) return props[i];

        // let's bind a function
        if (is(item, 'function')) {
          // default to autobind unless override
          return item.bind(elem || obj);
        }

        // return the unbound function or obj or value
        return item;
      }
    }
    return false;
  }

  /*>>testallprops*/
  /**
         * testPropsAll tests a list of DOM properties we want to check against.
         *   We specify literally ALL possible (known and/or likely) properties on
         *   the element including the non-vendor prefixed one, for forward-
         *   compatibility.
         */
  function testPropsAll(prop, prefixed, elem) {
    var ucProp = prop.charAt(0).toUpperCase() + prop.slice(1),
      props = (prop + ' ' + cssomPrefixes.join(ucProp + ' ') + ucProp).split(' ');

    // did they call .prefixed('boxSizing') or are we just testing a prop?
    if (is(prefixed, 'string') || is(prefixed, 'undefined')) {
      return testProps(props, prefixed);

      // otherwise, they called .prefixed('requestAnimationFrame', window[, elem])
    } else {
      props = (prop + ' ' + domPrefixes.join(ucProp + ' ') + ucProp).split(' ');
      return testDOMProps(props, prefixed, elem);
    }
  }
  /*>>testallprops*/

  /**
         * Tests
         * -----
         */

  // The *new* flexbox
  // dev.w3.org/csswg/css3-flexbox

  tests['flexbox'] = function() {
    return testPropsAll('flexWrap');
  };

  // The *old* flexbox
  // www.w3.org/TR/2009/WD-css3-flexbox-20090723/

  tests['flexboxlegacy'] = function() {
    return testPropsAll('boxDirection');
  };

  // On the S60 and BB Storm, getContext exists, but always returns undefined
  // so we actually have to call getContext() to verify
  // github.com/Modernizr/Modernizr/issues/issue/97/

  tests['canvas'] = function() {
    var elem = document.createElement('canvas');
    return !!(elem.getContext && elem.getContext('2d'));
  };

  tests['canvastext'] = function() {
    return !!(
      Modernizr['canvas'] &&
      is(document.createElement('canvas').getContext('2d').fillText, 'function')
    );
  };

  // webk.it/70117 is tracking a legit WebGL feature detect proposal

  // We do a soft detect which may false positive in order to avoid
  // an expensive context creation: bugzil.la/732441

  tests['webgl'] = function() {
    return !!window.WebGLRenderingContext;
  };

  /*
         * The Modernizr.touch test only indicates if the browser supports
         *    touch events, which does not necessarily reflect a touchscreen
         *    device, as evidenced by tablets running Windows 7 or, alas,
         *    the Palm Pre / WebOS (touch) phones.
         *
         * Additionally, Chrome (desktop) used to lie about its support on this,
         *    but that has since been rectified: crbug.com/36415
         *
         * We also test for Firefox 4 Multitouch Support.
         *
         * For more info, see: modernizr.github.com/Modernizr/touch.html
         */

  tests['touch'] = function() {
    var bool;

    if ('ontouchstart' in window || (window.DocumentTouch && document instanceof DocumentTouch)) {
      bool = true;
    } else {
      injectElementWithStyles(
        [
          '@media (',
          prefixes.join('touch-enabled),('),
          mod,
          ')',
          '{#modernizr{top:9px;position:absolute}}',
        ].join(''),
        function(node) {
          bool = node.offsetTop === 9;
        }
      );
    }

    return bool;
  };

  // geolocation is often considered a trivial feature detect...
  // Turns out, it's quite tricky to get right:
  //
  // Using !!navigator.geolocation does two things we don't want. It:
  //   1. Leaks memory in IE9: github.com/Modernizr/Modernizr/issues/513
  //   2. Disables page caching in WebKit: webk.it/43956
  //
  // Meanwhile, in Firefox < 8, an about:config setting could expose
  // a false positive that would throw an exception: bugzil.la/688158

  tests['geolocation'] = function() {
    return 'geolocation' in navigator;
  };

  tests['postmessage'] = function() {
    return !!window.postMessage;
  };

  // Chrome incognito mode used to throw an exception when using openDatabase
  // It doesn't anymore.
  tests['websqldatabase'] = function() {
    return !!window.openDatabase;
  };

  // Vendors had inconsistent prefixing with the experimental Indexed DB:
  // - Webkit's implementation is accessible through webkitIndexedDB
  // - Firefox shipped moz_indexedDB before FF4b9, but since then has been mozIndexedDB
  // For speed, we don't test the legacy (and beta-only) indexedDB
  tests['indexedDB'] = function() {
    return !!testPropsAll('indexedDB', window);
  };

  // documentMode logic from YUI to filter out IE8 Compat Mode
  //   which false positives.
  tests['hashchange'] = function() {
    return (
      isEventSupported('hashchange', window) &&
      (document.documentMode === undefined || document.documentMode > 7)
    );
  };

  // Per 1.6:
  // This used to be Modernizr.historymanagement but the longer
  // name has been deprecated in favor of a shorter and property-matching one.
  // The old API is still available in 1.6, but as of 2.0 will throw a warning,
  // and in the first release thereafter disappear entirely.
  tests['history'] = function() {
    return !!(window.history && history.pushState);
  };

  tests['draganddrop'] = function() {
    var div = document.createElement('div');
    return 'draggable' in div || ('ondragstart' in div && 'ondrop' in div);
  };

  // FF3.6 was EOL'ed on 4/24/12, but the ESR version of FF10
  // will be supported until FF19 (2/12/13), at which time, ESR becomes FF17.
  // FF10 still uses prefixes, so check for it until then.
  // for more ESR info, see: mozilla.org/en-US/firefox/organizations/faq/
  tests['websockets'] = function() {
    return 'WebSocket' in window || 'MozWebSocket' in window;
  };

  // css-tricks.com/rgba-browser-support/
  tests['rgba'] = function() {
    // Set an rgba() color and check the returned value

    setCss('background-color:rgba(150,255,150,.5)');

    return contains(mStyle.backgroundColor, 'rgba');
  };

  tests['hsla'] = function() {
    // Same as rgba(), in fact, browsers re-map hsla() to rgba() internally,
    //   except IE9 who retains it as hsla

    setCss('background-color:hsla(120,40%,100%,.5)');

    return contains(mStyle.backgroundColor, 'rgba') || contains(mStyle.backgroundColor, 'hsla');
  };

  tests['multiplebgs'] = function() {
    // Setting multiple images AND a color on the background shorthand property
    //  and then querying the style.background property value for the number of
    //  occurrences of "url(" is a reliable method for detecting ACTUAL support for this!

    setCss('background:url(https://),url(https://),red url(https://)');

    // If the UA supports multiple backgrounds, there should be three occurrences
    //   of the string "url(" in the return value for elemStyle.background

    return /(url\s*\(.*?){3}/.test(mStyle.background);
  };

  // this will false positive in Opera Mini
  //   github.com/Modernizr/Modernizr/issues/396

  tests['backgroundsize'] = function() {
    return testPropsAll('backgroundSize');
  };

  tests['borderimage'] = function() {
    return testPropsAll('borderImage');
  };

  // Super comprehensive table about all the unique implementations of
  // border-radius: muddledramblings.com/table-of-css3-border-radius-compliance

  tests['borderradius'] = function() {
    return testPropsAll('borderRadius');
  };

  // WebOS unfortunately false positives on this test.
  tests['boxshadow'] = function() {
    return testPropsAll('boxShadow');
  };

  // FF3.0 will false positive on this test
  tests['textshadow'] = function() {
    return document.createElement('div').style.textShadow === '';
  };

  tests['opacity'] = function() {
    // Browsers that actually have CSS Opacity implemented have done so
    //  according to spec, which means their return values are within the
    //  range of [0.0,1.0] - including the leading zero.

    setCssAll('opacity:.55');

    // The non-literal . in this regex is intentional:
    //   German Chrome returns this value as 0,55
    // github.com/Modernizr/Modernizr/issues/#issue/59/comment/516632
    return /^0.55$/.test(mStyle.opacity);
  };

  // Note, Android < 4 will pass this test, but can only animate
  //   a single property at a time
  //   goo.gl/v3V4Gp
  tests['cssanimations'] = function() {
    return testPropsAll('animationName');
  };

  tests['csscolumns'] = function() {
    return testPropsAll('columnCount');
  };

  tests['cssgradients'] = function() {
    /**
             * For CSS Gradients syntax, please see:
             * webkit.org/blog/175/introducing-css-gradients/
             * developer.mozilla.org/en/CSS/-moz-linear-gradient
             * developer.mozilla.org/en/CSS/-moz-radial-gradient
             * dev.w3.org/csswg/css3-images/#gradients-
             */

    var str1 = 'background-image:',
      str2 = 'gradient(linear,left top,right bottom,from(#9f9),to(white));',
      str3 = 'linear-gradient(left top,#9f9, white);';

    setCss(
      // legacy webkit syntax (FIXME: remove when syntax not in use anymore)
      (str1 +
        '-webkit- '.split(' ').join(str2 + str1) +
        // standard syntax             // trailing 'background-image:'
        prefixes.join(str3 + str1)).slice(0, -str1.length)
    );

    return contains(mStyle.backgroundImage, 'gradient');
  };

  tests['cssreflections'] = function() {
    return testPropsAll('boxReflect');
  };

  tests['csstransforms'] = function() {
    return !!testPropsAll('transform');
  };

  tests['csstransforms3d'] = function() {
    var ret = !!testPropsAll('perspective');

    // Webkit's 3D transforms are passed off to the browser's own graphics renderer.
    //   It works fine in Safari on Leopard and Snow Leopard, but not in Chrome in
    //   some conditions. As a result, Webkit typically recognizes the syntax but
    //   will sometimes throw a false positive, thus we must do a more thorough check:
    if (ret && 'webkitPerspective' in docElement.style) {
      // Webkit allows this media query to succeed only if the feature is enabled.
      // `@media (transform-3d),(-webkit-transform-3d){ ... }`
      injectElementWithStyles(
        '@media (transform-3d),(-webkit-transform-3d){#modernizr{left:9px;position:absolute;height:3px;}}',
        function(node, rule) {
          ret = node.offsetLeft === 9 && node.offsetHeight === 3;
        }
      );
    }
    return ret;
  };

  tests['csstransitions'] = function() {
    return testPropsAll('transition');
  };

  /*>>fontface*/
  // @font-face detection routine by Diego Perini
  // javascript.nwbox.com/CSSSupport/

  // false positives:
  //   WebOS github.com/Modernizr/Modernizr/issues/342
  //   WP7   github.com/Modernizr/Modernizr/issues/538
  tests['fontface'] = function() {
    var bool;

    injectElementWithStyles('@font-face {font-family:"font";src:url("https://")}', function(
      node,
      rule
    ) {
      var style = document.getElementById('smodernizr'),
        sheet = style.sheet || style.styleSheet,
        cssText = sheet
          ? sheet.cssRules && sheet.cssRules[0] ? sheet.cssRules[0].cssText : sheet.cssText || ''
          : '';

      bool = /src/i.test(cssText) && cssText.indexOf(rule.split(' ')[0]) === 0;
    });

    return bool;
  };
  /*>>fontface*/

  // CSS generated content detection
  tests['generatedcontent'] = function() {
    var bool;

    injectElementWithStyles(
      [
        '#',
        mod,
        '{font:0/0 a}#',
        mod,
        ':after{content:"',
        smile,
        '";visibility:hidden;font:3px/1 a}',
      ].join(''),
      function(node) {
        bool = node.offsetHeight >= 3;
      }
    );

    return bool;
  };

  // These tests evaluate support of the video/audio elements, as well as
  // testing what types of content they support.
  //
  // We're using the Boolean constructor here, so that we can extend the value
  // e.g.  Modernizr.video     // true
  //       Modernizr.video.ogg // 'probably'
  //
  // Codec values from : github.com/NielsLeenheer/html5test/blob/9106a8/index.html#L845
  //                     thx to NielsLeenheer and zcorpan

  // Note: in some older browsers, "no" was a return value instead of empty string.
  //   It was live in FF3.5.0 and 3.5.1, but fixed in 3.5.2
  //   It was also live in Safari 4.0.0 - 4.0.4, but fixed in 4.0.5

  tests['video'] = function() {
    var elem = document.createElement('video'),
      bool = false;

    // IE9 Running on Windows Server SKU can cause an exception to be thrown, bug #224
    try {
      if ((bool = !!elem.canPlayType)) {
        bool = new Boolean(bool);
        bool.ogg = elem.canPlayType('video/ogg; codecs="theora"').replace(/^no$/, '');

        // Without QuickTime, this value will be `undefined`. github.com/Modernizr/Modernizr/issues/546
        bool.h264 = elem.canPlayType('video/mp4; codecs="avc1.42E01E"').replace(/^no$/, '');

        bool.webm = elem.canPlayType('video/webm; codecs="vp8, vorbis"').replace(/^no$/, '');
      }
    } catch (e) {}

    return bool;
  };

  tests['audio'] = function() {
    var elem = document.createElement('audio'),
      bool = false;

    try {
      if ((bool = !!elem.canPlayType)) {
        bool = new Boolean(bool);
        bool.ogg = elem.canPlayType('audio/ogg; codecs="vorbis"').replace(/^no$/, '');
        bool.mp3 = elem.canPlayType('audio/mpeg;').replace(/^no$/, '');

        // Mimetypes accepted:
        //   developer.mozilla.org/En/Media_formats_supported_by_the_audio_and_video_elements
        //   bit.ly/iphoneoscodecs
        bool.wav = elem.canPlayType('audio/wav; codecs="1"').replace(/^no$/, '');
        bool.m4a = (elem.canPlayType('audio/x-m4a;') || elem.canPlayType('audio/aac;'))
          .replace(/^no$/, '');
      }
    } catch (e) {}

    return bool;
  };

  // In FF4, if disabled, window.localStorage should === null.

  // Normally, we could not test that directly and need to do a
  //   `('localStorage' in window) && ` test first because otherwise Firefox will
  //   throw bugzil.la/365772 if cookies are disabled

  // Also in iOS5 Private Browsing mode, attempting to use localStorage.setItem
  // will throw the exception:
  //   QUOTA_EXCEEDED_ERRROR DOM Exception 22.
  // Peculiarly, getItem and removeItem calls do not throw.

  // Because we are forced to try/catch this, we'll go aggressive.

  // Just FWIW: IE8 Compat mode supports these features completely:
  //   www.quirksmode.org/dom/html5.html
  // But IE8 doesn't support either with local files

  tests['localstorage'] = function() {
    try {
      localStorage.setItem(mod, mod);
      localStorage.removeItem(mod);
      return true;
    } catch (e) {
      return false;
    }
  };

  tests['sessionstorage'] = function() {
    try {
      sessionStorage.setItem(mod, mod);
      sessionStorage.removeItem(mod);
      return true;
    } catch (e) {
      return false;
    }
  };

  tests['webworkers'] = function() {
    return !!window.Worker;
  };

  tests['applicationcache'] = function() {
    return !!window.applicationCache;
  };

  // Thanks to Erik Dahlstrom
  tests['svg'] = function() {
    return !!document.createElementNS && !!document.createElementNS(ns.svg, 'svg').createSVGRect;
  };

  // specifically for SVG inline in HTML, not within XHTML
  // test page: paulirish.com/demo/inline-svg
  tests['inlinesvg'] = function() {
    var div = document.createElement('div');
    div.innerHTML = '<svg/>';
    return (div.firstChild && div.firstChild.namespaceURI) == ns.svg;
  };

  // SVG SMIL animation
  tests['smil'] = function() {
    return (
      !!document.createElementNS &&
      /SVGAnimate/.test(toString.call(document.createElementNS(ns.svg, 'animate')))
    );
  };

  // This test is only for clip paths in SVG proper, not clip paths on HTML content
  // demo: srufaculty.sru.edu/david.dailey/svg/newstuff/clipPath4.svg

  // However read the comments to dig into applying SVG clippaths to HTML content here:
  //   github.com/Modernizr/Modernizr/issues/213#issuecomment-1149491
  tests['svgclippaths'] = function() {
    return (
      !!document.createElementNS &&
      /SVGClipPath/.test(toString.call(document.createElementNS(ns.svg, 'clipPath')))
    );
  };

  /*>>webforms*/
  // input features and input types go directly onto the ret object, bypassing the tests loop.
  // Hold this guy to execute in a moment.
  function webforms() {
    /*>>input*/
    // Run through HTML5's new input attributes to see if the UA understands any.
    // We're using f which is the <input> element created early on
    // Mike Taylr has created a comprehensive resource for testing these attributes
    //   when applied to all input types:
    //   miketaylr.com/code/input-type-attr.html
    // spec: www.whatwg.org/specs/web-apps/current-work/multipage/the-input-element.html#input-type-attr-summary

    // Only input placeholder is tested while textarea's placeholder is not.
    // Currently Safari 4 and Opera 11 have support only for the input placeholder
    // Both tests are available in feature-detects/forms-placeholder.js
    Modernizr['input'] = (function(props) {
      for (var i = 0, len = props.length; i < len; i++) {
        attrs[props[i]] = !!(props[i] in inputElem);
      }
      if (attrs.list) {
        // safari false positive's on datalist: webk.it/74252
        // see also github.com/Modernizr/Modernizr/issues/146
        attrs.list = !!(document.createElement('datalist') && window.HTMLDataListElement);
      }
      return attrs;
    })('autocomplete autofocus list placeholder max min multiple pattern required step'.split(' '));
    /*>>input*/

    /*>>inputtypes*/
    // Run through HTML5's new input types to see if the UA understands any.
    //   This is put behind the tests runloop because it doesn't return a
    //   true/false like all the other tests; instead, it returns an object
    //   containing each input type with its corresponding true/false value

    // Big thanks to @miketaylr for the html5 forms expertise. miketaylr.com/
    Modernizr['inputtypes'] = (function(props) {
      for (var i = 0, bool, inputElemType, defaultView, len = props.length; i < len; i++) {
        inputElem.setAttribute('type', (inputElemType = props[i]));
        bool = inputElem.type !== 'text';

        // We first check to see if the type we give it sticks..
        // If the type does, we feed it a textual value, which shouldn't be valid.
        // If the value doesn't stick, we know there's input sanitization which infers a custom UI
        if (bool) {
          inputElem.value = smile;
          inputElem.style.cssText = 'position:absolute;visibility:hidden;';

          if (/^range$/.test(inputElemType) && inputElem.style.WebkitAppearance !== undefined) {
            docElement.appendChild(inputElem);
            defaultView = document.defaultView;

            // Safari 2-4 allows the smiley as a value, despite making a slider
            bool =
              defaultView.getComputedStyle &&
              defaultView.getComputedStyle(inputElem, null).WebkitAppearance !== 'textfield' &&
              // Mobile android web browser has false positive, so must
              // check the height to see if the widget is actually there.
              inputElem.offsetHeight !== 0;

            docElement.removeChild(inputElem);
          } else if (/^(search|tel)$/.test(inputElemType)) {
            // Spec doesn't define any special parsing or detectable UI
            //   behaviors so we pass these through as true
            // Interestingly, opera fails the earlier test, so it doesn't
            //  even make it here.
          } else if (/^(url|email)$/.test(inputElemType)) {
            // Real url and email support comes with prebaked validation.
            bool = inputElem.checkValidity && inputElem.checkValidity() === false;
          } else {
            // If the upgraded input compontent rejects the :) text, we got a winner
            bool = inputElem.value != smile;
          }
        }

        inputs[props[i]] = !!bool;
      }
      return inputs;
    })(
      'search tel url email datetime date month week time datetime-local number range color'.split(
        ' '
      )
    );
    /*>>inputtypes*/
  }
  /*>>webforms*/

  // End of test definitions
  // -----------------------

  // Run through all tests and detect their support in the current UA.
  // todo: hypothetically we could be doing an array of tests and use a basic loop here.
  for (var feature in tests) {
    if (hasOwnProp(tests, feature)) {
      // run the test, throw the return value into the Modernizr,
      //   then based on that boolean, define an appropriate className
      //   and push it into an array of classes we'll join later.
      featureName = feature.toLowerCase();
      Modernizr[featureName] = tests[feature]();

      classes.push((Modernizr[featureName] ? '' : 'no-') + featureName);
    }
  }

  /*>>webforms*/
  // input tests need to run.
  Modernizr.input || webforms();
  /*>>webforms*/

  /**
         * addTest allows the user to define their own feature tests
         * the result will be added onto the Modernizr object,
         * as well as an appropriate className set on the html element
         *
         * @param feature - String naming the feature
         * @param test - Function returning true if feature is supported, false if not
         */
  Modernizr.addTest = function(feature, test) {
    if (typeof feature == 'object') {
      for (var key in feature) {
        if (hasOwnProp(feature, key)) {
          Modernizr.addTest(key, feature[key]);
        }
      }
    } else {
      feature = feature.toLowerCase();

      if (Modernizr[feature] !== undefined) {
        // we're going to quit if you're trying to overwrite an existing test
        // if we were to allow it, we'd do this:
        //   var re = new RegExp("\\b(no-)?" + feature + "\\b");
        //   docElement.className = docElement.className.replace( re, '' );
        // but, no rly, stuff 'em.
        return Modernizr;
      }

      test = typeof test == 'function' ? test() : test;

      if (typeof enableClasses !== 'undefined' && enableClasses) {
        docElement.className += ' ' + (test ? '' : 'no-') + feature;
      }
      Modernizr[feature] = test;
    }

    return Modernizr; // allow chaining.
  };

  // Reset modElem.cssText to nothing to reduce memory footprint.
  setCss('');
  modElem = inputElem = null;

  /*>>shiv*/
  /**
         * @preserve HTML5 Shiv prev3.7.1 | @afarkas @jdalton @jon_neal @rem | MIT/GPL2 Licensed
         */
  (function(window, document) {
    /*jshint evil:true */
    /** version */
    var version = '3.7.0';

    /** Preset options */
    var options = window.html5 || {};

    /** Used to skip problem elements */
    var reSkip = /^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i;

    /** Not all elements can be cloned in IE **/
    var saveClones = /^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i;

    /** Detect whether the browser supports default html5 styles */
    var supportsHtml5Styles;

    /** Name of the expando, to work with multiple documents or to re-shiv one document */
    var expando = '_html5shiv';

    /** The id for the the documents expando */
    var expanID = 0;

    /** Cached data for each document */
    var expandoData = {};

    /** Detect whether the browser supports unknown elements */
    var supportsUnknownElements;

    (function() {
      try {
        var a = document.createElement('a');
        a.innerHTML = '<xyz></xyz>';
        //if the hidden property is implemented we can assume, that the browser supports basic HTML5 Styles
        supportsHtml5Styles = 'hidden' in a;

        supportsUnknownElements =
          a.childNodes.length == 1 ||
          (function() {
            // assign a false positive if unable to shiv
            document.createElement('a');
            var frag = document.createDocumentFragment();
            return (
              typeof frag.cloneNode == 'undefined' ||
              typeof frag.createDocumentFragment == 'undefined' ||
              typeof frag.createElement == 'undefined'
            );
          })();
      } catch (e) {
        // assign a false positive if detection fails => unable to shiv
        supportsHtml5Styles = true;
        supportsUnknownElements = true;
      }
    })();

    /*--------------------------------------------------------------------------*/

    /**
             * Creates a style sheet with the given CSS text and adds it to the document.
             * @private
             * @param {Document} ownerDocument The document.
             * @param {String} cssText The CSS text.
             * @returns {StyleSheet} The style element.
             */
    function addStyleSheet(ownerDocument, cssText) {
      var p = ownerDocument.createElement('p'),
        parent = ownerDocument.getElementsByTagName('head')[0] || ownerDocument.documentElement;

      p.innerHTML = 'x<style>' + cssText + '</style>';
      return parent.insertBefore(p.lastChild, parent.firstChild);
    }

    /**
             * Returns the value of `html5.elements` as an array.
             * @private
             * @returns {Array} An array of shived element node names.
             */
    function getElements() {
      var elements = html5.elements;
      return typeof elements == 'string' ? elements.split(' ') : elements;
    }

    /**
             * Returns the data associated to the given document
             * @private
             * @param {Document} ownerDocument The document.
             * @returns {Object} An object of data.
             */
    function getExpandoData(ownerDocument) {
      var data = expandoData[ownerDocument[expando]];
      if (!data) {
        data = {};
        expanID++;
        ownerDocument[expando] = expanID;
        expandoData[expanID] = data;
      }
      return data;
    }

    /**
             * returns a shived element for the given nodeName and document
             * @memberOf html5
             * @param {String} nodeName name of the element
             * @param {Document} ownerDocument The context document.
             * @returns {Object} The shived element.
             */
    function createElement(nodeName, ownerDocument, data) {
      if (!ownerDocument) {
        ownerDocument = document;
      }
      if (supportsUnknownElements) {
        return ownerDocument.createElement(nodeName);
      }
      if (!data) {
        data = getExpandoData(ownerDocument);
      }
      var node;

      if (data.cache[nodeName]) {
        node = data.cache[nodeName].cloneNode();
      } else if (saveClones.test(nodeName)) {
        node = (data.cache[nodeName] = data.createElem(nodeName)).cloneNode();
      } else {
        node = data.createElem(nodeName);
      }

      // Avoid adding some elements to fragments in IE < 9 because
      // * Attributes like `name` or `type` cannot be set/changed once an element
      //   is inserted into a document/fragment
      // * Link elements with `src` attributes that are inaccessible, as with
      //   a 403 response, will cause the tab/window to crash
      // * Script elements appended to fragments will execute when their `src`
      //   or `text` property is set
      return node.canHaveChildren && !reSkip.test(nodeName) && !node.tagUrn
        ? data.frag.appendChild(node)
        : node;
    }

    /**
             * returns a shived DocumentFragment for the given document
             * @memberOf html5
             * @param {Document} ownerDocument The context document.
             * @returns {Object} The shived DocumentFragment.
             */
    function createDocumentFragment(ownerDocument, data) {
      if (!ownerDocument) {
        ownerDocument = document;
      }
      if (supportsUnknownElements) {
        return ownerDocument.createDocumentFragment();
      }
      data = data || getExpandoData(ownerDocument);
      var clone = data.frag.cloneNode(),
        i = 0,
        elems = getElements(),
        l = elems.length;
      for (; i < l; i++) {
        clone.createElement(elems[i]);
      }
      return clone;
    }

    /**
             * Shivs the `createElement` and `createDocumentFragment` methods of the document.
             * @private
             * @param {Document|DocumentFragment} ownerDocument The document.
             * @param {Object} data of the document.
             */
    function shivMethods(ownerDocument, data) {
      if (!data.cache) {
        data.cache = {};
        data.createElem = ownerDocument.createElement;
        data.createFrag = ownerDocument.createDocumentFragment;
        data.frag = data.createFrag();
      }

      ownerDocument.createElement = function(nodeName) {
        //abort shiv
        if (!html5.shivMethods) {
          return data.createElem(nodeName);
        }
        return createElement(nodeName, ownerDocument, data);
      };

      ownerDocument.createDocumentFragment = Function(
        'h,f',
        'return function(){' +
          'var n=f.cloneNode(),c=n.createElement;' +
          'h.shivMethods&&(' +
          // unroll the `createElement` calls
          getElements().join().replace(/[\w\-]+/g, function(nodeName) {
            data.createElem(nodeName);
            data.frag.createElement(nodeName);
            return 'c("' + nodeName + '")';
          }) +
          ');return n}'
      )(html5, data.frag);
    }

    /*--------------------------------------------------------------------------*/

    /**
             * Shivs the given document.
             * @memberOf html5
             * @param {Document} ownerDocument The document to shiv.
             * @returns {Document} The shived document.
             */
    function shivDocument(ownerDocument) {
      if (!ownerDocument) {
        ownerDocument = document;
      }
      var data = getExpandoData(ownerDocument);

      if (html5.shivCSS && !supportsHtml5Styles && !data.hasCSS) {
        data.hasCSS = !!addStyleSheet(
          ownerDocument,
          // corrects block display not defined in IE6/7/8/9
          'article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}' +
            // adds styling not present in IE6/7/8/9
            'mark{background:#FF0;color:#000}' +
            // hides non-rendered elements
            'template{display:none}'
        );
      }
      if (!supportsUnknownElements) {
        shivMethods(ownerDocument, data);
      }
      return ownerDocument;
    }

    /*--------------------------------------------------------------------------*/

    /**
             * The `html5` object is exposed so that more elements can be shived and
             * existing shiving can be detected on iframes.
             * @type Object
             * @example
             *
             * // options can be changed before the script is included
             * html5 = { 'elements': 'mark section', 'shivCSS': false, 'shivMethods': false };
             */
    var html5 = {
      /**
               * An array or space separated string of node names of the elements to shiv.
               * @memberOf html5
               * @type Array|String
               */
      elements:
        options.elements ||
        'abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output progress section summary template time video',

      /**
               * current version of html5shiv
               */
      version: version,

      /**
               * A flag to indicate that the HTML5 style sheet should be inserted.
               * @memberOf html5
               * @type Boolean
               */
      shivCSS: options.shivCSS !== false,

      /**
               * Is equal to true if a browser supports creating unknown/HTML5 elements
               * @memberOf html5
               * @type boolean
               */
      supportsUnknownElements: supportsUnknownElements,

      /**
               * A flag to indicate that the document's `createElement` and `createDocumentFragment`
               * methods should be overwritten.
               * @memberOf html5
               * @type Boolean
               */
      shivMethods: options.shivMethods !== false,

      /**
               * A string to describe the type of `html5` object ("default" or "default print").
               * @memberOf html5
               * @type String
               */
      type: 'default',

      // shivs the document according to the specified `html5` object options
      shivDocument: shivDocument,

      //creates a shived element
      createElement: createElement,

      //creates a shived documentFragment
      createDocumentFragment: createDocumentFragment,
    };

    /*--------------------------------------------------------------------------*/

    // expose html5
    window.html5 = html5;

    // shiv the document
    shivDocument(document);
  })(this, document);
  /*>>shiv*/

  // Assign private properties to the return object with prefix
  Modernizr._version = version;

  // expose these for the plugin API. Look in the source for how to join() them against your input
  /*>>prefixes*/
  Modernizr._prefixes = prefixes;
  /*>>prefixes*/
  /*>>domprefixes*/
  Modernizr._domPrefixes = domPrefixes;
  Modernizr._cssomPrefixes = cssomPrefixes;
  /*>>domprefixes*/

  /*>>mq*/
  // Modernizr.mq tests a given media query, live against the current state of the window
  // A few important notes:
  //   * If a browser does not support media queries at all (eg. oldIE) the mq() will always return false
  //   * A max-width or orientation query will be evaluated against the current state, which may change later.
  //   * You must specify values. Eg. If you are testing support for the min-width media query use:
  //       Modernizr.mq('(min-width:0)')
  // usage:
  // Modernizr.mq('only screen and (max-width:768)')
  Modernizr.mq = testMediaQuery;
  /*>>mq*/

  /*>>hasevent*/
  // Modernizr.hasEvent() detects support for a given event, with an optional element to test on
  // Modernizr.hasEvent('gesturestart', elem)
  Modernizr.hasEvent = isEventSupported;
  /*>>hasevent*/

  /*>>testprop*/
  // Modernizr.testProp() investigates whether a given style property is recognized
  // Note that the property names must be provided in the camelCase variant.
  // Modernizr.testProp('pointerEvents')
  Modernizr.testProp = function(prop) {
    return testProps([prop]);
  };
  /*>>testprop*/

  /*>>testallprops*/
  // Modernizr.testAllProps() investigates whether a given style property,
  //   or any of its vendor-prefixed variants, is recognized
  // Note that the property names must be provided in the camelCase variant.
  // Modernizr.testAllProps('boxSizing')
  Modernizr.testAllProps = testPropsAll;
  /*>>testallprops*/

  /*>>teststyles*/
  // Modernizr.testStyles() allows you to add custom styles to the document and test an element afterwards
  // Modernizr.testStyles('#modernizr { position:absolute }', function(elem, rule){ ... })
  Modernizr.testStyles = injectElementWithStyles;
  /*>>teststyles*/

  /*>>prefixed*/
  // Modernizr.prefixed() returns the prefixed or nonprefixed property name variant of your input
  // Modernizr.prefixed('boxSizing') // 'MozBoxSizing'

  // Properties must be passed as dom-style camelcase, rather than `box-sizing` hypentated style.
  // Return values will also be the camelCase variant, if you need to translate that to hypenated style use:
  //
  //     str.replace(/([A-Z])/g, function(str,m1){ return '-' + m1.toLowerCase(); }).replace(/^ms-/,'-ms-');

  // If you're trying to ascertain which transition end event to bind to, you might do something like...
  //
  //     var transEndEventNames = {
  //       'WebkitTransition' : 'webkitTransitionEnd',
  //       'MozTransition'    : 'transitionend',
  //       'OTransition'      : 'oTransitionEnd',
  //       'msTransition'     : 'MSTransitionEnd',
  //       'transition'       : 'transitionend'
  //     },
  //     transEndEventName = transEndEventNames[ Modernizr.prefixed('transition') ];

  Modernizr.prefixed = function(prop, obj, elem) {
    if (!obj) {
      return testPropsAll(prop, 'pfx');
    } else {
      // Testing DOM property e.g. Modernizr.prefixed('requestAnimationFrame', window) // 'mozRequestAnimationFrame'
      return testPropsAll(prop, obj, elem);
    }
  };
  /*>>prefixed*/

  /*>>cssclasses*/
  // Remove "no-js" class from <html> element, if it exists:
  docElement.className =
    docElement.className.replace(/(^|\s)no-js(\s|$)/, '$1$2') +
    // Add the new classes to the <html> element.
    (enableClasses ? ' js ' + classes.join(' ') : '');
  /*>>cssclasses*/

  return Modernizr;
})(this, this.document);

// a[download] attribute
// When used on an <a>, this attribute signifies that the resource it
// points to should be downloaded by the browser rather than navigating to it.
// http://developers.whatwg.org/links.html#downloading-resources
// By Addy Osmani

Modernizr.addTest('adownload', 'download' in document.createElement('a'));
// Mozilla Audio Data API
// https://wiki.mozilla.org/Audio_Data_API
// by Addy Osmani
Modernizr.addTest('audiodata', !!window.Audio);
// Web Audio API
// https://dvcs.w3.org/hg/audio/raw-file/tip/webaudio/specification.html
// By Addy Osmani
Modernizr.addTest('webaudio', !!(window.webkitAudioContext || window.AudioContext));

// Battery API
// https://developer.mozilla.org/en/DOM/window.navigator.mozBattery
// By: Paul Sayre

Modernizr.addTest('battery', !!Modernizr.prefixed('battery', navigator));
// Low Battery Level
// Enable a developer to remove CPU intensive CSS/JS when battery is low
// developer.mozilla.org/en/DOM/window.navigator.mozBattery
// By: Paul Sayre

Modernizr.addTest('lowbattery', function() {
  var minLevel = 0.2,
    battery = Modernizr.prefixed('battery', navigator);
  return !!(battery && !battery.charging && battery.level <= minLevel);
});
// Blob constructor
// http://dev.w3.org/2006/webapi/FileAPI/#constructorBlob

Modernizr.addTest('blobconstructor', function() {
  try {
    return !!new Blob();
  } catch (e) {
    return false;
  }
});
// canvas.toDataURL type support
// http://www.w3.org/TR/html5/the-canvas-element.html#dom-canvas-todataurl

// This test is asynchronous. Watch out.

(function() {
  if (!Modernizr.canvas) {
    return false;
  }

  var image = new Image(),
    canvas = document.createElement('canvas'),
    ctx = canvas.getContext('2d');

  image.onload = function() {
    ctx.drawImage(image, 0, 0);

    Modernizr.addTest('todataurljpeg', function() {
      return canvas.toDataURL('image/jpeg').indexOf('data:image/jpeg') === 0;
    });
    Modernizr.addTest('todataurlwebp', function() {
      return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
    });
  };

  image.src =
    'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQABDQottAAAAABJRU5ErkJggg==';
})();
// contentEditable
// http://www.whatwg.org/specs/web-apps/current-work/multipage/editing.html#contenteditable

// this is known to false positive in some mobile browsers
// here is a whitelist of verified working browsers:
// https://github.com/NielsLeenheer/html5test/blob/549f6eac866aa861d9649a0707ff2c0157895706/scripts/engine.js#L2083

Modernizr.addTest('contenteditable', 'contentEditable' in document.documentElement);
// Test for (experimental) Content Security Policy 1.1 support.
//
// This feature is still quite experimental, but is available now in Chrome 22.
// If the `SecurityPolicy` property is available, you can be sure the browser
// supports CSP. If it's not available, the browser still might support an
// earlier version of the CSP spec.
//
// Editor's Draft: https://dvcs.w3.org/hg/content-security-policy/raw-file/tip/csp-specification.dev.html

Modernizr.addTest(
  'contentsecuritypolicy',
  'securityPolicy' in document || 'SecurityPolicy' in document
);
// http://www.w3.org/TR/html5/interactive-elements.html#context-menus
// Demo at http://thewebrocks.com/demos/context-menu/
Modernizr.addTest(
  'contextmenu',
  'contextMenu' in document.documentElement && 'HTMLMenuItemElement' in window
);

// by tauren
// https://github.com/Modernizr/Modernizr/issues/191

Modernizr.addTest('cookies', function() {
  // Quick test if browser has cookieEnabled host property
  if (navigator.cookieEnabled) return true;
  // Create cookie
  document.cookie = 'cookietest=1';
  var ret = document.cookie.indexOf('cookietest=') != -1;
  // Delete cookie
  document.cookie = 'cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT';
  return ret;
});

// cors
// By Theodoor van Donge
Modernizr.addTest(
  'cors',
  !!(window.XMLHttpRequest && 'withCredentials' in new XMLHttpRequest())
); /*
      https://developer.mozilla.org/en/CSS/background-position
      http://www.w3.org/TR/css3-background/#background-position

      Example: http://jsfiddle.net/Blink/bBXvt/
  */

(function() {
  var elem = document.createElement('a'),
    eStyle = elem.style,
    val = 'right 10px bottom 10px';

  Modernizr.addTest('bgpositionshorthand', function() {
    eStyle.cssText = 'background-position: ' + val + ';';
    return eStyle.backgroundPosition === val;
  });
})();
/*
      Allan Lei https://github.com/allanlei

      Check adapted from https://github.com/brandonaaron/jquery-cssHooks/blob/master/bgpos.js

      Test: http://jsfiddle.net/allanlei/R8AYS/
  */
Modernizr.addTest('bgpositionxy', function() {
  return Modernizr.testStyles('#modernizr {background-position: 3px 5px;}', function(elem) {
    var cssStyleDeclaration = window.getComputedStyle
      ? getComputedStyle(elem, null)
      : elem.currentStyle;
    var xSupport =
      cssStyleDeclaration.backgroundPositionX == '3px' ||
      cssStyleDeclaration['background-position-x'] == '3px';
    var ySupport =
      cssStyleDeclaration.backgroundPositionY == '5px' ||
      cssStyleDeclaration['background-position-y'] == '5px';
    return xSupport && ySupport;
  });
}); // developer.mozilla.org/en/CSS/background-repeat

// test page: jsbin.com/uzesun/
// http://jsfiddle.net/ryanseddon/yMLTQ/6/

(function() {
  function getBgRepeatValue(elem) {
    return window.getComputedStyle
      ? getComputedStyle(elem, null).getPropertyValue('background')
      : elem.currentStyle['background'];
  }

  Modernizr.testStyles(' #modernizr { background-repeat: round; } ', function(elem, rule) {
    Modernizr.addTest('bgrepeatround', getBgRepeatValue(elem) == 'round');
  });

  Modernizr.testStyles(' #modernizr { background-repeat: space; } ', function(elem, rule) {
    Modernizr.addTest('bgrepeatspace', getBgRepeatValue(elem) == 'space');
  });
})();

// developer.mozilla.org/en/CSS/background-size

Modernizr.testStyles('#modernizr{background-size:cover}', function(elem) {
  var style = window.getComputedStyle ? window.getComputedStyle(elem, null) : elem.currentStyle;

  Modernizr.addTest('bgsizecover', style.backgroundSize == 'cover');
});
// developer.mozilla.org/en/CSS/box-sizing
// github.com/Modernizr/Modernizr/issues/248

Modernizr.addTest('boxsizing', function() {
  return (
    Modernizr.testAllProps('boxSizing') &&
    (document.documentMode === undefined || document.documentMode > 7)
  );
});

// Method of allowing calculated values for length units, i.e. width: calc(100%-3em) http://caniuse.com/#search=calc
// By @calvein

Modernizr.addTest('csscalc', function() {
  var prop = 'width:';
  var value = 'calc(10px);';
  var el = document.createElement('div');

  el.style.cssText = prop + Modernizr._prefixes.join(value + prop);

  return !!el.style.length;
});
// cubic-bezier values can't be > 1 for Webkit until bug #45761 (https://bugs.webkit.org/show_bug.cgi?id=45761) is fixed
// By @calvein

Modernizr.addTest('cubicbezierrange', function() {
  var el = document.createElement('div');
  el.style.cssText = Modernizr._prefixes.join(
    'transition-timing-function' + ':cubic-bezier(1,0,0,1.1); '
  );
  return !!el.style.length;
});

// by alanhogan

// https://github.com/Modernizr/Modernizr/issues/198
// http://css-tricks.com/596-run-in/

Modernizr.testStyles(' #modernizr { display: run-in; } ', function(elem, rule) {
  var ret = window.getComputedStyle
    ? getComputedStyle(elem, null).getPropertyValue('display')
    : elem.currentStyle['display'];

  Modernizr.addTest('display-runin', ret == 'run-in');
});

// display: table and table-cell test. (both are tested under one name "table-cell" )
// By @scottjehl

// all additional table display values are here: http://pastebin.com/Gk9PeVaQ though Scott has seen some IE false positives with that sort of weak detection.
// more testing neccessary perhaps.

Modernizr.addTest('display-table', function() {
  var doc = window.document,
    docElem = doc.documentElement,
    parent = doc.createElement('div'),
    child = doc.createElement('div'),
    childb = doc.createElement('div'),
    ret;

  parent.style.cssText = 'display: table';
  child.style.cssText = childb.style.cssText = 'display: table-cell; padding: 10px';

  parent.appendChild(child);
  parent.appendChild(childb);
  docElem.insertBefore(parent, docElem.firstChild);

  ret = child.offsetLeft < childb.offsetLeft;
  docElem.removeChild(parent);
  return ret;
});

// https://github.com/Modernizr/Modernizr/issues/615
// documentMode is needed for false positives in oldIE, please see issue above
Modernizr.addTest('cssfilters', function() {
  var el = document.createElement('div');
  el.style.cssText = Modernizr._prefixes.join('filter' + ':blur(2px); ');
  return !!el.style.length && (document.documentMode === undefined || document.documentMode > 9);
}); /* see http://davidnewton.ca/the-current-state-of-hyphenation-on-the-web
     http://davidnewton.ca/demos/hyphenation/test.html


  There are three tests:
     1. csshyphens      - tests hyphens:auto actually adds hyphens to text
     2. softhyphens     - tests that &shy; does its job
     3. softhyphensfind - tests that in-browser Find functionality still works correctly with &shy;

  These tests currently require document.body to be present

  Hyphenation is language specific, sometimes.
    See for more details: http://code.google.com/p/hyphenator/source/diff?spec=svn975&r=975&format=side&path=/trunk/Hyphenator.js#sc_svn975_313

  If loading Hyphenator.js via Modernizr.load, be cautious of issue 158: http://code.google.com/p/hyphenator/issues/detail?id=158

  More details at https://github.com/Modernizr/Modernizr/issues/312

  */

(function() {
  if (!document.body) {
    window.console && console.warn("document.body doesn't exist. Modernizr hyphens test needs it.");
    return;
  }

  // functional test of adding hyphens:auto
  function test_hyphens_css() {
    try {
      /* create a div container and a span within that
               * these have to be appended to document.body, otherwise some browsers can give false negative */
      var div = document.createElement('div'),
        span = document.createElement('span'),
        divStyle = div.style,
        spanHeight = 0,
        spanWidth = 0,
        result = false,
        firstChild = document.body.firstElementChild || document.body.firstChild;

      div.appendChild(span);
      span.innerHTML =
        'Bacon ipsum dolor sit amet jerky velit in culpa hamburger et. Laborum dolor proident, enim dolore duis commodo et strip steak. Salami anim et, veniam consectetur dolore qui tenderloin jowl velit sirloin. Et ad culpa, fatback cillum jowl ball tip ham hock nulla short ribs pariatur aute. Pig pancetta ham bresaola, ut boudin nostrud commodo flank esse cow tongue culpa. Pork belly bresaola enim pig, ea consectetur nisi. Fugiat officia turkey, ea cow jowl pariatur ullamco proident do laborum velit sausage. Magna biltong sint tri-tip commodo sed bacon, esse proident aliquip. Ullamco ham sint fugiat, velit in enim sed mollit nulla cow ut adipisicing nostrud consectetur. Proident dolore beef ribs, laborum nostrud meatball ea laboris rump cupidatat labore culpa. Shankle minim beef, velit sint cupidatat fugiat tenderloin pig et ball tip. Ut cow fatback salami, bacon ball tip et in shank strip steak bresaola. In ut pork belly sed mollit tri-tip magna culpa veniam, short ribs qui in andouille ham consequat. Dolore bacon t-bone, velit short ribs enim strip steak nulla. Voluptate labore ut, biltong swine irure jerky. Cupidatat excepteur aliquip salami dolore. Ball tip strip steak in pork dolor. Ad in esse biltong. Dolore tenderloin exercitation ad pork loin t-bone, dolore in chicken ball tip qui pig. Ut culpa tongue, sint ribeye dolore ex shank voluptate hamburger. Jowl et tempor, boudin pork chop labore ham hock drumstick consectetur tri-tip elit swine meatball chicken ground round. Proident shankle mollit dolore. Shoulder ut duis t-bone quis reprehenderit. Meatloaf dolore minim strip steak, laboris ea aute bacon beef ribs elit shank in veniam drumstick qui. Ex laboris meatball cow tongue pork belly. Ea ball tip reprehenderit pig, sed fatback boudin dolore flank aliquip laboris eu quis. Beef ribs duis beef, cow corned beef adipisicing commodo nisi deserunt exercitation. Cillum dolor t-bone spare ribs, ham hock est sirloin. Brisket irure meatloaf in, boudin pork belly sirloin ball tip. Sirloin sint irure nisi nostrud aliqua. Nostrud nulla aute, enim officia culpa ham hock. Aliqua reprehenderit dolore sunt nostrud sausage, ea boudin pork loin ut t-bone ham tempor. Tri-tip et pancetta drumstick laborum. Ham hock magna do nostrud in proident. Ex ground round fatback, venison non ribeye in.';

      document.body.insertBefore(div, firstChild);

      /* get size of unhyphenated text */
      divStyle.cssText =
        'position:absolute;top:0;left:0;width:5em;text-align:justify;text-justification:newspaper;';
      spanHeight = span.offsetHeight;
      spanWidth = span.offsetWidth;

      /* compare size with hyphenated text */
      divStyle.cssText =
        'position:absolute;top:0;left:0;width:5em;text-align:justify;' +
        'text-justification:newspaper;' +
        Modernizr._prefixes.join('hyphens:auto; ');

      result = span.offsetHeight != spanHeight || span.offsetWidth != spanWidth;

      /* results and cleanup */
      document.body.removeChild(div);
      div.removeChild(span);

      return result;
    } catch (e) {
      return false;
    }
  }

  // for the softhyphens test
  function test_hyphens(delimiter, testWidth) {
    try {
      /* create a div container and a span within that
               * these have to be appended to document.body, otherwise some browsers can give false negative */
      var div = document.createElement('div'),
        span = document.createElement('span'),
        divStyle = div.style,
        spanSize = 0,
        result = false,
        result1 = false,
        result2 = false,
        firstChild = document.body.firstElementChild || document.body.firstChild;

      divStyle.cssText = 'position:absolute;top:0;left:0;overflow:visible;width:1.25em;';
      div.appendChild(span);
      document.body.insertBefore(div, firstChild);

      /* get height of unwrapped text */
      span.innerHTML = 'mm';
      spanSize = span.offsetHeight;

      /* compare height w/ delimiter, to see if it wraps to new line */
      span.innerHTML = 'm' + delimiter + 'm';
      result1 = span.offsetHeight > spanSize;

      /* if we're testing the width too (i.e. for soft-hyphen, not zws),
               * this is because tested Blackberry devices will wrap the text but not display the hyphen */
      if (testWidth) {
        /* get width of wrapped, non-hyphenated text */
        span.innerHTML = 'm<br />m';
        spanSize = span.offsetWidth;

        /* compare width w/ wrapped w/ delimiter to see if hyphen is present */
        span.innerHTML = 'm' + delimiter + 'm';
        result2 = span.offsetWidth > spanSize;
      } else {
        result2 = true;
      }

      /* results and cleanup */
      if (result1 === true && result2 === true) {
        result = true;
      }
      document.body.removeChild(div);
      div.removeChild(span);

      return result;
    } catch (e) {
      return false;
    }
  }

  // testing if in-browser Find functionality will work on hyphenated text
  function test_hyphens_find(delimiter) {
    return true;
  }

  Modernizr.addTest('csshyphens', function() {
    if (!Modernizr.testAllProps('hyphens')) return false;

    /* Chrome lies about its hyphens support so we need a more robust test
                  crbug.com/107111
          */
    try {
      return test_hyphens_css();
    } catch (e) {
      return false;
    }
  });

  Modernizr.addTest('softhyphens', function() {
    try {
      // use numeric entity instead of &shy; in case it's XHTML
      return test_hyphens('&#173;', true) && test_hyphens('&#8203;', false);
    } catch (e) {
      return false;
    }
  });

  Modernizr.addTest('softhyphensfind', function() {
    try {
      return test_hyphens_find('&#173;') && test_hyphens_find('&#8203;');
    } catch (e) {
      return false;
    }
  });
})();
// last-child pseudo selector
// https://github.com/Modernizr/Modernizr/pull/304

Modernizr.addTest('lastchild', function() {
  return Modernizr.testStyles(
    '#modernizr div {width:100px} #modernizr :last-child{width:200px;display:block}',
    function(elem) {
      return elem.lastChild.offsetWidth > elem.firstChild.offsetWidth;
    },
    2
  );
});
// this tests passes for webkit's proprietary `-webkit-mask` feature
//   www.webkit.org/blog/181/css-masks/
//   developer.apple.com/library/safari/#documentation/InternetWeb/Conceptual/SafariVisualEffectsProgGuide/Masks/Masks.html

// it does not pass mozilla's implementation of `mask` for SVG

//   developer.mozilla.org/en/CSS/mask
//   developer.mozilla.org/En/Applying_SVG_effects_to_HTML_content

// Can combine with clippaths for awesomeness: http://generic.cx/for/webkit/test.html

Modernizr.addTest('cssmask', Modernizr.testAllProps('maskRepeat'));

Modernizr.addTest('mediaqueries', Modernizr.mq('only all'));
// dev.opera.com/articles/view/css3-object-fit-object-position/

Modernizr.addTest('object-fit', !!Modernizr.prefixed('objectFit'));
// johanbrook.com/browsers/native-momentum-scrolling-ios-5/
// introduced in iOS5b2. Possible API may change...

Modernizr.addTest('overflowscrolling', function() {
  return Modernizr.testAllProps('overflowScrolling');
});

// developer.mozilla.org/en/CSS/pointer-events

// Test and project pages:
// ausi.github.com/Feature-detection-technique-for-pointer-events/
// github.com/ausi/Feature-detection-technique-for-pointer-events/wiki
// github.com/Modernizr/Modernizr/issues/80

Modernizr.addTest('pointerevents', function() {
  var element = document.createElement('x'),
    documentElement = document.documentElement,
    getComputedStyle = window.getComputedStyle,
    supports;
  if (!('pointerEvents' in element.style)) {
    return false;
  }
  element.style.pointerEvents = 'auto';
  element.style.pointerEvents = 'x';
  documentElement.appendChild(element);
  supports = getComputedStyle && getComputedStyle(element, '').pointerEvents === 'auto';
  documentElement.removeChild(element);
  return !!supports;
});
// Sticky positioning - constrains an element to be positioned inside the
// intersection of its container box, and the viewport.
Modernizr.addTest('csspositionsticky', function() {
  var prop = 'position:';
  var value = 'sticky';
  var el = document.createElement('modernizr');
  var mStyle = el.style;

  mStyle.cssText = prop + Modernizr._prefixes.join(value + ';' + prop).slice(0, -prop.length);

  return mStyle.position.indexOf(value) !== -1;
});
// CSS Regions
// http://www.w3.org/TR/css3-regions/
// By: Mihai Balan

// We start with a CSS parser test then we check page geometry to see if it's affected by regions
// Later we might be able to retire the second part, as WebKit builds with the false positives die out

Modernizr.addTest('regions', function() {
  /* Get the 'flowFrom' property name available in the browser. Either default or vendor prefixed.
      If the property name can't be found we'll get Boolean 'false' and fail quickly */
  var flowFromProperty = Modernizr.prefixed('flowFrom'),
    flowIntoProperty = Modernizr.prefixed('flowInto');

  if (!flowFromProperty || !flowIntoProperty) {
    return false;
  }

  /* If CSS parsing is there, try to determine if regions actually work. */
  var container = document.createElement('div'),
    content = document.createElement('div'),
    region = document.createElement('div'),
    /* we create a random, unlikely to be generated flow number to make sure we don't
      clash with anything more vanilla, like 'flow', or 'article', or 'f1' */
    flowName = 'modernizr_flow_for_regions_check';

  /* First create a div with two adjacent divs inside it. The first will be the
      content, the second will be the region. To be able to distinguish between the two,
      we'll give the region a particular padding */
  content.innerText = 'M';
  container.style.cssText = 'top: 150px; left: 150px; padding: 0px;';
  region.style.cssText = 'width: 50px; height: 50px; padding: 42px;';

  region.style[flowFromProperty] = flowName;
  container.appendChild(content);
  container.appendChild(region);
  document.documentElement.appendChild(container);

  /* Now compute the bounding client rect, before and after attempting to flow the
      content div in the region div. If regions are enabled, the after bounding rect
      should reflect the padding of the region div.*/
  var flowedRect,
    delta,
    plainRect = content.getBoundingClientRect();

  content.style[flowIntoProperty] = flowName;
  flowedRect = content.getBoundingClientRect();

  delta = flowedRect.left - plainRect.left;
  document.documentElement.removeChild(container);
  content = region = container = undefined;

  return delta == 42;
});

// test by github.com/nsfmc

// "The 'rem' unit ('root em') is relative to the computed
// value of the 'font-size' value of the root element."
// http://www.w3.org/TR/css3-values/#relative0
// you can test by checking if the prop was ditched

// http://snook.ca/archives/html_and_css/font-size-with-rem

Modernizr.addTest('cssremunit', function() {
  var div = document.createElement('div');
  try {
    div.style.fontSize = '3rem';
  } catch (er) {}
  return /rem/.test(div.style.fontSize);
});

// Test for CSS 3 UI "resize" property
// http://www.w3.org/TR/css3-ui/#resize
// https://developer.mozilla.org/en/CSS/resize

Modernizr.addTest('cssresize', Modernizr.testAllProps('resize'));

// Stylable scrollbars detection
Modernizr.addTest('cssscrollbar', function() {
  var bool,
    styles =
      '#modernizr{overflow: scroll; width: 40px }#' +
      Modernizr._prefixes
        .join('scrollbar{width:0px}' + ' #modernizr::')
        .split('#')
        .slice(1)
        .join('#') +
      'scrollbar{width:0px}';

  Modernizr.testStyles(styles, function(node) {
    bool = 'scrollWidth' in node && node.scrollWidth == 40;
  });

  return bool;
});

// http://html.adobe.com/webplatform/layout/shapes

Modernizr.addTest('shapes', Modernizr.testAllProps('shapeOutside', 'content-box', true));
/*
   * Test for SubPixel Font Rendering
   * (to infer if GDI or DirectWrite is used on Windows)
   * Authors: @derSchepp, @gerritvanaaken, @rodneyrehm, @yatil, @ryanseddon
   * Web: https://github.com/gerritvanaaken/subpixeldetect
   */
Modernizr.addTest('subpixelfont', function() {
  var bool,
    styles =
      '#modernizr{position: absolute; top: -10em; visibility:hidden; font: normal 10px arial;}#subpixel{float: left; font-size: 33.3333%;}';

  // see https://github.com/Modernizr/Modernizr/blob/master/modernizr.js#L97
  Modernizr.testStyles(
    styles,
    function(elem) {
      var subpixel = elem.firstChild;

      subpixel.innerHTML = 'This is a text written in Arial';

      bool = window.getComputedStyle
        ? window.getComputedStyle(subpixel, null).getPropertyValue('width') !== '44px'
        : false;
    },
    1,
    ['subpixel']
  );

  return bool;
});
// http://dev.w3.org/csswg/css3-conditional/#at-supports
// github.com/Modernizr/Modernizr/issues/648
// Relies on the fact that a browser vendor should expose the CSSSupportsRule interface
// http://dev.w3.org/csswg/css3-conditional/#the-csssupportsrule-interface

Modernizr.addTest('supports', 'CSSSupportsRule' in window); // -moz-user-select:none test.

// by ryan seddon
//https://github.com/Modernizr/Modernizr/issues/250

Modernizr.addTest('userselect', function() {
  return Modernizr.testAllProps('user-select');
});

// https://github.com/Modernizr/Modernizr/issues/572
// Similar to http://jsfiddle.net/FWeinb/etnYC/
Modernizr.addTest('cssvhunit', function() {
  var bool;
  Modernizr.testStyles('#modernizr { height: 50vh; }', function(elem, rule) {
    var height = parseInt(window.innerHeight / 2, 10),
      compStyle = parseInt(
        (window.getComputedStyle ? getComputedStyle(elem, null) : elem.currentStyle)['height'],
        10
      );

    bool = compStyle == height;
  });
  return bool;
}); // https://github.com/Modernizr/Modernizr/issues/572
// http://jsfiddle.net/glsee/JDsWQ/4/
Modernizr.addTest('cssvmaxunit', function() {
  var bool;
  Modernizr.testStyles('#modernizr { width: 50vmax; }', function(elem, rule) {
    var one_vw = window.innerWidth / 100,
      one_vh = window.innerHeight / 100,
      compWidth = parseInt(
        (window.getComputedStyle ? getComputedStyle(elem, null) : elem.currentStyle)['width'],
        10
      );
    bool = parseInt(Math.max(one_vw, one_vh) * 50, 10) == compWidth;
  });
  return bool;
}); // https://github.com/Modernizr/Modernizr/issues/572
// http://jsfiddle.net/glsee/JRmdq/8/
Modernizr.addTest('cssvminunit', function() {
  var bool;
  Modernizr.testStyles('#modernizr { width: 50vmin; }', function(elem, rule) {
    var one_vw = window.innerWidth / 100,
      one_vh = window.innerHeight / 100,
      compWidth = parseInt(
        (window.getComputedStyle ? getComputedStyle(elem, null) : elem.currentStyle)['width'],
        10
      );
    bool = parseInt(Math.min(one_vw, one_vh) * 50, 10) == compWidth;
  });
  return bool;
});
// https://github.com/Modernizr/Modernizr/issues/572
// http://jsfiddle.net/FWeinb/etnYC/
Modernizr.addTest('cssvwunit', function() {
  var bool;
  Modernizr.testStyles('#modernizr { width: 50vw; }', function(elem, rule) {
    var width = parseInt(window.innerWidth / 2, 10),
      compStyle = parseInt(
        (window.getComputedStyle ? getComputedStyle(elem, null) : elem.currentStyle)['width'],
        10
      );

    bool = compStyle == width;
  });
  return bool;
});
/*
      Custom protocol handler support
      http://developers.whatwg.org/timers.html#custom-handlers

      Added by @benschwarz
  */

Modernizr.addTest('customprotocolhandler', function() {
  return !!navigator.registerProtocolHandler;
});
// Dart
// By Theodoor van Donge

// https://chromiumcodereview.appspot.com/9232049/

Modernizr.addTest('dart', !!Modernizr.prefixed('startDart', navigator));
// DataView
// https://developer.mozilla.org/en/JavaScript_typed_arrays/DataView
// By Addy Osmani
Modernizr.addTest(
  'dataview',
  typeof DataView !== 'undefined' && 'getFloat64' in DataView.prototype
); // classList
// https://developer.mozilla.org/en/DOM/element.classList
// By Addy Osmani
Modernizr.addTest('classlist', 'classList' in document.documentElement);
// by james a rosen.
// https://github.com/Modernizr/Modernizr/issues/258

Modernizr.addTest('createelement-attrs', function() {
  try {
    return document.createElement("<input name='test' />").getAttribute('name') == 'test';
  } catch (e) {
    return false;
  }
});

// dataset API for data-* attributes
// test by @phiggins42

Modernizr.addTest('dataset', function() {
  var n = document.createElement('div');
  n.setAttribute('data-a-b', 'c');
  return !!(n.dataset && n.dataset.aB === 'c');
});
// Microdata support
// http://www.w3.org/TR/html5/microdata.html
// By Addy Osmani
Modernizr.addTest('microdata', !!document['getItems']);

// lol. we already have a test for datalist built in! silly you.

// Helpful links while you're here, though..

// http://css-tricks.com/15346-relevant-dropdowns-polyfill-for-datalist/
// http://miketaylr.com/test/datalist.html
// http://miketaylr.com/code/datalist.html

Modernizr.addTest('datalistelem', Modernizr.input.list);
// By @mathias, based on http://mths.be/axh
Modernizr.addTest('details', function() {
  var doc = document,
    el = doc.createElement('details'),
    fake,
    root,
    diff;
  if (!('open' in el)) {
    // return early if possible; thanks @aFarkas!
    return false;
  }
  root =
    doc.body ||
    (function() {
      var de = doc.documentElement;
      fake = true;
      return de.insertBefore(doc.createElement('body'), de.firstElementChild || de.firstChild);
    })();
  el.innerHTML = '<summary>a</summary>b';
  el.style.display = 'block';
  root.appendChild(el);
  diff = el.offsetHeight;
  el.open = true;
  diff = diff != el.offsetHeight;
  root.removeChild(el);
  fake && root.parentNode.removeChild(root);
  return diff;
}); // <output>
// http://www.whatwg.org/specs/web-apps/current-work/multipage/the-button-element.html#the-output-element
// by Addy Osmani
Modernizr.addTest('outputelem', 'value' in document.createElement('output'));
//By Stefan Wallin

//tests for progressbar-support. All browsers that don't support progressbar returns undefined =)
Modernizr.addTest('progressbar', function() {
  return document.createElement('progress').max !== undefined;
});

//tests for meter-support. All browsers that don't support meters returns undefined =)
Modernizr.addTest('meter', function() {
  return document.createElement('meter').max !== undefined;
});
// Browser support test for the HTML5 <ruby>, <rt> and <rp> elements
// http://www.whatwg.org/specs/web-apps/current-work/multipage/text-level-semantics.html#the-ruby-element
//
// by @alrra

Modernizr.addTest('ruby', function() {
  var ruby = document.createElement('ruby'),
    rt = document.createElement('rt'),
    rp = document.createElement('rp'),
    docElement = document.documentElement,
    displayStyleProperty = 'display',
    fontSizeStyleProperty = 'fontSize'; // 'fontSize' - because it`s only used for IE6 and IE7

  ruby.appendChild(rp);
  ruby.appendChild(rt);
  docElement.appendChild(ruby);

  // browsers that support <ruby> hide the <rp> via "display:none"
  if (
    getStyle(rp, displayStyleProperty) == 'none' || // for non-IE browsers
    // but in IE browsers <rp> has "display:inline" so, the test needs other conditions:
    (getStyle(ruby, displayStyleProperty) == 'ruby' &&
      getStyle(rt, displayStyleProperty) == 'ruby-text') || // for IE8 & IE9
    (getStyle(rp, fontSizeStyleProperty) == '6pt' && getStyle(rt, fontSizeStyleProperty) == '6pt')
  ) {
    // for IE6 & IE7

    cleanUp();
    return true;
  } else {
    cleanUp();
    return false;
  }

  function getStyle(element, styleProperty) {
    var result;

    if (window.getComputedStyle) {
      // for non-IE browsers
      result = document.defaultView.getComputedStyle(element, null).getPropertyValue(styleProperty);
    } else if (element.currentStyle) {
      // for IE
      result = element.currentStyle[styleProperty];
    }

    return result;
  }

  function cleanUp() {
    docElement.removeChild(ruby);
    // the removed child node still exists in memory, so ...
    ruby = null;
    rt = null;
    rp = null;
  }
});
// <time> element
// http://www.whatwg.org/specs/web-apps/current-work/multipage/rendering.html#the-time-element-0
// by Addy Osmani
Modernizr.addTest('time', 'valueAsDate' in document.createElement('time'));
// Track element + Timed Text Track API
// http://www.w3.org/TR/html5/video.html#the-track-element
// http://www.w3.org/TR/html5/media-elements.html#text-track-api
//
// While IE10 has implemented the track element, IE10 does not expose the underlying APIs to create timed text tracks by JS (really sad)
// By Addy Osmani
Modernizr.addTest({
  texttrackapi: typeof document.createElement('video').addTextTrack === 'function',
  // a more strict test for track including UI support: document.createElement('track').kind === 'subtitles'
  track: 'kind' in document.createElement('track'),
});
// Requires a Modernizr build with `canvastext` included
// http://www.modernizr.com/download/#-canvas-canvastext
Modernizr.addTest('emoji', function() {
  if (!Modernizr.canvastext) return false;
  var node = document.createElement('canvas'),
    ctx = node.getContext('2d');
  ctx.textBaseline = 'top';
  ctx.font = '32px Arial';
  ctx.fillText('\ud83d\ude03', 0, 0); // "smiling face with open mouth" emoji
  return ctx.getImageData(16, 16, 1, 1).data[0] !== 0;
}); // strict mode

// test by @kangax

Modernizr.addTest('strictmode', function() {
  return (function() {
    'use strict';
    return !this;
  })();
}); //By Shi Chuan
//Part of Device Access aspect of HTML5, same category as geolocation
//W3C Editor's Draft at http://dev.w3.org/geo/api/spec-source-orientation.html
//Implementation by iOS Safari at http://goo.gl/fhce3 and http://goo.gl/rLKz8

//test for Device Motion Event support, returns boolean value true/false
Modernizr.addTest('devicemotion', 'DeviceMotionEvent' in window);

//test for Device Orientation Event support, returns boolean value true/false
Modernizr.addTest('deviceorientation', 'DeviceOrientationEvent' in window);
// EXIF Orientation test

// iOS looks at the EXIF Orientation flag in jpgs and rotates the image
// accordingly. Looks like most desktop browsers just ignore this data.

// description: www.impulseadventure.com/photo/exif-orientation.html

// Bug trackers:
//    bugzil.la/298619 (unimplemented)
//    crbug.com/56845 (looks incomplete)
//    webk.it/19688 (available upstream but its up all ports to turn on individually)
//

// detect by Paul Sayre

(function() {
  var img = new Image();

  img.onerror = function() {
    Modernizr.addTest('exif-orientation', function() {
      return false;
    });
  };

  img.onload = function() {
    Modernizr.addTest('exif-orientation', function() {
      return img.width !== 2;
    });
  };

  // There may be a way to shrink this more, it's a 1x2 white jpg with the orientation flag set to 6
  img.src =
    'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/4QAiRXhpZgAASUkqAAgAAAABABIBAwABAAAABgASAAAAAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAIDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD+/iiiigD/2Q==';
})();
/**
   * file tests for the File API specification
   *   Tests for objects specific to the File API W3C specification without
   *   being redundant (don't bother testing for Blob since it is assumed
   *   to be the File object's prototype.
   *
   *   Will fail in Safari 5 due to its lack of support for the standards
   *   defined FileReader object
   */
Modernizr.addTest('filereader', function() {
  return !!(window.File && window.FileList && window.FileReader);
});
// Filesystem API
// dev.w3.org/2009/dap/file-system/file-dir-sys.html

// The API will be present in Chrome incognito, but will throw an exception.
// See crbug.com/93417
//
// By Eric Bidelman (@ebidel)

Modernizr.addTest('filesystem', !!Modernizr.prefixed('requestFileSystem', window));

// Detects whether input type="file" is available on the platform
// E.g. iOS < 6 and some android version don't support this

//  It's useful if you want to hide the upload feature of your app on devices that
//  don't support it (iphone, ipad, etc).

Modernizr.addTest('fileinput', function() {
  var elem = document.createElement('input');
  elem.type = 'file';
  return !elem.disabled;
});
// Detects whether input form="form_id" is available on the platform
// E.g. IE 10 (and below), don't support this
Modernizr.addTest('formattribute', function() {
  var form = document.createElement('form'),
    input = document.createElement('input'),
    div = document.createElement('div'),
    id = 'formtest' + new Date().getTime(),
    attr,
    bool = false;

  form.id = id;

  //IE6/7 confuses the form idl attribute and the form content attribute
  if (document.createAttribute) {
    attr = document.createAttribute('form');
    attr.nodeValue = id;
    input.setAttributeNode(attr);
    div.appendChild(form);
    div.appendChild(input);

    document.documentElement.appendChild(div);

    bool = form.elements.length === 1 && input.form == form;

    div.parentNode.removeChild(div);
  }

  return bool;
}); // input[type="number"] localized input/output
// // Detects whether input type="number" is capable of receiving and
// // displaying localized numbers, e.g. with comma separator
// // https://bugs.webkit.org/show_bug.cgi?id=42484
// // Based on http://trac.webkit.org/browser/trunk/LayoutTests/fast/forms/script-tests/input-number-keyoperation.js?rev=80096#L9
// // By Peter Janes

Modernizr.addTest('localizedNumber', function() {
  var doc = document,
    el = document.createElement('div'),
    fake,
    root,
    input,
    diff;
  root =
    doc.body ||
    (function() {
      var de = doc.documentElement;
      fake = true;
      return de.insertBefore(doc.createElement('body'), de.firstElementChild || de.firstChild);
    })();
  el.innerHTML = '<input type="number" value="1.0" step="0.1"/>';
  input = el.childNodes[0];
  root.appendChild(el);
  input.focus();
  try {
    doc.execCommand('InsertText', false, '1,1');
  } catch (e) {
    // prevent warnings in IE
  }
  diff = input.type === 'number' && input.valueAsNumber === 1.1 && input.checkValidity();
  root.removeChild(el);
  fake && root.parentNode.removeChild(root);
  return diff;
});
// testing for placeholder attribute in inputs and textareas
// re-using Modernizr.input if available

Modernizr.addTest('placeholder', function() {
  return !!(
    'placeholder' in (Modernizr.input || document.createElement('input')) &&
    'placeholder' in (Modernizr.textarea || document.createElement('textarea'))
  );
});
// speech input for inputs
// by @alrra

// `webkitSpeech` in elem
// doesn`t work correctly in all versions of Chromium based browsers.
//   It can return false even if they have support for speech i.imgur.com/2Y40n.png
//  Testing with 'onwebkitspeechchange' seems to fix this problem

// this detect only checks the webkit version because
// the speech attribute is likely to be deprecated in favor of a JavaScript API.
// http://lists.w3.org/Archives/Public/public-webapps/2011OctDec/att-1696/speechapi.html

// FIXME: add support for detecting the new spec'd behavior

Modernizr.addTest('speechinput', function() {
  var elem = document.createElement('input');
  return 'speech' in elem || 'onwebkitspeechchange' in elem;
}); // This implementation only tests support for interactive form validation.
// To check validation for a specific type or a specific other constraint,
// the test can be combined:
//    - Modernizr.inputtypes.numer && Modernizr.formvalidation (browser supports rangeOverflow, typeMismatch etc. for type=number)
//    - Modernizr.input.required && Modernizr.formvalidation (browser supports valueMissing)
//
(function(document, Modernizr) {
  Modernizr.formvalidationapi = false;
  Modernizr.formvalidationmessage = false;

  Modernizr.addTest('formvalidation', function() {
    var form = document.createElement('form');
    if (!('checkValidity' in form) || !('addEventListener' in form)) {
      return false;
    }
    if ('reportValidity' in form) {
      return true;
    }
    var invalidFired = false;
    var input;

    Modernizr.formvalidationapi = true;

    // Prevent form from being submitted
    form.addEventListener(
      'submit',
      function(e) {
        //Opera does not validate form, if submit is prevented
        if (!window.opera) {
          e.preventDefault();
        }
        e.stopPropagation();
      },
      false
    );

    // Calling form.submit() doesn't trigger interactive validation,
    // use a submit button instead
    //older opera browsers need a name attribute
    form.innerHTML = '<input name="modTest" required><button></button>';

    Modernizr.testStyles('#modernizr form{position:absolute;top:-99999em}', function(node) {
      node.appendChild(form);

      input = form.getElementsByTagName('input')[0];

      // Record whether "invalid" event is fired
      input.addEventListener(
        'invalid',
        function(e) {
          invalidFired = true;
          e.preventDefault();
          e.stopPropagation();
        },
        false
      );

      //Opera does not fully support the validationMessage property
      Modernizr.formvalidationmessage = !!input.validationMessage;

      // Submit form by clicking submit button
      form.getElementsByTagName('button')[0].click();
    });

    return invalidFired;
  });
})(document, window.Modernizr);
Modernizr.addTest('fullscreen', function() {
  for (var i = 0; i < Modernizr._domPrefixes.length; i++) {
    if (document[Modernizr._domPrefixes[i].toLowerCase() + 'CancelFullScreen']) return true;
  }
  return !!document['cancelFullScreen'] || false;
});

// http://developer.apple.com/library/safari/documentation/AudioVideo/Conceptual/Using_HTML5_Audio_Video/ControllingMediaWithJavaScript/ControllingMediaWithJavaScript.html#//apple_ref/doc/uid/TP40009523-CH3-SW20
// https://developer.mozilla.org/en/API/Fullscreen
// GamePad API
// https://dvcs.w3.org/hg/gamepad/raw-file/default/gamepad.html
// By Eric Bidelman

// FF has Gamepad API support only in special builds, but not in any release (even behind a flag)
// Their current implementation has no way to feature detect, only events to bind to.
//   http://www.html5rocks.com/en/tutorials/doodles/gamepad/#toc-featuredetect

// but a patch will bring them up to date with the spec when it lands (and they'll pass this test)
//   https://bugzilla.mozilla.org/show_bug.cgi?id=690935

Modernizr.addTest('gamepads', !!Modernizr.prefixed('getGamepads', navigator));
// getUserMedia
// http://www.whatwg.org/specs/web-apps/current-work/multipage/video-conferencing-and-peer-to-peer-communication.html
// By Eric Bidelman

Modernizr.addTest('getusermedia', !!Modernizr.prefixed('getUserMedia', navigator));
// IE8 compat mode aka Fake IE7
// by Erich Ocean

// In this case, IE8 will be acting as IE7. You may choose to remove features in this case.

// related:
// james.padolsey.com/javascript/detect-ie-in-js-using-conditional-comments/

Modernizr.addTest('ie8compat', function() {
  return !window.addEventListener && document.documentMode && document.documentMode === 7;
});
// Test for `sandbox` attribute in iframes.
//
// Spec: http://www.whatwg.org/specs/web-apps/current-work/multipage/the-iframe-element.html#attr-iframe-sandbox

Modernizr.addTest('sandbox', 'sandbox' in document.createElement('iframe'));
// Test for `seamless` attribute in iframes.
//
// Spec: http://www.whatwg.org/specs/web-apps/current-work/multipage/the-iframe-element.html#attr-iframe-seamless

Modernizr.addTest('seamless', 'seamless' in document.createElement('iframe'));
// Test for `srcdoc` attribute in iframes.
//
// Spec: http://www.whatwg.org/specs/web-apps/current-work/multipage/the-iframe-element.html#attr-iframe-srcdoc

Modernizr.addTest('srcdoc', 'srcdoc' in document.createElement('iframe'));
// Animated PNG
// http://en.wikipedia.org/wiki/APNG
// By Addy Osmani
(function() {
  if (!Modernizr.canvas) return false;

  var image = new Image(),
    canvas = document.createElement('canvas'),
    ctx = canvas.getContext('2d');

  image.onload = function() {
    Modernizr.addTest('apng', function() {
      if (typeof canvas.getContext == 'undefined') {
        return false;
      } else {
        ctx.drawImage(image, 0, 0);
        return ctx.getImageData(0, 0, 1, 1).data[3] === 0;
      }
    });
  };

  image.src =
    'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACGFjVEwAAAABAAAAAcMq2TYAAAANSURBVAiZY2BgYPgPAAEEAQB9ssjfAAAAGmZjVEwAAAAAAAAAAQAAAAEAAAAAAAAAAAD6A+gBAbNU+2sAAAARZmRBVAAAAAEImWNgYGBgAAAABQAB6MzFdgAAAABJRU5ErkJggg==';
})();
// code.google.com/speed/webp/
// by rich bradshaw, ryan seddon, and paul irish

// This test is asynchronous. Watch out.

(function() {
  var image = new Image();

  image.onerror = function() {
    Modernizr.addTest('webp', false);
  };
  image.onload = function() {
    Modernizr.addTest('webp', function() {
      return image.width == 1;
    });
  };

  image.src =
    'data:image/webp;base64,UklGRiwAAABXRUJQVlA4ICAAAAAUAgCdASoBAAEAL/3+/3+CAB/AAAFzrNsAAP5QAAAAAA==';
})(); // native JSON support.
// developer.mozilla.org/en/JSON

// this will also succeed if you've loaded the JSON2.js polyfill ahead of time
//   ... but that should be obvious. :)

Modernizr.addTest('json', !!window.JSON && !!JSON.parse);

// impressivewebs.com/reverse-ordered-lists-html5
// polyfill: github.com/impressivewebs/HTML5-Reverse-Ordered-Lists

Modernizr.addTest('olreversed', 'reversed' in document.createElement('ol'));
// MathML
// http://www.w3.org/Math/
// By Addy Osmani
// Based on work by Davide (@dpvc) and David (@davidcarlisle)
// in https://github.com/mathjax/MathJax/issues/182

Modernizr.addTest('mathml', function() {
/*
  var hasMathML = false;
  if (document.createElementNS) {
  
    var ns = 'http://www.w3.org/1998/Math/MathML',
      div = document.createElement('div');
    div.style.position = 'absolute';
    var mfrac = div
      .appendChild(document.createElementNS(ns, 'math'))
      .appendChild(document.createElementNS(ns, 'mfrac'));
    mfrac
      .appendChild(document.createElementNS(ns, 'mi'))
      .appendChild(document.createTextNode('xx'));
    mfrac
      .appendChild(document.createElementNS(ns, 'mi'))
      .appendChild(document.createTextNode('yy'));
    document.body.appendChild(div);
    hasMathML = div.offsetHeight > div.offsetWidth;
  }
  return hasMathML;
  */
}); // determining low-bandwidth via navigator.connection

// There are two iterations of the navigator.connection interface:

// The first is present in Android 2.2+ and only in the Browser (not WebView)
// : docs.phonegap.com/en/1.2.0/phonegap_connection_connection.md.html#connection.type
// : davidbcalhoun.com/2010/using-navigator-connection-android

// The second is specced at dev.w3.org/2009/dap/netinfo/ and perhaps landing in WebKit
// : bugs.webkit.org/show_bug.cgi?id=73528

// unknown devices are assumed as fast
// for more rigorous network testing, consider boomerang.js: github.com/bluesmoon/boomerang/

Modernizr.addTest('lowbandwidth', function() {
  var connection = navigator.connection || { type: 0 }; // polyfill

  return (
    connection.type == 3 || // connection.CELL_2G
    connection.type == 4 || // connection.CELL_3G
    /^[23]g$/.test(connection.type)
  ); // string value in new spec
});

// server sent events aka eventsource
// dev.w3.org/html5/eventsource/

Modernizr.addTest('eventsource', !!window.EventSource);

// XML HTTP Request Level 2
// www.w3.org/TR/XMLHttpRequest2/

// Much more details at github.com/Modernizr/Modernizr/issues/385

// all three of these details report consistently across all target browsers:
//   !!(window.ProgressEvent);
//   !!(window.FormData);
//   window.XMLHttpRequest && "withCredentials" in new XMLHttpRequest;

Modernizr.addTest('xhr2', 'FormData' in window);
// Notifications
// By Theodoor van Donge

// window.webkitNotifications is only used by Chrome
//    http://www.html5rocks.com/en/tutorials/notifications/quick/

// window.Notification only exist in the draft specs
//    http://dev.w3.org/2006/webapi/WebNotifications/publish/Notifications.html#idl-if-Notification

Modernizr.addTest(
  'notification',
  'Notification' in window &&
    'permission' in window.Notification &&
    'requestPermission' in window.Notification
);
// Navigation Timing (Performance)
// https://dvcs.w3.org/hg/webperf/raw-file/tip/specs/NavigationTiming/
// http://www.html5rocks.com/en/tutorials/webperformance/basics/
// By Scott Murphy (uxder)
Modernizr.addTest('performance', !!Modernizr.prefixed('performance', window)); // https://developer.mozilla.org/en-US/docs/API/Pointer_Lock_API

Modernizr.addTest('pointerlock', !!Modernizr.prefixed('pointerLockElement', document));

// Quota Storage Management API
// This API can be used to check how much quota an origin is using and request more

// Currently only implemented in Chrome.
// https://developers.google.com/chrome/whitepapers/storage
// By Addy Osmani

Modernizr.addTest('quotamanagement', function() {
  var storage = Modernizr.prefixed('StorageInfo', window);
  return !!(storage && 'TEMPORARY' in storage && 'PERSISTENT' in storage);
});

// requestAnimationFrame
// Offload animation repainting to browser for optimized performance.
// http://dvcs.w3.org/hg/webperf/raw-file/tip/specs/RequestAnimationFrame/Overview.html
// By Addy Osmani

Modernizr.addTest('raf', !!Modernizr.prefixed('requestAnimationFrame', window)); // async script
// By Theodoor van Donge
Modernizr.addTest('scriptasync', 'async' in document.createElement('script')); // defer script
// By Theodoor van Donge
Modernizr.addTest('scriptdefer', 'defer' in document.createElement('script')); // Browser support test for <style scoped>
// http://www.w3.org/TR/html5/the-style-element.html#attr-style-scoped
//
// by @alrra

Modernizr.addTest('stylescoped', 'scoped' in document.createElement('style'));
// Detect support for svg filters - http://www.w3.org/TR/SVG11/filters.html.
// Should fail in Safari: http://stackoverflow.com/questions/9739955/feature-detecting-support-for-svg-filters.
// detect by erik dahlstrom

Modernizr.addTest('svgfilters', function() {
  var result = false;
  try {
    result =
      typeof SVGFEColorMatrixElement !== undefined &&
      SVGFEColorMatrixElement.SVG_FECOLORMATRIX_TYPE_SATURATE == 2;
  } catch (e) {}
  return result;
});
/**
   * Unicode special character support
   *
   * Detection is made by testing missing glyph box rendering against star character
   * If widths are the same, this "probably" means the browser didn't support the star character and rendered a glyph box instead
   * Just need to ensure the font characters have different widths
   *
   * Warning : positive Unicode support doesn't mean you can use it inside <title>, this seams more related to OS & Language packs
   */
Modernizr.addTest('unicode', function() {
  var bool,
    missingGlyph = document.createElement('span'),
    star = document.createElement('span');

  Modernizr.testStyles('#modernizr{font-family:Arial,sans;font-size:300em;}', function(node) {
    missingGlyph.innerHTML = '&#5987';
    star.innerHTML = '&#9734';

    node.appendChild(missingGlyph);
    node.appendChild(star);

    bool = 'offsetWidth' in missingGlyph && missingGlyph.offsetWidth !== star.offsetWidth;
  });

  return bool;
}); // data uri test.
// https://github.com/Modernizr/Modernizr/issues/14

// This test is asynchronous. Watch out.

// in IE7 in HTTPS this can cause a Mixed Content security popup.
//  github.com/Modernizr/Modernizr/issues/362
// To avoid that you can create a new iframe and inject this.. perhaps..

(function() {
  var datauri = new Image();

  datauri.onerror = function() {
    Modernizr.addTest('datauri', function() {
      return false;
    });
  };
  datauri.onload = function() {
    Modernizr.addTest('datauri', function() {
      return datauri.width == 1 && datauri.height == 1;
    });
  };

  datauri.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
})();
// test if IE userdata supported
// msdn.microsoft.com/en-us/library/ms531424(v=vs.85).aspx
// test by @stereobooster

Modernizr.addTest('userdata', function() {
  return !!document.createElement('div').addBehavior;
});
// Vibration API
// http://www.w3.org/TR/vibration/
// https://developer.mozilla.org/en/DOM/window.navigator.mozVibrate
Modernizr.addTest('vibrate', !!Modernizr.prefixed('vibrate', navigator)); // Tests for the ability to use Web Intents (http://webintents.org).
// By Eric Bidelman

Modernizr.addTest('webintents', function() {
  return !!Modernizr.prefixed('startActivity', navigator);
});

// Grab the WebGL extensions currently supported and add to the Modernizr.webgl object
// spec: www.khronos.org/registry/webgl/specs/latest/#5.13.14

// based on code from ilmari heikkinen
// code.google.com/p/graphics-detect/source/browse/js/detect.js

(function() {
  if (!Modernizr.webgl) return;

  var canvas, ctx, exts;

  try {
    canvas = document.createElement('canvas');
    ctx = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
    exts = ctx.getSupportedExtensions();
  } catch (e) {
    return;
  }

  if (ctx === undefined) {
    Modernizr.webgl = new Boolean(false);
  } else {
    Modernizr.webgl = new Boolean(true);
  }

  for (var i = -1, len = exts.length; ++i < len; ) {
    Modernizr.webgl[exts[i]] = true;
  }

  // hack for addressing modernizr testsuite failures. sorry.
  if (window.TEST && TEST.audvid) {
    TEST.audvid.push('webgl');
  }

  canvas = undefined;
})();
// binaryType is truthy if there is support.. returns "blob" in new-ish chrome.
// plus.google.com/115535723976198353696/posts/ERN6zYozENV
// github.com/Modernizr/Modernizr/issues/370

Modernizr.addTest('websocketsbinary', function() {
  var protocol = 'https:' == location.protocol ? 'wss' : 'ws',
    protoBin;

  if ('WebSocket' in window) {
    if ((protoBin = 'binaryType' in WebSocket.prototype)) {
      return protoBin;
    }
    try {
      return !!new WebSocket(protocol + '://.').binaryType;
    } catch (e) {}
  }

  return false;
});

// tests if page is iframed

// github.com/Modernizr/Modernizr/issues/242

Modernizr.addTest('framed', function() {
  return window.location != top.location;
});
// by jussi-kalliokoski

// This test is asynchronous. Watch out.

// The test will potentially add garbage to console.

(function() {
  try {
    // we're avoiding using Modernizr._domPrefixes as the prefix capitalization on
    // these guys are notoriously peculiar.
    var BlobBuilder =
      window.MozBlobBuilder ||
      window.WebKitBlobBuilder ||
      window.MSBlobBuilder ||
      window.OBlobBuilder ||
      window.BlobBuilder;
    var URL = window.MozURL || window.webkitURL || window.MSURL || window.OURL || window.URL;
    var data = 'Modernizr',
      blob,
      bb,
      worker,
      url,
      timeout,
      scriptText = 'this.onmessage=function(e){postMessage(e.data)}';

    try {
      blob = new Blob([scriptText], { type: 'text/javascript' });
    } catch (e) {
      // we'll fall back to the deprecated BlobBuilder
    }
    if (!blob) {
      bb = new BlobBuilder();
      bb.append(scriptText);
      blob = bb.getBlob();
    }

    url = URL.createObjectURL(blob);
    worker = new Worker(url);

    worker.onmessage = function(e) {
      Modernizr.addTest('blobworkers', data === e.data);
      cleanup();
    };

    // Just in case...
    worker.onerror = fail;
    timeout = setTimeout(fail, 200);

    worker.postMessage(data);
  } catch (e) {
    fail();
  }

  function fail() {
    Modernizr.addTest('blobworkers', false);
    cleanup();
  }

  function cleanup() {
    if (url) {
      URL.revokeObjectURL(url);
    }
    if (worker) {
      worker.terminate();
    }
    if (timeout) {
      clearTimeout(timeout);
    }
  }
})();
// by jussi-kalliokoski

// This test is asynchronous. Watch out.

// The test will potentially add garbage to console.

(function() {
  try {
    var data = 'Modernizr',
      worker = new Worker(
        'data:text/javascript;base64,dGhpcy5vbm1lc3NhZ2U9ZnVuY3Rpb24oZSl7cG9zdE1lc3NhZ2UoZS5kYXRhKX0='
      );

    worker.onmessage = function(e) {
      worker.terminate();
      Modernizr.addTest('dataworkers', data === e.data);
      worker = null;
    };

    // Just in case...
    worker.onerror = function() {
      Modernizr.addTest('dataworkers', false);
      worker = null;
    };

    setTimeout(function() {
      Modernizr.addTest('dataworkers', false);
    }, 200);

    worker.postMessage(data);
  } catch (e) {
    Modernizr.addTest('dataworkers', false);
  }
})();
Modernizr.addTest('sharedworkers', function() {
  return !!window.SharedWorker;
});
