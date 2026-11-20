(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("CoreHome"), require("vue"), require("CorePluginsAdmin"), require("CoreVisualizations"), require("SegmentEditor"));
	else if(typeof define === 'function' && define.amd)
		define(["CoreHome", , "CorePluginsAdmin", "CoreVisualizations", "SegmentEditor"], factory);
	else if(typeof exports === 'object')
		exports["RebelMyReports"] = factory(require("CoreHome"), require("vue"), require("CorePluginsAdmin"), require("CoreVisualizations"), require("SegmentEditor"));
	else
		root["RebelMyReports"] = factory(root["CoreHome"], root["Vue"], root["CorePluginsAdmin"], root["CoreVisualizations"], root["SegmentEditor"]);
})((typeof self !== 'undefined' ? self : this), function(__WEBPACK_EXTERNAL_MODULE__19dc__, __WEBPACK_EXTERNAL_MODULE__8bbf__, __WEBPACK_EXTERNAL_MODULE_a5a2__, __WEBPACK_EXTERNAL_MODULE_b432__, __WEBPACK_EXTERNAL_MODULE_f06f__) {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "plugins/RebelMyReports/vue/dist/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "fae3");
/******/ })
/************************************************************************/
/******/ ({

/***/ "19dc":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE__19dc__;

/***/ }),

/***/ "8bbf":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE__8bbf__;

/***/ }),

/***/ "a5a2":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_a5a2__;

/***/ }),

/***/ "b432":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_b432__;

/***/ }),

/***/ "f06f":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_f06f__;

/***/ }),

/***/ "fae3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "InvalidateMyReports", function() { return /* reexport */ InvalidateMyReports; });
__webpack_require__.d(__webpack_exports__, "ManageMyReports", function() { return /* reexport */ ManageMyReports; });
__webpack_require__.d(__webpack_exports__, "MyDuplicateForm", function() { return /* reexport */ MyDuplicateForm; });
__webpack_require__.d(__webpack_exports__, "MyMetricsManager", function() { return /* reexport */ MyMetricsManager; });
__webpack_require__.d(__webpack_exports__, "MyReportExamples", function() { return /* reexport */ MyReportExamples; });
__webpack_require__.d(__webpack_exports__, "MyReportEditor", function() { return /* reexport */ MyReportEditor; });
__webpack_require__.d(__webpack_exports__, "MyReportExport", function() { return /* reexport */ MyReportExport; });
__webpack_require__.d(__webpack_exports__, "MyReportItemPicker", function() { return /* reexport */ MyReportItemPicker; });
__webpack_require__.d(__webpack_exports__, "MyReportKeyFigure", function() { return /* reexport */ MyReportKeyFigure; });
__webpack_require__.d(__webpack_exports__, "MyReportPreview", function() { return /* reexport */ MyReportPreview; });
__webpack_require__.d(__webpack_exports__, "MyReportsList", function() { return /* reexport */ MyReportsList; });

// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/setPublicPath.js
// This file is imported into lib/wc client bundles.

if (typeof window !== 'undefined') {
  var currentScript = window.document.currentScript
  if (false) { var getCurrentScript; }

  var src = currentScript && currentScript.src.match(/(.+\/)[^/]+\.js(\?.*)?$/)
  if (src) {
    __webpack_require__.p = src[1] // eslint-disable-line
  }
}

// Indicate to webpack that this file can be concatenated
/* harmony default export */ var setPublicPath = (null);

// EXTERNAL MODULE: external {"commonjs":"vue","commonjs2":"vue","root":"Vue"}
var external_commonjs_vue_commonjs2_vue_root_Vue_ = __webpack_require__("8bbf");

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/InvalidateMyReports/InvalidateMyReports.vue?vue&type=template&id=31301cb1

const _hoisted_1 = {
  class: "invalidateMyReports"
};
const _hoisted_2 = {
  class: "invalidateMyReports__dates"
};
const _hoisted_3 = {
  key: 0
};
function render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Field = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Field");
  const _component_SaveButton = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SaveButton");
  const _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", _hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ContentBlock, {
    "content-title": _ctx.translate('RebelMyReports_InvalidateReports')
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_InvalidateIntro')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "checkbox",
      name: "allReports",
      modelValue: _ctx.allReports,
      "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.allReports = $event),
      title: _ctx.translate('RebelMyReports_InvalidateAllReports')
    }, null, 8, ["modelValue", "title"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "checkbox",
      "var-type": "array",
      name: "idMyReports",
      modelValue: _ctx.idMyReports,
      "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => _ctx.idMyReports = $event),
      title: _ctx.translate('General_Reports'),
      options: _ctx.reportOptions
    }, null, 8, ["modelValue", "title", "options"])], 512), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], !_ctx.allReports]]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "select",
      name: "period",
      modelValue: _ctx.period,
      "onUpdate:modelValue": _cache[2] || (_cache[2] = $event => _ctx.period = $event),
      title: _ctx.translate('General_Period'),
      options: _ctx.periodOptions,
      "inline-help": _ctx.translate('RebelMyReports_InvalidatePeriodHelp')
    }, null, 8, ["modelValue", "title", "options", "inline-help"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", _hoisted_2, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("label", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate(_ctx.period === 'range' ? 'General_DateRangeFrom' : 'General_Date')) + " ", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      type: "date",
      class: "browser-default",
      name: "date",
      "onUpdate:modelValue": _cache[3] || (_cache[3] = $event => _ctx.date = $event)
    }, null, 512), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vModelText"], _ctx.date]])]), _ctx.period === 'range' ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("label", _hoisted_3, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_DateRangeTo')) + " ", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      type: "date",
      class: "browser-default",
      name: "endDate",
      "onUpdate:modelValue": _cache[4] || (_cache[4] = $event => _ctx.endDate = $event)
    }, null, 512), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vModelText"], _ctx.endDate]])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "select",
      name: "segment",
      modelValue: _ctx.segment,
      "onUpdate:modelValue": _cache[5] || (_cache[5] = $event => _ctx.segment = $event),
      title: _ctx.translate('General_Segment'),
      options: _ctx.segmentOptions
    }, null, 8, ["modelValue", "title", "options"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SaveButton, {
      value: _ctx.translate('RebelMyReports_InvalidateButton'),
      saving: _ctx.isSaving,
      disabled: !_ctx.canInvalidate,
      onConfirm: _cache[6] || (_cache[6] = $event => _ctx.invalidate())
    }, null, 8, ["value", "saving", "disabled"])]),
    _: 1
  }, 8, ["content-title"])]);
}
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/InvalidateMyReports/InvalidateMyReports.vue?vue&type=template&id=31301cb1

// EXTERNAL MODULE: external "CoreHome"
var external_CoreHome_ = __webpack_require__("19dc");

// EXTERNAL MODULE: external "CorePluginsAdmin"
var external_CorePluginsAdmin_ = __webpack_require__("a5a2");

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/InvalidateMyReports/InvalidateMyReports.vue?vue&type=script&lang=ts



function today() {
  const now = new Date();
  const pad = part => String(part).padStart(2, '0');
  return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;
}
/* harmony default export */ var InvalidateMyReportsvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    reports: {
      type: Array,
      required: true
    },
    segments: {
      type: Array,
      default: () => []
    }
  },
  components: {
    ContentBlock: external_CoreHome_["ContentBlock"],
    Field: external_CorePluginsAdmin_["Field"],
    SaveButton: external_CorePluginsAdmin_["SaveButton"]
  },
  data() {
    return {
      allReports: true,
      idMyReports: [],
      period: 'day',
      date: today(),
      endDate: today(),
      segment: '',
      isSaving: false
    };
  },
  computed: {
    reportOptions() {
      return this.reports.filter(report => !report.paused).map(report => {
        const site = report.site || Object(external_CoreHome_["translate"])('RebelMyReports_AllWebsites');
        return {
          key: report.idreport,
          value: external_CoreHome_["Matomo"].helper.htmlDecode(`${report.name} (${site})`)
        };
      });
    },
    segmentOptions() {
      return [{
        key: '',
        value: Object(external_CoreHome_["translate"])('RebelMyReports_InvalidateAllSegments')
      }, ...this.segments.map(segment => ({
        key: segment.definition,
        value: external_CoreHome_["Matomo"].helper.htmlDecode(segment.name)
      }))];
    },
    periodOptions() {
      return {
        day: Object(external_CoreHome_["translate"])('Intl_PeriodDay'),
        week: Object(external_CoreHome_["translate"])('Intl_PeriodWeek'),
        month: Object(external_CoreHome_["translate"])('Intl_PeriodMonth'),
        year: Object(external_CoreHome_["translate"])('Intl_PeriodYear'),
        range: Object(external_CoreHome_["translate"])('General_DateRangeInPeriodList')
      };
    },
    canInvalidate() {
      const hasDates = !!this.date && (this.period !== 'range' || this.endDate >= this.date);
      return hasDates && (this.allReports || this.idMyReports.length > 0);
    }
  },
  methods: {
    invalidate() {
      this.isSaving = true;
      external_CoreHome_["AjaxHelper"].post({
        method: 'RebelMyReports.invalidateReports'
      }, {
        period: this.period,
        date: this.period === 'range' ? `${this.date},${this.endDate}` : this.date,
        idMyReports: this.allReports ? [] : this.idMyReports,
        segmentFilter: this.segment
      }).then(response => {
        external_CoreHome_["NotificationsStore"].show({
          id: 'rebelMyReportsInvalidated',
          message: Object(external_CoreHome_["translate"])('RebelMyReports_InvalidateDone', response.value),
          context: 'success',
          type: 'transient'
        });
      }).finally(() => {
        this.isSaving = false;
      });
    }
  }
}));
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/InvalidateMyReports/InvalidateMyReports.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/InvalidateMyReports/InvalidateMyReports.vue



InvalidateMyReportsvue_type_script_lang_ts.render = render

/* harmony default export */ var InvalidateMyReports = (InvalidateMyReportsvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/ManageMyReports/ManageMyReports.vue?vue&type=template&id=72bd4005

const ManageMyReportsvue_type_template_id_72bd4005_hoisted_1 = {
  class: "manageMyReports"
};
function ManageMyReportsvue_type_template_id_72bd4005_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_MyReportsList = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MyReportsList");
  const _component_MyMetricsManager = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MyMetricsManager");
  const _component_MyReportEditor = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MyReportEditor");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", ManageMyReportsvue_type_template_id_72bd4005_hoisted_1, [!_ctx.isEditing ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_MyReportsList, {
    key: 0,
    reports: _ctx.reports,
    dimensions: _ctx.dimensions,
    metrics: _ctx.metrics,
    "archive-status": _ctx.archiveStatus,
    "is-loading": _ctx.isLoading,
    onCreate: _cache[0] || (_cache[0] = $event => _ctx.edit(null)),
    onEdit: _cache[1] || (_cache[1] = $event => _ctx.edit($event)),
    onDeleted: _cache[2] || (_cache[2] = $event => _ctx.onChanged())
  }, null, 8, ["reports", "dimensions", "metrics", "archive-status", "is-loading"])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), !_ctx.isEditing && !_ctx.isLoading ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_MyMetricsManager, {
    key: 1,
    metrics: _ctx.metrics,
    onChanged: _cache[3] || (_cache[3] = $event => _ctx.fetchMetrics())
  }, null, 8, ["metrics"])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.isEditing ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_MyReportEditor, {
    key: 2,
    report: _ctx.editedReport,
    dimensions: _ctx.dimensions,
    metrics: _ctx.metrics,
    categories: _ctx.categories,
    "rearchive-months": _ctx.rearchiveMonths,
    "max-dimensions": _ctx.maxDimensions,
    onSaved: _cache[4] || (_cache[4] = $event => _ctx.onChanged()),
    onCancel: _cache[5] || (_cache[5] = $event => _ctx.isEditing = false)
  }, null, 8, ["report", "dimensions", "metrics", "categories", "rearchive-months", "max-dimensions"])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]);
}
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/ManageMyReports/ManageMyReports.vue?vue&type=template&id=72bd4005

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportsList/MyReportsList.vue?vue&type=template&id=755e6297

