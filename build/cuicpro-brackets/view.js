/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@wordpress/interactivity":
/*!***************************************!*\
  !*** external ["wp","interactivity"] ***!
  \***************************************/
/***/ ((module) => {

module.exports = window["wp"]["interactivity"];

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
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
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
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**************************************!*\
  !*** ./src/cuicpro-brackets/view.js ***!
  \**************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/interactivity */ "@wordpress/interactivity");
/* harmony import */ var _wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__);
/**
 * WordPress dependencies
 */

const {
  state
} = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.store)("cuicpro-brackets", {
  state: {
    isBracketOpen: false,
    bracketId: null
  },
  actions: {
    toggleBracket() {
      const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.getContext)();
      const bracketId = context.bracketId;
      for (const key in context.bracketsState) {
        if (key === "bracketId_" + bracketId) continue;
        if (key.startsWith("bracketId_")) context.bracketsState[key] = false;
      }

      // clean scroll events
      jQuery("#bracket_" + bracketId).off("scroll");
      jQuery(".leader-line").remove();
      state.bracketId = bracketId;
      state.isBracketOpen = !context.bracketsState["bracketId_" + bracketId];
      context.bracketsState["bracketId_" + bracketId] = !context.bracketsState["bracketId_" + bracketId];
    }
  },
  callbacks: {
    drawLines: () => {
      const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.getContext)();
      const bracketId = context.bracketId;

      // clean scroll events
      jQuery("#bracket_" + bracketId).off("scroll");

      // add lines to elements
      if (!state.isBracketOpen || state.bracketId !== bracketId) return;
      jQuery(".leader-line").remove();
      const bracket = jQuery("#bracket_" + bracketId);
      const rounds = bracket.find(".bracket-round");
      for (let i = rounds.length - 1; i > 0; i--) {
        const matches = rounds[i].children;
        for (let j = 0; j < matches.length; j++) {
          const match = matches[j];
          const matchAttr = match.attributes;
          if (!matchAttr["class"].value.includes("line-required-up") && !matchAttr["class"].value.includes("line-required-down")) continue;
          const matchId = matchAttr["id"].value;
          const matchLink1 = rounds[i - 1].children[j * 2];
          const matchLink2 = rounds[i - 1].children[j * 2 + 1];
          if (matchAttr["class"].value.includes("line-required-up")) {
            const line1 = new LeaderLine(document.getElementById(matchLink1.attributes["id"].value), document.getElementById(matchId), {
              color: "#f6931f",
              size: 2,
              endPlug: "behind",
              endPlugSize: 2,
              path: "grid",
              startSocket: "right",
              endSocket: "left"
            });
            jQuery("#bracket_" + bracketId).on("scroll", function () {
              line1.position();
            });
          }
          if (matchAttr["class"].value.includes("line-required-down")) {
            const line2 = new LeaderLine(document.getElementById(matchLink2.attributes["id"].value), document.getElementById(matchId), {
              color: "#f6931f",
              size: 2,
              endPlug: "behind",
              endPlugSize: 2,
              path: "grid",
              startSocket: "right",
              endSocket: "left"
            });
            jQuery("#bracket_" + bracketId).on("scroll", function () {
              line2.position();
            });
          }
        }
      }
      const container = jQuery("#brackets-data");
      const lines = jQuery(".leader-line");
      jQuery("#bracket_" + bracketId).on("scroll", function () {
        lines.each(function (line) {
          lines[line].style.removeProperty("display");
          const lineEl = lines[line];
          const containerRect = container[0].getBoundingClientRect();
          const lineRect = lineEl.getBoundingClientRect();
          const isOutside = lineRect.right > containerRect.right || lineRect.left < containerRect.left;
          if (!isOutside) {
            lineEl.style.removeProperty("display");
          } else {
            lineEl.style.setProperty("display", "none");
          }
        });
      });
    }
  }
});
})();

/******/ })()
;
//# sourceMappingURL=view.js.map