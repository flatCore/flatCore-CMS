(function () {
    'use strict';

    var __assign = function () {
      __assign = Object.assign || function __assign(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
          s = arguments[i];
          for (var p in s)
            if (Object.prototype.hasOwnProperty.call(s, p))
              t[p] = s[p];
        }
        return t;
      };
      return __assign.apply(this, arguments);
    };

    var Global$1 = typeof window !== 'undefined' ? window : Function('return this;')();

    var jquery = function () {
      var _a;
      return (_a = Global$1 && Global$1.jQuery) !== null && _a !== void 0 ? _a : null;
    };
    var getJquery = function () {
      var jq = jquery();
      if (jq != null) {
        return jq;
      }
      throw new Error('Expected global jQuery');
    };

    const hasProto = (v, constructor, predicate) => {
      var _a;
      if (predicate(v, constructor.prototype)) {
        return true;
      } else {
        return ((_a = v.constructor) === null || _a === void 0 ? void 0 : _a.name) === constructor.name;
      }
    };
    const typeOf = x => {
      const t = typeof x;
      if (x === null) {
        return 'null';
      } else if (t === 'object' && Array.isArray(x)) {
        return 'array';
      } else if (t === 'object' && hasProto(x, String, (o, proto) => proto.isPrototypeOf(o))) {
        return 'string';
      } else {
        return t;
      }
    };
    const isType = type => value => typeOf(value) === type;
    const isSimpleType = type => value => typeof value === type;
    const isString = isType('string');
    const isObject = isType('object');
    const isArray = isType('array');
    const isFunction = isSimpleType('function');

    const keys = Object.keys;
    const hasOwnProperty = Object.hasOwnProperty;
    const has = (obj, key) => hasOwnProperty.call(obj, key);

    const cached = f => {
      let called = false;
      let r;
      return (...args) => {
        if (!called) {
          called = true;
          r = f.apply(null, args);
        }
        return r;
      };
    };

    const Global = typeof window !== 'undefined' ? window : Function('return this;')();

    const path = (parts, scope) => {
      let o = scope !== undefined && scope !== null ? scope : Global;
      for (let i = 0; i < parts.length && o !== undefined && o !== null; ++i) {
        o = o[parts[i]];
      }
      return o;
    };
    const resolve = (p, scope) => {
      const parts = p.split('.');
      return path(parts, scope);
    };

    const unsafe = (name, scope) => {
      return resolve(name, scope);
    };
    const getOrDie = (name, scope) => {
      const actual = unsafe(name, scope);
      if (actual === undefined || actual === null) {
        throw new Error(name + ' not available on this browser');
      }
      return actual;
    };

    const getPrototypeOf = Object.getPrototypeOf;
    const sandHTMLElement = scope => {
      return getOrDie('HTMLElement', scope);
    };
    const isPrototypeOf = x => {
      const scope = resolve('ownerDocument.defaultView', x);
      return isObject(x) && (sandHTMLElement(scope).prototype.isPrototypeOf(x) || /^HTML\w*Element$/.test(getPrototypeOf(x).constructor.name));
    };

    var tinymce = function () {
      var _a;
      return (_a = Global$1.tinymce) !== null && _a !== void 0 ? _a : null;
    };
    var hasTinymce = function () {
      return !!tinymce();
    };
    var getTinymce = function () {
      var tiny = tinymce();
      if (tiny != null) {
        return tiny;
      }
      throw new Error('Expected global tinymce');
    };
    var getTinymceInstance = function (element) {
      var ed = null;
      if (element && element.id && hasTinymce()) {
        ed = getTinymce().get(element.id);
      }
      return ed;
    };
    var withTinymceInstance = function (node, ifPresent, ifMissing) {
      var ed = getTinymceInstance(node);
      if (ed) {
        return ifPresent(ed);
      } else if (ifMissing) {
        return ifMissing(node);
      }
    };
    var LoadStatus;
    (function (LoadStatus) {
      LoadStatus[LoadStatus['NOT_LOADING'] = 0] = 'NOT_LOADING';
      LoadStatus[LoadStatus['LOADING_STARTED'] = 1] = 'LOADING_STARTED';
      LoadStatus[LoadStatus['LOADING_FINISHED'] = 2] = 'LOADING_FINISHED';
    }(LoadStatus || (LoadStatus = {})));
    var lazyLoading = LoadStatus.NOT_LOADING;
    var callbacks = [];
    var loadTinymce = function (url, callback) {
      if (!hasTinymce() && lazyLoading === LoadStatus.NOT_LOADING) {
        lazyLoading = LoadStatus.LOADING_STARTED;
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.onload = function (e) {
          if (lazyLoading !== LoadStatus.LOADING_FINISHED && e.type === 'load') {
            lazyLoading = LoadStatus.LOADING_FINISHED;
            var tiny = getTinymce();
            callback(tiny, true);
            for (var i = 0; i < callbacks.length; i++) {
              callbacks[i](tiny, false);
            }
          }
        };
        script.src = url;
        document.body.appendChild(script);
      } else {
        if (lazyLoading === LoadStatus.LOADING_STARTED) {
          callbacks.push(callback);
        } else {
          callback(getTinymce(), false);
        }
      }
    };

    var withEachContainedEditor = function (subject, callback) {
      subject.each(function (i, elem) {
        for (var _a = 0, _b = getTinymce().get(); _a < _b.length; _a++) {
          var editor = _b[_a];
          if ($.contains(elem, editor.getContentAreaContainer())) {
            if (callback(editor, elem, subject) === false) {
              return false;
            }
          }
        }
        return;
      });
    };
    var withEachLinkedEditor = function (subject, callback) {
      subject.each(function (_i, elm) {
        return withTinymceInstance(elm, function (ed) {
          return callback(ed, elm, subject);
        });
      });
    };
    var removeTargetElementEditor = function (subject) {
      return withEachLinkedEditor(subject, function (ed) {
        return ed.remove();
      });
    };
    var removeChildEditors = function (subject) {
      return withEachContainedEditor(subject, function (ed) {
        return ed.remove();
      });
    };
    var removeEditors = function (subject) {
      removeTargetElementEditor(subject);
      removeChildEditors(subject);
    };
    var patchJqAttr = function (origAttrFn) {
      return function () {
        var _this = this;
        var args = [];
        for (var _a = 0; _a < arguments.length; _a++) {
          args[_a] = arguments[_a];
        }
        var setValue = function (valueOrProducer) {
          if (valueOrProducer === undefined) {
            return;
          }
          removeChildEditors(_this);
          _this.each(function (i, elm) {
            return withTinymceInstance(elm, function (ed) {
              var value = isFunction(valueOrProducer) ? valueOrProducer.call(elm, i, ed.getContent()) : valueOrProducer;
              if (value !== undefined) {
                ed.setContent(value === null ? '' : ''.concat(value));
              }
            }, function (el) {
              if (isFunction(valueOrProducer)) {
                var origValue = origAttrFn.call($(el), 'value');
                var newValue = valueOrProducer.call(el, i, origValue);
                origAttrFn.call($(el), 'value', newValue);
              } else {
                origAttrFn.call($(el), 'value', valueOrProducer);
              }
            });
          });
        };
        var nameOrBatch = args[0];
        if (isString(nameOrBatch)) {
          var name_1 = nameOrBatch;
          if (name_1 !== 'value') {
            return origAttrFn.apply(this, args);
          }
          var value = args[1];
          if (value !== undefined) {
            setValue(value);
            return this;
          } else {
            if (this.length >= 1) {
              return withTinymceInstance(this[0], function (ed) {
                return ed.getContent();
              }, function (_elm) {
                return origAttrFn.call(_this, 'value');
              });
            }
            return undefined;
          }
        } else {
          var batch = __assign({}, nameOrBatch);
          if (has(batch, 'value')) {
            setValue(batch.value);
            delete batch.value;
          }
          return keys(batch).length > 0 ? origAttrFn.call(this, batch) : this;
        }
      };
    };
    var patchJqRemove = function (origFn) {
      return function (selector) {
        removeEditors(selector !== undefined ? this.filter(selector) : this);
        return origFn.call(this, selector);
      };
    };
    var patchJqEmpty = function (origFn) {
      return function () {
        removeChildEditors(this);
        withEachLinkedEditor(this, function (ed) {
          return void ed.setContent('');
        });
        return origFn.call(this);
      };
    };
    var stringifyContent = function (origFn, content) {
      var dummy = document.createElement('div');
      origFn.apply($(dummy), content);
      return dummy.innerHTML;
    };
    var patchJqPend = function (origFn, position) {
      return function () {
        var args = [];
        for (var _a = 0; _a < arguments.length; _a++) {
          args[_a] = arguments[_a];
        }
        var prepend = position === 'prepend';
        var contentStr;
        if (args.length === 1 && isFunction(args[0])) {
          var contentFn_1 = args[0];
          contentStr = function (el, origContent) {
            return stringifyContent(origFn, [contentFn_1.call(el, 0, origContent)]);
          };
        } else {
          var content_1 = args;
          contentStr = cached(function (_el, _origContent) {
            return stringifyContent(origFn, content_1);
          });
        }
        this.each(function (_i2, elm) {
          return withTinymceInstance(elm, function (ed) {
            var oldContent = ed.getContent();
            var addition = contentStr(elm, oldContent);
            ed.setContent(prepend ? addition + oldContent : oldContent + addition);
          }, function (el) {
            return void origFn.apply($(el), args);
          });
        });
        return this;
      };
    };
    var patchJqHtml = function (origFn) {
      return function (htmlOrNodeOrProducer) {
        if (htmlOrNodeOrProducer === undefined) {
          if (this.length >= 1) {
            return withTinymceInstance(this[0], function (ed) {
              return ed.getContent();
            }, function (el) {
              return origFn.call($(el));
            });
          }
          return undefined;
        } else {
          removeChildEditors(this);
          this.each(function (i, el) {
            withTinymceInstance(el, function (ed) {
              var htmlOrNode = isFunction(htmlOrNodeOrProducer) ? htmlOrNodeOrProducer.call(el, i, ed.getContent()) : htmlOrNodeOrProducer;
              var html = isString(htmlOrNode) ? htmlOrNode : function () {
                if (isPrototypeOf(htmlOrNode)) {
                  removeEditors($(htmlOrNode));
                }
                var elem = document.createElement('div');
                origFn.call($(elem), htmlOrNode);
                return elem.innerHTML;
              }();
              ed.setContent(html);
            }, function (elm) {
              if (isFunction(htmlOrNodeOrProducer)) {
                var origValue = origFn.call($(el));
                var newValue = htmlOrNodeOrProducer.call(el, i, origValue);
                origFn.call($(el), newValue);
              } else {
                origFn.call($(elm), htmlOrNodeOrProducer);
              }
            });
          });
          return this;
        }
      };
    };
    var patchJqText = function (origFn) {
      return function (valueOrProducer) {
        if (valueOrProducer === undefined) {
          var out_1 = '';
          this.each(function (_i, el) {
            out_1 += withTinymceInstance(el, function (ed) {
              return ed.getContent({ format: 'text' });
            }, function (elm) {
              return origFn.call($(elm));
            });
          });
          return out_1;
        } else {
          removeChildEditors(this);
          this.each(function (i, el) {
            withTinymceInstance(el, function (ed) {
              var val = isFunction(valueOrProducer) ? valueOrProducer.call(el, i, ed.getContent({ format: 'text' })) : valueOrProducer;
              var dummy = document.createElement('div');
              dummy.innerText = ''.concat(val);
              ed.setContent(dummy.innerHTML);
            }, function (elm) {
              if (isFunction(valueOrProducer)) {
                var origValue = origFn.call($(el));
                var newValue = valueOrProducer.call(el, i, origValue);
                origFn.call($(el), newValue);
              } else {
                origFn.call($(elm), valueOrProducer);
              }
            });
          });
          return this;
        }
      };
    };
    var patchJqVal = function (origFn) {
      return function (valueOrProducer) {
        if (valueOrProducer === undefined) {
          if (this.length >= 1) {
            return withTinymceInstance(this[0], function (ed) {
              return ed.getContent();
            }, function (elm) {
              return origFn.call($(elm));
            });
          }
          return undefined;
        } else {
          this.each(function (i, el) {
            withTinymceInstance(el, function (ed) {
              var val = isFunction(valueOrProducer) ? valueOrProducer.call(el, i, ed.getContent()) : valueOrProducer;
              var html = isArray(val) ? val.join('') : ''.concat(val);
              ed.setContent(html);
            }, function (elm) {
              if (isFunction(valueOrProducer)) {
                var origValue = origFn.call($(el));
                var newValue = valueOrProducer.call(el, i, origValue !== null && origValue !== void 0 ? origValue : '');
                origFn.call($(el), newValue);
              } else {
                origFn.call($(elm), valueOrProducer);
              }
            });
          });
        }
        return this;
      };
    };
    var patchJQueryFunctions = function (jq) {
      jq.fn.html = patchJqHtml(jq.fn.html);
      jq.fn.text = patchJqText(jq.fn.text);
      jq.fn.val = patchJqVal(jq.fn.val);
      jq.fn.append = patchJqPend(jq.fn.append, 'append');
      jq.fn.prepend = patchJqPend(jq.fn.prepend, 'prepend');
      jq.fn.remove = patchJqRemove(jq.fn.remove);
      jq.fn.empty = patchJqEmpty(jq.fn.empty);
      jq.fn.attr = patchJqAttr(jq.fn.attr);
    };

    var getScriptSrc = function (settings) {
      if (typeof settings.script_url === 'string') {
        return settings.script_url;
      } else {
        var channel = typeof settings.channel === 'string' ? settings.channel : '6';
        var apiKey = typeof settings.api_key === 'string' ? settings.api_key : 'no-api-key';
        return 'https://cdn.tiny.cloud/1/'.concat(apiKey, '/tinymce/').concat(channel, '/tinymce.min.js');
      }
    };
    var getEditors = function (tinymce, self) {
      var out = [];
      self.each(function (i, ele) {
        out.push(tinymce.get(ele.id));
      });
      return out;
    };
    var resolveFunction = function (tiny, fnOrStr) {
      if (typeof fnOrStr === 'string') {
        var func = tiny.resolve(fnOrStr);
        if (typeof func === 'function') {
          var scope = fnOrStr.indexOf('.') === -1 ? tiny : tiny.resolve(fnOrStr.replace(/\.\w+$/, ''));
          return func.bind(scope);
        }
      } else if (typeof fnOrStr === 'function') {
        return fnOrStr.bind(tiny);
      }
      return null;
    };
    var patchApplied = false;
    var tinymceFn = function (settings) {
      var _this = this;
      var _a;
      if (!this.length) {
        return !settings ? undefined : Promise.resolve([]);
      }
      if (!settings) {
        return (_a = getTinymceInstance(this[0])) !== null && _a !== void 0 ? _a : undefined;
      }
      this.css('visibility', 'hidden');
      return new Promise(function (resolve) {
        loadTinymce(getScriptSrc(settings), function (tinymce, loadedFromProvidedUrl) {
          if (loadedFromProvidedUrl && settings.script_loaded) {
            settings.script_loaded();
          }
          if (!patchApplied) {
            patchApplied = true;
            patchJQueryFunctions(getJquery());
          }
          var initCount = 0;
          var allInitCallback = resolveFunction(tinymce, settings.oninit);
          var allInitialized = function () {
            var editors = getEditors(tinymce, _this);
            if (allInitCallback) {
              allInitCallback(editors);
            }
            resolve(editors);
          };
          _this.each(function (_i, elm) {
            if (!elm.id) {
              elm.id = tinymce.DOM.uniqueId();
            }
            if (tinymce.get(elm.id)) {
              initCount++;
              return;
            }
            var initInstanceCallback = function (editor) {
              _this.css('visibility', '');
              initCount++;
              var origFn = settings.init_instance_callback;
              if (typeof origFn === 'function') {
                origFn.call(editor, editor);
              }
              if (initCount === _this.length) {
                allInitialized();
              }
            };
            tinymce.init(__assign(__assign({}, settings), {
              selector: undefined,
              target: elm,
              init_instance_callback: initInstanceCallback
            }));
          });
          if (initCount === _this.length) {
            allInitialized();
          }
        });
      });
    };
    var setupIntegration = function () {
      var jq = getJquery();
      jq.expr.pseudos.tinymce = function (e) {
        return !!getTinymceInstance(e);
      };
      jq.fn.tinymce = tinymceFn;
    };

    setupIntegration();

})();