const MyReportsListvue_type_template_id_755e6297_hoisted_1 = {
  class: "myReportsList"
};
const MyReportsListvue_type_template_id_755e6297_hoisted_2 = ["href"];
const MyReportsListvue_type_template_id_755e6297_hoisted_3 = {
  class: "loadingPiwik"
};
const _hoisted_4 = ["placeholder", "aria-label"];
const _hoisted_5 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, null, -1);
const _hoisted_6 = {
  key: 0
};
const _hoisted_7 = {
  colspan: "9"
};
const _hoisted_8 = ["draggable", "onDragstart", "onDrop"];
const _hoisted_9 = {
  class: "myReportsList__handleCell"
};
const _hoisted_10 = ["title", "disabled", "onKeydown"];
const _hoisted_11 = {
  class: "myReportsList__nameCell"
};
const _hoisted_12 = ["href", "title"];
const _hoisted_13 = {
  key: 0,
  class: "myReportsList__chip"
};
const _hoisted_14 = {
  key: 1,
  class: "myReportsList__description"
};
const _hoisted_15 = {
  key: 0,
  class: "myReportsList__none"
};
const _hoisted_16 = {
  key: 0,
  class: "myReportsList__date"
};
const _hoisted_17 = {
  key: 1,
  class: "myReportsList__none"
};
const _hoisted_18 = {
  key: 2,
  class: "myReportsList__description"
};
const _hoisted_19 = {
  key: 3,
  class: "myReportsList__problem"
};
const _hoisted_20 = {
  class: "myReportsList__date"
};
const _hoisted_21 = {
  class: "myReportsList__description"
};
const _hoisted_22 = {
  class: "myReportsList__date"
};
const _hoisted_23 = {
  class: "myReportsList__description"
};
const _hoisted_24 = {
  class: "myReportsList__actions"
};
const _hoisted_25 = ["title", "disabled", "onClick"];
const _hoisted_26 = ["title", "disabled", "onClick"];
const _hoisted_27 = ["title", "onClick"];
const _hoisted_28 = ["title", "disabled", "onClick"];
const _hoisted_29 = {
  key: 0,
  class: "myReportsList__duplicate"
};
const _hoisted_30 = {
  class: "tableActionBar"
};
const _hoisted_31 = ["disabled"];
const _hoisted_32 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-add"
}, null, -1);
const _hoisted_33 = {
  class: "ui-confirm",
  ref: "confirm"
};
const _hoisted_34 = ["value"];
const _hoisted_35 = ["value"];
function MyReportsListvue_type_template_id_755e6297_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_MatomoLoader = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MatomoLoader");
  const _component_MyDuplicateForm = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MyDuplicateForm");
  const _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");
  const _directive_content_table = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveDirective"])("content-table");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportsListvue_type_template_id_755e6297_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ContentBlock, null, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_ManageReportsIntro')) + " ", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
      href: _ctx.documentationUrl
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_Documentation')), 9, MyReportsListvue_type_template_id_755e6297_hoisted_2)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", MyReportsListvue_type_template_id_755e6297_hoisted_3, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_MatomoLoader), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_LoadingData')), 1)])], 512), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], _ctx.isLoading]]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      type: "search",
      class: "myReportsList__search browser-default",
      "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.search = $event),
      placeholder: _ctx.translate('General_Search'),
      "aria-label": _ctx.translate('General_Search')
    }, null, 8, _hoisted_4), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vModelText"], _ctx.search], [external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], !_ctx.isLoading && _ctx.reports.length > 1]]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])((Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("table", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("thead", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tr", null, [_hoisted_5, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Name')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_Dimensions')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Metrics')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_AvailableFor')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_ArchivedData')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_Created')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_Updated')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Actions')), 1)])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tbody", null, [!_ctx.visibleReports.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tr", _hoisted_6, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_7, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate(_ctx.reports.length ? 'General_SearchNoResults' : 'RebelMyReports_NoReportsYet')), 1)])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.visibleReports, report => {
      return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tr", {
        key: report.idreport,
        draggable: _ctx.canManage(report),
        onDragstart: $event => _ctx.dragged = report,
        onDragover: _cache[1] || (_cache[1] = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])(() => {}, ["prevent"])),
        onDrop: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])($event => _ctx.move(_ctx.dragged, report), ["prevent"])
      }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_9, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
        type: "button",
        class: "table-action icon-menu-hamburger myReportsList__handle",
        title: _ctx.translate('RebelMyReports_Reorder'),
        disabled: !_ctx.canManage(report),
        onKeydown: [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withKeys"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])($event => _ctx.moveBy(report, -1), ["prevent"]), ["up"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withKeys"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])($event => _ctx.moveBy(report, 1), ["prevent"]), ["down"])]
      }, null, 40, _hoisted_10)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_11, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
        class: "myReportsList__name",
        href: _ctx.reportUrl(report),
        title: _ctx.translate('RebelMyReports_ViewReport')
      }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.decode(report.name)), 9, _hoisted_12), report.paused ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", _hoisted_13, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_Paused')), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), report.description ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", _hoisted_14, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.decode(report.description)), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(report.dimensions, id => {
        return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", {
          class: "myReportsList__chip",
          key: id
        }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.dimensionNames[id] || id), 1);
      }), 128)), !report.dimensions.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", _hoisted_15, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(report.report_type === 'keyfigure' ? _ctx.translate('RebelMyReports_TypeKeyFigure') : _ctx.translate('RebelMyReports_TypeEvolution')), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(report.metrics, id => {
        return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", {
          class: "myReportsList__chip myReportsList__chip--metric",
          key: id
        }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.metricNames[id] || id), 1);
      }), 128))]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(report.idsite === 0 ? _ctx.translate('RebelMyReports_AllWebsites') : _ctx.translate('RebelMyReports_ThisWebsite')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, [_ctx.statusById[report.idreport] ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], {
        key: 0
      }, [_ctx.statusById[report.idreport].first_date ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", _hoisted_16, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.statusById[report.idreport].first_date) + " – " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.statusById[report.idreport].last_date), 1)) : (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", _hoisted_17, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_NoArchivedData')), 1)), _ctx.statusById[report.idreport].is_queued ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", _hoisted_18, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.statusById[report.idreport].queued_days ? _ctx.translate('RebelMyReports_DaysWaiting', _ctx.statusById[report.idreport].queued_days) : _ctx.translate('RebelMyReports_WaitingToBeArchived')), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.statusById[report.idreport].problem ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", _hoisted_19, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.problemText(_ctx.statusById[report.idreport])), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)], 64)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", _hoisted_20, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(report.created_date.slice(0, 10)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", _hoisted_21, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(report.created_by), 1)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, [report.updated_date !== report.created_date ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], {
        key: 0
      }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", _hoisted_22, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(report.updated_date.slice(0, 10)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", _hoisted_23, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(report.updated_by), 1)], 64)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_24, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
        type: "button",
        class: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeClass"])(["table-action", report.paused ? 'icon-play' : 'icon-pause']),
        title: _ctx.translate(report.paused ? 'RebelMyReports_Resume' : 'RebelMyReports_Pause'),
        disabled: !_ctx.canManage(report),
        onClick: $event => _ctx.setPaused(report, !report.paused)
      }, null, 10, _hoisted_25), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
        type: "button",
        class: "table-action icon-edit",
        title: _ctx.translate('General_Edit'),
        disabled: !_ctx.canManage(report),
        onClick: $event => _ctx.$emit('edit', report)
      }, null, 8, _hoisted_26), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
        type: "button",
        class: "table-action icon-content-copy",
        title: _ctx.translate('RebelMyReports_Duplicate'),
        onClick: $event => _ctx.duplicated = report
      }, null, 8, _hoisted_27), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
        type: "button",
        class: "table-action icon-delete",
        title: _ctx.translate('General_Delete'),
        disabled: !_ctx.canManage(report),
        onClick: $event => _ctx.confirmDelete(report)
      }, null, 8, _hoisted_28)])], 40, _hoisted_8);
    }), 128))])])), [[_directive_content_table], [external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], !_ctx.isLoading]]), _ctx.duplicated ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", _hoisted_29, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_MyDuplicateForm, {
      key: _ctx.duplicated.idreport,
      "item-name": _ctx.decode(_ctx.duplicated.name),
      "is-busy": _ctx.isDuplicating,
      onConfirm: _cache[2] || (_cache[2] = $event => _ctx.duplicate($event)),
      onCancel: _cache[3] || (_cache[3] = $event => _ctx.duplicated = null)
    }, null, 8, ["item-name", "is-busy"]))])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", _hoisted_30, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "btn",
      disabled: _ctx.isLoading,
      onClick: _cache[4] || (_cache[4] = $event => _ctx.$emit('create'))
    }, [_hoisted_32, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_CreateNewReport')), 1)], 8, _hoisted_31)])]),
    _: 1
  }), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", _hoisted_33, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h2", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_DeleteReportConfirm', _ctx.reportToDeleteName)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
    role: "yes",
    type: "button",
    value: _ctx.translate('General_Yes')
  }, null, 8, _hoisted_34), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
    role: "no",
    type: "button",
    value: _ctx.translate('General_No')
  }, null, 8, _hoisted_35)], 512)]);
}
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportsList/MyReportsList.vue?vue&type=template&id=755e6297

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyDuplicateForm/MyDuplicateForm.vue?vue&type=template&id=eb4ea0f8

const MyDuplicateFormvue_type_template_id_eb4ea0f8_hoisted_1 = {
  class: "myDuplicateForm"
};
const MyDuplicateFormvue_type_template_id_eb4ea0f8_hoisted_2 = {
  class: "myDuplicateForm__text"
};
const MyDuplicateFormvue_type_template_id_eb4ea0f8_hoisted_3 = {
  class: "myDuplicateForm__site"
};
const MyDuplicateFormvue_type_template_id_eb4ea0f8_hoisted_4 = ["disabled"];
function MyDuplicateFormvue_type_template_id_eb4ea0f8_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_SiteSelector = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SiteSelector");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyDuplicateFormvue_type_template_id_eb4ea0f8_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", MyDuplicateFormvue_type_template_id_eb4ea0f8_hoisted_2, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_DuplicateTo', _ctx.itemName)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyDuplicateFormvue_type_template_id_eb4ea0f8_hoisted_3, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SiteSelector, {
    modelValue: _ctx.site,
    "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.site = $event),
    "show-selected-site": true,
    "show-all-sites-item": false,
    "switch-site-on-select": false,
    "only-sites-with-at-least-write-access": true
  }, null, 8, ["modelValue"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
    type: "button",
    class: "btn myDuplicateForm__confirm",
    disabled: _ctx.isBusy,
    onClick: _cache[1] || (_cache[1] = $event => _ctx.$emit('confirm', _ctx.site))
  }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_Duplicate')), 9, MyDuplicateFormvue_type_template_id_eb4ea0f8_hoisted_4), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
    type: "button",
    class: "myDuplicateForm__cancel",
    onClick: _cache[2] || (_cache[2] = $event => _ctx.$emit('cancel'))
  }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Cancel')), 1)]);
}
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyDuplicateForm/MyDuplicateForm.vue?vue&type=template&id=eb4ea0f8

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyDuplicateForm/MyDuplicateForm.vue?vue&type=script&lang=ts


/* harmony default export */ var MyDuplicateFormvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    itemName: {
      type: String,
      required: true
    },
    isBusy: Boolean
  },
  emits: ['confirm', 'cancel'],
  components: {
    SiteSelector: external_CoreHome_["SiteSelector"]
  },
  data() {
    return {
      site: {
        id: external_CoreHome_["Matomo"].idSite,
        name: external_CoreHome_["Matomo"].helper.htmlDecode(external_CoreHome_["Matomo"].siteName)
      }
    };
  }
}));
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyDuplicateForm/MyDuplicateForm.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyDuplicateForm/MyDuplicateForm.vue



MyDuplicateFormvue_type_script_lang_ts.render = MyDuplicateFormvue_type_template_id_eb4ea0f8_render

