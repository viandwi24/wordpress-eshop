/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/scripts/app.js":
/*!*******************************!*\
  !*** ./assets/scripts/app.js ***!
  \*******************************/
/***/ (() => {

function _toArray(arr) { return _arrayWithHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

// Bots
var Bots = {};

(function ($) {
  var variables = {},
      botlist = [];

  Bots.setVariable = function (name, value) {
    variables = setVariableWrapper(name, value, variables);
  };

  Bots.push = function (bot) {
    console.log(bot.variables);
    var d = {
      "precatch": function precatch(text) {
        return text;
      },
      "actions": [],
      "postcatch": function postcatch(text) {
        return text;
      },
      "functions": {},
      "synonyms": [],
      "variables": {}
    };
    bot = $.extend({}, d, bot);
    console.log(bot.variables);

    bot.setVariable = function (name, value) {
      bot.variables = setVariableWrapper(name, value, bot.variables);
    };

    bot.getVariable = function (name) {
      var ret = false;
      if (bot.variables.hasOwnProperty(name)) ret = bot.variables[name];else if (variables.hasOwnProperty(name)) ret = variables[name];
      assert(ret, "Can't find variable called " + name + " for bot " + bot.name);
      return ret(bot);
    };

    bot.processActionQueue = function (message) {
      var outs = [];

      for (var x = 0; x < bot.actionQueue.length; x++) {
        var actions = bot.actionQueue[x];
        if (!$.isArray(actions)) actions = bot.functions[actions];
        assert(actions, "No such action function called " + actions + " for bot " + bot.name);
        var match = false;

        for (var i = 0; i < actions.length; i++) {
          var action = actions[i];
          var c = action["catch"],
              r = action["response"];
          if (!$.isArray(c)) c = [c];
          if (!$.isArray(r)) r = [r];

          for (var y = 0; y < c.length; y++) {
            var options = getSynonymOptions(c[y], bot);

            for (var z = 0; z < options.length; z++) {
              if (catchMatches(options[z], message, bot)) {
                match = true;
                break;
              }
            }
          }

          if (match) {
            var response = arrayRandom(r);
            if (_typeof(response) !== 'object') response = {
              "text": response || ""
            };
            var from = action["from"] || "*";

            if (catchMatches(from, bot.getVariable("USER"), bot)) {
              if (!$.isArray(response["text"])) response["text"] = [response["text"]];
              var rtext = arrayRandom(response["text"]);
              bot.setVariable("INPUT", message);
              var synonymed = replaceSynonyms(replaceVariables(rtext, rtext, bot), bot);

              if (response["actions"]) {
                bot.nextActionQueue.push(response["actions"]);
              }

              outs.push(synonymed);
              break;
            }
          }
        }

        if (match) break;
      } //console.log(bot.actionQueue, bot.nextActionQueue);


      bot.actionQueue = bot.nextActionQueue;
      bot.nextActionQueue = []; //console.log(bot.nextActionQueue);

      return outs;
    };

    bot.actionQueue = [];
    bot.nextActionQueue = [];
    botlist.push(bot);
  };

  Bots.find = function (name) {
    for (var i = 0; i < botlist.length; i++) {
      if (botlist[i].name.toLowerCase() == name.toLowerCase()) return botlist[i];
    }

    assert(false, "Can't find a bot called " + name);
    return false;
  };

  Bots.sendMessage = function (botname, message, variables) {
    var bot = Bots.find(botname);

    for (var key in variables) {
      if (variables.hasOwnProperty(key)) {
        bot.setVariable(key, variables[key]);
      }
    }

    message = bot.precatch.call(bot, message);
    message = Bots.processActions(bot, message);

    if (message.length > 0) {
      message = $.map(message, function (val) {
        var post = bot.postcatch.call(bot, val);
        return replaceVariables(post, post, bot);
      });
    }

    bot.setVariable("PREVIOUS", bot.getVariable("USER"));
    return message;
  };

  Bots.processActions = function (bot, message) {
    var actions = bot.actions;
    bot.actionQueue.push(bot.actions);
    var r = bot.processActionQueue(message);
    return r;
    /*var outs = [];
    for (var i = 0; i < actions.length; i++) {
        var action = actions[i];
        var c = action["catch"], r = action["response"];
        
        if (!Array.isArray(c)) c = [c];
        if (!Array.isArray(r)) r = [r];
        
        var match = false;
        for (var x = 0; x < c.length; x++) {
            var options = getSynonymOptions(c[x], bot);
            for (var y = 0; y < options.length; y++) {
                if (catchMatches(options[y], message, bot)) {
                    match = true;
                    break;
                }
            }
        }
        
        if (match) {
            var response = arrayRandom(r);
            if (typeof response !== 'object') response = { "text": response };
            
            var from = response["from"] || "*";
            
            if (catchMatches(from, bot.getVariable("USER"), bot)) {
                //console.log(replaceVariables(response["text"], response["text"], bot));
                var synonymed = replaceSynonyms(replaceVariables(response["text"], response["text"], bot), bot);
                //console.log("S: " + synonymed);
                outs.push(synonymed);
            }
        }
    }
    
    return outs;*/
  };

  Bots.utils = {
    "debug": true
  };
  /* Utility functions */

  function log(text, important) {
    if (Bots.utils.debug) {
      var title = "[Bots] ";
      if (important) title += "[Important] ";
      console.log(title + text);
    }
  }

  function assert(value, fmsg, tmsg) {
    if (!value) {
      if (fmsg === undefined) fmsg = "Assertion failed.";
      log(fmsg, true);
    } else if (tmsg !== undefined) log(tmsg);
  }

  function setVariableWrapper(name, value, object) {
    if (typeof value !== 'function') {
      var plainvalue = value;

      value = function value() {
        return plainvalue;
      };
    }

    object[name.toUpperCase()] = value;
    return object;
  }

  function getSynonym(original, list) {
    return arrayRandom(getSynonymList(original, list));
  }

  function getSynonymList(original, list) {
    assert(list !== undefined, "No such list for synonyms '" + original + "'");

    for (var i = 0; i < list.length; i++) {
      var slist = list[i];
      if (slist.indexOf(original) > -1) return slist;
    }

    return [original];
  }

  function arrayRandom(array) {
    return array[Math.floor(Math.random() * array.length)];
  }

  function removePunctuation(text) {
    var punctuationLess = text.replace(/[\.,-\/#!$%\^&;:{}=\-_`~()@\+\?><\[\]\+]/g, "");
    return punctuationLess.replace(/\s{2,}/g, " ");
  }

  function replaceVariables(text, message, bot) {
    var pieces = getByDelimiter(text, "{", "}");

    for (var x = 1; x < pieces.length; x += 2) {
      var piece = pieces[x];

      if (piece.charAt(0) === "=") {
        var vname = piece.slice(1);
        var wordpos = false;

        if (text.indexOf("*") < text.indexOf("{" + piece + "}")) {
          wordpos = (pieces.length - x - 1) * -1;
        } else wordpos = x;

        var splitmessage = message.split(" ");
        if (wordpos < 0) wordpos = splitmessage.length + wordpos;
        var word = removePunctuation(splitmessage[wordpos]);
        if (word !== undefined) bot.setVariable(vname, word);
        pieces[x] = "*";
      } else {
        if (pieces[x] !== "") pieces[x] = bot.getVariable(piece);
      }
    } //console.log(text + ", " + pieces.join(""));


    return pieces.join("");
  }

  function replaceSynonyms(text, bot) {
    var pieces = getByDelimiter(text, "^", "^"); //console.log(pieces);

    for (var i = 1; i < pieces.length; i += 2) {
      pieces[i] = getSynonym(pieces[i], bot.synonyms);
    }

    return pieces.join("");
  }

  function getSynonymOptions(text, bot) {
    var pieces = getByDelimiter(text, "^", "^");
    var synonyms = [],
        words = [],
        wcount = 0;

    for (var i = 1; i < pieces.length; i += 2) {
      //console.log(bot);
      synonyms.push(getSynonymList(pieces[i], bot.synonyms));
      words.push(pieces[i]);
      wcount++;
    } //console.log(pieces);


    function allPossibleCases(arr) {
      if (arr.length === 1) return [arr[0]];
      var result = [];
      var remaining = allPossibleCases(arr.slice(1));

      for (var i = 0; i < remaining.length; i++) {
        for (var j = 0; j < arr[0].length; j++) {
          result.push([arr[0][j]].concat(remaining[i]));
        }
      }

      return result;
    } //words = allPossibleCases(words);


    synonyms = getCombinations(synonyms, wcount);
    var results = [],
        wpos = 0;

    for (var i = 0; i < synonyms.length; i++) {
      var options = synonyms[i],
          pcopy = pieces.slice();

      for (var x = 0; x < options.length; x++) {
        pcopy[x * 2 + 1] = options[x];
      }

      results.push(pcopy.join(""));
    } //console.log(results, words);


    return results;
  }

  function getCombinations(arr, n) {
    var _ref, _ref2;

    var i,
        j,
        k,
        elem,
        l = arr.length,
        childperm,
        ret = [];

    if (n == 1) {
      for (var i = 0; i < arr.length; i++) {
        for (var j = 0; j < arr[i].length; j++) {
          ret.push([arr[i][j]]);
        }
      }

      return ret;
    } else {
      for (i = 0; i < l; i++) {
        elem = arr.shift();

        for (j = 0; j < elem.length; j++) {
          childperm = getCombinations(arr.slice(), n - 1);

          for (k = 0; k < childperm.length; k++) {
            ret.push([elem[j]].concat(childperm[k]));
          }
        }
      }

      return ret;
    }

    i = j = k = elem = l = childperm = ret = (_ref = null, _ref2 = _toArray(_ref), _ref);
  }

  function getByDelimiter(text, start, end) {
    if (text.indexOf(start) < 0 || text.indexOf(end) < 0) return [text, ""];
    var firstpieces = text.split(start),
        pieces = [];

    for (var i = 0; i < firstpieces.length; i++) {
      var secondpieces = firstpieces[i].split(end);
      pieces.push.apply(pieces, secondpieces);
    }

    assert(pieces.length > 0, "Not enough pieces in text " + text);
    return pieces;
  }

  function catchMatches(ctext, message, bot) {
    ctext = removePunctuation(replaceVariables(ctext, message, bot)).toLowerCase();
    message = removePunctuation(message).toLowerCase();
    console.log(message, ctext, wildCompare(message, ctext));
    return wildCompare(message, ctext);
  }

  function wildCompare(string, search) {
    if (search.indexOf('*') < 0 && string != search) return false;
    if (string.length < 1 && search.length > 0 || search.length < 1 && string.length > 0) return false;
    var startIndex = 0,
        array = search.split('*');

    for (var i = 0; i < array.length; i++) {
      var index = string.indexOf(array[i], startIndex);
      if (index == -1) return false;else startIndex = index;
    }

    return true;
  }
})(jQuery); // navbar


var navbarInit = function navbarInit() {
  var navbar = document.querySelector('#navbar');
  if (!navbar) return false;
  var navbarTop = document.querySelector('#navbar-top');
  var navbarBottom = document.querySelector('#navbar-bottom');
  var navbarBaseHeight = navbarTop.offsetHeight;
  var navbarTopBaseHeight = navbarTop.clientHeight;
  var offset = 20;

  var onWindowScroll = function onWindowScroll() {
    var requiredClass = ['fixed', 'w-full', 'z-20', 'top-0', 'left-0'];

    if (window.scrollY > navbarBottom.offsetHeight + offset) {
      var result = navbarTopBaseHeight + navbarBottom.clientHeight;

      if (navbarTop.clientHeight !== result) {
        navbarTop.style.height = "".concat(result, "px");
      }

      if (!navbarBottom.classList.contains(requiredClass[0])) {
        var _navbarBottom$classLi;

        (_navbarBottom$classLi = navbarBottom.classList).add.apply(_navbarBottom$classLi, requiredClass);

        navbarBottom.animate([{
          transform: 'translateY(-10vh)'
        }, {
          transform: 'translateY(0)'
        }], {
          duration: 300,
          easing: 'ease-out'
        });
      }
    } else if (window.scrollY < navbarBottom.offsetHeight * 2 + offset) {
      var _navbarBottom$classLi2;

      navbarTop.style.height = "".concat(navbarTopBaseHeight, "px");

      (_navbarBottom$classLi2 = navbarBottom.classList).remove.apply(_navbarBottom$classLi2, requiredClass);
    }
  };

  window.addEventListener('scroll', onWindowScroll);
  window.addEventListener('DOMContentLoaded', onWindowScroll);
};

var sidebarInit = function sidebarInit() {
  var el = document.querySelector('.sidebar-mobile');
  var bg = el.querySelectorAll('.bg');
  var sidebarToggle = document.querySelectorAll('.toggle-sidebar-mobile');
  if (!el) return false;

  var toggle = function toggle() {
    if (el.classList.contains('fixed')) {
      el.classList.remove('fixed');
      el.classList.add('hidden');
    } else {
      el.classList.add('fixed');
      el.classList.remove('hidden');
    }
  };

  bg.forEach(function (el) {
    return el.addEventListener('click', toggle);
  });
  sidebarToggle.forEach(function (el) {
    return el.addEventListener('click', toggle);
  });
};

var liveChatInit = function liveChatInit() {
  var el = document.querySelector('.floating-chat');
  var btnClose = el.querySelector('.btn-close');
  var messages = el.querySelector('.messages');
  var input = el.querySelector('.text-box');
  var firstOpened = true; // 

  Bots.push({
    "name": "bot",
    "precatch": function precatch(text) {
      return text;
    },
    "actions": [{
      "catch": ["*halo*"],
      "response": "Halo Juga!"
    }, {
      "catch": ["*nama toko*"],
      "response": "Nama Toko Ini adalah <strong>Ilham Camera</strong>"
    }, {
      "catch": ["*alamat*"],
      "response": "Alamat Toko Ini di <strong>Jl. Raya Gayaman No.164, Gayaman, Kec. Mojoanyar, Mojokerto, Jawa Timur 61364</strong>"
    }],
    "postcatch": function postcatch(text) {
      return text;
    },
    "functions": {},
    "synonyms": [],
    "variables": {}
  }); // toggle

  var toggle = function toggle() {
    if (el.classList.contains('opened')) {
      el.classList.remove('opened');
    } else {
      el.classList.add('opened');
    }
  };

  el.addEventListener('click', function () {
    if (!this.classList.contains('opened')) {
      setTimeout(function () {
        el.classList.add('opened');

        if (firstOpened) {
          firstOpened = false;
          setTimeout(function () {
            commit('Halo selamat datang di Toko Kami!');
          }, 500);
        }
      }, 150);
    }
  });
  btnClose.addEventListener('click', function () {
    return setTimeout(function () {
      return el.classList.remove('opened');
    }, 150);
  }); // 

  input.addEventListener('keyup', function (e) {
    if (e.keyCode === 13) {
      var text = input.value;
      commit(text, false);
      input.value = '';
      setTimeout(function () {
        // 
        var r = Bots.sendMessage("bot", text, {
          "USER": 'self'
        });

        if (r.length > 0) {
          for (var i = 0; i < r.length; i++) {
            commit(r[i]);
          }
        } else {
          commit("\n                        Maaf, aku tidak mengerti maksudmu. Kamu dapat\n                        <a href=\"https://wa.me/0895337617550\" style=\"text-decoration: underline;\">link ini</a>\n                        ini untuk menghubungi WhatsApp Toko.\n                    ");
        }
      }, Math.random() * 1000);
    }
  }); // commit

  var commit = function commit(msg) {
    var a = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
    var newMsg = msg instanceof HTMLElement ? msg : document.createElement('li');

    if (msg instanceof HTMLElement) {} else {
      newMsg.innerHTML = msg;
    }

    newMsg.classList.add(a ? 'other' : 'self');
    messages.appendChild(newMsg); // animate scroll messages to bottom

    messages.scrollTop = messages.scrollHeight;
  };
}; // 


navbarInit();
sidebarInit();
liveChatInit();

/***/ }),

/***/ "./assets/scss/vendor.scss":
/*!*********************************!*\
  !*** ./assets/scss/vendor.scss ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/scss/app.scss":
/*!******************************!*\
  !*** ./assets/scss/app.scss ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/js/app": 0,
/******/ 			"style": 0,
/******/ 			"css/vendor": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkIds[i]] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunk"] = self["webpackChunk"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["style","css/vendor"], () => (__webpack_require__("./assets/scripts/app.js")))
/******/ 	__webpack_require__.O(undefined, ["style","css/vendor"], () => (__webpack_require__("./assets/scss/vendor.scss")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["style","css/vendor"], () => (__webpack_require__("./assets/scss/app.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;