/* harmony default export */ var MyDuplicateForm = (MyDuplicateFormvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportsList/MyReportsList.vue?vue&type=script&lang=ts



function namesById(items) {
  return Object.fromEntries(items.map(item => [item.id, item.name]));
}
/* harmony default export */ var MyReportsListvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    reports: {
      type: Array,
      required: true
    },
    dimensions: {
      type: Array,
      required: true
    },
    metrics: {
      type: Array,
      required: true
    },
    archiveStatus: {
      type: Array,
      required: true
    },
    isLoading: Boolean
  },
  emits: ['create', 'edit', 'deleted'],
  components: {
    ContentBlock: external_CoreHome_["ContentBlock"],
    MatomoLoader: external_CoreHome_["MatomoLoader"],
    MyDuplicateForm: MyDuplicateForm
  },
  directives: {
    ContentTable: external_CoreHome_["ContentTable"]
  },
  data() {
    return {
      reportToDeleteName: '',
      duplicated: null,
      dragged: null,
      isDuplicating: false,
      search: ''
    };
  },
  computed: {
    documentationUrl() {
      return `?${external_CoreHome_["MatomoUrl"].stringify({
        module: 'RebelMyReports',
        action: 'documentation'
      })}`;
    },
    dimensionNames() {
      return namesById(this.dimensions);
    },
    metricNames() {
      return namesById(this.metrics);
    },
    visibleReports() {
      const words = this.search.toLowerCase().split(/\s+/).filter(word => word);
      return this.reports.filter(report => {
        const text = [this.decode(report.name), this.decode(report.description), ...report.dimensions.map(id => this.dimensionNames[id] || id), ...report.metrics.map(id => this.metricNames[id] || id)].join(' ').toLowerCase();
        return words.every(word => text.includes(word));
      });
    },
    statusById() {
      return Object.fromEntries(this.archiveStatus.map(status => [status.idreport, status]));
    }
  },
  methods: {
    decode(text) {
      return external_CoreHome_["Matomo"].helper.htmlDecode(text);
    },
    canManage(report) {
      return report.idsite !== 0 || external_CoreHome_["Matomo"].hasSuperUserAccess;
    },
    reportUrl(report) {
      const query = external_CoreHome_["MatomoUrl"].stringify(Object.assign(Object.assign({}, external_CoreHome_["MatomoUrl"].hashParsed.value), {}, {
        category: report.category_id,
        subcategory: report.subcategory_id
      }));
      return `#?${query}`;
    },
    problemText(status) {
      if (!status.problem) {
        return '';
      }
      const [start, end] = status.problem.period.split(',');
      const type = status.problem.type.replace(/^./, letter => letter.toUpperCase());
      return Object(external_CoreHome_["translate"])(`RebelMyReports_Problem${type}`, start === end ? start : `${start} – ${end}`);
    },
    moveBy(report, step) {
      const movable = this.reports.filter(other => this.canManage(other));
      this.move(report, movable[movable.indexOf(report) + step] || null);
    },
    move(report, target) {
      if (!report || !target || report === target || !this.canManage(target)) {
        return;
      }
      const movable = this.reports.filter(other => this.canManage(other));
      movable.splice(movable.indexOf(target), 0, movable.splice(movable.indexOf(report), 1)[0]);
      // Reports the user cannot manage keep their place.
      const ordered = this.reports.map(other => this.canManage(other) ? movable.shift() : other);
      external_CoreHome_["AjaxHelper"].post({
        method: 'RebelMyReports.setReportOrder'
      }, {
        idMyReports: ordered.map(other => other.idreport)
      }).then(() => {
        this.$emit('deleted');
      });
    },
    setPaused(report, paused) {
      external_CoreHome_["AjaxHelper"].post({
        method: paused ? 'RebelMyReports.pauseReport' : 'RebelMyReports.resumeReport',
        idMyReport: report.idreport
      }).then(() => {
        this.$emit('deleted');
      });
    },
    duplicate(site) {
      const report = this.duplicated;
      if (!report) {
        return;
      }
      this.isDuplicating = true;
      external_CoreHome_["AjaxHelper"].post({
        method: 'RebelMyReports.duplicateReport',
        idMyReport: report.idreport
      }, {
        idDestinationSite: site.id
      }).then(() => {
        external_CoreHome_["NotificationsStore"].show({
          id: 'rebelMyReportsDuplicated',
          message: Object(external_CoreHome_["translate"])('RebelMyReports_DuplicatedTo', this.decode(report.name), site.name),
          context: 'success',
          type: 'transient'
        });
        this.duplicated = null;
        this.$emit('deleted');
      }).finally(() => {
        this.isDuplicating = false;
      });
    },
    confirmDelete(report) {
      this.reportToDeleteName = this.decode(report.name);
      external_CoreHome_["Matomo"].helper.modalConfirm(this.$refs.confirm, {
        yes: () => {
          external_CoreHome_["AjaxHelper"].post({
            method: 'RebelMyReports.deleteReport',
            idMyReport: report.idreport
          }).then(() => {
            this.$emit('deleted');
          });
        }
      });
    }
  }
}));
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportsList/MyReportsList.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportsList/MyReportsList.vue



MyReportsListvue_type_script_lang_ts.render = MyReportsListvue_type_template_id_755e6297_render

/* harmony default export */ var MyReportsList = (MyReportsListvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportEditor/MyReportEditor.vue?vue&type=template&id=ca82af3c

const MyReportEditorvue_type_template_id_ca82af3c_hoisted_1 = {
  class: "myReportEditor"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_2 = {
  class: "myReportEditor__form"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_3 = {
  class: "myReportEditor__field"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_4 = {
  class: "myReportEditor__field"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_5 = {
  class: "myReportEditor__field"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_6 = {
  class: "myReportEditor__picker"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_7 = {
  class: "myReportEditor__valueGroups"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_8 = {
  class: "myReportEditor__sectionTitle"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_9 = {
  class: "myReportEditor__help"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_10 = {
  class: "myReportEditor__row"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_11 = ["title", "aria-label", "onClick"];
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_12 = {
  class: "myReportEditor__groupValues"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_13 = ["title", "aria-label", "onClick"];
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_14 = ["name", "onUpdate:modelValue", "list", "placeholder", "aria-label", "onKeydown", "onChange", "onInput"];
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_15 = ["id"];
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_16 = ["value"];
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_17 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-add"
}, null, -1);
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_18 = {
  class: "myReportEditor__picker"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_19 = {
  class: "myReportEditor__sectionTitle"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_20 = {
  class: "myReportEditor__help"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_21 = {
  class: "myReportEditor__segment"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_22 = {
  class: "myReportEditor__sectionTitle"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_23 = {
  class: "myReportEditor__field"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_24 = {
  class: "myReportEditor__row"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_25 = {
  class: "myReportEditor__field"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_26 = ["title", "aria-label", "onClick"];
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_27 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-add"
}, null, -1);
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_28 = {
  class: "myReportEditor__help"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_29 = {
  class: "myReportEditor__sectionTitle"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_30 = {
  class: "myReportEditor__field"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_31 = {
  class: "myReportEditor__field"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_32 = {
  key: 0,
  class: "myReportEditor__field"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_33 = {
  key: 1,
  class: "myReportEditor__warning"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_34 = {
  class: "myReportEditor__actions"
};
const MyReportEditorvue_type_template_id_ca82af3c_hoisted_35 = {
  class: "myReportEditor__save"
};
const _hoisted_36 = {
  class: "myReportEditor__preview"
};
function MyReportEditorvue_type_template_id_ca82af3c_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Field = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Field");
  const _component_MyReportItemPicker = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MyReportItemPicker");
  const _component_SegmentGenerator = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SegmentGenerator");
  const _component_Notification = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Notification");
  const _component_SaveButton = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SaveButton");
  const _component_MyReportPreview = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MyReportPreview");
  const _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ContentBlock, {
    "content-title": _ctx.title
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("form", {
      class: "myReportEditor__layout",
      onSubmit: _cache[17] || (_cache[17] = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])($event => _ctx.save(), ["prevent"]))
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_2, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_3, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "text",
      name: "name",
      modelValue: _ctx.form.name,
      "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.form.name = $event),
      maxlength: 100,
      "full-width": true,
      title: _ctx.translate('General_Name')
    }, null, 8, ["modelValue", "title"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_4, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "textarea",
      name: "description",
      modelValue: _ctx.form.description,
      "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => _ctx.form.description = $event),
      maxlength: 1000,
      "full-width": true,
      title: _ctx.translate('General_Description'),
      "ui-control-attributes": {
        class: 'compact-textarea'
      }
    }, null, 8, ["modelValue", "title"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_5, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "radio",
      name: "reportType",
      modelValue: _ctx.form.reportType,
      "onUpdate:modelValue": _cache[2] || (_cache[2] = $event => _ctx.form.reportType = $event),
      "full-width": true,
      title: _ctx.translate('RebelMyReports_ReportType'),
      options: _ctx.reportTypes
    }, null, 8, ["modelValue", "title", "options"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", {
      class: "myReportEditor__help"
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_KeyFigureHelp')), 513), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], _ctx.form.reportType === 'keyfigure']]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_6, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_MyReportItemPicker, {
      name: "dimensions",
      modelValue: _ctx.form.dimensions,
      "onUpdate:modelValue": _cache[3] || (_cache[3] = $event => _ctx.form.dimensions = $event),
      items: _ctx.dimensions,
      max: _ctx.maxDimensions,
      "hide-site-specific": _ctx.form.allWebsites,
      title: _ctx.translate('RebelMyReports_Dimensions'),
      "add-title": _ctx.translate('RebelMyReports_AddDimension'),
      "limit-text": _ctx.translate('RebelMyReports_MaxDimensionsReached', _ctx.maxDimensions)
    }, null, 8, ["modelValue", "items", "max", "hide-site-specific", "title", "add-title", "limit-text"])], 512), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], _ctx.form.reportType === 'table']]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_7, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h3", MyReportEditorvue_type_template_id_ca82af3c_hoisted_8, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_ValueGroups')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", MyReportEditorvue_type_template_id_ca82af3c_hoisted_9, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_ValueGroupsHelp')), 1), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.form.valueGroups, (group, index) => {
      return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", {
        class: "myReportEditor__valueGroup",
        key: index
      }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_10, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "text",
        name: `groupName${index}`,
        modelValue: group.name,
        "onUpdate:modelValue": $event => group.name = $event,
        "full-width": true,
        maxlength: 100,
        title: _ctx.translate('RebelMyReports_ValueGroupName')
      }, null, 8, ["name", "modelValue", "onUpdate:modelValue", "title"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "select",
        name: `groupDimension${index}`,
        modelValue: group.dimension,
        "onUpdate:modelValue": $event => group.dimension = $event,
        "full-width": true,
        title: _ctx.translate('RebelMyReports_ValueGroupDimension'),
        options: _ctx.dimensionOptions
      }, null, 8, ["name", "modelValue", "onUpdate:modelValue", "title", "options"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "select",
        name: `groupOperator${index}`,
        modelValue: group.operator,
        "onUpdate:modelValue": $event => group.operator = $event,
        "full-width": true,
        title: _ctx.translate('RebelMyReports_ValueGroupOperator'),
        options: _ctx.groupOperatorOptions
      }, null, 8, ["name", "modelValue", "onUpdate:modelValue", "title", "options"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
        type: "button",
        class: "myReportEditor__removeFilter btn-flat icon-delete",
        title: _ctx.translate('General_Remove'),
        "aria-label": _ctx.translate('General_Remove'),
        onClick: $event => _ctx.form.valueGroups.splice(index, 1)
      }, null, 8, MyReportEditorvue_type_template_id_ca82af3c_hoisted_11)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_12, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(group.values, (value, valueIndex) => {
        return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", {
          class: "myReportEditor__groupValue",
          key: value
        }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.valueLabel(group.dimension, value)) + " ", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
          type: "button",
          class: "icon-close",
          title: _ctx.translate('General_Remove'),
          "aria-label": _ctx.translate('General_Remove'),
          onClick: $event => group.values.splice(valueIndex, 1)
        }, null, 8, MyReportEditorvue_type_template_id_ca82af3c_hoisted_13)]);
      }), 128)), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
        type: "text",
        class: "myReportEditor__groupValueInput browser-default",
        name: `groupValue${index}`,
        "onUpdate:modelValue": $event => group.draft = $event,
        maxlength: "255",
        list: `myReportValues_${group.dimension}`,
        placeholder: _ctx.translate('RebelMyReports_ValueGroupAddValue'),
        "aria-label": _ctx.translate('RebelMyReports_ValueGroupAddValue'),
        onKeydown: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withKeys"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])($event => _ctx.addGroupValue(group), ["prevent"]), ["enter"]),
        onChange: $event => _ctx.addGroupValue(group),
        onInput: $event => _ctx.onGroupValueInput(group, $event)
      }, null, 40, MyReportEditorvue_type_template_id_ca82af3c_hoisted_14), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vModelText"], group.draft]])])]);
    }), 128)), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.dimensionValues, (values, dimension) => {
      return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("datalist", {
        key: dimension,
        id: `myReportValues_${dimension}`
      }, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(values, item => {
        return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("option", {
          key: item.value,
          value: item.value
        }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(item.label), 9, MyReportEditorvue_type_template_id_ca82af3c_hoisted_16);
      }), 128))], 8, MyReportEditorvue_type_template_id_ca82af3c_hoisted_15);
    }), 128)), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "btn-flat myReportEditor__addValueGroup",
      onClick: _cache[4] || (_cache[4] = $event => _ctx.addValueGroup())
    }, [MyReportEditorvue_type_template_id_ca82af3c_hoisted_17, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_AddValueGroup')), 1)])], 512), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], _ctx.form.reportType === 'table' && _ctx.form.dimensions.length]]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_18, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_MyReportItemPicker, {
      name: "metrics",
      modelValue: _ctx.form.metrics,
      "onUpdate:modelValue": _cache[5] || (_cache[5] = $event => _ctx.form.metrics = $event),
      items: _ctx.metrics,
      max: _ctx.form.reportType === 'keyfigure' ? 1 : 0,
      "hide-site-specific": _ctx.form.allWebsites,
      title: _ctx.translate('General_Metrics'),
      "add-title": _ctx.translate('RebelMyReports_AddMetric'),
      "limit-text": _ctx.translate('RebelMyReports_MaxMetricsReached')
    }, null, 8, ["modelValue", "items", "max", "hide-site-specific", "title", "add-title", "limit-text"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h3", MyReportEditorvue_type_template_id_ca82af3c_hoisted_19, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_SegmentFilter')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", MyReportEditorvue_type_template_id_ca82af3c_hoisted_20, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_SegmentFilterHelp')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_21, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SegmentGenerator, {
      modelValue: _ctx.form.segmentFilter,
      "onUpdate:modelValue": _cache[6] || (_cache[6] = $event => _ctx.form.segmentFilter = $event)
    }, null, 8, ["modelValue"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h3", MyReportEditorvue_type_template_id_ca82af3c_hoisted_22, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_Rows')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_23, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "select",
      name: "visualization",
      modelValue: _ctx.form.visualization,
      "onUpdate:modelValue": _cache[7] || (_cache[7] = $event => _ctx.form.visualization = $event),
      "full-width": true,
      title: _ctx.translate('RebelMyReports_Visualization'),
      "inline-help": _ctx.translate('RebelMyReports_VisualizationHelp'),
      options: _ctx.visualizationOptions
    }, null, 8, ["modelValue", "title", "inline-help", "options"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_24, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "select",
      name: "sortMetric",
      modelValue: _ctx.form.sortMetric,
      "onUpdate:modelValue": _cache[8] || (_cache[8] = $event => _ctx.form.sortMetric = $event),
      "full-width": true,
      title: _ctx.translate('RebelMyReports_SortBy'),
      options: _ctx.sortMetricOptions
    }, null, 8, ["modelValue", "title", "options"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "select",
      name: "sortOrder",
      modelValue: _ctx.form.sortOrder,
      "onUpdate:modelValue": _cache[9] || (_cache[9] = $event => _ctx.form.sortOrder = $event),
      "full-width": true,
      title: _ctx.translate('RebelMyReports_SortOrder'),
      options: _ctx.sortOrderOptions
    }, null, 8, ["modelValue", "title", "options"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_25, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "number",
      name: "rowLimit",
      modelValue: _ctx.form.rowLimit,
      "onUpdate:modelValue": _cache[10] || (_cache[10] = $event => _ctx.form.rowLimit = $event),
      min: 0,
      "full-width": true,
      title: _ctx.translate('RebelMyReports_RowLimit'),
      "inline-help": _ctx.translate('RebelMyReports_RowLimitHelp')
    }, null, 8, ["modelValue", "title", "inline-help"])]), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.form.metricFilters, (filter, index) => {
      return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", {
        class: "myReportEditor__row",
        key: index
      }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "select",
        name: `filterMetric${index}`,
        modelValue: filter.metric,
        "onUpdate:modelValue": $event => filter.metric = $event,
        "full-width": true,
        title: _ctx.translate('RebelMyReports_OnlyRowsWhere'),
        options: _ctx.metricOptions
      }, null, 8, ["name", "modelValue", "onUpdate:modelValue", "title", "options"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "select",
        name: `filterOperator${index}`,
        modelValue: filter.operator,
        "onUpdate:modelValue": $event => filter.operator = $event,
        "full-width": true,
        title: _ctx.translate('RebelMyReports_FilterOperator'),
        options: _ctx.filterOperatorOptions
      }, null, 8, ["name", "modelValue", "onUpdate:modelValue", "title", "options"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
        uicontrol: "number",
        name: `filterValue${index}`,
        modelValue: filter.value,
        "onUpdate:modelValue": $event => filter.value = $event,
        "full-width": true,
        title: _ctx.translate('General_Value')
      }, null, 8, ["name", "modelValue", "onUpdate:modelValue", "title"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
        type: "button",
        class: "myReportEditor__removeFilter btn-flat icon-delete",
        title: _ctx.translate('General_Remove'),
        "aria-label": _ctx.translate('General_Remove'),
        onClick: $event => _ctx.form.metricFilters.splice(index, 1)
      }, null, 8, MyReportEditorvue_type_template_id_ca82af3c_hoisted_26)]);
    }), 128)), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "btn-flat myReportEditor__addFilter",
      onClick: _cache[11] || (_cache[11] = $event => _ctx.addFilter())
    }, [MyReportEditorvue_type_template_id_ca82af3c_hoisted_27, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_AddFilter')), 1)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", MyReportEditorvue_type_template_id_ca82af3c_hoisted_28, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_RowsHelp')), 1)], 512), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], _ctx.form.reportType === 'table']]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h3", MyReportEditorvue_type_template_id_ca82af3c_hoisted_29, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_WhereToShow')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_30, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "select",
      name: "category",
      modelValue: _ctx.form.category,
      "onUpdate:modelValue": _cache[12] || (_cache[12] = $event => _ctx.form.category = $event),
      "full-width": true,
      title: _ctx.translate('RebelMyReports_ReportingCategory'),
      options: _ctx.categoryOptions
    }, null, 8, ["modelValue", "title", "options"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_31, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "select",
      name: "subcategory",
      modelValue: _ctx.form.subcategory,
      "onUpdate:modelValue": _cache[13] || (_cache[13] = $event => _ctx.form.subcategory = $event),
      "full-width": true,
      title: _ctx.translate('RebelMyReports_ReportPage'),
      "inline-help": _ctx.translate('RebelMyReports_ReportPageHelp'),
      options: _ctx.pageOptions
    }, null, 8, ["modelValue", "title", "inline-help", "options"])]), _ctx.isSuperUser && !_ctx.report ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_32, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "checkbox",
      name: "allWebsites",
      modelValue: _ctx.form.allWebsites,
      "onUpdate:modelValue": _cache[14] || (_cache[14] = $event => _ctx.form.allWebsites = $event),
      "full-width": true,
      title: _ctx.translate('RebelMyReports_AvailableForAllWebsites'),
      "inline-help": _ctx.translate('RebelMyReports_AvailableForAllWebsitesHelp')
    }, null, 8, ["modelValue", "title", "inline-help"])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.changesData && _ctx.rearchiveMonths > 0 ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_33, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Notification, {
      context: "warning",
      noclear: true
    }, {
      default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_UpdateWarning', _ctx.rearchiveMonths)), 1)]),
      _: 1
    })])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_34, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportEditorvue_type_template_id_ca82af3c_hoisted_35, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SaveButton, {
      saving: _ctx.isSaving,
      disabled: !_ctx.canSave,
      value: _ctx.translate('General_Save'),
      onConfirm: _cache[15] || (_cache[15] = $event => _ctx.save())
    }, null, 8, ["saving", "disabled", "value"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "myReportEditor__cancel",
      onClick: _cache[16] || (_cache[16] = $event => _ctx.$emit('cancel'))
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Cancel')), 1)])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", _hoisted_36, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_MyReportPreview, {
      dimensions: _ctx.selectedDimensions,
      metrics: _ctx.form.metrics,
      "segment-filter": _ctx.form.segmentFilter,
      "row-settings": _ctx.rowSettings,
      visualization: _ctx.form.reportType === 'table' ? _ctx.form.visualization : 'table',
      "available-dimensions": _ctx.dimensions,
      "available-metrics": _ctx.metrics
    }, null, 8, ["dimensions", "metrics", "segment-filter", "row-settings", "visualization", "available-dimensions", "available-metrics"])])], 32)]),
    _: 1
  }, 8, ["content-title"])]);
}
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportEditor/MyReportEditor.vue?vue&type=template&id=ca82af3c

// EXTERNAL MODULE: external "SegmentEditor"
var external_SegmentEditor_ = __webpack_require__("f06f");

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportItemPicker/MyReportItemPicker.vue?vue&type=template&id=519d843a

const MyReportItemPickervue_type_template_id_519d843a_hoisted_1 = {
  class: "myReportItemPicker"
};
const MyReportItemPickervue_type_template_id_519d843a_hoisted_2 = {
  class: "myReportItemPicker__title"
};
const MyReportItemPickervue_type_template_id_519d843a_hoisted_3 = {
  key: 0,
  class: "myReportItemPicker__chipList"
};
const MyReportItemPickervue_type_template_id_519d843a_hoisted_4 = {
  class: "myReportItemPicker__position"
};
const MyReportItemPickervue_type_template_id_519d843a_hoisted_5 = {
  class: "myReportItemPicker__name"
};
const MyReportItemPickervue_type_template_id_519d843a_hoisted_6 = ["title", "disabled", "onClick"];
const MyReportItemPickervue_type_template_id_519d843a_hoisted_7 = ["title", "disabled", "onClick"];
const MyReportItemPickervue_type_template_id_519d843a_hoisted_8 = ["title", "onClick"];
const MyReportItemPickervue_type_template_id_519d843a_hoisted_9 = {
  key: 1,
  class: "myReportItemPicker__add"
};
const MyReportItemPickervue_type_template_id_519d843a_hoisted_10 = {
  key: 2,
  class: "myReportItemPicker__limit"
};
function MyReportItemPickervue_type_template_id_519d843a_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Field = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Field");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportItemPickervue_type_template_id_519d843a_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportItemPickervue_type_template_id_519d843a_hoisted_2, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.title), 1), _ctx.modelValue.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("ol", MyReportItemPickervue_type_template_id_519d843a_hoisted_3, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.modelValue, (id, index) => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("li", {
      class: "myReportItemPicker__chip",
      key: id
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", MyReportItemPickervue_type_template_id_519d843a_hoisted_4, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(index + 1), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", MyReportItemPickervue_type_template_id_519d843a_hoisted_5, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.names[id] || id), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "myReportItemPicker__button icon-arrow-left",
      title: _ctx.translate('RebelMyReports_MoveEarlier'),
      disabled: index === 0,
      onClick: $event => _ctx.move(index, -1)
    }, null, 8, MyReportItemPickervue_type_template_id_519d843a_hoisted_6), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "myReportItemPicker__button icon-arrow-right",
      title: _ctx.translate('RebelMyReports_MoveLater'),
      disabled: index === _ctx.modelValue.length - 1,
      onClick: $event => _ctx.move(index, 1)
    }, null, 8, MyReportItemPickervue_type_template_id_519d843a_hoisted_7), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "myReportItemPicker__button icon-close",
      title: _ctx.translate('General_Remove'),
      onClick: $event => _ctx.remove(index)
    }, null, 8, MyReportItemPickervue_type_template_id_519d843a_hoisted_8)]);
  }), 128))])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.canAdd ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportItemPickervue_type_template_id_519d843a_hoisted_9, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
    uicontrol: "expandable-select",
    name: _ctx.name,
    "full-width": true,
    "model-value": '',
    title: _ctx.addTitle,
    options: _ctx.options,
    "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.add($event))
  }, null, 8, ["name", "title", "options"])])) : (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", MyReportItemPickervue_type_template_id_519d843a_hoisted_10, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.limitText), 1))]);
}
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportItemPicker/MyReportItemPicker.vue?vue&type=template&id=519d843a

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportItemPicker/MyReportItemPicker.vue?vue&type=script&lang=ts


/* harmony default export */ var MyReportItemPickervue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    name: {
      type: String,
      required: true
    },
    title: {
      type: String,
      required: true
    },
    addTitle: {
      type: String,
      required: true
    },
    limitText: {
      type: String,
      default: ''
    },
    items: {
      type: Array,
      required: true
    },
    modelValue: {
      type: Array,
      required: true
    },
    max: {
      type: Number,
      default: 0
    },
    hideSiteSpecific: Boolean
  },
  emits: ['update:modelValue'],
  components: {
    Field: external_CorePluginsAdmin_["Field"]
  },
  computed: {
    names() {
      return Object.fromEntries(this.items.map(item => [item.id, item.name]));
    },
    canAdd() {
      return !this.max || this.modelValue.length < this.max;
    },
    options() {
      return this.items.filter(item => !this.modelValue.includes(item.id)).filter(item => !this.hideSiteSpecific || !item.siteSpecific).map(item => ({
        group: item.category,
        key: item.id,
        value: item.name
      }));
    }
  },
  methods: {
    add(id) {
      if (id && !this.modelValue.includes(id)) {
        this.$emit('update:modelValue', [...this.modelValue, id]);
      }
    },
    remove(index) {
      this.$emit('update:modelValue', this.modelValue.filter((id, i) => i !== index));
    },
    move(index, offset) {
      const ids = [...this.modelValue];
      [ids[index], ids[index + offset]] = [ids[index + offset], ids[index]];
      this.$emit('update:modelValue', ids);
    }
  }
}));
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportItemPicker/MyReportItemPicker.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportItemPicker/MyReportItemPicker.vue



MyReportItemPickervue_type_script_lang_ts.render = MyReportItemPickervue_type_template_id_519d843a_render

/* harmony default export */ var MyReportItemPicker = (MyReportItemPickervue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportPreview/MyReportPreview.vue?vue&type=template&id=86e8e0c6

const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_1 = {
  class: "myReportPreview"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_2 = {
  class: "myReportPreview__title"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_3 = {
  class: "myReportPreview__hint"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_4 = {
  key: 0,
  class: "myReportPreview__message"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_5 = {
  key: 1,
  class: "myReportPreview__message"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_6 = {
  class: "loadingPiwik"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_7 = {
  key: 2,
  class: "myReportPreview__message myReportPreview__message--error"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_8 = {
  key: 3,
  class: "myReportPreview__message"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_9 = {
  key: 4,
  class: "myReportPreview__message myReportPreview__message--error"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_10 = {
  class: "myReportPreview__hint"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_11 = {
  key: 0,
  class: "myReportPreview__bars"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_12 = ["title"];
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_13 = {
  class: "myReportPreview__barValue"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_14 = {
  class: "myReportPreview__barLabel"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_15 = {
  key: 1,
  class: "myReportPreview__pie"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_16 = {
  class: "myReportPreview__legend"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_17 = {
  key: 2,
  class: "myReportPreview__cloud"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_18 = ["title"];
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_19 = {
  key: 6,
  class: "myReportPreview__scroll"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_20 = {
  class: "myReportPreview__table"
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_21 = {
  key: 0
};
const MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_22 = {
  key: 0
};
function MyReportPreviewvue_type_template_id_86e8e0c6_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_MatomoLoader = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MatomoLoader");
  const _directive_content_table = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveDirective"])("content-table");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h3", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_2, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_Preview')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_3, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_PreviewHint')), 1), !_ctx.metrics.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_4, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_PreviewNeedsMetric')), 1)) : _ctx.isLoading ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_5, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_6, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_MatomoLoader), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_LoadingData')), 1)])])) : _ctx.error ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_7, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.error), 1)) : !_ctx.rows.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_8, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('CoreHome_ThereIsNoDataForThisReport')), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.slowSeconds && !_ctx.isLoading ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_9, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_PreviewSlow', _ctx.slowSeconds)), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.showsRows && _ctx.visualization !== 'table' ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], {
    key: 5
  }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_10, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.metricNames[_ctx.metrics[0]] || _ctx.metrics[0]), 1), _ctx.visualization === 'graphVerticalBar' ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_11, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.graphItems, (item, index) => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", {
      class: "myReportPreview__bar",
      key: index,
      title: `${item.label}: ${item.formatted}`
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_13, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(item.formatted), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
      class: "myReportPreview__barFill",
      style: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeStyle"])({
        height: `${item.share * 100}%`
      })
    }, null, 4), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_14, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(item.label), 1)], 8, MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_12);
  }), 128))])) : _ctx.visualization === 'graphPie' ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_15, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
    class: "myReportPreview__pieChart",
    style: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeStyle"])({
      background: _ctx.pieBackground
    })
  }, null, 4), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("ul", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_16, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.graphItems, (item, index) => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("li", {
      key: index
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
      class: "myReportPreview__dot",
      style: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeStyle"])({
        background: item.color
      })
    }, null, 4), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(item.label) + ": " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(item.formatted), 1)]);
  }), 128))])])) : (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_17, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.graphItems, (item, index) => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", {
      key: index,
      style: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeStyle"])({
        fontSize: `${12 + item.share * 18}px`
      }),
      title: item.formatted
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(item.label), 13, MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_18);
  }), 128))]))], 64)) : _ctx.showsRows ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_19, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])((Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("table", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_20, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("thead", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tr", null, [_ctx.dimensions.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("th", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_21, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.labelTitle), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.metrics, id => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("th", {
      key: id
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.metricNames[id] || id), 1);
  }), 128))])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tbody", null, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.rows, (row, index) => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tr", {
      key: index
    }, [_ctx.dimensions.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("td", MyReportPreviewvue_type_template_id_86e8e0c6_hoisted_22, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(row.label), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.metrics, id => {
      var _row$id;
      return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("td", {
        key: id
      }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])((_row$id = row[id]) !== null && _row$id !== void 0 ? _row$id : 0), 1);
    }), 128))]);
  }), 128))])])), [[_directive_content_table]])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]);
}
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportPreview/MyReportPreview.vue?vue&type=template&id=86e8e0c6

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportPreview/MyReportPreview.vue?vue&type=script&lang=ts


const GRAPH_COLORS = ['#3450a3', '#43a047', '#f4a261', '#d4291f', '#8e6bbf', '#2a9d8f', '#e9c46a', '#6c757d', '#e76f51', '#58a6d6'];
// A preview of one day that takes this long tells of a report that is heavy to archive.
const SLOW_SECONDS = 5;
/* harmony default export */ var MyReportPreviewvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    dimensions: {
      type: Array,
      required: true
    },
    metrics: {
      type: Array,
      required: true
    },
    segmentFilter: {
      type: String,
      default: ''
    },
    rowSettings: {
      type: Object,
      default: () => ({})
    },
    visualization: {
      type: String,
      default: 'table'
    },
    availableDimensions: {
      type: Array,
      required: true
    },
    availableMetrics: {
      type: Array,
      required: true
    }
  },
  components: {
    MatomoLoader: external_CoreHome_["MatomoLoader"]
  },
  directives: {
    ContentTable: external_CoreHome_["ContentTable"]
  },
  data() {
    return {
      isLoading: false,
      error: '',
      rows: [],
      requestId: 0,
      slowSeconds: 0,
      timer: 0
    };
  },
  created() {
    this.$watch(() => [this.dimensions, this.metrics, this.segmentFilter, this.rowSettings], () => {
      this.isLoading = this.metrics.length > 0;
      window.clearTimeout(this.timer);
      this.timer = window.setTimeout(() => this.fetch(), 600);
    }, {
      deep: true,
      immediate: true
    });
  },
  computed: {
    showsRows() {
      return this.metrics.length > 0 && !this.isLoading && !this.error && this.rows.length > 0;
    },
    // Like the graphs of Matomo, the preview of a graph shows the first metric.
    graphItems() {
      const values = this.rows.map(row => Math.max(0, Number(row.graph_value) || 0));
      const highest = Math.max(...values, 0) || 1;
      return this.rows.map((row, index) => {
        var _row$this$metrics$;
        return {
          label: `${row.label}`,
          formatted: (_row$this$metrics$ = row[this.metrics[0]]) !== null && _row$this$metrics$ !== void 0 ? _row$this$metrics$ : 0,
          value: values[index],
          share: values[index] / highest,
          color: GRAPH_COLORS[index % GRAPH_COLORS.length]
        };
      });
    },
    pieBackground() {
      const total = this.graphItems.reduce((sum, item) => sum + item.value, 0) || 1;
      let start = 0;
      const slices = this.graphItems.map(item => {
        const end = start + item.value / total * 100;
        const slice = `${item.color} ${start}% ${end}%`;
        start = end;
        return slice;
      });
      return `conic-gradient(${slices.join(', ')})`;
    },
    metricNames() {
      return Object.fromEntries(this.availableMetrics.map(item => [item.id, item.name]));
    },
    labelTitle() {
      const names = Object.fromEntries(this.availableDimensions.map(d => [d.id, d.name]));
      return this.dimensions.map(id => names[id] || id).join(' - ');
    }
  },
  methods: {
    fetch() {
      if (!this.metrics.length) {
        return;
      }
      this.requestId += 1;
      const {
        requestId
      } = this;
      const started = Date.now();
      this.slowSeconds = 0;
      external_CoreHome_["AjaxHelper"].fetch(Object.assign(Object.assign({
        method: 'RebelMyReports.previewReport',
        metrics: this.metrics,
        dimensions: this.dimensions,
        segmentFilter: this.segmentFilter
      }, this.rowSettings), {}, {
        format_metrics: 1
      }), {
        createErrorNotification: false
      }).then(response => {
        if (requestId === this.requestId) {
          this.rows = Array.isArray(response) ? response : [response];
          this.error = '';
          const seconds = Math.round((Date.now() - started) / 1000);
          this.slowSeconds = seconds >= SLOW_SECONDS ? seconds : 0;
        }
      }).catch(error => {
        if (requestId === this.requestId) {
          this.rows = [];
          this.error = (error === null || error === void 0 ? void 0 : error.message) || `${error}`;
        }
      }).finally(() => {
        if (requestId === this.requestId) {
          this.isLoading = false;
        }
      });
    }
  }
}));
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportPreview/MyReportPreview.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportPreview/MyReportPreview.vue



MyReportPreviewvue_type_script_lang_ts.render = MyReportPreviewvue_type_template_id_86e8e0c6_render

/* harmony default export */ var MyReportPreview = (MyReportPreviewvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportEditor/MyReportEditor.vue?vue&type=script&lang=ts
const _excluded = ["draft"];
function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }
function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }






// Names and values are stored HTML-encoded, like everything Matomo tracks.
function decodeGroups(groups) {
  return groups.map(group => ({
    dimension: group.dimension,
    name: external_CoreHome_["Matomo"].helper.htmlDecode(group.name),
    operator: group.operator,
    values: group.values.map(value => external_CoreHome_["Matomo"].helper.htmlDecode(value)),
    draft: ''
  }));
}
const NOTIFICATION_ID = 'rebelMyReportsSaved';
/* harmony default export */ var MyReportEditorvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    report: {
      type: Object,
      default: null
    },
    dimensions: {
      type: Array,
      required: true
    },
    metrics: {
      type: Array,
      required: true
    },
    categories: {
      type: Array,
      required: true
    },
    rearchiveMonths: {
      type: Number,
      default: 0
    },
    maxDimensions: {
      type: Number,
      default: 3
    }
  },
  emits: ['saved', 'cancel'],
  components: {
    ContentBlock: external_CoreHome_["ContentBlock"],
    Field: external_CorePluginsAdmin_["Field"],
    Notification: external_CoreHome_["Notification"],
    SaveButton: external_CorePluginsAdmin_["SaveButton"],
    SegmentGenerator: external_SegmentEditor_["SegmentGenerator"],
    MyReportItemPicker: MyReportItemPicker,
    MyReportPreview: MyReportPreview
  },
  data() {
    const {
      report
    } = this;
    return {
      isSaving: false,
      dimensionValues: {},
      form: {
        name: report ? external_CoreHome_["Matomo"].helper.htmlDecode(report.name) : '',
        description: report ? external_CoreHome_["Matomo"].helper.htmlDecode(report.description) : '',
        reportType: report ? report.report_type : 'table',
        dimensions: report ? [...report.dimensions] : [],
        metrics: report ? [...report.metrics] : ['nb_visits'],
        segmentFilter: report ? report.segment_filter : '',
        category: report ? report.category : '',
        subcategory: report ? report.subcategory : '',
        allWebsites: report ? report.idsite === 0 : false,
        sortMetric: report ? report.sort_metric : '',
        sortOrder: report ? report.sort_order : 'desc',
        rowLimit: report ? report.row_limit : 0,
        visualization: report ? report.visualization : 'table',
        metricFilters: report ? report.metric_filters.map(filter => Object.assign({}, filter)) : [],
        valueGroups: report ? decodeGroups(report.value_groups) : []
      }
    };
  },
  computed: {
    title() {
      return this.report ? Object(external_CoreHome_["translate"])('RebelMyReports_EditReport', external_CoreHome_["Matomo"].helper.htmlDecode(this.report.name)) : Object(external_CoreHome_["translate"])('RebelMyReports_CreateNewReport');
    },
    isSuperUser() {
      return external_CoreHome_["Matomo"].hasSuperUserAccess;
    },
    reportTypes() {
      return {
        table: Object(external_CoreHome_["translate"])('RebelMyReports_TypeTable'),
        evolution: Object(external_CoreHome_["translate"])('RebelMyReports_TypeEvolution'),
        keyfigure: Object(external_CoreHome_["translate"])('RebelMyReports_TypeKeyFigure')
      };
    },
    pageOptions() {
      const categoryId = this.form.category || 'RebelMyReports_MyReports';
      const category = this.categories.find(c => c.id === categoryId);
      return [{
        key: '',
        value: Object(external_CoreHome_["translate"])('RebelMyReports_NewPageForReport')
      }, ...((category === null || category === void 0 ? void 0 : category.pages) || []).map(page => ({
        key: page.id,
        value: external_CoreHome_["Matomo"].helper.htmlDecode(page.name)
      }))];
    },
    categoryOptions() {
      return [{
        key: '',
        value: Object(external_CoreHome_["translate"])('RebelMyReports_MyReports')
      }, ...this.categories.map(category => ({
        key: category.id,
        value: category.name
      }))];
    },
    metricOptions() {
      return this.form.metrics.map(id => {
        var _this$metrics$find;
        return {
          key: id,
          value: ((_this$metrics$find = this.metrics.find(metric => metric.id === id)) === null || _this$metrics$find === void 0 ? void 0 : _this$metrics$find.name) || id
        };
      });
    },
    rowSettings() {
      if (this.form.reportType !== 'table') {
        return {};
      }
      return {
        sortMetric: this.form.sortMetric,
        sortOrder: this.form.sortOrder,
        rowLimit: Number(this.form.rowLimit) || 0,
        metricFilters: this.form.metricFilters.map(filter => Object.assign(Object.assign({}, filter), {}, {
          value: Number(filter.value) || 0
        })),
        valueGroups: this.completeValueGroups
      };
    },
    // A group without value or name is still being filled in.
    completeValueGroups() {
      return this.form.valueGroups.map(group => {
        const draft = group.draft.trim();
        return {
          dimension: group.dimension,
          name: group.name.trim(),
          operator: group.operator,
          values: draft && !group.values.includes(draft) ? [...group.values, draft] : group.values
        };
      }).filter(group => group.name && group.values.length);
    },
    dimensionOptions() {
      return this.form.dimensions.map(id => {
        var _this$dimensions$find;
        return {
          key: id,
          value: ((_this$dimensions$find = this.dimensions.find(dimension => dimension.id === id)) === null || _this$dimensions$find === void 0 ? void 0 : _this$dimensions$find.name) || id
        };
      });
    },
    visualizationOptions() {
      return {
        table: Object(external_CoreHome_["translate"])('General_Table'),
        graphVerticalBar: Object(external_CoreHome_["translate"])('General_VBarGraph'),
        graphPie: Object(external_CoreHome_["translate"])('General_Piechart'),
        cloud: Object(external_CoreHome_["translate"])('General_TagCloud')
      };
    },
    groupOperatorOptions() {
      return {
        equals: Object(external_CoreHome_["translate"])('RebelMyReports_ValueGroupEquals'),
        contains: Object(external_CoreHome_["translate"])('RebelMyReports_ValueGroupContains'),
        starts: Object(external_CoreHome_["translate"])('RebelMyReports_ValueGroupStarts'),
        ends: Object(external_CoreHome_["translate"])('RebelMyReports_ValueGroupEnds')
      };
    },
    sortMetricOptions() {
      return [{
        key: '',
        value: Object(external_CoreHome_["translate"])('RebelMyReports_FirstMetric')
      }, ...this.metricOptions];
    },
    sortOrderOptions() {
      return {
        desc: Object(external_CoreHome_["translate"])('RebelMyReports_SortDescending'),
        asc: Object(external_CoreHome_["translate"])('RebelMyReports_SortAscending')
      };
    },
    filterOperatorOptions() {
      return {
        gt: Object(external_CoreHome_["translate"])('RebelMyReports_OperatorGreaterThan'),
        gte: Object(external_CoreHome_["translate"])('RebelMyReports_OperatorAtLeast'),
        lt: Object(external_CoreHome_["translate"])('RebelMyReports_OperatorLessThan'),
        lte: Object(external_CoreHome_["translate"])('RebelMyReports_OperatorAtMost'),
        eq: Object(external_CoreHome_["translate"])('RebelMyReports_OperatorEquals')
      };
    },
    selectedDimensions() {
      return this.form.reportType === 'table' ? this.form.dimensions : [];
    },
    changesData() {
      // Name, description, type and page only change how the archived data is shown.
      const {
        report
      } = this;
      return !!report && (JSON.stringify(this.selectedDimensions) !== JSON.stringify(report.dimensions) || JSON.stringify(this.rowSettings.valueGroups || []) !== JSON.stringify(decodeGroups(report.value_groups).map(_ref => {
        let {
            draft
          } = _ref,
          group = _objectWithoutProperties(_ref, _excluded);
        return group;
      })) || JSON.stringify(this.form.metrics) !== JSON.stringify(report.metrics) || this.form.segmentFilter !== report.segment_filter);
    },
    canSave() {
      const hasDimension = this.form.reportType !== 'table' || this.form.dimensions.length > 0;
      const hasCompleteGroups = this.form.reportType !== 'table' || this.completeValueGroups.length === this.form.valueGroups.length;
      return !!this.form.name.trim() && this.form.metrics.length > 0 && hasDimension && hasCompleteGroups;
    }
  },
  watch: {
    'form.reportType': function onTypeChange(type) {
      if (type === 'keyfigure') {
        this.form.metrics = this.form.metrics.slice(0, 1);
      }
    },
    'form.dimensions': function onDimensionsChange(dimensions) {
      this.form.valueGroups = this.form.valueGroups.filter(group => dimensions.includes(group.dimension));
    },
    'form.valueGroups': {
      deep: true,
      immediate: true,
      handler: function onValueGroupsChange(groups) {
        groups.forEach(group => this.fetchDimensionValues(group.dimension));
      }
    },
    'form.metrics': function onMetricsChange(metrics) {
      if (!metrics.includes(this.form.sortMetric)) {
        this.form.sortMetric = '';
      }
      this.form.metricFilters = this.form.metricFilters.filter(filter => metrics.includes(filter.metric));
    },
    'form.category': function onCategoryChange() {
      if (!this.pageOptions.some(page => page.key === this.form.subcategory)) {
        this.form.subcategory = '';
      }
    }
  },
  methods: {
    addValueGroup() {
      this.form.valueGroups.push({
        dimension: this.form.dimensions[0],
        operator: 'equals',
        values: [],
        name: '',
        draft: ''
      });
    },
    addGroupValue(group) {
      const value = group.draft.trim();
      if (value && !group.values.includes(value)) {
        group.values.push(value);
      }
      group.draft = '';
    },
    // Choosing a suggestion adds it right away. Typing does not, also when it equals one.
    onGroupValueInput(group, event) {
      const {
        inputType
      } = event;
      if (!inputType || inputType === 'insertReplacementText') {
        this.addGroupValue(group);
      }
    },
    valueLabel(dimension, value) {
      const known = (this.dimensionValues[dimension] || []).find(item => item.value === value);
      return known && known.label !== value ? `${known.label} (${value})` : value;
    },
    // The values to suggest. Without them the value can still be typed.
    fetchDimensionValues(dimension) {
      if (this.dimensionValues[dimension]) {
        return;
      }
      this.dimensionValues[dimension] = [];
      external_CoreHome_["AjaxHelper"].fetch({
        method: 'RebelMyReports.getDimensionValues',
        dimension,
        metric: this.form.metrics[0] || 'nb_visits',
        filter_limit: '-1'
      }, {
        createErrorNotification: false
      }).then(values => {
        this.dimensionValues[dimension] = values.map(item => ({
          value: external_CoreHome_["Matomo"].helper.htmlDecode(item.value),
          label: external_CoreHome_["Matomo"].helper.htmlDecode(item.label)
        }));
      }).catch(() => {
        // No suggestions.
      });
    },
    addFilter() {
      this.form.metricFilters.push({
        metric: this.form.metrics[0],
        operator: 'gt',
        value: 0
      });
    },
    save() {
      if (!this.canSave || this.isSaving) {
        return;
      }
      this.isSaving = true;
      const params = {
        method: this.report ? 'RebelMyReports.updateReport' : 'RebelMyReports.addReport'
      };
      if (this.report) {
        params.idMyReport = this.report.idreport;
      }
      external_CoreHome_["AjaxHelper"].post(params, Object.assign({
        name: this.form.name,
        description: this.form.description,
        reportType: this.form.reportType,
        dimensions: this.selectedDimensions,
        metrics: this.form.metrics,
        segmentFilter: this.form.segmentFilter,
        category: this.form.category,
        subcategory: this.form.subcategory,
        allWebsites: this.form.allWebsites ? 1 : 0,
        sortMetric: '',
        sortOrder: 'desc',
        rowLimit: 0,
        metricFilters: [],
        valueGroups: [],
        visualization: this.form.visualization
      }, this.rowSettings)).then(() => {
        external_CoreHome_["NotificationsStore"].show({
          id: NOTIFICATION_ID,
          message: Object(external_CoreHome_["translate"])('RebelMyReports_ReportSaved'),
          context: 'success',
          type: 'transient'
        });
        this.$emit('saved');
      }).finally(() => {
        this.isSaving = false;
      });
    }
  }
}));
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportEditor/MyReportEditor.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportEditor/MyReportEditor.vue



MyReportEditorvue_type_script_lang_ts.render = MyReportEditorvue_type_template_id_ca82af3c_render

/* harmony default export */ var MyReportEditor = (MyReportEditorvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyMetricsManager/MyMetricsManager.vue?vue&type=template&id=51007dc2

const MyMetricsManagervue_type_template_id_51007dc2_hoisted_1 = {
  class: "myMetricsManager"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_2 = {
  key: 0
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_3 = {
  key: 0
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_4 = {
  colspan: "4"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_5 = {
  class: "myMetricsManager__formula"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_6 = {
  class: "myMetricsManager__actions"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_7 = ["title", "onClick"];
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_8 = ["title", "onClick"];
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_9 = ["title", "onClick"];
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_10 = {
  key: 0,
  class: "myMetricsManager__duplicate"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_11 = {
  class: "tableActionBar"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_12 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-add"
}, null, -1);
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_13 = {
  class: "myMetricsManager__field"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_14 = {
  class: "myMetricsManager__field"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_15 = {
  class: "myMetricsManager__field"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_16 = {
  class: "myMetricsManager__field"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_17 = {
  class: "myMetricsManager__buttons"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_18 = {
  class: "myMetricsManager__save"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_19 = {
  class: "ui-confirm",
  ref: "confirm"
};
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_20 = ["value"];
const MyMetricsManagervue_type_template_id_51007dc2_hoisted_21 = ["value"];
function MyMetricsManagervue_type_template_id_51007dc2_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_MyDuplicateForm = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MyDuplicateForm");
  const _component_Field = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Field");
  const _component_SaveButton = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SaveButton");
  const _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");
  const _directive_content_table = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveDirective"])("content-table");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyMetricsManagervue_type_template_id_51007dc2_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ContentBlock, {
    "content-title": _ctx.translate('RebelMyReports_CalculatedMetrics')
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_CalculatedMetricsIntro')), 1), !_ctx.edited ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyMetricsManagervue_type_template_id_51007dc2_hoisted_2, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])((Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("table", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("thead", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tr", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Name')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_Formula')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_Format')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Actions')), 1)])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tbody", null, [!_ctx.calculatedMetrics.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tr", MyMetricsManagervue_type_template_id_51007dc2_hoisted_3, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", MyMetricsManagervue_type_template_id_51007dc2_hoisted_4, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_NoCalculatedMetricsYet')), 1)])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.calculatedMetrics, metric => {
      return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tr", {
        key: metric.idmetric
      }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.decode(metric.name)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("code", MyMetricsManagervue_type_template_id_51007dc2_hoisted_5, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(metric.formula), 1)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.formats[metric.format]), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", MyMetricsManagervue_type_template_id_51007dc2_hoisted_6, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
        type: "button",
        class: "table-action icon-edit",
        title: _ctx.translate('General_Edit'),
        onClick: $event => _ctx.edit(metric)
      }, null, 8, MyMetricsManagervue_type_template_id_51007dc2_hoisted_7), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
        type: "button",
        class: "table-action icon-content-copy",
        title: _ctx.translate('RebelMyReports_Duplicate'),
        onClick: $event => _ctx.duplicated = metric
      }, null, 8, MyMetricsManagervue_type_template_id_51007dc2_hoisted_8), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
        type: "button",
        class: "table-action icon-delete",
        title: _ctx.translate('General_Delete'),
        onClick: $event => _ctx.confirmDelete(metric)
      }, null, 8, MyMetricsManagervue_type_template_id_51007dc2_hoisted_9)])]);
    }), 128))])])), [[_directive_content_table]]), _ctx.duplicated ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyMetricsManagervue_type_template_id_51007dc2_hoisted_10, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_MyDuplicateForm, {
      key: _ctx.duplicated.idmetric,
      "item-name": _ctx.decode(_ctx.duplicated.name),
      "is-busy": _ctx.isDuplicating,
      onConfirm: _cache[0] || (_cache[0] = $event => _ctx.duplicate($event)),
      onCancel: _cache[1] || (_cache[1] = $event => _ctx.duplicated = null)
    }, null, 8, ["item-name", "is-busy"]))])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyMetricsManagervue_type_template_id_51007dc2_hoisted_11, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "btn",
      onClick: _cache[2] || (_cache[2] = $event => _ctx.edit(null))
    }, [MyMetricsManagervue_type_template_id_51007dc2_hoisted_12, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_CreateCalculatedMetric')), 1)])])])) : (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("form", {
      key: 1,
      class: "myMetricsManager__form",
      onSubmit: _cache[9] || (_cache[9] = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])($event => _ctx.save(), ["prevent"]))
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyMetricsManagervue_type_template_id_51007dc2_hoisted_13, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "text",
      name: "metricName",
      modelValue: _ctx.edited.name,
      "onUpdate:modelValue": _cache[3] || (_cache[3] = $event => _ctx.edited.name = $event),
      maxlength: 100,
      "full-width": true,
      title: _ctx.translate('General_Name')
    }, null, 8, ["modelValue", "title"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyMetricsManagervue_type_template_id_51007dc2_hoisted_14, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "text",
      name: "metricFormula",
      modelValue: _ctx.edited.formula,
      "onUpdate:modelValue": _cache[4] || (_cache[4] = $event => _ctx.edited.formula = $event),
      maxlength: 1000,
      "full-width": true,
      title: _ctx.translate('RebelMyReports_Formula'),
      "inline-help": _ctx.translate('RebelMyReports_FormulaHelp')
    }, null, 8, ["modelValue", "title", "inline-help"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyMetricsManagervue_type_template_id_51007dc2_hoisted_15, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "expandable-select",
      name: "insertMetric",
      "full-width": true,
      "model-value": '',
      title: _ctx.translate('RebelMyReports_InsertMetric'),
      options: _ctx.insertOptions,
      "onUpdate:modelValue": _cache[5] || (_cache[5] = $event => _ctx.insert($event))
    }, null, 8, ["title", "options"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyMetricsManagervue_type_template_id_51007dc2_hoisted_16, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "select",
      name: "metricFormat",
      modelValue: _ctx.edited.format,
      "onUpdate:modelValue": _cache[6] || (_cache[6] = $event => _ctx.edited.format = $event),
      "full-width": true,
      title: _ctx.translate('RebelMyReports_Format'),
      options: _ctx.formatOptions
    }, null, 8, ["modelValue", "title", "options"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyMetricsManagervue_type_template_id_51007dc2_hoisted_17, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyMetricsManagervue_type_template_id_51007dc2_hoisted_18, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SaveButton, {
      saving: _ctx.isSaving,
      disabled: !_ctx.edited.name.trim() || !_ctx.edited.formula.trim(),
      value: _ctx.translate('General_Save'),
      onConfirm: _cache[7] || (_cache[7] = $event => _ctx.save())
    }, null, 8, ["saving", "disabled", "value"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "myMetricsManager__cancel",
      onClick: _cache[8] || (_cache[8] = $event => _ctx.edited = null)
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Cancel')), 1)])], 32))]),
    _: 1
  }, 8, ["content-title"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyMetricsManagervue_type_template_id_51007dc2_hoisted_19, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h2", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_DeleteMetricConfirm', _ctx.metricToDeleteName)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
    role: "yes",
    type: "button",
    value: _ctx.translate('General_Yes')
  }, null, 8, MyMetricsManagervue_type_template_id_51007dc2_hoisted_20), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
    role: "no",
    type: "button",
    value: _ctx.translate('General_No')
  }, null, 8, MyMetricsManagervue_type_template_id_51007dc2_hoisted_21)], 512)]);
}
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyMetricsManager/MyMetricsManager.vue?vue&type=template&id=51007dc2

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyMetricsManager/MyMetricsManager.vue?vue&type=script&lang=ts




/* harmony default export */ var MyMetricsManagervue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    metrics: {
      type: Array,
      required: true
    }
  },
  emits: ['changed'],
  components: {
    ContentBlock: external_CoreHome_["ContentBlock"],
    Field: external_CorePluginsAdmin_["Field"],
    SaveButton: external_CorePluginsAdmin_["SaveButton"],
    MyDuplicateForm: MyDuplicateForm
  },
  directives: {
    ContentTable: external_CoreHome_["ContentTable"]
  },
  data() {
    return {
      calculatedMetrics: [],
      edited: null,
      isSaving: false,
      metricToDeleteName: '',
      duplicated: null,
      isDuplicating: false
    };
  },
  created() {
    this.fetch();
  },
  computed: {
    formats() {
      return {
        number: Object(external_CoreHome_["translate"])('RebelMyReports_FormatNumber'),
        percent: Object(external_CoreHome_["translate"])('RebelMyReports_FormatPercent'),
        duration: Object(external_CoreHome_["translate"])('RebelMyReports_FormatDuration'),
        money: Object(external_CoreHome_["translate"])('RebelMyReports_FormatMoney')
      };
    },
    formatOptions() {
      return Object.entries(this.formats).map(([key, value]) => ({
        key,
        value
      }));
    },
    insertOptions() {
      return this.metrics.filter(metric => metric.usableInFormula).map(metric => ({
        group: metric.category,
        key: metric.id,
        value: `${metric.name} (${metric.id})`
      }));
    }
  },
  methods: {
    decode(text) {
      return external_CoreHome_["Matomo"].helper.htmlDecode(text);
    },
    fetch() {
      return external_CoreHome_["AjaxHelper"].fetch({
        method: 'RebelMyReports.getCalculatedMetrics',
        filter_limit: '-1'
      }).then(metrics => {
        this.calculatedMetrics = metrics;
      });
    },
    edit(metric) {
      this.edited = metric ? {
        idmetric: metric.idmetric,
        name: this.decode(metric.name),
        formula: metric.formula,
        format: metric.format
      } : {
        idmetric: null,
        name: '',
        formula: '',
        format: 'number'
      };
    },
    insert(id) {
      if (id && this.edited) {
        this.edited.formula = `${this.edited.formula} ${id}`.trim();
      }
    },
    onChanged() {
      this.edited = null;
      this.fetch();
      this.$emit('changed');
    },
    save() {
      const {
        edited
      } = this;
      if (!edited || this.isSaving) {
        return;
      }
      this.isSaving = true;
      const params = {
        method: edited.idmetric ? 'RebelMyReports.updateCalculatedMetric' : 'RebelMyReports.addCalculatedMetric'
      };
      if (edited.idmetric) {
        params.idMetric = edited.idmetric;
      }
      external_CoreHome_["AjaxHelper"].post(params, {
        name: edited.name,
        formula: edited.formula,
        metricFormat: edited.format
      }).then(() => {
        this.onChanged();
      }).finally(() => {
        this.isSaving = false;
      });
    },
    duplicate(site) {
      const metric = this.duplicated;
      if (!metric) {
        return;
      }
      this.isDuplicating = true;
      external_CoreHome_["AjaxHelper"].post({
        method: 'RebelMyReports.duplicateCalculatedMetric',
        idMetric: metric.idmetric
      }, {
        idDestinationSite: site.id
      }).then(() => {
        external_CoreHome_["NotificationsStore"].show({
          id: 'rebelMyReportsDuplicated',
          message: Object(external_CoreHome_["translate"])('RebelMyReports_DuplicatedTo', this.decode(metric.name), site.name),
          context: 'success',
          type: 'transient'
        });
        this.duplicated = null;
        this.onChanged();
      }).finally(() => {
        this.isDuplicating = false;
      });
    },
    confirmDelete(metric) {
      this.metricToDeleteName = this.decode(metric.name);
      external_CoreHome_["Matomo"].helper.modalConfirm(this.$refs.confirm, {
        yes: () => {
          external_CoreHome_["AjaxHelper"].post({
            method: 'RebelMyReports.deleteCalculatedMetric',
            idMetric: metric.idmetric
          }).then(() => {
            this.onChanged();
          });
        }
      });
    }
  }
}));
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyMetricsManager/MyMetricsManager.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyMetricsManager/MyMetricsManager.vue



MyMetricsManagervue_type_script_lang_ts.render = MyMetricsManagervue_type_template_id_51007dc2_render

/* harmony default export */ var MyMetricsManager = (MyMetricsManagervue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/ManageMyReports/ManageMyReports.vue?vue&type=script&lang=ts





/* harmony default export */ var ManageMyReportsvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    rearchiveMonths: {
      type: Number,
      default: 0
    },
    maxDimensions: {
      type: Number,
      default: 3
    }
  },
  components: {
    MyReportsList: MyReportsList,
    MyReportEditor: MyReportEditor,
    MyMetricsManager: MyMetricsManager
  },
  data() {
    return {
      isLoading: true,
      isEditing: false,
      editedReport: null,
      reports: [],
      archiveStatus: [],
      dimensions: [],
      metrics: [],
      categories: []
    };
  },
  created() {
    Promise.all([this.fetchReports(), external_CoreHome_["AjaxHelper"].fetch({
      method: 'RebelMyReports.getAvailableDimensions',
      filter_limit: '-1'
    }), this.fetchMetrics(), external_CoreHome_["AjaxHelper"].fetch({
      method: 'RebelMyReports.getAvailableCategories',
      filter_limit: '-1'
    })]).then(([, dimensions,, categories]) => {
      this.dimensions = dimensions;
      this.categories = categories;
    }).finally(() => {
      this.isLoading = false;
    });
  },
  methods: {
    fetchReports() {
      // Not waited for: it reads the archive tables, which can take longer than the list.
      external_CoreHome_["AjaxHelper"].fetch({
        method: 'RebelMyReports.getArchiveStatus',
        filter_limit: '-1'
      }).then(archiveStatus => {
        this.archiveStatus = archiveStatus;
      });
      return external_CoreHome_["AjaxHelper"].fetch({
        method: 'RebelMyReports.getReports',
        filter_limit: '-1'
      }).then(reports => {
        this.reports = reports;
      });
    },
    fetchMetrics() {
      return external_CoreHome_["AjaxHelper"].fetch({
        method: 'RebelMyReports.getAvailableMetrics',
        filter_limit: '-1'
      }).then(metrics => {
        this.metrics = metrics;
      });
    },
    edit(report) {
      this.editedReport = report;
      this.isEditing = true;
      external_CoreHome_["Matomo"].helper.lazyScrollToContent();
    },
    onChanged() {
      this.isEditing = false;
      this.fetchReports();
      // The reports are pages of the reporting menu.
      external_CoreHome_["ReportingMenuStore"].reloadMenuItems();
    }
  }
}));
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/ManageMyReports/ManageMyReports.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/ManageMyReports/ManageMyReports.vue



ManageMyReportsvue_type_script_lang_ts.render = ManageMyReportsvue_type_template_id_72bd4005_render

/* harmony default export */ var ManageMyReports = (ManageMyReportsvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportExamples/MyReportExamples.vue?vue&type=template&id=4d349481

const MyReportExamplesvue_type_template_id_4d349481_hoisted_1 = {
  class: "myReportExamples"
};
const MyReportExamplesvue_type_template_id_4d349481_hoisted_2 = {
  class: "myReportExamples__site"
};
const MyReportExamplesvue_type_template_id_4d349481_hoisted_3 = {
  class: "myReportExamples__text"
};
const MyReportExamplesvue_type_template_id_4d349481_hoisted_4 = {
  class: "myReportExamples__name"
};
const MyReportExamplesvue_type_template_id_4d349481_hoisted_5 = {
  class: "myReportExamples__parts"
};
const MyReportExamplesvue_type_template_id_4d349481_hoisted_6 = ["disabled", "onClick"];
function MyReportExamplesvue_type_template_id_4d349481_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_SiteSelector = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SiteSelector");
  const _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportExamplesvue_type_template_id_4d349481_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ContentBlock, {
    "content-title": _ctx.translate('RebelMyReports_ExamplesTitle')
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_ExamplesIntro')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportExamplesvue_type_template_id_4d349481_hoisted_2, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SiteSelector, {
      modelValue: _ctx.site,
      "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.site = $event),
      "show-selected-site": true,
      "default-to-first-site": true,
      "show-all-sites-item": false,
      "switch-site-on-select": false,
      "only-sites-with-at-least-write-access": true
    }, null, 8, ["modelValue"])]), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.examples, example => {
      return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", {
        class: "myReportExamples__example",
        key: example.id
      }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportExamplesvue_type_template_id_4d349481_hoisted_3, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h3", MyReportExamplesvue_type_template_id_4d349481_hoisted_4, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(example.name), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(example.description), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", MyReportExamplesvue_type_template_id_4d349481_hoisted_5, [example.dimensions.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], {
        key: 0
      }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('RebelMyReports_Dimensions')) + ": " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(example.dimensions.join(', ')) + ". ", 1)], 64)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Metrics')) + ": " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(example.metrics.join(', ')) + ". ", 1)])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
        type: "button",
        class: "btn",
        disabled: !_ctx.site || _ctx.busy === example.id || _ctx.isCreated(example),
        onClick: $event => _ctx.create(example)
      }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate(_ctx.isCreated(example) ? 'RebelMyReports_ExampleCreated' : 'RebelMyReports_ExampleCreate')), 9, MyReportExamplesvue_type_template_id_4d349481_hoisted_6)]);
    }), 128))]),
    _: 1
  }, 8, ["content-title"])]);
}
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportExamples/MyReportExamples.vue?vue&type=template&id=4d349481

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportExamples/MyReportExamples.vue?vue&type=script&lang=ts


/* harmony default export */ var MyReportExamplesvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    examples: {
      type: Array,
      required: true
    }
  },
  components: {
    ContentBlock: external_CoreHome_["ContentBlock"],
    SiteSelector: external_CoreHome_["SiteSelector"]
  },
  data() {
    return {
      busy: '',
      created: [],
      // Without a site the selector chooses one.
      site: external_CoreHome_["Matomo"].idSite ? {
        id: external_CoreHome_["Matomo"].idSite,
        name: external_CoreHome_["Matomo"].helper.htmlDecode(external_CoreHome_["Matomo"].siteName)
      } : undefined
    };
  },
  methods: {
    isCreated(example) {
      var _this$site;
      return this.created.includes(`${(_this$site = this.site) === null || _this$site === void 0 ? void 0 : _this$site.id}:${example.id}`);
    },
    create(example) {
      var _this$site2;
      const idSite = (_this$site2 = this.site) === null || _this$site2 === void 0 ? void 0 : _this$site2.id;
      this.busy = example.id;
      external_CoreHome_["AjaxHelper"].post({
        method: 'RebelMyReports.addExampleReport',
        idSite
      }, {
        example: example.id
      }).then(() => {
        this.created.push(`${idSite}:${example.id}`);
        external_CoreHome_["NotificationsStore"].show({
          id: 'rebelMyReportsExampleCreated',
          message: Object(external_CoreHome_["translate"])('RebelMyReports_ReportSaved'),
          context: 'success',
          type: 'transient'
        });
      }).finally(() => {
        this.busy = '';
      });
    }
  }
}));
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportExamples/MyReportExamples.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportExamples/MyReportExamples.vue



MyReportExamplesvue_type_script_lang_ts.render = MyReportExamplesvue_type_template_id_4d349481_render

/* harmony default export */ var MyReportExamples = (MyReportExamplesvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportExport/MyReportExport.vue?vue&type=template&id=47682f32

const MyReportExportvue_type_template_id_47682f32_hoisted_1 = {
  class: "myReportExport",
  "data-report": "RebelMyReports.getReportData",
  ref: "root"
};
const MyReportExportvue_type_template_id_47682f32_hoisted_2 = ["title"];
const MyReportExportvue_type_template_id_47682f32_hoisted_3 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
  class: "icon-export"
}, null, -1);
function MyReportExportvue_type_template_id_47682f32_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _directive_report_export = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveDirective"])("report-export");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportExportvue_type_template_id_47682f32_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])((Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("a", {
    class: "myReportExport__link",
    href: "",
    title: _ctx.translate('General_ExportThisReport'),
    onClick: _cache[0] || (_cache[0] = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])(() => {}, ["prevent"]))
  }, [MyReportExportvue_type_template_id_47682f32_hoisted_3, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('General_Export')), 1)], 8, MyReportExportvue_type_template_id_47682f32_hoisted_2)), [[_directive_report_export, _ctx.exportBinding]])], 512);
}
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportExport/MyReportExport.vue?vue&type=template&id=47682f32

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportExport/MyReportExport.vue?vue&type=script&lang=ts


const {
  $
} = window;
/* harmony default export */ var MyReportExportvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    idMyReport: {
      type: Number,
      required: true
    },
    reportTitle: {
      type: String,
      default: ''
    }
  },
  directives: {
    ReportExport: external_CoreHome_["ReportExport"]
  },
  mounted() {
    // The export dialog reads the site, period and segment from the data table it belongs
    // to. A key figure has no data table, so it gets the same values from the page URL.
    $(this.$refs.root).data('uiControlObject', {
      param: {
        idSite: external_CoreHome_["Matomo"].idSite,
        period: external_CoreHome_["MatomoUrl"].parsed.value.period,
        date: external_CoreHome_["MatomoUrl"].parsed.value.date,
        segment: external_CoreHome_["MatomoUrl"].parsed.value.segment || '',
        filter_limit: -1
      },
      props: {}
    });
  },
  computed: {
    exportBinding() {
      return {
        reportTitle: external_CoreHome_["Matomo"].helper.htmlDecode(this.reportTitle),
        requestParams: {
          idMyReport: this.idMyReport
        },
        apiMethod: 'RebelMyReports.getReportData',
        reportFormats: {
          TSV: 'TSV (Excel)',
          HTML: 'HTML',
          JSON: 'JSON',
          XML: 'XML',
          CSV: 'CSV',
          RSS: 'RSS'
        },
        maxFilterLimit: 0,
        canExportFlat: false
      };
    }
  }
}));
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportExport/MyReportExport.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportExport/MyReportExport.vue



MyReportExportvue_type_script_lang_ts.render = MyReportExportvue_type_template_id_47682f32_render

/* harmony default export */ var MyReportExport = (MyReportExportvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportKeyFigure/MyReportKeyFigure.vue?vue&type=template&id=fb93f13c

const MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_1 = {
  class: "myReportKeyFigure"
};
const MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_2 = {
  class: "myReportKeyFigure__name"
};
const MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_3 = {
  class: "myReportKeyFigure__headline"
};
const MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_4 = {
  class: "myReportKeyFigure__value"
};
const MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_5 = {
  key: 0,
  class: "myReportKeyFigure__badge"
};
const MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_6 = {
  key: 0,
  class: "myReportKeyFigure__chart",
  ref: "chart"
};
const MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_7 = {
  class: "myReportKeyFigure__line",
  viewBox: "0 0 100 100",
  preserveAspectRatio: "none",
  "aria-hidden": "true"
};
const MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_8 = ["points"];
const MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_9 = ["title"];
const MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_10 = {
  key: 0,
  class: "myReportKeyFigure__label"
};
const MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_11 = {
  class: "myReportKeyFigure__export"
};
function MyReportKeyFigurevue_type_template_id_fb93f13c_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_EvolutionBadge = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("EvolutionBadge");
  const _component_MyReportExport = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MyReportExport");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_2, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.metricName), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_3, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_4, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.value), 1), _ctx.evolution ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_5, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_EvolutionBadge, {
    percent: _ctx.evolution.percent,
    trend: _ctx.evolution.trend,
    "is-lower-value-better": _ctx.evolution.isLowerValueBetter,
    tooltip: _ctx.evolution.tooltip
  }, null, 8, ["percent", "trend", "is-lower-value-better", "tooltip"])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.comparisons, (comparison, index) => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", {
      class: "myReportKeyFigure__comparison",
      key: index
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(comparison.label) + ": ", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("strong", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(comparison.formatted), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_EvolutionBadge, {
      percent: comparison.evolution.percent,
      trend: comparison.evolution.trend,
      "is-lower-value-better": comparison.evolution.isLowerValueBetter,
      tooltip: comparison.evolution.tooltip
    }, null, 8, ["percent", "trend", "is-lower-value-better", "tooltip"])]);
  }), 128)), _ctx.points.length > 1 ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_6, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("svg", MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_7, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("polyline", {
    points: _ctx.linePoints
  }, null, 8, MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_8)])), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.positions, (point, index) => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", {
      class: "myReportKeyFigure__point",
      key: index,
      style: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeStyle"])({
        left: `${point.x}%`,
        bottom: `${point.y}%`
      }),
      title: `${point.label}: ${point.formatted}`
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
      class: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeClass"])(["myReportKeyFigure__dot", {
        'myReportKeyFigure__dot--last': index === _ctx.positions.length - 1
      }])
    }, null, 2), point.showLabel ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_10, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(point.formatted), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)], 12, MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_9);
  }), 128))], 512)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", MyReportKeyFigurevue_type_template_id_fb93f13c_hoisted_11, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_MyReportExport, {
    "id-my-report": _ctx.idMyReport,
    "report-title": _ctx.reportTitle
  }, null, 8, ["id-my-report", "report-title"])])]);
}
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportKeyFigure/MyReportKeyFigure.vue?vue&type=template&id=fb93f13c

// EXTERNAL MODULE: external "CoreVisualizations"
var external_CoreVisualizations_ = __webpack_require__("b432");

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/RebelMyReports/vue/src/MyReportKeyFigure/MyReportKeyFigure.vue?vue&type=script&lang=ts



// Room one value label needs, in pixels.
const LABEL_WIDTH = 44;
/* harmony default export */ var MyReportKeyFigurevue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    idMyReport: {
      type: Number,
      required: true
    },
    reportTitle: {
      type: String,
      default: ''
    },
    metricName: {
      type: String,
      required: true
    },
    value: {
      type: [String, Number],
      required: true
    },
    points: {
      type: Array,
      default: () => []
    },
    evolution: {
      type: Object,
      default: null
    },
    comparisons: {
      type: Array,
      default: () => []
    }
  },
  components: {
    EvolutionBadge: external_CoreVisualizations_["EvolutionBadge"],
    MyReportExport: MyReportExport
  },
  data() {
    return {
      chartWidth: 0,
      resizeObserver: null
    };
  },
  mounted() {
    const chart = this.$refs.chart;
    if (!chart) {
      return;
    }
    this.chartWidth = chart.clientWidth;
    if (typeof ResizeObserver !== 'undefined') {
      this.resizeObserver = new ResizeObserver(() => {
        this.chartWidth = chart.clientWidth;
      });
      this.resizeObserver.observe(chart);
    }
  },
  beforeUnmount() {
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
  },
  computed: {
    // Every point has a label when there is room. Otherwise every nth one, counted from the
    // last point so that the current value is always labelled.
    labelStep() {
      if (!this.chartWidth) {
        return 1;
      }
      return Math.max(1, Math.ceil(this.points.length * LABEL_WIDTH / this.chartWidth));
    },
    positions() {
      const values = this.points.map(point => point.value);
      const max = Math.max(...values, 0);
      const min = Math.min(...values, 0);
      const range = max - min || 1;
      const last = this.points.length - 1;
      return this.points.map((point, index) => Object.assign(Object.assign({}, point), {}, {
        x: last > 0 ? index / last * 100 : 50,
        y: (point.value - min) / range * 100,
        showLabel: (last - index) % this.labelStep === 0
      }));
    },
    linePoints() {
      return this.positions.map(point => `${point.x},${100 - point.y}`).join(' ');
    }
  }
}));
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportKeyFigure/MyReportKeyFigure.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/MyReportKeyFigure/MyReportKeyFigure.vue



MyReportKeyFigurevue_type_script_lang_ts.render = MyReportKeyFigurevue_type_template_id_fb93f13c_render

/* harmony default export */ var MyReportKeyFigure = (MyReportKeyFigurevue_type_script_lang_ts);
// CONCATENATED MODULE: ./plugins/RebelMyReports/vue/src/index.ts











// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/entry-lib-no-default.js




/***/ })

/******/ });
});
//# sourceMappingURL=RebelMyReports.umd.js.map