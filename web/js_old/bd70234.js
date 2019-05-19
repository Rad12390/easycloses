/* ===========================================================
 * Bootstrap: fileinput.js v3.0.0-p7
 * http://jasny.github.com/bootstrap/javascript.html#fileinput
 * ===========================================================
 * Copyright 2012 Jasny BV, Netherlands.
 *
 * Licensed under the Apache License, Version 2.0 (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

+function ($) { "use strict";

  var isIE = window.navigator.appName == 'Microsoft Internet Explorer'

  // FILEUPLOAD PUBLIC CLASS DEFINITION
  // =================================

  var Fileupload = function (element, options) {
    this.$element = $(element)
      
    this.$input = this.$element.find(':file')
    if (this.$input.length === 0) return

    this.name = this.$input.attr('name') || options.name

    this.$hidden = this.$element.find('input[type=hidden][name="'+this.name+'"]')
    if (this.$hidden.length === 0) {
      this.$hidden = $('<input type="hidden" />')
      this.$element.prepend(this.$hidden)
    }

    this.$preview = this.$element.find('.fileinput-preview')
    var height = this.$preview.css('height')
    if (this.$preview.css('display') != 'inline' && height != '0px' && height != 'none') this.$preview.css('line-height', height)

    this.original = {
      exists: this.$element.hasClass('fileinput-exists'),
      preview: this.$preview.html(),
      hiddenVal: this.$hidden.val()
    }
    
    this.listen()
  }
  
  Fileupload.prototype.listen = function() {
    this.$input.on('change.bs.fileinput', $.proxy(this.change, this))
    $(this.$input[0].form).on('reset.bs.fileinput', $.proxy(this.reset, this))
    
    this.$element.find('[data-trigger="fileinput"]').on('click.bs.fileinput', $.proxy(this.trigger, this))
    this.$element.find('[data-dismiss="fileinput"]').on('click.bs.fileinput', $.proxy(this.clear, this))
  },

  Fileupload.prototype.change = function(e) {
    if (e.target.files === undefined) e.target.files = e.target && e.target.value ? [ {name: e.target.value.replace(/^.+\\/, '')} ] : []
    if (e.target.files.length === 0) return

    this.$hidden.val('')
    this.$hidden.attr('name', '')
    this.$input.attr('name', this.name)

    var file = e.target.files[0]

    if (this.$preview.length > 0 && (typeof file.type !== "undefined" ? file.type.match('image.*') : file.name.match(/\.(gif|png|jpe?g)$/i)) && typeof FileReader !== "undefined") {
      var reader = new FileReader()
      var preview = this.$preview
      var element = this.$element

      reader.onload = function(re) {
        var $img = $('<img>') // .attr('src', re.target.result)
        $img[0].src = re.target.result
        e.target.files[0].result = re.target.result
        
        element.find('.fileinput-filename').text(file.name)
        
        // if parent has max-height, using `(max-)height: 100%` on child doesn't take padding and border into account
        if (preview.css('max-height') != 'none') $img.css('max-height', parseInt(preview.css('max-height'), 10) - parseInt(preview.css('padding-top'), 10) - parseInt(preview.css('padding-bottom'), 10)  - parseInt(preview.css('border-top'), 10) - parseInt(preview.css('border-bottom'), 10))
        
        preview.html($img)
        element.addClass('fileinput-exists').removeClass('fileinput-new')

        element.trigger('change.bs.fileinput', e.target.files)
      }

      reader.readAsDataURL(file)
    } else {
      this.$element.find('.fileinput-filename').text(file.name)
      this.$preview.text(file.name)
      
      this.$element.addClass('fileinput-exists').removeClass('fileinput-new')
      
      this.$element.trigger('change.bs.fileinput')
    }
  },

  Fileupload.prototype.clear = function(e) {
    if (e) e.preventDefault()
    
    this.$hidden.val('')
    this.$hidden.attr('name', this.name)
    this.$input.attr('name', '')

    //ie8+ doesn't support changing the value of input with type=file so clone instead
    if (isIE) { 
      var inputClone = this.$input.clone(true);
      this.$input.after(inputClone);
      this.$input.remove();
      this.$input = inputClone;
    } else {
      this.$input.val('')
    }

    this.$preview.html('')
    this.$element.find('.fileinput-filename').text('')
    this.$element.addClass('fileinput-new').removeClass('fileinput-exists')
    
    if (e !== false) {
      this.$input.trigger('change')
      this.$element.trigger('clear.bs.fileinput')
    }
  },

  Fileupload.prototype.reset = function() {
    this.clear(false)

    this.$hidden.val(this.original.hiddenVal)
    this.$preview.html(this.original.preview)
    this.$element.find('.fileinput-filename').text('')

    if (this.original.exists) this.$element.addClass('fileinput-exists').removeClass('fileinput-new')
     else this.$element.addClass('fileinput-new').removeClass('fileinput-exists')
    
    this.$element.trigger('reset.bs.fileinput')
  },

  Fileupload.prototype.trigger = function(e) {
    this.$input.trigger('click')
    e.preventDefault()
  }

  
  // FILEUPLOAD PLUGIN DEFINITION
  // ===========================

  $.fn.fileinput = function (options) {
    return this.each(function () {
      var $this = $(this)
      , data = $this.data('fileinput')
      if (!data) $this.data('fileinput', (data = new Fileupload(this, options)))
      if (typeof options == 'string') data[options]()
    })
  }

  $.fn.fileinput.Constructor = Fileupload


  // FILEUPLOAD DATA-API
  // ==================

  $(document).on('click.fileinput.data-api', '[data-provides="fileinput"]', function (e) {
    var $this = $(this)
    if ($this.data('fileinput')) return
    $this.fileinput($this.data())
      
    var $target = $(e.target).closest('[data-dismiss="fileinput"],[data-trigger="fileinput"]');
    if ($target.length > 0) {
      e.preventDefault()
      $target.trigger('click.bs.fileinput')
    }
  })

}(window.jQuery);
/*!

 handlebars v1.3.0

Copyright (C) 2011 by Yehuda Katz

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

@license
*/
/* exported Handlebars */
var Handlebars=function(){var t=function(){"use strict";function t(t){this.string=t}var e;return t.prototype.toString=function(){return""+this.string},e=t}(),e=function(t){"use strict";function e(t){return a[t]||"&amp;"}function s(t,e){for(var s in e)Object.prototype.hasOwnProperty.call(e,s)&&(t[s]=e[s])}function i(t){return t instanceof o?t.toString():t||0===t?(t=""+t,p.test(t)?t.replace(h,e):t):""}function n(t){return t||0===t?u(t)&&0===t.length?!0:!1:!0}var r={},o=t,a={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},h=/[&<>"'`]/g,p=/[&<>"'`]/;r.extend=s;var l=Object.prototype.toString;r.toString=l;var c=function(t){return"function"==typeof t};c(/x/)&&(c=function(t){return"function"==typeof t&&"[object Function]"===l.call(t)});var c;r.isFunction=c;var u=Array.isArray||function(t){return t&&"object"==typeof t?"[object Array]"===l.call(t):!1};return r.isArray=u,r.escapeExpression=i,r.isEmpty=n,r}(t),s=function(){"use strict";function t(t,e){var i;e&&e.firstLine&&(i=e.firstLine,t+=" - "+i+":"+e.firstColumn);for(var n=Error.prototype.constructor.call(this,t),r=0;r<s.length;r++)this[s[r]]=n[s[r]];i&&(this.lineNumber=i,this.column=e.firstColumn)}var e,s=["description","fileName","lineNumber","message","name","number","stack"];return t.prototype=new Error,e=t}(),i=function(t,e){"use strict";function s(t,e){this.helpers=t||{},this.partials=e||{},i(this)}function i(t){t.registerHelper("helperMissing",function(t){if(2===arguments.length)return void 0;throw new a("Missing helper: '"+t+"'")}),t.registerHelper("blockHelperMissing",function(e,s){var i=s.inverse||function(){},n=s.fn;return u(e)&&(e=e.call(this)),e===!0?n(this):e===!1||null==e?i(this):c(e)?e.length>0?t.helpers.each(e,s):i(this):n(e)}),t.registerHelper("each",function(t,e){var s,i=e.fn,n=e.inverse,r=0,o="";if(u(t)&&(t=t.call(this)),e.data&&(s=m(e.data)),t&&"object"==typeof t)if(c(t))for(var a=t.length;a>r;r++)s&&(s.index=r,s.first=0===r,s.last=r===t.length-1),o+=i(t[r],{data:s});else for(var h in t)t.hasOwnProperty(h)&&(s&&(s.key=h,s.index=r,s.first=0===r),o+=i(t[h],{data:s}),r++);return 0===r&&(o=n(this)),o}),t.registerHelper("if",function(t,e){return u(t)&&(t=t.call(this)),!e.hash.includeZero&&!t||o.isEmpty(t)?e.inverse(this):e.fn(this)}),t.registerHelper("unless",function(e,s){return t.helpers["if"].call(this,e,{fn:s.inverse,inverse:s.fn,hash:s.hash})}),t.registerHelper("with",function(t,e){return u(t)&&(t=t.call(this)),o.isEmpty(t)?void 0:e.fn(t)}),t.registerHelper("log",function(e,s){var i=s.data&&null!=s.data.level?parseInt(s.data.level,10):1;t.log(i,e)})}function n(t,e){g.log(t,e)}var r={},o=t,a=e,h="1.3.0";r.VERSION=h;var p=4;r.COMPILER_REVISION=p;var l={1:"<= 1.0.rc.2",2:"== 1.0.0-rc.3",3:"== 1.0.0-rc.4",4:">= 1.0.0"};r.REVISION_CHANGES=l;var c=o.isArray,u=o.isFunction,f=o.toString,d="[object Object]";r.HandlebarsEnvironment=s,s.prototype={constructor:s,logger:g,log:n,registerHelper:function(t,e,s){if(f.call(t)===d){if(s||e)throw new a("Arg not supported with multiple helpers");o.extend(this.helpers,t)}else s&&(e.not=s),this.helpers[t]=e},registerPartial:function(t,e){f.call(t)===d?o.extend(this.partials,t):this.partials[t]=e}};var g={methodMap:{0:"debug",1:"info",2:"warn",3:"error"},DEBUG:0,INFO:1,WARN:2,ERROR:3,level:3,log:function(t,e){if(g.level<=t){var s=g.methodMap[t];"undefined"!=typeof console&&console[s]&&console[s].call(console,e)}}};r.logger=g,r.log=n;var m=function(t){var e={};return o.extend(e,t),e};return r.createFrame=m,r}(e,s),n=function(t,e,s){"use strict";function i(t){var e=t&&t[0]||1,s=u;if(e!==s){if(s>e){var i=f[s],n=f[e];throw new c("Template was precompiled with an older version of Handlebars than the current runtime. Please update your precompiler to a newer version ("+i+") or downgrade your runtime to an older version ("+n+").")}throw new c("Template was precompiled with a newer version of Handlebars than the current runtime. Please update your runtime to a newer version ("+t[1]+").")}}function n(t,e){if(!e)throw new c("No environment passed to template");var s=function(t,s,i,n,r,o){var a=e.VM.invokePartial.apply(this,arguments);if(null!=a)return a;if(e.compile){var h={helpers:n,partials:r,data:o};return r[s]=e.compile(t,{data:void 0!==o},e),r[s](i,h)}throw new c("The partial "+s+" could not be compiled when running in runtime-only mode")},i={escapeExpression:l.escapeExpression,invokePartial:s,programs:[],program:function(t,e,s){var i=this.programs[t];return s?i=o(t,e,s):i||(i=this.programs[t]=o(t,e)),i},merge:function(t,e){var s=t||e;return t&&e&&t!==e&&(s={},l.extend(s,e),l.extend(s,t)),s},programWithDepth:e.VM.programWithDepth,noop:e.VM.noop,compilerInfo:null};return function(s,n){n=n||{};var r,o,a=n.partial?n:e;n.partial||(r=n.helpers,o=n.partials);var h=t.call(i,a,s,r,o,n.data);return n.partial||e.VM.checkRevision(i.compilerInfo),h}}function r(t,e,s){var i=Array.prototype.slice.call(arguments,3),n=function(t,n){return n=n||{},e.apply(this,[t,n.data||s].concat(i))};return n.program=t,n.depth=i.length,n}function o(t,e,s){var i=function(t,i){return i=i||{},e(t,i.data||s)};return i.program=t,i.depth=0,i}function a(t,e,s,i,n,r){var o={partial:!0,helpers:i,partials:n,data:r};if(void 0===t)throw new c("The partial "+e+" could not be found");return t instanceof Function?t(s,o):void 0}function h(){return""}var p={},l=t,c=e,u=s.COMPILER_REVISION,f=s.REVISION_CHANGES;return p.checkRevision=i,p.template=n,p.programWithDepth=r,p.program=o,p.invokePartial=a,p.noop=h,p}(e,s,i),r=function(t,e,s,i,n){"use strict";var r,o=t,a=e,h=s,p=i,l=n,c=function(){var t=new o.HandlebarsEnvironment;return p.extend(t,o),t.SafeString=a,t.Exception=h,t.Utils=p,t.VM=l,t.template=function(e){return l.template(e,t)},t},u=c();return u.create=c,r=u}(i,t,s,e,n),o=function(t){"use strict";function e(t){t=t||{},this.firstLine=t.first_line,this.firstColumn=t.first_column,this.lastColumn=t.last_column,this.lastLine=t.last_line}var s,i=t,n={ProgramNode:function(t,s,i,r){var o,a;3===arguments.length?(r=i,i=null):2===arguments.length&&(r=s,s=null),e.call(this,r),this.type="program",this.statements=t,this.strip={},i?(a=i[0],a?(o={first_line:a.firstLine,last_line:a.lastLine,last_column:a.lastColumn,first_column:a.firstColumn},this.inverse=new n.ProgramNode(i,s,o)):this.inverse=new n.ProgramNode(i,s),this.strip.right=s.left):s&&(this.strip.left=s.right)},MustacheNode:function(t,s,i,r,o){if(e.call(this,o),this.type="mustache",this.strip=r,null!=i&&i.charAt){var a=i.charAt(3)||i.charAt(2);this.escaped="{"!==a&&"&"!==a}else this.escaped=!!i;this.sexpr=t instanceof n.SexprNode?t:new n.SexprNode(t,s),this.sexpr.isRoot=!0,this.id=this.sexpr.id,this.params=this.sexpr.params,this.hash=this.sexpr.hash,this.eligibleHelper=this.sexpr.eligibleHelper,this.isHelper=this.sexpr.isHelper},SexprNode:function(t,s,i){e.call(this,i),this.type="sexpr",this.hash=s;var n=this.id=t[0],r=this.params=t.slice(1),o=this.eligibleHelper=n.isSimple;this.isHelper=o&&(r.length||s)},PartialNode:function(t,s,i,n){e.call(this,n),this.type="partial",this.partialName=t,this.context=s,this.strip=i},BlockNode:function(t,s,n,r,o){if(e.call(this,o),t.sexpr.id.original!==r.path.original)throw new i(t.sexpr.id.original+" doesn't match "+r.path.original,this);this.type="block",this.mustache=t,this.program=s,this.inverse=n,this.strip={left:t.strip.left,right:r.strip.right},(s||n).strip.left=t.strip.right,(n||s).strip.right=r.strip.left,n&&!s&&(this.isInverse=!0)},ContentNode:function(t,s){e.call(this,s),this.type="content",this.string=t},HashNode:function(t,s){e.call(this,s),this.type="hash",this.pairs=t},IdNode:function(t,s){e.call(this,s),this.type="ID";for(var n="",r=[],o=0,a=0,h=t.length;h>a;a++){var p=t[a].part;if(n+=(t[a].separator||"")+p,".."===p||"."===p||"this"===p){if(r.length>0)throw new i("Invalid path: "+n,this);".."===p?o++:this.isScoped=!0}else r.push(p)}this.original=n,this.parts=r,this.string=r.join("."),this.depth=o,this.isSimple=1===t.length&&!this.isScoped&&0===o,this.stringModeValue=this.string},PartialNameNode:function(t,s){e.call(this,s),this.type="PARTIAL_NAME",this.name=t.original},DataNode:function(t,s){e.call(this,s),this.type="DATA",this.id=t},StringNode:function(t,s){e.call(this,s),this.type="STRING",this.original=this.string=this.stringModeValue=t},IntegerNode:function(t,s){e.call(this,s),this.type="INTEGER",this.original=this.integer=t,this.stringModeValue=Number(t)},BooleanNode:function(t,s){e.call(this,s),this.type="BOOLEAN",this.bool=t,this.stringModeValue="true"===t},CommentNode:function(t,s){e.call(this,s),this.type="comment",this.comment=t}};return s=n}(s),a=function(){"use strict";var t,e=function(){function t(t,e){return{left:"~"===t.charAt(2),right:"~"===e.charAt(0)||"~"===e.charAt(1)}}function e(){this.yy={}}var s={trace:function(){},yy:{},symbols_:{error:2,root:3,statements:4,EOF:5,program:6,simpleInverse:7,statement:8,openInverse:9,closeBlock:10,openBlock:11,mustache:12,partial:13,CONTENT:14,COMMENT:15,OPEN_BLOCK:16,sexpr:17,CLOSE:18,OPEN_INVERSE:19,OPEN_ENDBLOCK:20,path:21,OPEN:22,OPEN_UNESCAPED:23,CLOSE_UNESCAPED:24,OPEN_PARTIAL:25,partialName:26,partial_option0:27,sexpr_repetition0:28,sexpr_option0:29,dataName:30,param:31,STRING:32,INTEGER:33,BOOLEAN:34,OPEN_SEXPR:35,CLOSE_SEXPR:36,hash:37,hash_repetition_plus0:38,hashSegment:39,ID:40,EQUALS:41,DATA:42,pathSegments:43,SEP:44,$accept:0,$end:1},terminals_:{2:"error",5:"EOF",14:"CONTENT",15:"COMMENT",16:"OPEN_BLOCK",18:"CLOSE",19:"OPEN_INVERSE",20:"OPEN_ENDBLOCK",22:"OPEN",23:"OPEN_UNESCAPED",24:"CLOSE_UNESCAPED",25:"OPEN_PARTIAL",32:"STRING",33:"INTEGER",34:"BOOLEAN",35:"OPEN_SEXPR",36:"CLOSE_SEXPR",40:"ID",41:"EQUALS",42:"DATA",44:"SEP"},productions_:[0,[3,2],[3,1],[6,2],[6,3],[6,2],[6,1],[6,1],[6,0],[4,1],[4,2],[8,3],[8,3],[8,1],[8,1],[8,1],[8,1],[11,3],[9,3],[10,3],[12,3],[12,3],[13,4],[7,2],[17,3],[17,1],[31,1],[31,1],[31,1],[31,1],[31,1],[31,3],[37,1],[39,3],[26,1],[26,1],[26,1],[30,2],[21,1],[43,3],[43,1],[27,0],[27,1],[28,0],[28,2],[29,0],[29,1],[38,1],[38,2]],performAction:function(e,s,i,n,r,o){var a=o.length-1;switch(r){case 1:return new n.ProgramNode(o[a-1],this._$);case 2:return new n.ProgramNode([],this._$);case 3:this.$=new n.ProgramNode([],o[a-1],o[a],this._$);break;case 4:this.$=new n.ProgramNode(o[a-2],o[a-1],o[a],this._$);break;case 5:this.$=new n.ProgramNode(o[a-1],o[a],[],this._$);break;case 6:this.$=new n.ProgramNode(o[a],this._$);break;case 7:this.$=new n.ProgramNode([],this._$);break;case 8:this.$=new n.ProgramNode([],this._$);break;case 9:this.$=[o[a]];break;case 10:o[a-1].push(o[a]),this.$=o[a-1];break;case 11:this.$=new n.BlockNode(o[a-2],o[a-1].inverse,o[a-1],o[a],this._$);break;case 12:this.$=new n.BlockNode(o[a-2],o[a-1],o[a-1].inverse,o[a],this._$);break;case 13:this.$=o[a];break;case 14:this.$=o[a];break;case 15:this.$=new n.ContentNode(o[a],this._$);break;case 16:this.$=new n.CommentNode(o[a],this._$);break;case 17:this.$=new n.MustacheNode(o[a-1],null,o[a-2],t(o[a-2],o[a]),this._$);break;case 18:this.$=new n.MustacheNode(o[a-1],null,o[a-2],t(o[a-2],o[a]),this._$);break;case 19:this.$={path:o[a-1],strip:t(o[a-2],o[a])};break;case 20:this.$=new n.MustacheNode(o[a-1],null,o[a-2],t(o[a-2],o[a]),this._$);break;case 21:this.$=new n.MustacheNode(o[a-1],null,o[a-2],t(o[a-2],o[a]),this._$);break;case 22:this.$=new n.PartialNode(o[a-2],o[a-1],t(o[a-3],o[a]),this._$);break;case 23:this.$=t(o[a-1],o[a]);break;case 24:this.$=new n.SexprNode([o[a-2]].concat(o[a-1]),o[a],this._$);break;case 25:this.$=new n.SexprNode([o[a]],null,this._$);break;case 26:this.$=o[a];break;case 27:this.$=new n.StringNode(o[a],this._$);break;case 28:this.$=new n.IntegerNode(o[a],this._$);break;case 29:this.$=new n.BooleanNode(o[a],this._$);break;case 30:this.$=o[a];break;case 31:o[a-1].isHelper=!0,this.$=o[a-1];break;case 32:this.$=new n.HashNode(o[a],this._$);break;case 33:this.$=[o[a-2],o[a]];break;case 34:this.$=new n.PartialNameNode(o[a],this._$);break;case 35:this.$=new n.PartialNameNode(new n.StringNode(o[a],this._$),this._$);break;case 36:this.$=new n.PartialNameNode(new n.IntegerNode(o[a],this._$));break;case 37:this.$=new n.DataNode(o[a],this._$);break;case 38:this.$=new n.IdNode(o[a],this._$);break;case 39:o[a-2].push({part:o[a],separator:o[a-1]}),this.$=o[a-2];break;case 40:this.$=[{part:o[a]}];break;case 43:this.$=[];break;case 44:o[a-1].push(o[a]);break;case 47:this.$=[o[a]];break;case 48:o[a-1].push(o[a])}},table:[{3:1,4:2,5:[1,3],8:4,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,11],22:[1,13],23:[1,14],25:[1,15]},{1:[3]},{5:[1,16],8:17,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,11],22:[1,13],23:[1,14],25:[1,15]},{1:[2,2]},{5:[2,9],14:[2,9],15:[2,9],16:[2,9],19:[2,9],20:[2,9],22:[2,9],23:[2,9],25:[2,9]},{4:20,6:18,7:19,8:4,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,21],20:[2,8],22:[1,13],23:[1,14],25:[1,15]},{4:20,6:22,7:19,8:4,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,21],20:[2,8],22:[1,13],23:[1,14],25:[1,15]},{5:[2,13],14:[2,13],15:[2,13],16:[2,13],19:[2,13],20:[2,13],22:[2,13],23:[2,13],25:[2,13]},{5:[2,14],14:[2,14],15:[2,14],16:[2,14],19:[2,14],20:[2,14],22:[2,14],23:[2,14],25:[2,14]},{5:[2,15],14:[2,15],15:[2,15],16:[2,15],19:[2,15],20:[2,15],22:[2,15],23:[2,15],25:[2,15]},{5:[2,16],14:[2,16],15:[2,16],16:[2,16],19:[2,16],20:[2,16],22:[2,16],23:[2,16],25:[2,16]},{17:23,21:24,30:25,40:[1,28],42:[1,27],43:26},{17:29,21:24,30:25,40:[1,28],42:[1,27],43:26},{17:30,21:24,30:25,40:[1,28],42:[1,27],43:26},{17:31,21:24,30:25,40:[1,28],42:[1,27],43:26},{21:33,26:32,32:[1,34],33:[1,35],40:[1,28],43:26},{1:[2,1]},{5:[2,10],14:[2,10],15:[2,10],16:[2,10],19:[2,10],20:[2,10],22:[2,10],23:[2,10],25:[2,10]},{10:36,20:[1,37]},{4:38,8:4,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,11],20:[2,7],22:[1,13],23:[1,14],25:[1,15]},{7:39,8:17,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,21],20:[2,6],22:[1,13],23:[1,14],25:[1,15]},{17:23,18:[1,40],21:24,30:25,40:[1,28],42:[1,27],43:26},{10:41,20:[1,37]},{18:[1,42]},{18:[2,43],24:[2,43],28:43,32:[2,43],33:[2,43],34:[2,43],35:[2,43],36:[2,43],40:[2,43],42:[2,43]},{18:[2,25],24:[2,25],36:[2,25]},{18:[2,38],24:[2,38],32:[2,38],33:[2,38],34:[2,38],35:[2,38],36:[2,38],40:[2,38],42:[2,38],44:[1,44]},{21:45,40:[1,28],43:26},{18:[2,40],24:[2,40],32:[2,40],33:[2,40],34:[2,40],35:[2,40],36:[2,40],40:[2,40],42:[2,40],44:[2,40]},{18:[1,46]},{18:[1,47]},{24:[1,48]},{18:[2,41],21:50,27:49,40:[1,28],43:26},{18:[2,34],40:[2,34]},{18:[2,35],40:[2,35]},{18:[2,36],40:[2,36]},{5:[2,11],14:[2,11],15:[2,11],16:[2,11],19:[2,11],20:[2,11],22:[2,11],23:[2,11],25:[2,11]},{21:51,40:[1,28],43:26},{8:17,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,11],20:[2,3],22:[1,13],23:[1,14],25:[1,15]},{4:52,8:4,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,11],20:[2,5],22:[1,13],23:[1,14],25:[1,15]},{14:[2,23],15:[2,23],16:[2,23],19:[2,23],20:[2,23],22:[2,23],23:[2,23],25:[2,23]},{5:[2,12],14:[2,12],15:[2,12],16:[2,12],19:[2,12],20:[2,12],22:[2,12],23:[2,12],25:[2,12]},{14:[2,18],15:[2,18],16:[2,18],19:[2,18],20:[2,18],22:[2,18],23:[2,18],25:[2,18]},{18:[2,45],21:56,24:[2,45],29:53,30:60,31:54,32:[1,57],33:[1,58],34:[1,59],35:[1,61],36:[2,45],37:55,38:62,39:63,40:[1,64],42:[1,27],43:26},{40:[1,65]},{18:[2,37],24:[2,37],32:[2,37],33:[2,37],34:[2,37],35:[2,37],36:[2,37],40:[2,37],42:[2,37]},{14:[2,17],15:[2,17],16:[2,17],19:[2,17],20:[2,17],22:[2,17],23:[2,17],25:[2,17]},{5:[2,20],14:[2,20],15:[2,20],16:[2,20],19:[2,20],20:[2,20],22:[2,20],23:[2,20],25:[2,20]},{5:[2,21],14:[2,21],15:[2,21],16:[2,21],19:[2,21],20:[2,21],22:[2,21],23:[2,21],25:[2,21]},{18:[1,66]},{18:[2,42]},{18:[1,67]},{8:17,9:5,11:6,12:7,13:8,14:[1,9],15:[1,10],16:[1,12],19:[1,11],20:[2,4],22:[1,13],23:[1,14],25:[1,15]},{18:[2,24],24:[2,24],36:[2,24]},{18:[2,44],24:[2,44],32:[2,44],33:[2,44],34:[2,44],35:[2,44],36:[2,44],40:[2,44],42:[2,44]},{18:[2,46],24:[2,46],36:[2,46]},{18:[2,26],24:[2,26],32:[2,26],33:[2,26],34:[2,26],35:[2,26],36:[2,26],40:[2,26],42:[2,26]},{18:[2,27],24:[2,27],32:[2,27],33:[2,27],34:[2,27],35:[2,27],36:[2,27],40:[2,27],42:[2,27]},{18:[2,28],24:[2,28],32:[2,28],33:[2,28],34:[2,28],35:[2,28],36:[2,28],40:[2,28],42:[2,28]},{18:[2,29],24:[2,29],32:[2,29],33:[2,29],34:[2,29],35:[2,29],36:[2,29],40:[2,29],42:[2,29]},{18:[2,30],24:[2,30],32:[2,30],33:[2,30],34:[2,30],35:[2,30],36:[2,30],40:[2,30],42:[2,30]},{17:68,21:24,30:25,40:[1,28],42:[1,27],43:26},{18:[2,32],24:[2,32],36:[2,32],39:69,40:[1,70]},{18:[2,47],24:[2,47],36:[2,47],40:[2,47]},{18:[2,40],24:[2,40],32:[2,40],33:[2,40],34:[2,40],35:[2,40],36:[2,40],40:[2,40],41:[1,71],42:[2,40],44:[2,40]},{18:[2,39],24:[2,39],32:[2,39],33:[2,39],34:[2,39],35:[2,39],36:[2,39],40:[2,39],42:[2,39],44:[2,39]},{5:[2,22],14:[2,22],15:[2,22],16:[2,22],19:[2,22],20:[2,22],22:[2,22],23:[2,22],25:[2,22]},{5:[2,19],14:[2,19],15:[2,19],16:[2,19],19:[2,19],20:[2,19],22:[2,19],23:[2,19],25:[2,19]},{36:[1,72]},{18:[2,48],24:[2,48],36:[2,48],40:[2,48]},{41:[1,71]},{21:56,30:60,31:73,32:[1,57],33:[1,58],34:[1,59],35:[1,61],40:[1,28],42:[1,27],43:26},{18:[2,31],24:[2,31],32:[2,31],33:[2,31],34:[2,31],35:[2,31],36:[2,31],40:[2,31],42:[2,31]},{18:[2,33],24:[2,33],36:[2,33],40:[2,33]}],defaultActions:{3:[2,2],16:[2,1],50:[2,42]},parseError:function(t){throw new Error(t)},parse:function(t){function e(){var t;return t=s.lexer.lex()||1,"number"!=typeof t&&(t=s.symbols_[t]||t),t}var s=this,i=[0],n=[null],r=[],o=this.table,a="",h=0,p=0,l=0;this.lexer.setInput(t),this.lexer.yy=this.yy,this.yy.lexer=this.lexer,this.yy.parser=this,"undefined"==typeof this.lexer.yylloc&&(this.lexer.yylloc={});var c=this.lexer.yylloc;r.push(c);var u=this.lexer.options&&this.lexer.options.ranges;"function"==typeof this.yy.parseError&&(this.parseError=this.yy.parseError);for(var f,d,g,m,v,y,S,k,x,b={};;){if(g=i[i.length-1],this.defaultActions[g]?m=this.defaultActions[g]:((null===f||"undefined"==typeof f)&&(f=e()),m=o[g]&&o[g][f]),"undefined"==typeof m||!m.length||!m[0]){var _="";if(!l){x=[];for(y in o[g])this.terminals_[y]&&y>2&&x.push("'"+this.terminals_[y]+"'");_=this.lexer.showPosition?"Parse error on line "+(h+1)+":\n"+this.lexer.showPosition()+"\nExpecting "+x.join(", ")+", got '"+(this.terminals_[f]||f)+"'":"Parse error on line "+(h+1)+": Unexpected "+(1==f?"end of input":"'"+(this.terminals_[f]||f)+"'"),this.parseError(_,{text:this.lexer.match,token:this.terminals_[f]||f,line:this.lexer.yylineno,loc:c,expected:x})}}if(m[0]instanceof Array&&m.length>1)throw new Error("Parse Error: multiple actions possible at state: "+g+", token: "+f);switch(m[0]){case 1:i.push(f),n.push(this.lexer.yytext),r.push(this.lexer.yylloc),i.push(m[1]),f=null,d?(f=d,d=null):(p=this.lexer.yyleng,a=this.lexer.yytext,h=this.lexer.yylineno,c=this.lexer.yylloc,l>0&&l--);break;case 2:if(S=this.productions_[m[1]][1],b.$=n[n.length-S],b._$={first_line:r[r.length-(S||1)].first_line,last_line:r[r.length-1].last_line,first_column:r[r.length-(S||1)].first_column,last_column:r[r.length-1].last_column},u&&(b._$.range=[r[r.length-(S||1)].range[0],r[r.length-1].range[1]]),v=this.performAction.call(b,a,p,h,this.yy,m[1],n,r),"undefined"!=typeof v)return v;S&&(i=i.slice(0,-1*S*2),n=n.slice(0,-1*S),r=r.slice(0,-1*S)),i.push(this.productions_[m[1]][0]),n.push(b.$),r.push(b._$),k=o[i[i.length-2]][i[i.length-1]],i.push(k);break;case 3:return!0}}return!0}},i=function(){var t={EOF:1,parseError:function(t,e){if(!this.yy.parser)throw new Error(t);this.yy.parser.parseError(t,e)},setInput:function(t){return this._input=t,this._more=this._less=this.done=!1,this.yylineno=this.yyleng=0,this.yytext=this.matched=this.match="",this.conditionStack=["INITIAL"],this.yylloc={first_line:1,first_column:0,last_line:1,last_column:0},this.options.ranges&&(this.yylloc.range=[0,0]),this.offset=0,this},input:function(){var t=this._input[0];this.yytext+=t,this.yyleng++,this.offset++,this.match+=t,this.matched+=t;var e=t.match(/(?:\r\n?|\n).*/g);return e?(this.yylineno++,this.yylloc.last_line++):this.yylloc.last_column++,this.options.ranges&&this.yylloc.range[1]++,this._input=this._input.slice(1),t},unput:function(t){var e=t.length,s=t.split(/(?:\r\n?|\n)/g);this._input=t+this._input,this.yytext=this.yytext.substr(0,this.yytext.length-e-1),this.offset-=e;var i=this.match.split(/(?:\r\n?|\n)/g);this.match=this.match.substr(0,this.match.length-1),this.matched=this.matched.substr(0,this.matched.length-1),s.length-1&&(this.yylineno-=s.length-1);var n=this.yylloc.range;return this.yylloc={first_line:this.yylloc.first_line,last_line:this.yylineno+1,first_column:this.yylloc.first_column,last_column:s?(s.length===i.length?this.yylloc.first_column:0)+i[i.length-s.length].length-s[0].length:this.yylloc.first_column-e},this.options.ranges&&(this.yylloc.range=[n[0],n[0]+this.yyleng-e]),this},more:function(){return this._more=!0,this},less:function(t){this.unput(this.match.slice(t))},pastInput:function(){var t=this.matched.substr(0,this.matched.length-this.match.length);return(t.length>20?"...":"")+t.substr(-20).replace(/\n/g,"")},upcomingInput:function(){var t=this.match;return t.length<20&&(t+=this._input.substr(0,20-t.length)),(t.substr(0,20)+(t.length>20?"...":"")).replace(/\n/g,"")},showPosition:function(){var t=this.pastInput(),e=new Array(t.length+1).join("-");return t+this.upcomingInput()+"\n"+e+"^"},next:function(){if(this.done)return this.EOF;this._input||(this.done=!0);var t,e,s,i,n;this._more||(this.yytext="",this.match="");for(var r=this._currentRules(),o=0;o<r.length&&(s=this._input.match(this.rules[r[o]]),!s||e&&!(s[0].length>e[0].length)||(e=s,i=o,this.options.flex));o++);return e?(n=e[0].match(/(?:\r\n?|\n).*/g),n&&(this.yylineno+=n.length),this.yylloc={first_line:this.yylloc.last_line,last_line:this.yylineno+1,first_column:this.yylloc.last_column,last_column:n?n[n.length-1].length-n[n.length-1].match(/\r?\n?/)[0].length:this.yylloc.last_column+e[0].length},this.yytext+=e[0],this.match+=e[0],this.matches=e,this.yyleng=this.yytext.length,this.options.ranges&&(this.yylloc.range=[this.offset,this.offset+=this.yyleng]),this._more=!1,this._input=this._input.slice(e[0].length),this.matched+=e[0],t=this.performAction.call(this,this.yy,this,r[i],this.conditionStack[this.conditionStack.length-1]),this.done&&this._input&&(this.done=!1),t?t:void 0):""===this._input?this.EOF:this.parseError("Lexical error on line "+(this.yylineno+1)+". Unrecognized text.\n"+this.showPosition(),{text:"",token:null,line:this.yylineno})},lex:function(){var t=this.next();return"undefined"!=typeof t?t:this.lex()},begin:function(t){this.conditionStack.push(t)},popState:function(){return this.conditionStack.pop()},_currentRules:function(){return this.conditions[this.conditionStack[this.conditionStack.length-1]].rules},topState:function(){return this.conditionStack[this.conditionStack.length-2]},pushState:function(t){this.begin(t)}};return t.options={},t.performAction=function(t,e,s,i){function n(t,s){return e.yytext=e.yytext.substr(t,e.yyleng-s)}switch(s){case 0:if("\\\\"===e.yytext.slice(-2)?(n(0,1),this.begin("mu")):"\\"===e.yytext.slice(-1)?(n(0,1),this.begin("emu")):this.begin("mu"),e.yytext)return 14;break;case 1:return 14;case 2:return this.popState(),14;case 3:return n(0,4),this.popState(),15;case 4:return 35;case 5:return 36;case 6:return 25;case 7:return 16;case 8:return 20;case 9:return 19;case 10:return 19;case 11:return 23;case 12:return 22;case 13:this.popState(),this.begin("com");break;case 14:return n(3,5),this.popState(),15;case 15:return 22;case 16:return 41;case 17:return 40;case 18:return 40;case 19:return 44;case 20:break;case 21:return this.popState(),24;case 22:return this.popState(),18;case 23:return e.yytext=n(1,2).replace(/\\"/g,'"'),32;case 24:return e.yytext=n(1,2).replace(/\\'/g,"'"),32;case 25:return 42;case 26:return 34;case 27:return 34;case 28:return 33;case 29:return 40;case 30:return e.yytext=n(1,2),40;case 31:return"INVALID";case 32:return 5}},t.rules=[/^(?:[^\x00]*?(?=(\{\{)))/,/^(?:[^\x00]+)/,/^(?:[^\x00]{2,}?(?=(\{\{|\\\{\{|\\\\\{\{|$)))/,/^(?:[\s\S]*?--\}\})/,/^(?:\()/,/^(?:\))/,/^(?:\{\{(~)?>)/,/^(?:\{\{(~)?#)/,/^(?:\{\{(~)?\/)/,/^(?:\{\{(~)?\^)/,/^(?:\{\{(~)?\s*else\b)/,/^(?:\{\{(~)?\{)/,/^(?:\{\{(~)?&)/,/^(?:\{\{!--)/,/^(?:\{\{![\s\S]*?\}\})/,/^(?:\{\{(~)?)/,/^(?:=)/,/^(?:\.\.)/,/^(?:\.(?=([=~}\s\/.)])))/,/^(?:[\/.])/,/^(?:\s+)/,/^(?:\}(~)?\}\})/,/^(?:(~)?\}\})/,/^(?:"(\\["]|[^"])*")/,/^(?:'(\\[']|[^'])*')/,/^(?:@)/,/^(?:true(?=([~}\s)])))/,/^(?:false(?=([~}\s)])))/,/^(?:-?[0-9]+(?=([~}\s)])))/,/^(?:([^\s!"#%-,\.\/;->@\[-\^`\{-~]+(?=([=~}\s\/.)]))))/,/^(?:\[[^\]]*\])/,/^(?:.)/,/^(?:$)/],t.conditions={mu:{rules:[4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32],inclusive:!1},emu:{rules:[2],inclusive:!1},com:{rules:[3],inclusive:!1},INITIAL:{rules:[0,1,32],inclusive:!0}},t}();return s.lexer=i,e.prototype=s,s.Parser=e,new e}();return t=e}(),h=function(t,e){"use strict";function s(t){return t.constructor===r.ProgramNode?t:(n.yy=r,n.parse(t))}var i={},n=t,r=e;return i.parser=n,i.parse=s,i}(a,o),p=function(t){"use strict";function e(){}function s(t,e,s){if(null==t||"string"!=typeof t&&t.constructor!==s.AST.ProgramNode)throw new r("You must pass a string or Handlebars AST to Handlebars.precompile. You passed "+t);e=e||{},"data"in e||(e.data=!0);var i=s.parse(t),n=(new s.Compiler).compile(i,e);return(new s.JavaScriptCompiler).compile(n,e)}function i(t,e,s){function i(){var i=s.parse(t),n=(new s.Compiler).compile(i,e),r=(new s.JavaScriptCompiler).compile(n,e,void 0,!0);return s.template(r)}if(null==t||"string"!=typeof t&&t.constructor!==s.AST.ProgramNode)throw new r("You must pass a string or Handlebars AST to Handlebars.compile. You passed "+t);e=e||{},"data"in e||(e.data=!0);var n;return function(t,e){return n||(n=i()),n.call(this,t,e)}}var n={},r=t;return n.Compiler=e,e.prototype={compiler:e,disassemble:function(){for(var t,e,s,i=this.opcodes,n=[],r=0,o=i.length;o>r;r++)if(t=i[r],"DECLARE"===t.opcode)n.push("DECLARE "+t.name+"="+t.value);else{e=[];for(var a=0;a<t.args.length;a++)s=t.args[a],"string"==typeof s&&(s='"'+s.replace("\n","\\n")+'"'),e.push(s);n.push(t.opcode+" "+e.join(" "))}return n.join("\n")},equals:function(t){var e=this.opcodes.length;if(t.opcodes.length!==e)return!1;for(var s=0;e>s;s++){var i=this.opcodes[s],n=t.opcodes[s];if(i.opcode!==n.opcode||i.args.length!==n.args.length)return!1;for(var r=0;r<i.args.length;r++)if(i.args[r]!==n.args[r])return!1}if(e=this.children.length,t.children.length!==e)return!1;for(s=0;e>s;s++)if(!this.children[s].equals(t.children[s]))return!1;return!0},guid:0,compile:function(t,e){this.opcodes=[],this.children=[],this.depths={list:[]},this.options=e;var s=this.options.knownHelpers;if(this.options.knownHelpers={helperMissing:!0,blockHelperMissing:!0,each:!0,"if":!0,unless:!0,"with":!0,log:!0},s)for(var i in s)this.options.knownHelpers[i]=s[i];return this.accept(t)},accept:function(t){var e,s=t.strip||{};return s.left&&this.opcode("strip"),e=this[t.type](t),s.right&&this.opcode("strip"),e},program:function(t){for(var e=t.statements,s=0,i=e.length;i>s;s++)this.accept(e[s]);return this.isSimple=1===i,this.depths.list=this.depths.list.sort(function(t,e){return t-e}),this},compileProgram:function(t){var e,s=(new this.compiler).compile(t,this.options),i=this.guid++;this.usePartial=this.usePartial||s.usePartial,this.children[i]=s;for(var n=0,r=s.depths.list.length;r>n;n++)e=s.depths.list[n],2>e||this.addDepth(e-1);return i},block:function(t){var e=t.mustache,s=t.program,i=t.inverse;s&&(s=this.compileProgram(s)),i&&(i=this.compileProgram(i));var n=e.sexpr,r=this.classifySexpr(n);"helper"===r?this.helperSexpr(n,s,i):"simple"===r?(this.simpleSexpr(n),this.opcode("pushProgram",s),this.opcode("pushProgram",i),this.opcode("emptyHash"),this.opcode("blockValue")):(this.ambiguousSexpr(n,s,i),this.opcode("pushProgram",s),this.opcode("pushProgram",i),this.opcode("emptyHash"),this.opcode("ambiguousBlockValue")),this.opcode("append")},hash:function(t){var e,s,i=t.pairs;this.opcode("pushHash");for(var n=0,r=i.length;r>n;n++)e=i[n],s=e[1],this.options.stringParams?(s.depth&&this.addDepth(s.depth),this.opcode("getContext",s.depth||0),this.opcode("pushStringParam",s.stringModeValue,s.type),"sexpr"===s.type&&this.sexpr(s)):this.accept(s),this.opcode("assignToHash",e[0]);this.opcode("popHash")},partial:function(t){var e=t.partialName;this.usePartial=!0,t.context?this.ID(t.context):this.opcode("push","depth0"),this.opcode("invokePartial",e.name),this.opcode("append")},content:function(t){this.opcode("appendContent",t.string)},mustache:function(t){this.sexpr(t.sexpr),this.opcode(t.escaped&&!this.options.noEscape?"appendEscaped":"append")},ambiguousSexpr:function(t,e,s){var i=t.id,n=i.parts[0],r=null!=e||null!=s;this.opcode("getContext",i.depth),this.opcode("pushProgram",e),this.opcode("pushProgram",s),this.opcode("invokeAmbiguous",n,r)},simpleSexpr:function(t){var e=t.id;"DATA"===e.type?this.DATA(e):e.parts.length?this.ID(e):(this.addDepth(e.depth),this.opcode("getContext",e.depth),this.opcode("pushContext")),this.opcode("resolvePossibleLambda")},helperSexpr:function(t,e,s){var i=this.setupFullMustacheParams(t,e,s),n=t.id.parts[0];if(this.options.knownHelpers[n])this.opcode("invokeKnownHelper",i.length,n);else{if(this.options.knownHelpersOnly)throw new r("You specified knownHelpersOnly, but used the unknown helper "+n,t);this.opcode("invokeHelper",i.length,n,t.isRoot)}},sexpr:function(t){var e=this.classifySexpr(t);"simple"===e?this.simpleSexpr(t):"helper"===e?this.helperSexpr(t):this.ambiguousSexpr(t)},ID:function(t){this.addDepth(t.depth),this.opcode("getContext",t.depth);var e=t.parts[0];e?this.opcode("lookupOnContext",t.parts[0]):this.opcode("pushContext");for(var s=1,i=t.parts.length;i>s;s++)this.opcode("lookup",t.parts[s])},DATA:function(t){if(this.options.data=!0,t.id.isScoped||t.id.depth)throw new r("Scoped data references are not supported: "+t.original,t);this.opcode("lookupData");for(var e=t.id.parts,s=0,i=e.length;i>s;s++)this.opcode("lookup",e[s])},STRING:function(t){this.opcode("pushString",t.string)},INTEGER:function(t){this.opcode("pushLiteral",t.integer)},BOOLEAN:function(t){this.opcode("pushLiteral",t.bool)},comment:function(){},opcode:function(t){this.opcodes.push({opcode:t,args:[].slice.call(arguments,1)})},declare:function(t,e){this.opcodes.push({opcode:"DECLARE",name:t,value:e})},addDepth:function(t){0!==t&&(this.depths[t]||(this.depths[t]=!0,this.depths.list.push(t)))},classifySexpr:function(t){var e=t.isHelper,s=t.eligibleHelper,i=this.options;if(s&&!e){var n=t.id.parts[0];i.knownHelpers[n]?e=!0:i.knownHelpersOnly&&(s=!1)}return e?"helper":s?"ambiguous":"simple"},pushParams:function(t){for(var e,s=t.length;s--;)e=t[s],this.options.stringParams?(e.depth&&this.addDepth(e.depth),this.opcode("getContext",e.depth||0),this.opcode("pushStringParam",e.stringModeValue,e.type),"sexpr"===e.type&&this.sexpr(e)):this[e.type](e)},setupFullMustacheParams:function(t,e,s){var i=t.params;return this.pushParams(i),this.opcode("pushProgram",e),this.opcode("pushProgram",s),t.hash?this.hash(t.hash):this.opcode("emptyHash"),i}},n.precompile=s,n.compile=i,n}(s),l=function(t,e){"use strict";function s(t){this.value=t}function i(){}var n,r=t.COMPILER_REVISION,o=t.REVISION_CHANGES,a=t.log,h=e;i.prototype={nameLookup:function(t,e){var s,n;return 0===t.indexOf("depth")&&(s=!0),n=/^[0-9]+$/.test(e)?t+"["+e+"]":i.isValidJavaScriptVariableName(e)?t+"."+e:t+"['"+e+"']",s?"("+t+" && "+n+")":n},compilerInfo:function(){var t=r,e=o[t];return"this.compilerInfo = ["+t+",'"+e+"'];\n"},appendToBuffer:function(t){return this.environment.isSimple?"return "+t+";":{appendToBuffer:!0,content:t,toString:function(){return"buffer += "+t+";"}}},initializeBuffer:function(){return this.quotedString("")},namespace:"Handlebars",compile:function(t,e,s,i){this.environment=t,this.options=e||{},a("debug",this.environment.disassemble()+"\n\n"),this.name=this.environment.name,this.isChild=!!s,this.context=s||{programs:[],environments:[],aliases:{}},this.preamble(),this.stackSlot=0,this.stackVars=[],this.registers={list:[]},this.hashes=[],this.compileStack=[],this.inlineStack=[],this.compileChildren(t,e);
var n,r=t.opcodes;this.i=0;for(var o=r.length;this.i<o;this.i++)n=r[this.i],"DECLARE"===n.opcode?this[n.name]=n.value:this[n.opcode].apply(this,n.args),n.opcode!==this.stripNext&&(this.stripNext=!1);if(this.pushSource(""),this.stackSlot||this.inlineStack.length||this.compileStack.length)throw new h("Compile completed with content left on stack");return this.createFunctionContext(i)},preamble:function(){var t=[];if(this.isChild)t.push("");else{var e=this.namespace,s="helpers = this.merge(helpers, "+e+".helpers);";this.environment.usePartial&&(s=s+" partials = this.merge(partials, "+e+".partials);"),this.options.data&&(s+=" data = data || {};"),t.push(s)}t.push(this.environment.isSimple?"":", buffer = "+this.initializeBuffer()),this.lastContext=0,this.source=t},createFunctionContext:function(t){var e=this.stackVars.concat(this.registers.list);if(e.length>0&&(this.source[1]=this.source[1]+", "+e.join(", ")),!this.isChild)for(var s in this.context.aliases)this.context.aliases.hasOwnProperty(s)&&(this.source[1]=this.source[1]+", "+s+"="+this.context.aliases[s]);this.source[1]&&(this.source[1]="var "+this.source[1].substring(2)+";"),this.isChild||(this.source[1]+="\n"+this.context.programs.join("\n")+"\n"),this.environment.isSimple||this.pushSource("return buffer;");for(var i=this.isChild?["depth0","data"]:["Handlebars","depth0","helpers","partials","data"],n=0,r=this.environment.depths.list.length;r>n;n++)i.push("depth"+this.environment.depths.list[n]);var o=this.mergeSource();if(this.isChild||(o=this.compilerInfo()+o),t)return i.push(o),Function.apply(this,i);var h="function "+(this.name||"")+"("+i.join(",")+") {\n  "+o+"}";return a("debug",h+"\n\n"),h},mergeSource:function(){for(var t,e="",s=0,i=this.source.length;i>s;s++){var n=this.source[s];n.appendToBuffer?t=t?t+"\n    + "+n.content:n.content:(t&&(e+="buffer += "+t+";\n  ",t=void 0),e+=n+"\n  ")}return e},blockValue:function(){this.context.aliases.blockHelperMissing="helpers.blockHelperMissing";var t=["depth0"];this.setupParams(0,t),this.replaceStack(function(e){return t.splice(1,0,e),"blockHelperMissing.call("+t.join(", ")+")"})},ambiguousBlockValue:function(){this.context.aliases.blockHelperMissing="helpers.blockHelperMissing";var t=["depth0"];this.setupParams(0,t);var e=this.topStack();t.splice(1,0,e),this.pushSource("if (!"+this.lastHelper+") { "+e+" = blockHelperMissing.call("+t.join(", ")+"); }")},appendContent:function(t){this.pendingContent&&(t=this.pendingContent+t),this.stripNext&&(t=t.replace(/^\s+/,"")),this.pendingContent=t},strip:function(){this.pendingContent&&(this.pendingContent=this.pendingContent.replace(/\s+$/,"")),this.stripNext="strip"},append:function(){this.flushInline();var t=this.popStack();this.pushSource("if("+t+" || "+t+" === 0) { "+this.appendToBuffer(t)+" }"),this.environment.isSimple&&this.pushSource("else { "+this.appendToBuffer("''")+" }")},appendEscaped:function(){this.context.aliases.escapeExpression="this.escapeExpression",this.pushSource(this.appendToBuffer("escapeExpression("+this.popStack()+")"))},getContext:function(t){this.lastContext!==t&&(this.lastContext=t)},lookupOnContext:function(t){this.push(this.nameLookup("depth"+this.lastContext,t,"context"))},pushContext:function(){this.pushStackLiteral("depth"+this.lastContext)},resolvePossibleLambda:function(){this.context.aliases.functionType='"function"',this.replaceStack(function(t){return"typeof "+t+" === functionType ? "+t+".apply(depth0) : "+t})},lookup:function(t){this.replaceStack(function(e){return e+" == null || "+e+" === false ? "+e+" : "+this.nameLookup(e,t,"context")})},lookupData:function(){this.pushStackLiteral("data")},pushStringParam:function(t,e){this.pushStackLiteral("depth"+this.lastContext),this.pushString(e),"sexpr"!==e&&("string"==typeof t?this.pushString(t):this.pushStackLiteral(t))},emptyHash:function(){this.pushStackLiteral("{}"),this.options.stringParams&&(this.push("{}"),this.push("{}"))},pushHash:function(){this.hash&&this.hashes.push(this.hash),this.hash={values:[],types:[],contexts:[]}},popHash:function(){var t=this.hash;this.hash=this.hashes.pop(),this.options.stringParams&&(this.push("{"+t.contexts.join(",")+"}"),this.push("{"+t.types.join(",")+"}")),this.push("{\n    "+t.values.join(",\n    ")+"\n  }")},pushString:function(t){this.pushStackLiteral(this.quotedString(t))},push:function(t){return this.inlineStack.push(t),t},pushLiteral:function(t){this.pushStackLiteral(t)},pushProgram:function(t){this.pushStackLiteral(null!=t?this.programExpression(t):null)},invokeHelper:function(t,e,s){this.context.aliases.helperMissing="helpers.helperMissing",this.useRegister("helper");var i=this.lastHelper=this.setupHelper(t,e,!0),n=this.nameLookup("depth"+this.lastContext,e,"context"),r="helper = "+i.name+" || "+n;i.paramsInit&&(r+=","+i.paramsInit),this.push("("+r+",helper ? helper.call("+i.callParams+") : helperMissing.call("+i.helperMissingParams+"))"),s||this.flushInline()},invokeKnownHelper:function(t,e){var s=this.setupHelper(t,e);this.push(s.name+".call("+s.callParams+")")},invokeAmbiguous:function(t,e){this.context.aliases.functionType='"function"',this.useRegister("helper"),this.emptyHash();var s=this.setupHelper(0,t,e),i=this.lastHelper=this.nameLookup("helpers",t,"helper"),n=this.nameLookup("depth"+this.lastContext,t,"context"),r=this.nextStack();s.paramsInit&&this.pushSource(s.paramsInit),this.pushSource("if (helper = "+i+") { "+r+" = helper.call("+s.callParams+"); }"),this.pushSource("else { helper = "+n+"; "+r+" = typeof helper === functionType ? helper.call("+s.callParams+") : helper; }")},invokePartial:function(t){var e=[this.nameLookup("partials",t,"partial"),"'"+t+"'",this.popStack(),"helpers","partials"];this.options.data&&e.push("data"),this.context.aliases.self="this",this.push("self.invokePartial("+e.join(", ")+")")},assignToHash:function(t){var e,s,i=this.popStack();this.options.stringParams&&(s=this.popStack(),e=this.popStack());var n=this.hash;e&&n.contexts.push("'"+t+"': "+e),s&&n.types.push("'"+t+"': "+s),n.values.push("'"+t+"': ("+i+")")},compiler:i,compileChildren:function(t,e){for(var s,i,n=t.children,r=0,o=n.length;o>r;r++){s=n[r],i=new this.compiler;var a=this.matchExistingProgram(s);null==a?(this.context.programs.push(""),a=this.context.programs.length,s.index=a,s.name="program"+a,this.context.programs[a]=i.compile(s,e,this.context),this.context.environments[a]=s):(s.index=a,s.name="program"+a)}},matchExistingProgram:function(t){for(var e=0,s=this.context.environments.length;s>e;e++){var i=this.context.environments[e];if(i&&i.equals(t))return e}},programExpression:function(t){if(this.context.aliases.self="this",null==t)return"self.noop";for(var e,s=this.environment.children[t],i=s.depths.list,n=[s.index,s.name,"data"],r=0,o=i.length;o>r;r++)e=i[r],n.push(1===e?"depth0":"depth"+(e-1));return(0===i.length?"self.program(":"self.programWithDepth(")+n.join(", ")+")"},register:function(t,e){this.useRegister(t),this.pushSource(t+" = "+e+";")},useRegister:function(t){this.registers[t]||(this.registers[t]=!0,this.registers.list.push(t))},pushStackLiteral:function(t){return this.push(new s(t))},pushSource:function(t){this.pendingContent&&(this.source.push(this.appendToBuffer(this.quotedString(this.pendingContent))),this.pendingContent=void 0),t&&this.source.push(t)},pushStack:function(t){this.flushInline();var e=this.incrStack();return t&&this.pushSource(e+" = "+t+";"),this.compileStack.push(e),e},replaceStack:function(t){var e,i,n,r="",o=this.isInline();if(o){var a=this.popStack(!0);if(a instanceof s)e=a.value,n=!0;else{i=!this.stackSlot;var h=i?this.incrStack():this.topStackName();r="("+this.push(h)+" = "+a+"),",e=this.topStack()}}else e=this.topStack();var p=t.call(this,e);return o?(n||this.popStack(),i&&this.stackSlot--,this.push("("+r+p+")")):(/^stack/.test(e)||(e=this.nextStack()),this.pushSource(e+" = ("+r+p+");")),e},nextStack:function(){return this.pushStack()},incrStack:function(){return this.stackSlot++,this.stackSlot>this.stackVars.length&&this.stackVars.push("stack"+this.stackSlot),this.topStackName()},topStackName:function(){return"stack"+this.stackSlot},flushInline:function(){var t=this.inlineStack;if(t.length){this.inlineStack=[];for(var e=0,i=t.length;i>e;e++){var n=t[e];n instanceof s?this.compileStack.push(n):this.pushStack(n)}}},isInline:function(){return this.inlineStack.length},popStack:function(t){var e=this.isInline(),i=(e?this.inlineStack:this.compileStack).pop();if(!t&&i instanceof s)return i.value;if(!e){if(!this.stackSlot)throw new h("Invalid stack pop");this.stackSlot--}return i},topStack:function(t){var e=this.isInline()?this.inlineStack:this.compileStack,i=e[e.length-1];return!t&&i instanceof s?i.value:i},quotedString:function(t){return'"'+t.replace(/\\/g,"\\\\").replace(/"/g,'\\"').replace(/\n/g,"\\n").replace(/\r/g,"\\r").replace(/\u2028/g,"\\u2028").replace(/\u2029/g,"\\u2029")+'"'},setupHelper:function(t,e,s){var i=[],n=this.setupParams(t,i,s),r=this.nameLookup("helpers",e,"helper");return{params:i,paramsInit:n,name:r,callParams:["depth0"].concat(i).join(", "),helperMissingParams:s&&["depth0",this.quotedString(e)].concat(i).join(", ")}},setupOptions:function(t,e){var s,i,n,r=[],o=[],a=[];r.push("hash:"+this.popStack()),this.options.stringParams&&(r.push("hashTypes:"+this.popStack()),r.push("hashContexts:"+this.popStack())),i=this.popStack(),n=this.popStack(),(n||i)&&(n||(this.context.aliases.self="this",n="self.noop"),i||(this.context.aliases.self="this",i="self.noop"),r.push("inverse:"+i),r.push("fn:"+n));for(var h=0;t>h;h++)s=this.popStack(),e.push(s),this.options.stringParams&&(a.push(this.popStack()),o.push(this.popStack()));return this.options.stringParams&&(r.push("contexts:["+o.join(",")+"]"),r.push("types:["+a.join(",")+"]")),this.options.data&&r.push("data:data"),r},setupParams:function(t,e,s){var i="{"+this.setupOptions(t,e).join(",")+"}";return s?(this.useRegister("options"),e.push("options"),"options="+i):(e.push(i),"")}};for(var p="break else new var case finally return void catch for switch while continue function this with default if throw delete in try do instanceof typeof abstract enum int short boolean export interface static byte extends long super char final native synchronized class float package throws const goto private transient debugger implements protected volatile double import public let yield".split(" "),l=i.RESERVED_WORDS={},c=0,u=p.length;u>c;c++)l[p[c]]=!0;return i.isValidJavaScriptVariableName=function(t){return!i.RESERVED_WORDS[t]&&/^[a-zA-Z_$][0-9a-zA-Z_$]*$/.test(t)?!0:!1},n=i}(i,s),c=function(t,e,s,i,n){"use strict";var r,o=t,a=e,h=s.parser,p=s.parse,l=i.Compiler,c=i.compile,u=i.precompile,f=n,d=o.create,g=function(){var t=d();return t.compile=function(e,s){return c(e,s,t)},t.precompile=function(e,s){return u(e,s,t)},t.AST=a,t.Compiler=l,t.JavaScriptCompiler=f,t.Parser=h,t.parse=p,t};return o=g(),o.create=g,r=o}(r,o,h,p,l);return c}();
/*!
 * typeahead.js 0.10.2
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2014 Twitter, Inc. and other contributors; Licensed MIT
 */

(function($) {
    var _ = {
        isMsie: function() {
            return /(msie|trident)/i.test(navigator.userAgent) ? navigator.userAgent.match(/(msie |rv:)(\d+(.\d+)?)/i)[2] : false;
        },
        isBlankString: function(str) {
            return !str || /^\s*$/.test(str);
        },
        escapeRegExChars: function(str) {
            return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
        },
        isString: function(obj) {
            return typeof obj === "string";
        },
        isNumber: function(obj) {
            return typeof obj === "number";
        },
        isArray: $.isArray,
        isFunction: $.isFunction,
        isObject: $.isPlainObject,
        isUndefined: function(obj) {
            return typeof obj === "undefined";
        },
        bind: $.proxy,
        each: function(collection, cb) {
            $.each(collection, reverseArgs);
            function reverseArgs(index, value) {
                return cb(value, index);
            }
        },
        map: $.map,
        filter: $.grep,
        every: function(obj, test) {
            var result = true;
            if (!obj) {
                return result;
            }
            $.each(obj, function(key, val) {
                if (!(result = test.call(null, val, key, obj))) {
                    return false;
                }
            });
            return !!result;
        },
        some: function(obj, test) {
            var result = false;
            if (!obj) {
                return result;
            }
            $.each(obj, function(key, val) {
                if (result = test.call(null, val, key, obj)) {
                    return false;
                }
            });
            return !!result;
        },
        mixin: $.extend,
        getUniqueId: function() {
            var counter = 0;
            return function() {
                return counter++;
            };
        }(),
        templatify: function templatify(obj) {
            return $.isFunction(obj) ? obj : template;
            function template() {
                return String(obj);
            }
        },
        defer: function(fn) {
            setTimeout(fn, 0);
        },
        debounce: function(func, wait, immediate) {
            var timeout, result;
            return function() {
                var context = this, args = arguments, later, callNow;
                later = function() {
                    timeout = null;
                    if (!immediate) {
                        result = func.apply(context, args);
                    }
                };
                callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) {
                    result = func.apply(context, args);
                }
                return result;
            };
        },
        throttle: function(func, wait) {
            var context, args, timeout, result, previous, later;
            previous = 0;
            later = function() {
                previous = new Date();
                timeout = null;
                result = func.apply(context, args);
            };
            return function() {
                var now = new Date(), remaining = wait - (now - previous);
                context = this;
                args = arguments;
                if (remaining <= 0) {
                    clearTimeout(timeout);
                    timeout = null;
                    previous = now;
                    result = func.apply(context, args);
                } else if (!timeout) {
                    timeout = setTimeout(later, remaining);
                }
                return result;
            };
        },
        noop: function() {}
    };
    var VERSION = "0.10.2";
    var tokenizers = function(root) {
        return {
            nonword: nonword,
            whitespace: whitespace,
            obj: {
                nonword: getObjTokenizer(nonword),
                whitespace: getObjTokenizer(whitespace)
            }
        };
        function whitespace(s) {
            return s.split(/\s+/);
        }
        function nonword(s) {
            return s.split(/\W+/);
        }
        function getObjTokenizer(tokenizer) {
            return function setKey(key) {
                return function tokenize(o) {
                    return tokenizer(o[key]);
                };
            };
        }
    }();
    var LruCache = function() {
        function LruCache(maxSize) {
            this.maxSize = maxSize || 100;
            this.size = 0;
            this.hash = {};
            this.list = new List();
        }
        _.mixin(LruCache.prototype, {
            set: function set(key, val) {
                var tailItem = this.list.tail, node;
                if (this.size >= this.maxSize) {
                    this.list.remove(tailItem);
                    delete this.hash[tailItem.key];
                }
                if (node = this.hash[key]) {
                    node.val = val;
                    this.list.moveToFront(node);
                } else {
                    node = new Node(key, val);
                    this.list.add(node);
                    this.hash[key] = node;
                    this.size++;
                }
            },
            get: function get(key) {
                var node = this.hash[key];
                if (node) {
                    this.list.moveToFront(node);
                    return node.val;
                }
            }
        });
        function List() {
            this.head = this.tail = null;
        }
        _.mixin(List.prototype, {
            add: function add(node) {
                if (this.head) {
                    node.next = this.head;
                    this.head.prev = node;
                }
                this.head = node;
                this.tail = this.tail || node;
            },
            remove: function remove(node) {
                node.prev ? node.prev.next = node.next : this.head = node.next;
                node.next ? node.next.prev = node.prev : this.tail = node.prev;
            },
            moveToFront: function(node) {
                this.remove(node);
                this.add(node);
            }
        });
        function Node(key, val) {
            this.key = key;
            this.val = val;
            this.prev = this.next = null;
        }
        return LruCache;
    }();
    var PersistentStorage = function() {
        var ls, methods;
        try {
            ls = window.localStorage;
            ls.setItem("~~~", "!");
            ls.removeItem("~~~");
        } catch (err) {
            ls = null;
        }
        function PersistentStorage(namespace) {
            this.prefix = [ "__", namespace, "__" ].join("");
            this.ttlKey = "__ttl__";
            this.keyMatcher = new RegExp("^" + this.prefix);
        }
        if (ls && window.JSON) {
            methods = {
                _prefix: function(key) {
                    return this.prefix + key;
                },
                _ttlKey: function(key) {
                    return this._prefix(key) + this.ttlKey;
                },
                get: function(key) {
                    if (this.isExpired(key)) {
                        this.remove(key);
                    }
                    return decode(ls.getItem(this._prefix(key)));
                },
                set: function(key, val, ttl) {
                    if (_.isNumber(ttl)) {
                        ls.setItem(this._ttlKey(key), encode(now() + ttl));
                    } else {
                        ls.removeItem(this._ttlKey(key));
                    }
                    return ls.setItem(this._prefix(key), encode(val));
                },
                remove: function(key) {
                    ls.removeItem(this._ttlKey(key));
                    ls.removeItem(this._prefix(key));
                    return this;
                },
                clear: function() {
                    var i, key, keys = [], len = ls.length;
                    for (i = 0; i < len; i++) {
                        if ((key = ls.key(i)).match(this.keyMatcher)) {
                            keys.push(key.replace(this.keyMatcher, ""));
                        }
                    }
                    for (i = keys.length; i--; ) {
                        this.remove(keys[i]);
                    }
                    return this;
                },
                isExpired: function(key) {
                    var ttl = decode(ls.getItem(this._ttlKey(key)));
                    return _.isNumber(ttl) && now() > ttl ? true : false;
                }
            };
        } else {
            methods = {
                get: _.noop,
                set: _.noop,
                remove: _.noop,
                clear: _.noop,
                isExpired: _.noop
            };
        }
        _.mixin(PersistentStorage.prototype, methods);
        return PersistentStorage;
        function now() {
            return new Date().getTime();
        }
        function encode(val) {
            return JSON.stringify(_.isUndefined(val) ? null : val);
        }
        function decode(val) {
            return JSON.parse(val);
        }
    }();
    var Transport = function() {
        var pendingRequestsCount = 0, pendingRequests = {}, maxPendingRequests = 6, requestCache = new LruCache(10);
        function Transport(o) {
            o = o || {};
            this._send = o.transport ? callbackToDeferred(o.transport) : $.ajax;
            this._get = o.rateLimiter ? o.rateLimiter(this._get) : this._get;
        }
        Transport.setMaxPendingRequests = function setMaxPendingRequests(num) {
            maxPendingRequests = num;
        };
        Transport.resetCache = function clearCache() {
            requestCache = new LruCache(10);
        };
        _.mixin(Transport.prototype, {
            _get: function(url, o, cb) {
                var that = this, jqXhr;
                if (jqXhr = pendingRequests[url]) {
                    jqXhr.done(done).fail(fail);
                } else if (pendingRequestsCount < maxPendingRequests) {
                    pendingRequestsCount++;
                    pendingRequests[url] = this._send(url, o).done(done).fail(fail).always(always);
                } else {
                    this.onDeckRequestArgs = [].slice.call(arguments, 0);
                }
                function done(resp) {
                    cb && cb(null, resp);
                    requestCache.set(url, resp);
                }
                function fail() {
                    cb && cb(true);
                }
                function always() {
                    pendingRequestsCount--;
                    delete pendingRequests[url];
                    if (that.onDeckRequestArgs) {
                        that._get.apply(that, that.onDeckRequestArgs);
                        that.onDeckRequestArgs = null;
                    }
                }
            },
            get: function(url, o, cb) {
                var resp;
                if (_.isFunction(o)) {
                    cb = o;
                    o = {};
                }
                if (resp = requestCache.get(url)) {
                    _.defer(function() {
                        cb && cb(null, resp);
                    });
                } else {
                    this._get(url, o, cb);
                }
                return !!resp;
            }
        });
        return Transport;
        function callbackToDeferred(fn) {
            return function customSendWrapper(url, o) {
                var deferred = $.Deferred();
                fn(url, o, onSuccess, onError);
                return deferred;
                function onSuccess(resp) {
                    _.defer(function() {
                        deferred.resolve(resp);
                    });
                }
                function onError(err) {
                    _.defer(function() {
                        deferred.reject(err);
                    });
                }
            };
        }
    }();
    var SearchIndex = function() {
        function SearchIndex(o) {
            o = o || {};
            if (!o.datumTokenizer || !o.queryTokenizer) {
                $.error("datumTokenizer and queryTokenizer are both required");
            }
            this.datumTokenizer = o.datumTokenizer;
            this.queryTokenizer = o.queryTokenizer;
            this.reset();
        }
        _.mixin(SearchIndex.prototype, {
            bootstrap: function bootstrap(o) {
                this.datums = o.datums;
                this.trie = o.trie;
            },
            add: function(data) {
                var that = this;
                data = _.isArray(data) ? data : [ data ];
                _.each(data, function(datum) {
                    var id, tokens;
                    id = that.datums.push(datum) - 1;
                    tokens = normalizeTokens(that.datumTokenizer(datum));
                    _.each(tokens, function(token) {
                        var node, chars, ch;
                        node = that.trie;
                        chars = token.split("");
                        while (ch = chars.shift()) {
                            node = node.children[ch] || (node.children[ch] = newNode());
                            node.ids.push(id);
                        }
                    });
                });
            },
            get: function get(query) {
                var that = this, tokens, matches;
                tokens = normalizeTokens(this.queryTokenizer(query));
                _.each(tokens, function(token) {
                    var node, chars, ch, ids;
                    if (matches && matches.length === 0) {
                        return false;
                    }
                    node = that.trie;
                    chars = token.split("");
                    while (node && (ch = chars.shift())) {
                        node = node.children[ch];
                    }
                    if (node && chars.length === 0) {
                        ids = node.ids.slice(0);
                        matches = matches ? getIntersection(matches, ids) : ids;
                    } else {
                        matches = [];
                        return false;
                    }
                });
                return matches ? _.map(unique(matches), function(id) {
                    return that.datums[id];
                }) : [];
            },
            reset: function reset() {
                this.datums = [];
                this.trie = newNode();
            },
            serialize: function serialize() {
                return {
                    datums: this.datums,
                    trie: this.trie
                };
            }
        });
        return SearchIndex;
        function normalizeTokens(tokens) {
            tokens = _.filter(tokens, function(token) {
                return !!token;
            });
            tokens = _.map(tokens, function(token) {
                return token.toLowerCase();
            });
            return tokens;
        }
        function newNode() {
            return {
                ids: [],
                children: {}
            };
        }
        function unique(array) {
            var seen = {}, uniques = [];
            for (var i = 0; i < array.length; i++) {
                if (!seen[array[i]]) {
                    seen[array[i]] = true;
                    uniques.push(array[i]);
                }
            }
            return uniques;
        }
        function getIntersection(arrayA, arrayB) {
            var ai = 0, bi = 0, intersection = [];
            arrayA = arrayA.sort(compare);
            arrayB = arrayB.sort(compare);
            while (ai < arrayA.length && bi < arrayB.length) {
                if (arrayA[ai] < arrayB[bi]) {
                    ai++;
                } else if (arrayA[ai] > arrayB[bi]) {
                    bi++;
                } else {
                    intersection.push(arrayA[ai]);
                    ai++;
                    bi++;
                }
            }
            return intersection;
            function compare(a, b) {
                return a - b;
            }
        }
    }();
    var oParser = function() {
        return {
            local: getLocal,
            prefetch: getPrefetch,
            remote: getRemote
        };
        function getLocal(o) {
            return o.local || null;
        }
        function getPrefetch(o) {
            var prefetch, defaults;
            defaults = {
                url: null,
                thumbprint: "",
                ttl: 24 * 60 * 60 * 1e3,
                filter: null,
                ajax: {}
            };
            if (prefetch = o.prefetch || null) {
                prefetch = _.isString(prefetch) ? {
                    url: prefetch
                } : prefetch;
                prefetch = _.mixin(defaults, prefetch);
                prefetch.thumbprint = VERSION + prefetch.thumbprint;
                prefetch.ajax.type = prefetch.ajax.type || "GET";
                prefetch.ajax.dataType = prefetch.ajax.dataType || "json";
                !prefetch.url && $.error("prefetch requires url to be set");
            }
            return prefetch;
        }
        function getRemote(o) {
            var remote, defaults;
            defaults = {
                url: null,
                wildcard: "%QUERY",
                replace: null,
                rateLimitBy: "debounce",
                rateLimitWait: 300,
                send: null,
                filter: null,
                ajax: {}
            };
            if (remote = o.remote || null) {
                remote = _.isString(remote) ? {
                    url: remote
                } : remote;
                remote = _.mixin(defaults, remote);
                remote.rateLimiter = /^throttle$/i.test(remote.rateLimitBy) ? byThrottle(remote.rateLimitWait) : byDebounce(remote.rateLimitWait);
                remote.ajax.type = remote.ajax.type || "GET";
                remote.ajax.dataType = remote.ajax.dataType || "json";
                delete remote.rateLimitBy;
                delete remote.rateLimitWait;
                !remote.url && $.error("remote requires url to be set");
            }
            return remote;
            function byDebounce(wait) {
                return function(fn) {
                    return _.debounce(fn, wait);
                };
            }
            function byThrottle(wait) {
                return function(fn) {
                    return _.throttle(fn, wait);
                };
            }
        }
    }();
    (function(root) {
        var old, keys;
        old = root.Bloodhound;
        keys = {
            data: "data",
            protocol: "protocol",
            thumbprint: "thumbprint"
        };
        root.Bloodhound = Bloodhound;
        function Bloodhound(o) {
            if (!o || !o.local && !o.prefetch && !o.remote) {
                $.error("one of local, prefetch, or remote is required");
            }
            this.limit = o.limit || 5;
            this.sorter = getSorter(o.sorter);
            this.dupDetector = o.dupDetector || ignoreDuplicates;
            this.local = oParser.local(o);
            this.prefetch = oParser.prefetch(o);
            this.remote = oParser.remote(o);
            this.cacheKey = this.prefetch ? this.prefetch.cacheKey || this.prefetch.url : null;
            this.index = new SearchIndex({
                datumTokenizer: o.datumTokenizer,
                queryTokenizer: o.queryTokenizer
            });
            this.storage = this.cacheKey ? new PersistentStorage(this.cacheKey) : null;
        }
        Bloodhound.noConflict = function noConflict() {
            root.Bloodhound = old;
            return Bloodhound;
        };
        Bloodhound.tokenizers = tokenizers;
        _.mixin(Bloodhound.prototype, {
            _loadPrefetch: function loadPrefetch(o) {
                var that = this, serialized, deferred;
                if (serialized = this._readFromStorage(o.thumbprint)) {
                    this.index.bootstrap(serialized);
                    deferred = $.Deferred().resolve();
                } else {
                    deferred = $.ajax(o.url, o.ajax).done(handlePrefetchResponse);
                }
                return deferred;
                function handlePrefetchResponse(resp) {
                    that.clear();
                    that.add(o.filter ? o.filter(resp) : resp);
                    that._saveToStorage(that.index.serialize(), o.thumbprint, o.ttl);
                }
            },
            _getFromRemote: function getFromRemote(query, cb) {
                var that = this, url, uriEncodedQuery;
                query = query || "";
                uriEncodedQuery = encodeURIComponent(query);
                url = this.remote.replace ? this.remote.replace(this.remote.url, query) : this.remote.url.replace(this.remote.wildcard, uriEncodedQuery);
                return this.transport.get(url, this.remote.ajax, handleRemoteResponse);
                function handleRemoteResponse(err, resp) {
                    err ? cb([]) : cb(that.remote.filter ? that.remote.filter(resp) : resp);
                }
            },
            _saveToStorage: function saveToStorage(data, thumbprint, ttl) {
                if (this.storage) {
                    this.storage.set(keys.data, data, ttl);
                    this.storage.set(keys.protocol, location.protocol, ttl);
                    this.storage.set(keys.thumbprint, thumbprint, ttl);
                }
            },
            _readFromStorage: function readFromStorage(thumbprint) {
                var stored = {}, isExpired;
                if (this.storage) {
                    stored.data = this.storage.get(keys.data);
                    stored.protocol = this.storage.get(keys.protocol);
                    stored.thumbprint = this.storage.get(keys.thumbprint);
                }
                isExpired = stored.thumbprint !== thumbprint || stored.protocol !== location.protocol;
                return stored.data && !isExpired ? stored.data : null;
            },
            _initialize: function initialize() {
                var that = this, local = this.local, deferred;
                deferred = this.prefetch ? this._loadPrefetch(this.prefetch) : $.Deferred().resolve();
                local && deferred.done(addLocalToIndex);
                this.transport = this.remote ? new Transport(this.remote) : null;
                return this.initPromise = deferred.promise();
                function addLocalToIndex() {
                    that.add(_.isFunction(local) ? local() : local);
                }
            },
            initialize: function initialize(force) {
                return !this.initPromise || force ? this._initialize() : this.initPromise;
            },
            add: function add(data) {
                this.index.add(data);
            },
            get: function get(query, cb) {
                var that = this, matches = [], cacheHit = false;
                matches = this.index.get(query);
                matches = this.sorter(matches).slice(0, this.limit);
                if (matches.length < this.limit && this.transport) {
                    cacheHit = this._getFromRemote(query, returnRemoteMatches);
                }
                if (!cacheHit) {
                    (matches.length > 0 || !this.transport) && cb && cb(matches);
                }
                function returnRemoteMatches(remoteMatches) {
                    var matchesWithBackfill = matches.slice(0);
                    _.each(remoteMatches, function(remoteMatch) {
                        var isDuplicate;
                        isDuplicate = _.some(matchesWithBackfill, function(match) {
                            return that.dupDetector(remoteMatch, match);
                        });
                        !isDuplicate && matchesWithBackfill.push(remoteMatch);
                        return matchesWithBackfill.length < that.limit;
                    });
                    cb && cb(that.sorter(matchesWithBackfill));
                }
            },
            clear: function clear() {
                this.index.reset();
            },
            clearPrefetchCache: function clearPrefetchCache() {
                this.storage && this.storage.clear();
            },
            clearRemoteCache: function clearRemoteCache() {
                this.transport && Transport.resetCache();
            },
            ttAdapter: function ttAdapter() {
                return _.bind(this.get, this);
            }
        });
        return Bloodhound;
        function getSorter(sortFn) {
            return _.isFunction(sortFn) ? sort : noSort;
            function sort(array) {
                return array.sort(sortFn);
            }
            function noSort(array) {
                return array;
            }
        }
        function ignoreDuplicates() {
            return false;
        }
    })(this);
    var html = {
        wrapper: '<span class="twitter-typeahead"></span>',
        dropdown: '<span class="tt-dropdown-menu"></span>',
        dataset: '<div class="tt-dataset-%CLASS%"></div>',
        suggestions: '<span class="tt-suggestions"></span>',
        suggestion: '<div class="tt-suggestion"></div>'
    };
    var css = {
        wrapper: {
            position: "relative",
            display: "inline-block"
        },
        hint: {
            position: "absolute",
            top: "0",
            left: "0",
            borderColor: "transparent",
            boxShadow: "none"
        },
        input: {
            position: "relative",
            verticalAlign: "top",
            backgroundColor: "transparent"
        },
        inputWithNoHint: {
            position: "relative",
            verticalAlign: "top"
        },
        dropdown: {
            position: "absolute",
            top: "100%",
            left: "0",
            zIndex: "100",
            display: "none"
        },
        suggestions: {
            display: "block"
        },
        suggestion: {
            whiteSpace: "nowrap",
            cursor: "pointer"
        },
        suggestionChild: {
            whiteSpace: "normal"
        },
        ltr: {
            left: "0",
            right: "auto"
        },
        rtl: {
            left: "auto",
            right: " 0"
        }
    };
    if (_.isMsie()) {
        _.mixin(css.input, {
            backgroundImage: "url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7)"
        });
    }
    if (_.isMsie() && _.isMsie() <= 7) {
        _.mixin(css.input, {
            marginTop: "-1px"
        });
    }
    var EventBus = function() {
        var namespace = "typeahead:";
        function EventBus(o) {
            if (!o || !o.el) {
                $.error("EventBus initialized without el");
            }
            this.$el = $(o.el);
        }
        _.mixin(EventBus.prototype, {
            trigger: function(type) {
                var args = [].slice.call(arguments, 1);
                this.$el.trigger(namespace + type, args);
            }
        });
        return EventBus;
    }();
    var EventEmitter = function() {
        var splitter = /\s+/, nextTick = getNextTick();
        return {
            onSync: onSync,
            onAsync: onAsync,
            off: off,
            trigger: trigger
        };
        function on(method, types, cb, context) {
            var type;
            if (!cb) {
                return this;
            }
            types = types.split(splitter);
            cb = context ? bindContext(cb, context) : cb;
            this._callbacks = this._callbacks || {};
            while (type = types.shift()) {
                this._callbacks[type] = this._callbacks[type] || {
                    sync: [],
                    async: []
                };
                this._callbacks[type][method].push(cb);
            }
            return this;
        }
        function onAsync(types, cb, context) {
            return on.call(this, "async", types, cb, context);
        }
        function onSync(types, cb, context) {
            return on.call(this, "sync", types, cb, context);
        }
        function off(types) {
            var type;
            if (!this._callbacks) {
                return this;
            }
            types = types.split(splitter);
            while (type = types.shift()) {
                delete this._callbacks[type];
            }
            return this;
        }
        function trigger(types) {
            var type, callbacks, args, syncFlush, asyncFlush;
            if (!this._callbacks) {
                return this;
            }
            types = types.split(splitter);
            args = [].slice.call(arguments, 1);
            while ((type = types.shift()) && (callbacks = this._callbacks[type])) {
                syncFlush = getFlush(callbacks.sync, this, [ type ].concat(args));
                asyncFlush = getFlush(callbacks.async, this, [ type ].concat(args));
                syncFlush() && nextTick(asyncFlush);
            }
            return this;
        }
        function getFlush(callbacks, context, args) {
            return flush;
            function flush() {
                var cancelled;
                for (var i = 0; !cancelled && i < callbacks.length; i += 1) {
                    cancelled = callbacks[i].apply(context, args) === false;
                }
                return !cancelled;
            }
        }
        function getNextTick() {
            var nextTickFn;
            if (window.setImmediate) {
                nextTickFn = function nextTickSetImmediate(fn) {
                    setImmediate(function() {
                        fn();
                    });
                };
            } else {
                nextTickFn = function nextTickSetTimeout(fn) {
                    setTimeout(function() {
                        fn();
                    }, 0);
                };
            }
            return nextTickFn;
        }
        function bindContext(fn, context) {
            return fn.bind ? fn.bind(context) : function() {
                fn.apply(context, [].slice.call(arguments, 0));
            };
        }
    }();
    var highlight = function(doc) {
        var defaults = {
            node: null,
            pattern: null,
            tagName: "strong",
            className: null,
            wordsOnly: false,
            caseSensitive: false
        };
        return function hightlight(o) {
            var regex;
            o = _.mixin({}, defaults, o);
            if (!o.node || !o.pattern) {
                return;
            }
            o.pattern = _.isArray(o.pattern) ? o.pattern : [ o.pattern ];
            regex = getRegex(o.pattern, o.caseSensitive, o.wordsOnly);
            traverse(o.node, hightlightTextNode);
            function hightlightTextNode(textNode) {
                var match, patternNode;
                if (match = regex.exec(textNode.data)) {
                    wrapperNode = doc.createElement(o.tagName);
                    o.className && (wrapperNode.className = o.className);
                    patternNode = textNode.splitText(match.index);
                    patternNode.splitText(match[0].length);
                    wrapperNode.appendChild(patternNode.cloneNode(true));
                    textNode.parentNode.replaceChild(wrapperNode, patternNode);
                }
                return !!match;
            }
            function traverse(el, hightlightTextNode) {
                var childNode, TEXT_NODE_TYPE = 3;
                for (var i = 0; i < el.childNodes.length; i++) {
                    childNode = el.childNodes[i];
                    if (childNode.nodeType === TEXT_NODE_TYPE) {
                        i += hightlightTextNode(childNode) ? 1 : 0;
                    } else {
                        traverse(childNode, hightlightTextNode);
                    }
                }
            }
        };
        function getRegex(patterns, caseSensitive, wordsOnly) {
            var escapedPatterns = [], regexStr;
            for (var i = 0; i < patterns.length; i++) {
                escapedPatterns.push(_.escapeRegExChars(patterns[i]));
            }
            regexStr = wordsOnly ? "\\b(" + escapedPatterns.join("|") + ")\\b" : "(" + escapedPatterns.join("|") + ")";
            return caseSensitive ? new RegExp(regexStr) : new RegExp(regexStr, "i");
        }
    }(window.document);
    var Input = function() {
        var specialKeyCodeMap;
        specialKeyCodeMap = {
            9: "tab",
            27: "esc",
            37: "left",
            39: "right",
            13: "enter",
            38: "up",
            40: "down"
        };
        function Input(o) {
            var that = this, onBlur, onFocus, onKeydown, onInput;
            o = o || {};
            if (!o.input) {
                $.error("input is missing");
            }
            onBlur = _.bind(this._onBlur, this);
            onFocus = _.bind(this._onFocus, this);
            onKeydown = _.bind(this._onKeydown, this);
            onInput = _.bind(this._onInput, this);
            this.$hint = $(o.hint);
            this.$input = $(o.input).on("blur.tt", onBlur).on("focus.tt", onFocus).on("keydown.tt", onKeydown);
            if (this.$hint.length === 0) {
                this.setHint = this.getHint = this.clearHint = this.clearHintIfInvalid = _.noop;
            }
            if (!_.isMsie()) {
                this.$input.on("input.tt", onInput);
            } else {
                this.$input.on("keydown.tt keypress.tt cut.tt paste.tt", function($e) {
                    if (specialKeyCodeMap[$e.which || $e.keyCode]) {
                        return;
                    }
                    _.defer(_.bind(that._onInput, that, $e));
                });
            }
            this.query = this.$input.val();
            this.$overflowHelper = buildOverflowHelper(this.$input);
        }
        Input.normalizeQuery = function(str) {
            return (str || "").replace(/^\s*/g, "").replace(/\s{2,}/g, " ");
        };
        _.mixin(Input.prototype, EventEmitter, {
            _onBlur: function onBlur() {
                this.resetInputValue();
                this.trigger("blurred");
            },
            _onFocus: function onFocus() {
                this.trigger("focused");
            },
            _onKeydown: function onKeydown($e) {
                var keyName = specialKeyCodeMap[$e.which || $e.keyCode];
                this._managePreventDefault(keyName, $e);
                if (keyName && this._shouldTrigger(keyName, $e)) {
                    this.trigger(keyName + "Keyed", $e);
                }
            },
            _onInput: function onInput() {
                this._checkInputValue();
            },
            _managePreventDefault: function managePreventDefault(keyName, $e) {
                var preventDefault, hintValue, inputValue;
                switch (keyName) {
                  case "tab":
                    hintValue = this.getHint();
                    inputValue = this.getInputValue();
                    preventDefault = hintValue && hintValue !== inputValue && !withModifier($e);
                    break;

                  case "up":
                  case "down":
                    preventDefault = !withModifier($e);
                    break;

                  default:
                    preventDefault = false;
                }
                preventDefault && $e.preventDefault();
            },
            _shouldTrigger: function shouldTrigger(keyName, $e) {
                var trigger;
                switch (keyName) {
                  case "tab":
                    trigger = !withModifier($e);
                    break;

                  default:
                    trigger = true;
                }
                return trigger;
            },
            _checkInputValue: function checkInputValue() {
                var inputValue, areEquivalent, hasDifferentWhitespace;
                inputValue = this.getInputValue();
                areEquivalent = areQueriesEquivalent(inputValue, this.query);
                hasDifferentWhitespace = areEquivalent ? this.query.length !== inputValue.length : false;
                if (!areEquivalent) {
                    this.trigger("queryChanged", this.query = inputValue);
                } else if (hasDifferentWhitespace) {
                    this.trigger("whitespaceChanged", this.query);
                }
            },
            focus: function focus() {
                this.$input.focus();
            },
            blur: function blur() {
                this.$input.blur();
            },
            getQuery: function getQuery() {
                return this.query;
            },
            setQuery: function setQuery(query) {
                this.query = query;
            },
            getInputValue: function getInputValue() {
                return this.$input.val();
            },
            setInputValue: function setInputValue(value, silent) {
                this.$input.val(value);
                silent ? this.clearHint() : this._checkInputValue();
            },
            resetInputValue: function resetInputValue() {
                this.setInputValue(this.query, true);
            },
            getHint: function getHint() {
                return this.$hint.val();
            },
            setHint: function setHint(value) {
                this.$hint.val(value);
            },
            clearHint: function clearHint() {
                this.setHint("");
            },
            clearHintIfInvalid: function clearHintIfInvalid() {
                var val, hint, valIsPrefixOfHint, isValid;
                val = this.getInputValue();
                hint = this.getHint();
                valIsPrefixOfHint = val !== hint && hint.indexOf(val) === 0;
                isValid = val !== "" && valIsPrefixOfHint && !this.hasOverflow();
                !isValid && this.clearHint();
            },
            getLanguageDirection: function getLanguageDirection() {
                return (this.$input.css("direction") || "ltr").toLowerCase();
            },
            hasOverflow: function hasOverflow() {
                var constraint = this.$input.width() - 2;
                this.$overflowHelper.text(this.getInputValue());
                return this.$overflowHelper.width() >= constraint;
            },
            isCursorAtEnd: function() {
                var valueLength, selectionStart, range;
                valueLength = this.$input.val().length;
                selectionStart = this.$input[0].selectionStart;
                if (_.isNumber(selectionStart)) {
                    return selectionStart === valueLength;
                } else if (document.selection) {
                    range = document.selection.createRange();
                    range.moveStart("character", -valueLength);
                    return valueLength === range.text.length;
                }
                return true;
            },
            destroy: function destroy() {
                this.$hint.off(".tt");
                this.$input.off(".tt");
                this.$hint = this.$input = this.$overflowHelper = null;
            }
        });
        return Input;
        function buildOverflowHelper($input) {
            return $('<pre aria-hidden="true"></pre>').css({
                position: "absolute",
                visibility: "hidden",
                whiteSpace: "pre",
                fontFamily: $input.css("font-family"),
                fontSize: $input.css("font-size"),
                fontStyle: $input.css("font-style"),
                fontVariant: $input.css("font-variant"),
                fontWeight: $input.css("font-weight"),
                wordSpacing: $input.css("word-spacing"),
                letterSpacing: $input.css("letter-spacing"),
                textIndent: $input.css("text-indent"),
                textRendering: $input.css("text-rendering"),
                textTransform: $input.css("text-transform")
            }).insertAfter($input);
        }
        function areQueriesEquivalent(a, b) {
            return Input.normalizeQuery(a) === Input.normalizeQuery(b);
        }
        function withModifier($e) {
            return $e.altKey || $e.ctrlKey || $e.metaKey || $e.shiftKey;
        }
    }();
    var Dataset = function() {
        var datasetKey = "ttDataset", valueKey = "ttValue", datumKey = "ttDatum";
        function Dataset(o) {
            o = o || {};
            o.templates = o.templates || {};
            if (!o.source) {
                $.error("missing source");
            }
            if (o.name && !isValidName(o.name)) {
                $.error("invalid dataset name: " + o.name);
            }
            this.query = null;
            this.highlight = !!o.highlight;
            this.name = o.name || _.getUniqueId();
            this.source = o.source;
            this.displayFn = getDisplayFn(o.display || o.displayKey);
            this.templates = getTemplates(o.templates, this.displayFn);
            this.$el = $(html.dataset.replace("%CLASS%", this.name));
        }
        Dataset.extractDatasetName = function extractDatasetName(el) {
            return $(el).data(datasetKey);
        };
        Dataset.extractValue = function extractDatum(el) {
            return $(el).data(valueKey);
        };
        Dataset.extractDatum = function extractDatum(el) {
            return $(el).data(datumKey);
        };
        _.mixin(Dataset.prototype, EventEmitter, {
            _render: function render(query, suggestions) {
                if (!this.$el) {
                    return;
                }
                var that = this, hasSuggestions;
                this.$el.empty();
                hasSuggestions = suggestions && suggestions.length;
                if (!hasSuggestions && this.templates.empty) {
                    this.$el.html(getEmptyHtml()).prepend(that.templates.header ? getHeaderHtml() : null).append(that.templates.footer ? getFooterHtml() : null);
                } else if (hasSuggestions) {
                    this.$el.html(getSuggestionsHtml()).prepend(that.templates.header ? getHeaderHtml() : null).append(that.templates.footer ? getFooterHtml() : null);
                }
                this.trigger("rendered");
                function getEmptyHtml() {
                    return that.templates.empty({
                        query: query,
                        isEmpty: true
                    });
                }
                function getSuggestionsHtml() {
                    var $suggestions, nodes;
                    $suggestions = $(html.suggestions).css(css.suggestions);
                    nodes = _.map(suggestions, getSuggestionNode);
                    $suggestions.append.apply($suggestions, nodes);
                    that.highlight && highlight({
                        node: $suggestions[0],
                        pattern: query
                    });
                    return $suggestions;
                    function getSuggestionNode(suggestion) {
                        var $el;
                        $el = $(html.suggestion).append(that.templates.suggestion(suggestion)).data(datasetKey, that.name).data(valueKey, that.displayFn(suggestion)).data(datumKey, suggestion);
                        $el.children().each(function() {
                            $(this).css(css.suggestionChild);
                        });
                        return $el;
                    }
                }
                function getHeaderHtml() {
                    return that.templates.header({
                        query: query,
                        isEmpty: !hasSuggestions
                    });
                }
                function getFooterHtml() {
                    return that.templates.footer({
                        query: query,
                        isEmpty: !hasSuggestions
                    });
                }
            },
            getRoot: function getRoot() {
                return this.$el;
            },
            update: function update(query) {
                var that = this;
                this.query = query;
                this.canceled = false;
                this.source(query, render);
                function render(suggestions) {
                    if (!that.canceled && query === that.query) {
                        that._render(query, suggestions);
                    }
                }
            },
            cancel: function cancel() {
                this.canceled = true;
            },
            clear: function clear() {
                this.cancel();
                this.$el.empty();
                this.trigger("rendered");
            },
            isEmpty: function isEmpty() {
                return this.$el.is(":empty");
            },
            destroy: function destroy() {
                this.$el = null;
            }
        });
        return Dataset;
        function getDisplayFn(display) {
            display = display || "value";
            return _.isFunction(display) ? display : displayFn;
            function displayFn(obj) {
                return obj[display];
            }
        }
        function getTemplates(templates, displayFn) {
            return {
                empty: templates.empty && _.templatify(templates.empty),
                header: templates.header && _.templatify(templates.header),
                footer: templates.footer && _.templatify(templates.footer),
                suggestion: templates.suggestion || suggestionTemplate
            };
            function suggestionTemplate(context) {
                return "<p>" + displayFn(context) + "</p>";
            }
        }
        function isValidName(str) {
            return /^[_a-zA-Z0-9-]+$/.test(str);
        }
    }();
    var Dropdown = function() {
        function Dropdown(o) {
            var that = this, onSuggestionClick, onSuggestionMouseEnter, onSuggestionMouseLeave;
            o = o || {};
            if (!o.menu) {
                $.error("menu is required");
            }
            this.isOpen = false;
            this.isEmpty = true;
            this.datasets = _.map(o.datasets, initializeDataset);
            onSuggestionClick = _.bind(this._onSuggestionClick, this);
            onSuggestionMouseEnter = _.bind(this._onSuggestionMouseEnter, this);
            onSuggestionMouseLeave = _.bind(this._onSuggestionMouseLeave, this);
            this.$menu = $(o.menu).on("click.tt", ".tt-suggestion", onSuggestionClick).on("mouseenter.tt", ".tt-suggestion", onSuggestionMouseEnter).on("mouseleave.tt", ".tt-suggestion", onSuggestionMouseLeave);
            _.each(this.datasets, function(dataset) {
                that.$menu.append(dataset.getRoot());
                dataset.onSync("rendered", that._onRendered, that);
            });
        }
        _.mixin(Dropdown.prototype, EventEmitter, {
            _onSuggestionClick: function onSuggestionClick($e) {
                this.trigger("suggestionClicked", $($e.currentTarget));
            },
            _onSuggestionMouseEnter: function onSuggestionMouseEnter($e) {
                this._removeCursor();
                this._setCursor($($e.currentTarget), true);
            },
            _onSuggestionMouseLeave: function onSuggestionMouseLeave() {
                this._removeCursor();
            },
            _onRendered: function onRendered() {
                this.isEmpty = _.every(this.datasets, isDatasetEmpty);
                this.isEmpty ? this._hide() : this.isOpen && this._show();
                this.trigger("datasetRendered");
                function isDatasetEmpty(dataset) {
                    return dataset.isEmpty();
                }
            },
            _hide: function() {
                this.$menu.hide();
            },
            _show: function() {
                this.$menu.css("display", "block");
            },
            _getSuggestions: function getSuggestions() {
                return this.$menu.find(".tt-suggestion");
            },
            _getCursor: function getCursor() {
                return this.$menu.find(".tt-cursor").first();
            },
            _setCursor: function setCursor($el, silent) {
                $el.first().addClass("tt-cursor");
                !silent && this.trigger("cursorMoved");
            },
            _removeCursor: function removeCursor() {
                this._getCursor().removeClass("tt-cursor");
            },
            _moveCursor: function moveCursor(increment) {
                var $suggestions, $oldCursor, newCursorIndex, $newCursor;
                if (!this.isOpen) {
                    return;
                }
                $oldCursor = this._getCursor();
                $suggestions = this._getSuggestions();
                this._removeCursor();
                newCursorIndex = $suggestions.index($oldCursor) + increment;
                newCursorIndex = (newCursorIndex + 1) % ($suggestions.length + 1) - 1;
                if (newCursorIndex === -1) {
                    this.trigger("cursorRemoved");
                    return;
                } else if (newCursorIndex < -1) {
                    newCursorIndex = $suggestions.length - 1;
                }
                this._setCursor($newCursor = $suggestions.eq(newCursorIndex));
                this._ensureVisible($newCursor);
            },
            _ensureVisible: function ensureVisible($el) {
                var elTop, elBottom, menuScrollTop, menuHeight;
                elTop = $el.position().top;
                elBottom = elTop + $el.outerHeight(true);
                menuScrollTop = this.$menu.scrollTop();
                menuHeight = this.$menu.height() + parseInt(this.$menu.css("paddingTop"), 10) + parseInt(this.$menu.css("paddingBottom"), 10);
                if (elTop < 0) {
                    this.$menu.scrollTop(menuScrollTop + elTop);
                } else if (menuHeight < elBottom) {
                    this.$menu.scrollTop(menuScrollTop + (elBottom - menuHeight));
                }
            },
            close: function close() {
                if (this.isOpen) {
                    this.isOpen = false;
                    this._removeCursor();
                    this._hide();
                    this.trigger("closed");
                }
            },
            open: function open() {
                if (!this.isOpen) {
                    this.isOpen = true;
                    !this.isEmpty && this._show();
                    this.trigger("opened");
                }
            },
            setLanguageDirection: function setLanguageDirection(dir) {
                this.$menu.css(dir === "ltr" ? css.ltr : css.rtl);
            },
            moveCursorUp: function moveCursorUp() {
                this._moveCursor(-1);
            },
            moveCursorDown: function moveCursorDown() {
                this._moveCursor(+1);
            },
            getDatumForSuggestion: function getDatumForSuggestion($el) {
                var datum = null;
                if ($el.length) {
                    datum = {
                        raw: Dataset.extractDatum($el),
                        value: Dataset.extractValue($el),
                        datasetName: Dataset.extractDatasetName($el)
                    };
                }
                return datum;
            },
            getDatumForCursor: function getDatumForCursor() {
                return this.getDatumForSuggestion(this._getCursor().first());
            },
            getDatumForTopSuggestion: function getDatumForTopSuggestion() {
                return this.getDatumForSuggestion(this._getSuggestions().first());
            },
            update: function update(query) {
                _.each(this.datasets, updateDataset);
                function updateDataset(dataset) {
                    dataset.update(query);
                }
            },
            empty: function empty() {
                _.each(this.datasets, clearDataset);
                this.isEmpty = true;
                function clearDataset(dataset) {
                    dataset.clear();
                }
            },
            isVisible: function isVisible() {
                return this.isOpen && !this.isEmpty;
            },
            destroy: function destroy() {
                this.$menu.off(".tt");
                this.$menu = null;
                _.each(this.datasets, destroyDataset);
                function destroyDataset(dataset) {
                    dataset.destroy();
                }
            }
        });
        return Dropdown;
        function initializeDataset(oDataset) {
            return new Dataset(oDataset);
        }
    }();
    var Typeahead = function() {
        var attrsKey = "ttAttrs";
        function Typeahead(o) {
            var $menu, $input, $hint;
            o = o || {};
            if (!o.input) {
                $.error("missing input");
            }
            this.isActivated = false;
            this.autoselect = !!o.autoselect;
            this.minLength = _.isNumber(o.minLength) ? o.minLength : 1;
            this.$node = buildDomStructure(o.input, o.withHint);
            $menu = this.$node.find(".tt-dropdown-menu");
            $input = this.$node.find(".tt-input");
            $hint = this.$node.find(".tt-hint");
            $input.on("blur.tt", function($e) {
                var active, isActive, hasActive;
                active = document.activeElement;
                isActive = $menu.is(active);
                hasActive = $menu.has(active).length > 0;
                if (_.isMsie() && (isActive || hasActive)) {
                    $e.preventDefault();
                    $e.stopImmediatePropagation();
                    _.defer(function() {
                        $input.focus();
                    });
                }
            });
            $menu.on("mousedown.tt", function($e) {
                $e.preventDefault();
            });
            this.eventBus = o.eventBus || new EventBus({
                el: $input
            });
            this.dropdown = new Dropdown({
                menu: $menu,
                datasets: o.datasets
            }).onSync("suggestionClicked", this._onSuggestionClicked, this).onSync("cursorMoved", this._onCursorMoved, this).onSync("cursorRemoved", this._onCursorRemoved, this).onSync("opened", this._onOpened, this).onSync("closed", this._onClosed, this).onAsync("datasetRendered", this._onDatasetRendered, this);
            this.input = new Input({
                input: $input,
                hint: $hint
            }).onSync("focused", this._onFocused, this).onSync("blurred", this._onBlurred, this).onSync("enterKeyed", this._onEnterKeyed, this).onSync("tabKeyed", this._onTabKeyed, this).onSync("escKeyed", this._onEscKeyed, this).onSync("upKeyed", this._onUpKeyed, this).onSync("downKeyed", this._onDownKeyed, this).onSync("leftKeyed", this._onLeftKeyed, this).onSync("rightKeyed", this._onRightKeyed, this).onSync("queryChanged", this._onQueryChanged, this).onSync("whitespaceChanged", this._onWhitespaceChanged, this);
            this._setLanguageDirection();
        }
        _.mixin(Typeahead.prototype, {
            _onSuggestionClicked: function onSuggestionClicked(type, $el) {
                var datum;
                if (datum = this.dropdown.getDatumForSuggestion($el)) {
                    this._select(datum);
                }
            },
            _onCursorMoved: function onCursorMoved() {
                var datum = this.dropdown.getDatumForCursor();
                this.input.setInputValue(datum.value, true);
                this.eventBus.trigger("cursorchanged", datum.raw, datum.datasetName);
            },
            _onCursorRemoved: function onCursorRemoved() {
                this.input.resetInputValue();
                this._updateHint();
            },
            _onDatasetRendered: function onDatasetRendered() {
                this._updateHint();
            },
            _onOpened: function onOpened() {
                this._updateHint();
                this.eventBus.trigger("opened");
            },
            _onClosed: function onClosed() {
                this.input.clearHint();
                this.eventBus.trigger("closed");
            },
            _onFocused: function onFocused() {
                this.isActivated = true;
                this.dropdown.open();
            },
            _onBlurred: function onBlurred() {
                this.isActivated = false;
                this.dropdown.empty();
                this.dropdown.close();
            },
            _onEnterKeyed: function onEnterKeyed(type, $e) {
                var cursorDatum, topSuggestionDatum;
                cursorDatum = this.dropdown.getDatumForCursor();
                topSuggestionDatum = this.dropdown.getDatumForTopSuggestion();
                if (cursorDatum) {
                    this._select(cursorDatum);
                    $e.preventDefault();
                } else if (this.autoselect && topSuggestionDatum) {
                    this._select(topSuggestionDatum);
                    $e.preventDefault();
                }
            },
            _onTabKeyed: function onTabKeyed(type, $e) {
                var datum;
                if (datum = this.dropdown.getDatumForCursor()) {
                    this._select(datum);
                    $e.preventDefault();
                } else {
                    this._autocomplete(true);
                }
            },
            _onEscKeyed: function onEscKeyed() {
                this.dropdown.close();
                this.input.resetInputValue();
            },
            _onUpKeyed: function onUpKeyed() {
                var query = this.input.getQuery();
                this.dropdown.isEmpty && query.length >= this.minLength ? this.dropdown.update(query) : this.dropdown.moveCursorUp();
                this.dropdown.open();
            },
            _onDownKeyed: function onDownKeyed() {
                var query = this.input.getQuery();
                this.dropdown.isEmpty && query.length >= this.minLength ? this.dropdown.update(query) : this.dropdown.moveCursorDown();
                this.dropdown.open();
            },
            _onLeftKeyed: function onLeftKeyed() {
                this.dir === "rtl" && this._autocomplete();
            },
            _onRightKeyed: function onRightKeyed() {
                this.dir === "ltr" && this._autocomplete();
            },
            _onQueryChanged: function onQueryChanged(e, query) {
                this.input.clearHintIfInvalid();
                query.length >= this.minLength ? this.dropdown.update(query) : this.dropdown.empty();
                this.dropdown.open();
                this._setLanguageDirection();
            },
            _onWhitespaceChanged: function onWhitespaceChanged() {
                this._updateHint();
                this.dropdown.open();
            },
            _setLanguageDirection: function setLanguageDirection() {
                var dir;
                if (this.dir !== (dir = this.input.getLanguageDirection())) {
                    this.dir = dir;
                    this.$node.css("direction", dir);
                    this.dropdown.setLanguageDirection(dir);
                }
            },
            _updateHint: function updateHint() {
                var datum, val, query, escapedQuery, frontMatchRegEx, match;
                datum = this.dropdown.getDatumForTopSuggestion();
                if (datum && this.dropdown.isVisible() && !this.input.hasOverflow()) {
                    val = this.input.getInputValue();
                    query = Input.normalizeQuery(val);
                    escapedQuery = _.escapeRegExChars(query);
                    frontMatchRegEx = new RegExp("^(?:" + escapedQuery + ")(.+$)", "i");
                    match = frontMatchRegEx.exec(datum.value);
                    match ? this.input.setHint(val + match[1]) : this.input.clearHint();
                } else {
                    this.input.clearHint();
                }
            },
            _autocomplete: function autocomplete(laxCursor) {
                var hint, query, isCursorAtEnd, datum;
                hint = this.input.getHint();
                query = this.input.getQuery();
                isCursorAtEnd = laxCursor || this.input.isCursorAtEnd();
                if (hint && query !== hint && isCursorAtEnd) {
                    datum = this.dropdown.getDatumForTopSuggestion();
                    datum && this.input.setInputValue(datum.value);
                    this.eventBus.trigger("autocompleted", datum.raw, datum.datasetName);
                }
            },
            _select: function select(datum) {
                this.input.setQuery(datum.value);
                this.input.setInputValue(datum.value, true);
                this._setLanguageDirection();
                this.eventBus.trigger("selected", datum.raw, datum.datasetName);
                this.dropdown.close();
                _.defer(_.bind(this.dropdown.empty, this.dropdown));
            },
            open: function open() {
                this.dropdown.open();
            },
            close: function close() {
                this.dropdown.close();
            },
            setVal: function setVal(val) {
                if (this.isActivated) {
                    this.input.setInputValue(val);
                } else {
                    this.input.setQuery(val);
                    this.input.setInputValue(val, true);
                }
                this._setLanguageDirection();
            },
            getVal: function getVal() {
                return this.input.getQuery();
            },
            destroy: function destroy() {
                this.input.destroy();
                this.dropdown.destroy();
                destroyDomStructure(this.$node);
                this.$node = null;
            }
        });
        return Typeahead;
        function buildDomStructure(input, withHint) {
            var $input, $wrapper, $dropdown, $hint;
            $input = $(input);
            $wrapper = $(html.wrapper).css(css.wrapper);
            $dropdown = $(html.dropdown).css(css.dropdown);
            $hint = $input.clone().css(css.hint).css(getBackgroundStyles($input));
            $hint.val("").removeData().addClass("tt-hint").removeAttr("id name placeholder").prop("disabled", true).attr({
                autocomplete: "off",
                spellcheck: "false"
            });
            $input.data(attrsKey, {
                dir: $input.attr("dir"),
                autocomplete: $input.attr("autocomplete"),
                spellcheck: $input.attr("spellcheck"),
                style: $input.attr("style")
            });
            $input.addClass("tt-input").attr({
                autocomplete: "off",
                spellcheck: false
            }).css(withHint ? css.input : css.inputWithNoHint);
            try {
                !$input.attr("dir") && $input.attr("dir", "auto");
            } catch (e) {}
            return $input.wrap($wrapper).parent().prepend(withHint ? $hint : null).append($dropdown);
        }
        function getBackgroundStyles($el) {
            return {
                backgroundAttachment: $el.css("background-attachment"),
                backgroundClip: $el.css("background-clip"),
                backgroundColor: $el.css("background-color"),
                backgroundImage: $el.css("background-image"),
                backgroundOrigin: $el.css("background-origin"),
                backgroundPosition: $el.css("background-position"),
                backgroundRepeat: $el.css("background-repeat"),
                backgroundSize: $el.css("background-size")
            };
        }
        function destroyDomStructure($node) {
            var $input = $node.find(".tt-input");
            _.each($input.data(attrsKey), function(val, key) {
                _.isUndefined(val) ? $input.removeAttr(key) : $input.attr(key, val);
            });
            $input.detach().removeData(attrsKey).removeClass("tt-input").insertAfter($node);
            $node.remove();
        }
    }();
    (function() {
        var old, typeaheadKey, methods;
        old = $.fn.typeahead;
        typeaheadKey = "ttTypeahead";
        methods = {
            initialize: function initialize(o, datasets) {
                datasets = _.isArray(datasets) ? datasets : [].slice.call(arguments, 1);
                o = o || {};
                return this.each(attach);
                function attach() {
                    var $input = $(this), eventBus, typeahead;
                    _.each(datasets, function(d) {
                        d.highlight = !!o.highlight;
                    });
                    typeahead = new Typeahead({
                        input: $input,
                        eventBus: eventBus = new EventBus({
                            el: $input
                        }),
                        withHint: _.isUndefined(o.hint) ? true : !!o.hint,
                        minLength: o.minLength,
                        autoselect: o.autoselect,
                        datasets: datasets
                    });
                    $input.data(typeaheadKey, typeahead);
                }
            },
            open: function open() {
                return this.each(openTypeahead);
                function openTypeahead() {
                    var $input = $(this), typeahead;
                    if (typeahead = $input.data(typeaheadKey)) {
                        typeahead.open();
                    }
                }
            },
            close: function close() {
                return this.each(closeTypeahead);
                function closeTypeahead() {
                    var $input = $(this), typeahead;
                    if (typeahead = $input.data(typeaheadKey)) {
                        typeahead.close();
                    }
                }
            },
            val: function val(newVal) {
                return !arguments.length ? getVal(this.first()) : this.each(setVal);
                function setVal() {
                    var $input = $(this), typeahead;
                    if (typeahead = $input.data(typeaheadKey)) {
                        typeahead.setVal(newVal);
                    }
                }
                function getVal($input) {
                    var typeahead, query;
                    if (typeahead = $input.data(typeaheadKey)) {
                        query = typeahead.getVal();
                    }
                    return query;
                }
            },
            destroy: function destroy() {
                return this.each(unattach);
                function unattach() {
                    var $input = $(this), typeahead;
                    if (typeahead = $input.data(typeaheadKey)) {
                        typeahead.destroy();
                        $input.removeData(typeaheadKey);
                    }
                }
            }
        };
        $.fn.typeahead = function(method) {
            if (methods[method]) {
                return methods[method].apply(this, [].slice.call(arguments, 1));
            } else {
                return methods.initialize.apply(this, arguments);
            }
        };
        $.fn.typeahead.noConflict = function noConflict() {
            $.fn.typeahead = old;
            return this;
        };
    })();
})(window.jQuery);
!function(t){function e(){return new Date(Date.UTC.apply(Date,arguments))}var i=function(e,i){var a=this;this.element=t(e),this.language=i.language||this.element.data("date-language")||"en",this.language=this.language in n?this.language:"en",this.isRTL=n[this.language].rtl||"rtl"==t("body").css("direction"),this.formatType=i.formatType||this.element.data("format-type")||"standard",this.format=s.parseFormat(i.format||this.element.data("date-format")||n[this.language].format||s.getDefaultFormat(this.formatType,"input"),this.formatType),this.isInline=!1,this.isVisible=!1,this.isInput=this.element.is("input"),this.component=this.element.is(".date")?this.element.find(".date-set").parent():!1,this.componentReset=this.element.is(".date")?this.element.find(".date-reset").parent():!1,this.hasInput=this.component&&this.element.find("input").length,this.component&&0===this.component.length&&(this.component=!1),this.linkField=i.linkField||this.element.data("link-field")||!1,this.linkFormat=s.parseFormat(i.linkFormat||this.element.data("link-format")||s.getDefaultFormat(this.formatType,"link"),this.formatType),this.minuteStep=i.minuteStep||this.element.data("minute-step")||5,this.pickerPosition=i.pickerPosition||this.element.data("picker-position")||"bottom-right",this.showMeridian=i.showMeridian||this.element.data("show-meridian")||!1,this.initialDate=i.initialDate||new Date,this._attachEvents(),this.formatViewType="datetime","formatViewType"in i?this.formatViewType=i.formatViewType:"formatViewType"in this.element.data()&&(this.formatViewType=this.element.data("formatViewType")),this.minView=0,"minView"in i?this.minView=i.minView:"minView"in this.element.data()&&(this.minView=this.element.data("min-view")),this.minView=s.convertViewMode(this.minView),this.maxView=s.modes.length-1,"maxView"in i?this.maxView=i.maxView:"maxView"in this.element.data()&&(this.maxView=this.element.data("max-view")),this.maxView=s.convertViewMode(this.maxView),this.wheelViewModeNavigation=!1,"wheelViewModeNavigation"in i?this.wheelViewModeNavigation=i.wheelViewModeNavigation:"wheelViewModeNavigation"in this.element.data()&&(this.wheelViewModeNavigation=this.element.data("view-mode-wheel-navigation")),this.wheelViewModeNavigationInverseDirection=!1,"wheelViewModeNavigationInverseDirection"in i?this.wheelViewModeNavigationInverseDirection=i.wheelViewModeNavigationInverseDirection:"wheelViewModeNavigationInverseDirection"in this.element.data()&&(this.wheelViewModeNavigationInverseDirection=this.element.data("view-mode-wheel-navigation-inverse-dir")),this.wheelViewModeNavigationDelay=100,"wheelViewModeNavigationDelay"in i?this.wheelViewModeNavigationDelay=i.wheelViewModeNavigationDelay:"wheelViewModeNavigationDelay"in this.element.data()&&(this.wheelViewModeNavigationDelay=this.element.data("view-mode-wheel-navigation-delay")),this.startViewMode=2,"startView"in i?this.startViewMode=i.startView:"startView"in this.element.data()&&(this.startViewMode=this.element.data("start-view")),this.startViewMode=s.convertViewMode(this.startViewMode),this.viewMode=this.startViewMode,this.viewSelect=this.minView,"viewSelect"in i?this.viewSelect=i.viewSelect:"viewSelect"in this.element.data()&&(this.viewSelect=this.element.data("view-select")),this.viewSelect=s.convertViewMode(this.viewSelect),this.forceParse=!0,"forceParse"in i?this.forceParse=i.forceParse:"dateForceParse"in this.element.data()&&(this.forceParse=this.element.data("date-force-parse")),this.picker=t(s.template).appendTo(this.isInline?this.element:"body").on({click:t.proxy(this.click,this),mousedown:t.proxy(this.mousedown,this)}),this.wheelViewModeNavigation&&(t.fn.mousewheel?this.picker.on({mousewheel:t.proxy(this.mousewheel,this)}):console.log("Mouse Wheel event is not supported. Please include the jQuery Mouse Wheel plugin before enabling this option")),this.isInline?this.picker.addClass("datetimepicker-inline"):this.picker.addClass("datetimepicker-dropdown-"+this.pickerPosition+" dropdown-menu"),this.isRTL&&(this.picker.addClass("datetimepicker-rtl"),this.picker.find(".prev i, .next i").toggleClass("fa-arrow-left fa-arrow-right")),t(document).on("mousedown",function(e){0===t(e.target).closest(".datetimepicker").length&&a.hide()}),this.autoclose=!1,"autoclose"in i?this.autoclose=i.autoclose:"dateAutoclose"in this.element.data()&&(this.autoclose=this.element.data("date-autoclose")),this.keyboardNavigation=!0,"keyboardNavigation"in i?this.keyboardNavigation=i.keyboardNavigation:"dateKeyboardNavigation"in this.element.data()&&(this.keyboardNavigation=this.element.data("date-keyboard-navigation")),this.todayBtn=i.todayBtn||this.element.data("date-today-btn")||!1,this.todayHighlight=i.todayHighlight||this.element.data("date-today-highlight")||!1,this.weekStart=(i.weekStart||this.element.data("date-weekstart")||n[this.language].weekStart||0)%7,this.weekEnd=(this.weekStart+6)%7,this.startDate=-1/0,this.endDate=1/0,this.daysOfWeekDisabled=[],this.setStartDate(i.startDate||this.element.data("date-startdate")),this.setEndDate(i.endDate||this.element.data("date-enddate")),this.setDaysOfWeekDisabled(i.daysOfWeekDisabled||this.element.data("date-days-of-week-disabled")),this.fillDow(),this.fillMonths(),this.update(),this.showMode(),this.isInline&&this.show()};i.prototype={constructor:i,_events:[],_attachEvents:function(){this._detachEvents(),this.isInput?this._events=[[this.element,{focus:t.proxy(this.show,this),keyup:t.proxy(this.update,this),keydown:t.proxy(this.keydown,this)}]]:this.component&&this.hasInput?(this._events=[[this.element.find("input"),{focus:t.proxy(this.show,this),keyup:t.proxy(this.update,this),keydown:t.proxy(this.keydown,this)}],[this.component,{click:t.proxy(this.show,this)}]],this.componentReset&&this._events.push([this.componentReset,{click:t.proxy(this.reset,this)}])):this.element.is("div")?this.isInline=!0:this._events=[[this.element,{click:t.proxy(this.show,this)}]];for(var e,i,n=0;n<this._events.length;n++)e=this._events[n][0],i=this._events[n][1],e.on(i)},_detachEvents:function(){for(var t,e,i=0;i<this._events.length;i++)t=this._events[i][0],e=this._events[i][1],t.off(e);this._events=[]},show:function(e){this.picker.show(),this.height=this.component?this.component.outerHeight():this.element.outerHeight(),this.forceParse&&this.update(),this.place(),t(window).on("resize",t.proxy(this.place,this)),e&&(e.stopPropagation(),e.preventDefault()),this.isVisible=!0,this.element.trigger({type:"show",date:this.date})},hide:function(){this.isVisible&&(this.isInline||(this.picker.hide(),t(window).off("resize",this.place),this.viewMode=this.startViewMode,this.showMode(),this.isInput||t(document).off("mousedown",this.hide),this.forceParse&&(this.isInput&&this.element.val()||this.hasInput&&this.element.find("input").val())&&this.setValue(),this.isVisible=!1,this.element.trigger({type:"hide",date:this.date})))},remove:function(){this._detachEvents(),this.picker.remove(),delete this.picker,delete this.element.data().datetimepicker},getDate:function(){var t=this.getUTCDate();return new Date(t.getTime()+6e4*t.getTimezoneOffset())},getUTCDate:function(){return this.date},setDate:function(t){this.setUTCDate(new Date(t.getTime()-6e4*t.getTimezoneOffset()))},setUTCDate:function(t){t>=this.startDate&&t<=this.endDate?(this.date=t,this.setValue(),this.viewDate=this.date,this.fill()):this.element.trigger({type:"outOfRange",date:t,startDate:this.startDate,endDate:this.endDate})},setFormat:function(t){this.format=s.parseFormat(t,this.formatType);var e;this.isInput?e=this.element:this.component&&(e=this.element.find("input")),e&&e.val()&&this.setValue()},setValue:function(){var e=this.getFormattedDate();this.isInput?this.element.val(e):(this.component&&this.element.find("input").val(e),this.element.data("date",e)),this.linkField&&t("#"+this.linkField).val(this.getFormattedDate(this.linkFormat))},getFormattedDate:function(t){return void 0==t&&(t=this.format),s.formatDate(this.date,t,this.language,this.formatType)},setStartDate:function(t){this.startDate=t||-1/0,this.startDate!==-1/0&&(this.startDate=s.parseDate(this.startDate,this.format,this.language,this.formatType)),this.update(),this.updateNavArrows()},setEndDate:function(t){this.endDate=t||1/0,1/0!==this.endDate&&(this.endDate=s.parseDate(this.endDate,this.format,this.language,this.formatType)),this.update(),this.updateNavArrows()},setDaysOfWeekDisabled:function(e){this.daysOfWeekDisabled=e||[],t.isArray(this.daysOfWeekDisabled)||(this.daysOfWeekDisabled=this.daysOfWeekDisabled.split(/,\s*/)),this.daysOfWeekDisabled=t.map(this.daysOfWeekDisabled,function(t){return parseInt(t,10)}),this.update(),this.updateNavArrows()},place:function(){if(!this.isInline){var e,i,n,s=parseInt(this.element.parents().filter(function(){return"auto"!=t(this).css("z-index")}).first().css("z-index"))+10;this.component?(e=this.component.offset(),n=e.left,("bottom-left"==this.pickerPosition||"top-left"==this.pickerPosition)&&(n+=this.component.outerWidth()-this.picker.outerWidth())):(e=this.element.offset(),n=e.left),i="top-left"==this.pickerPosition||"top-right"==this.pickerPosition?e.top-this.picker.outerHeight():e.top+this.height,this.picker.css({top:i,left:n,zIndex:s})}},update:function(){var t,e=!1;arguments&&arguments.length&&("string"==typeof arguments[0]||arguments[0]instanceof Date)?(t=arguments[0],e=!0):t=this.element.data("date")||(this.isInput?this.element.val():this.element.find("input").val())||this.initialDate,t||(t=new Date,e=!1),this.date=s.parseDate(t,this.format,this.language,this.formatType),e&&this.setValue(),this.viewDate=this.date<this.startDate?new Date(this.startDate):this.date>this.endDate?new Date(this.endDate):new Date(this.date),this.fill()},fillDow:function(){for(var t=this.weekStart,e="<tr>";t<this.weekStart+7;)e+='<th class="dow">'+n[this.language].daysMin[t++%7]+"</th>";e+="</tr>",this.picker.find(".datetimepicker-days thead").append(e)},fillMonths:function(){for(var t="",e=0;12>e;)t+='<span class="month">'+n[this.language].monthsShort[e++]+"</span>";this.picker.find(".datetimepicker-months td").html(t)},fill:function(){if(null!=this.date&&null!=this.viewDate){var i=new Date(this.viewDate),a=i.getUTCFullYear(),o=i.getUTCMonth(),r=i.getUTCDate(),l=i.getUTCHours(),c=i.getUTCMinutes(),d=this.startDate!==-1/0?this.startDate.getUTCFullYear():-1/0,h=this.startDate!==-1/0?this.startDate.getUTCMonth():-1/0,u=1/0!==this.endDate?this.endDate.getUTCFullYear():1/0,p=1/0!==this.endDate?this.endDate.getUTCMonth():1/0,f=new e(this.date.getUTCFullYear(),this.date.getUTCMonth(),this.date.getUTCDate()).valueOf(),g=new Date;if(this.picker.find(".datetimepicker-days thead th:eq(1)").text(n[this.language].months[o]+" "+a),"time"==this.formatViewType){var v=l%12?l%12:12,m=(10>v?"0":"")+v,b=(10>c?"0":"")+c,w=n[this.language].meridiem[12>l?0:1];this.picker.find(".datetimepicker-hours thead th:eq(1)").text(m+":"+b+" "+w.toUpperCase()),this.picker.find(".datetimepicker-minutes thead th:eq(1)").text(m+":"+b+" "+w.toUpperCase())}else this.picker.find(".datetimepicker-hours thead th:eq(1)").text(r+" "+n[this.language].months[o]+" "+a),this.picker.find(".datetimepicker-minutes thead th:eq(1)").text(r+" "+n[this.language].months[o]+" "+a);this.picker.find("tfoot th.today").text(n[this.language].today).toggle(this.todayBtn!==!1),this.updateNavArrows(),this.fillMonths();var y=e(a,o-1,28,0,0,0,0),x=s.getDaysInMonth(y.getUTCFullYear(),y.getUTCMonth());y.setUTCDate(x),y.setUTCDate(x-(y.getUTCDay()-this.weekStart+7)%7);var T=new Date(y);T.setUTCDate(T.getUTCDate()+42),T=T.valueOf();for(var C,S=[];y.valueOf()<T;)y.getUTCDay()==this.weekStart&&S.push("<tr>"),C="",y.getUTCFullYear()<a||y.getUTCFullYear()==a&&y.getUTCMonth()<o?C+=" old":(y.getUTCFullYear()>a||y.getUTCFullYear()==a&&y.getUTCMonth()>o)&&(C+=" new"),this.todayHighlight&&y.getUTCFullYear()==g.getFullYear()&&y.getUTCMonth()==g.getMonth()&&y.getUTCDate()==g.getDate()&&(C+=" today"),y.valueOf()==f&&(C+=" active"),(y.valueOf()+864e5<=this.startDate||y.valueOf()>this.endDate||-1!==t.inArray(y.getUTCDay(),this.daysOfWeekDisabled))&&(C+=" disabled"),S.push('<td class="day'+C+'">'+y.getUTCDate()+"</td>"),y.getUTCDay()==this.weekEnd&&S.push("</tr>"),y.setUTCDate(y.getUTCDate()+1);this.picker.find(".datetimepicker-days tbody").empty().append(S.join("")),S=[];for(var k="",_="",$="",D=0;24>D;D++){var I=e(a,o,r,D);C="",I.valueOf()+36e5<=this.startDate||I.valueOf()>this.endDate?C+=" disabled":l==D&&(C+=" active"),this.showMeridian&&2==n[this.language].meridiem.length?(_=12>D?n[this.language].meridiem[0]:n[this.language].meridiem[1],_!=$&&(""!=$&&S.push("</fieldset>"),S.push('<fieldset class="hour"><legend>'+_.toUpperCase()+"</legend>")),$=_,k=D%12?D%12:12,S.push('<span class="hour'+C+" hour_"+(12>D?"am":"pm")+'">'+k+"</span>"),23==D&&S.push("</fieldset>")):(k=D+":00",S.push('<span class="hour'+C+'">'+k+"</span>"))}this.picker.find(".datetimepicker-hours td").html(S.join("")),S=[],k="",_="",$="";for(var D=0;60>D;D+=this.minuteStep){var I=e(a,o,r,l,D,0);C="",I.valueOf()<this.startDate||I.valueOf()>this.endDate?C+=" disabled":Math.floor(c/this.minuteStep)==Math.floor(D/this.minuteStep)&&(C+=" active"),this.showMeridian&&2==n[this.language].meridiem.length?(_=12>l?n[this.language].meridiem[0]:n[this.language].meridiem[1],_!=$&&(""!=$&&S.push("</fieldset>"),S.push('<fieldset class="minute"><legend>'+_.toUpperCase()+"</legend>")),$=_,k=l%12?l%12:12,S.push('<span class="minute'+C+'">'+k+":"+(10>D?"0"+D:D)+"</span>"),59==D&&S.push("</fieldset>")):(k=D+":00",S.push('<span class="minute'+C+'">'+l+":"+(10>D?"0"+D:D)+"</span>"))}this.picker.find(".datetimepicker-minutes td").html(S.join(""));var M=this.date.getUTCFullYear(),E=this.picker.find(".datetimepicker-months").find("th:eq(1)").text(a).end().find("span").removeClass("active");M==a&&E.eq(this.date.getUTCMonth()).addClass("active"),(d>a||a>u)&&E.addClass("disabled"),a==d&&E.slice(0,h).addClass("disabled"),a==u&&E.slice(p+1).addClass("disabled"),S="",a=10*parseInt(a/10,10);var A=this.picker.find(".datetimepicker-years").find("th:eq(1)").text(a+"-"+(a+9)).end().find("td");a-=1;for(var D=-1;11>D;D++)S+='<span class="year'+(-1==D||10==D?" old":"")+(M==a?" active":"")+(d>a||a>u?" disabled":"")+'">'+a+"</span>",a+=1;A.html(S),this.place()}},updateNavArrows:function(){var t=new Date(this.viewDate),e=t.getUTCFullYear(),i=t.getUTCMonth(),n=t.getUTCDate(),s=t.getUTCHours();switch(this.viewMode){case 0:this.startDate!==-1/0&&e<=this.startDate.getUTCFullYear()&&i<=this.startDate.getUTCMonth()&&n<=this.startDate.getUTCDate()&&s<=this.startDate.getUTCHours()?this.picker.find(".prev").css({visibility:"hidden"}):this.picker.find(".prev").css({visibility:"visible"}),1/0!==this.endDate&&e>=this.endDate.getUTCFullYear()&&i>=this.endDate.getUTCMonth()&&n>=this.endDate.getUTCDate()&&s>=this.endDate.getUTCHours()?this.picker.find(".next").css({visibility:"hidden"}):this.picker.find(".next").css({visibility:"visible"});break;case 1:this.startDate!==-1/0&&e<=this.startDate.getUTCFullYear()&&i<=this.startDate.getUTCMonth()&&n<=this.startDate.getUTCDate()?this.picker.find(".prev").css({visibility:"hidden"}):this.picker.find(".prev").css({visibility:"visible"}),1/0!==this.endDate&&e>=this.endDate.getUTCFullYear()&&i>=this.endDate.getUTCMonth()&&n>=this.endDate.getUTCDate()?this.picker.find(".next").css({visibility:"hidden"}):this.picker.find(".next").css({visibility:"visible"});break;case 2:this.startDate!==-1/0&&e<=this.startDate.getUTCFullYear()&&i<=this.startDate.getUTCMonth()?this.picker.find(".prev").css({visibility:"hidden"}):this.picker.find(".prev").css({visibility:"visible"}),1/0!==this.endDate&&e>=this.endDate.getUTCFullYear()&&i>=this.endDate.getUTCMonth()?this.picker.find(".next").css({visibility:"hidden"}):this.picker.find(".next").css({visibility:"visible"});break;case 3:case 4:this.startDate!==-1/0&&e<=this.startDate.getUTCFullYear()?this.picker.find(".prev").css({visibility:"hidden"}):this.picker.find(".prev").css({visibility:"visible"}),1/0!==this.endDate&&e>=this.endDate.getUTCFullYear()?this.picker.find(".next").css({visibility:"hidden"}):this.picker.find(".next").css({visibility:"visible"})}},mousewheel:function(e){if(e.preventDefault(),e.stopPropagation(),!this.wheelPause){this.wheelPause=!0;var i=e.originalEvent,n=i.wheelDelta,s=n>0?1:0===n?0:-1;this.wheelViewModeNavigationInverseDirection&&(s=-s),this.showMode(s),setTimeout(t.proxy(function(){this.wheelPause=!1},this),this.wheelViewModeNavigationDelay)}},click:function(i){i.stopPropagation(),i.preventDefault();var n=t(i.target).closest("span, td, th, legend");if(1==n.length){if(n.is(".disabled"))return this.element.trigger({type:"outOfRange",date:this.viewDate,startDate:this.startDate,endDate:this.endDate}),void 0;switch(n[0].nodeName.toLowerCase()){case"th":switch(n[0].className){case"switch":this.showMode(1);break;case"prev":case"next":var a=s.modes[this.viewMode].navStep*("prev"==n[0].className?-1:1);switch(this.viewMode){case 0:this.viewDate=this.moveHour(this.viewDate,a);break;case 1:this.viewDate=this.moveDate(this.viewDate,a);break;case 2:this.viewDate=this.moveMonth(this.viewDate,a);break;case 3:case 4:this.viewDate=this.moveYear(this.viewDate,a)}this.fill();break;case"today":var o=new Date;o=e(o.getFullYear(),o.getMonth(),o.getDate(),o.getHours(),o.getMinutes(),o.getSeconds(),0),this.viewMode=this.startViewMode,this.showMode(0),this._setDate(o),this.fill(),this.autoclose&&this.hide()}break;case"span":if(!n.is(".disabled")){var r=this.viewDate.getUTCFullYear(),l=this.viewDate.getUTCMonth(),c=this.viewDate.getUTCDate(),d=this.viewDate.getUTCHours(),h=this.viewDate.getUTCMinutes(),u=this.viewDate.getUTCSeconds();if(n.is(".month")?(this.viewDate.setUTCDate(1),l=n.parent().find("span").index(n),c=this.viewDate.getUTCDate(),this.viewDate.setUTCMonth(l),this.element.trigger({type:"changeMonth",date:this.viewDate}),this.viewSelect>=3&&this._setDate(e(r,l,c,d,h,u,0))):n.is(".year")?(this.viewDate.setUTCDate(1),r=parseInt(n.text(),10)||0,this.viewDate.setUTCFullYear(r),this.element.trigger({type:"changeYear",date:this.viewDate}),this.viewSelect>=4&&this._setDate(e(r,l,c,d,h,u,0))):n.is(".hour")?(d=parseInt(n.text(),10)||0,(n.hasClass("hour_am")||n.hasClass("hour_pm"))&&(12==d&&n.hasClass("hour_am")?d=0:12!=d&&n.hasClass("hour_pm")&&(d+=12)),this.viewDate.setUTCHours(d),this.element.trigger({type:"changeHour",date:this.viewDate}),this.viewSelect>=1&&this._setDate(e(r,l,c,d,h,u,0))):n.is(".minute")&&(h=parseInt(n.text().substr(n.text().indexOf(":")+1),10)||0,this.viewDate.setUTCMinutes(h),this.element.trigger({type:"changeMinute",date:this.viewDate}),this.viewSelect>=0&&this._setDate(e(r,l,c,d,h,u,0))),0!=this.viewMode){var p=this.viewMode;this.showMode(-1),this.fill(),p==this.viewMode&&this.autoclose&&this.hide()}else this.fill(),this.autoclose&&this.hide()}break;case"td":if(n.is(".day")&&!n.is(".disabled")){var c=parseInt(n.text(),10)||1,r=this.viewDate.getUTCFullYear(),l=this.viewDate.getUTCMonth(),d=this.viewDate.getUTCHours(),h=this.viewDate.getUTCMinutes(),u=this.viewDate.getUTCSeconds();n.is(".old")?0===l?(l=11,r-=1):l-=1:n.is(".new")&&(11==l?(l=0,r+=1):l+=1),this.viewDate.setUTCFullYear(r),this.viewDate.setUTCMonth(l),this.viewDate.setUTCDate(c),this.element.trigger({type:"changeDay",date:this.viewDate}),this.viewSelect>=2&&this._setDate(e(r,l,c,d,h,u,0))}var p=this.viewMode;this.showMode(-1),this.fill(),p==this.viewMode&&this.autoclose&&this.hide()}}},_setDate:function(t,e){e&&"date"!=e||(this.date=t),e&&"view"!=e||(this.viewDate=t),this.fill(),this.setValue();var i;this.isInput?i=this.element:this.component&&(i=this.element.find("input")),i&&(i.change(),this.autoclose&&(!e||"date"==e)),this.element.trigger({type:"changeDate",date:this.date})},moveMinute:function(t,e){if(!e)return t;var i=new Date(t.valueOf());return i.setUTCMinutes(i.getUTCMinutes()+e*this.minuteStep),i},moveHour:function(t,e){if(!e)return t;var i=new Date(t.valueOf());return i.setUTCHours(i.getUTCHours()+e),i},moveDate:function(t,e){if(!e)return t;var i=new Date(t.valueOf());return i.setUTCDate(i.getUTCDate()+e),i},moveMonth:function(t,e){if(!e)return t;var i,n,s=new Date(t.valueOf()),a=s.getUTCDate(),o=s.getUTCMonth(),r=Math.abs(e);if(e=e>0?1:-1,1==r)n=-1==e?function(){return s.getUTCMonth()==o}:function(){return s.getUTCMonth()!=i},i=o+e,s.setUTCMonth(i),(0>i||i>11)&&(i=(i+12)%12);else{for(var l=0;r>l;l++)s=this.moveMonth(s,e);i=s.getUTCMonth(),s.setUTCDate(a),n=function(){return i!=s.getUTCMonth()}}for(;n();)s.setUTCDate(--a),s.setUTCMonth(i);return s},moveYear:function(t,e){return this.moveMonth(t,12*e)},dateWithinRange:function(t){return t>=this.startDate&&t<=this.endDate},keydown:function(t){if(this.picker.is(":not(:visible)"))return 27==t.keyCode&&this.show(),void 0;var e,i,n,s=!1;switch(t.keyCode){case 27:this.hide(),t.preventDefault();break;case 37:case 39:if(!this.keyboardNavigation)break;e=37==t.keyCode?-1:1,viewMode=this.viewMode,t.ctrlKey?viewMode+=2:t.shiftKey&&(viewMode+=1),4==viewMode?(i=this.moveYear(this.date,e),n=this.moveYear(this.viewDate,e)):3==viewMode?(i=this.moveMonth(this.date,e),n=this.moveMonth(this.viewDate,e)):2==viewMode?(i=this.moveDate(this.date,e),n=this.moveDate(this.viewDate,e)):1==viewMode?(i=this.moveHour(this.date,e),n=this.moveHour(this.viewDate,e)):0==viewMode&&(i=this.moveMinute(this.date,e),n=this.moveMinute(this.viewDate,e)),this.dateWithinRange(i)&&(this.date=i,this.viewDate=n,this.setValue(),this.update(),t.preventDefault(),s=!0);break;case 38:case 40:if(!this.keyboardNavigation)break;e=38==t.keyCode?-1:1,viewMode=this.viewMode,t.ctrlKey?viewMode+=2:t.shiftKey&&(viewMode+=1),4==viewMode?(i=this.moveYear(this.date,e),n=this.moveYear(this.viewDate,e)):3==viewMode?(i=this.moveMonth(this.date,e),n=this.moveMonth(this.viewDate,e)):2==viewMode?(i=this.moveDate(this.date,7*e),n=this.moveDate(this.viewDate,7*e)):1==viewMode?this.showMeridian?(i=this.moveHour(this.date,6*e),n=this.moveHour(this.viewDate,6*e)):(i=this.moveHour(this.date,4*e),n=this.moveHour(this.viewDate,4*e)):0==viewMode&&(i=this.moveMinute(this.date,4*e),n=this.moveMinute(this.viewDate,4*e)),this.dateWithinRange(i)&&(this.date=i,this.viewDate=n,this.setValue(),this.update(),t.preventDefault(),s=!0);break;case 13:if(0!=this.viewMode){var a=this.viewMode;this.showMode(-1),this.fill(),a==this.viewMode&&this.autoclose&&this.hide()}else this.fill(),this.autoclose&&this.hide();t.preventDefault();break;case 9:this.hide()}if(s){var o;this.isInput?o=this.element:this.component&&(o=this.element.find("input")),o&&o.change(),this.element.trigger({type:"changeDate",date:this.date})}},showMode:function(t){if(t){var e=Math.max(0,Math.min(s.modes.length-1,this.viewMode+t));e>=this.minView&&e<=this.maxView&&(this.element.trigger({type:"changeMode",date:this.viewDate,oldViewMode:this.viewMode,newViewMode:e}),this.viewMode=e)}this.picker.find(">div").hide().filter(".datetimepicker-"+s.modes[this.viewMode].clsName).css("display","block"),this.updateNavArrows()},reset:function(){this._setDate(null,"date")}},t.fn.datetimepicker=function(e){var n=Array.apply(null,arguments);return n.shift(),this.each(function(){var s=t(this),a=s.data("datetimepicker"),o="object"==typeof e&&e;a||s.data("datetimepicker",a=new i(this,t.extend({},t.fn.datetimepicker.defaults,o))),"string"==typeof e&&"function"==typeof a[e]&&a[e].apply(a,n)})},t.fn.datetimepicker.defaults={},t.fn.datetimepicker.Constructor=i;var n=t.fn.datetimepicker.dates={en:{days:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],daysShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat","Sun"],daysMin:["Su","Mo","Tu","We","Th","Fr","Sa","Su"],months:["January","February","March","April","May","June","July","August","September","October","November","December"],monthsShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],meridiem:["am","pm"],suffix:["st","nd","rd","th"],today:"Today"}},s={modes:[{clsName:"minutes",navFnc:"Hours",navStep:1},{clsName:"hours",navFnc:"Date",navStep:1},{clsName:"days",navFnc:"Month",navStep:1},{clsName:"months",navFnc:"FullYear",navStep:1},{clsName:"years",navFnc:"FullYear",navStep:10}],isLeapYear:function(t){return 0===t%4&&0!==t%100||0===t%400},getDaysInMonth:function(t,e){return[31,s.isLeapYear(t)?29:28,31,30,31,30,31,31,30,31,30,31][e]},getDefaultFormat:function(t,e){if("standard"==t)return"input"==e?"yyyy-mm-dd hh:ii":"yyyy-mm-dd hh:ii:ss";if("php"==t)return"input"==e?"Y-m-d H:i":"Y-m-d H:i:s";throw new Error("Invalid format type.")},validParts:function(t){if("standard"==t)return/hh?|HH?|p|P|ii?|ss?|dd?|DD?|mm?|MM?|yy(?:yy)?/g;if("php"==t)return/[dDjlNwzFmMnStyYaABgGhHis]/g;throw new Error("Invalid format type.")},nonpunctuation:/[^ -\/:-@\[-`{-~\t\n\rTZ]+/g,parseFormat:function(t,e){var i=t.replace(this.validParts(e),"\0").split("\0"),n=t.match(this.validParts(e));if(!i||!i.length||!n||0==n.length)throw new Error("Invalid date format.");return{separators:i,parts:n}},parseDate:function(s,a,o,r){if(s instanceof Date){var l=new Date(s.valueOf()-6e4*s.getTimezoneOffset());return l.setMilliseconds(0),l}if(/^\d{4}\-\d{1,2}\-\d{1,2}$/.test(s)&&(a=this.parseFormat("yyyy-mm-dd",r)),/^\d{4}\-\d{1,2}\-\d{1,2}[T ]\d{1,2}\:\d{1,2}$/.test(s)&&(a=this.parseFormat("yyyy-mm-dd hh:ii",r)),/^\d{4}\-\d{1,2}\-\d{1,2}[T ]\d{1,2}\:\d{1,2}\:\d{1,2}[Z]{0,1}$/.test(s)&&(a=this.parseFormat("yyyy-mm-dd hh:ii:ss",r)),/^[-+]\d+[dmwy]([\s,]+[-+]\d+[dmwy])*$/.test(s)){var c,d,h=/([-+]\d+)([dmwy])/,u=s.match(/([-+]\d+)([dmwy])/g);s=new Date;for(var p=0;p<u.length;p++)switch(c=h.exec(u[p]),d=parseInt(c[1]),c[2]){case"d":s.setUTCDate(s.getUTCDate()+d);break;case"m":s=i.prototype.moveMonth.call(i.prototype,s,d);break;case"w":s.setUTCDate(s.getUTCDate()+7*d);break;case"y":s=i.prototype.moveYear.call(i.prototype,s,d)}return e(s.getUTCFullYear(),s.getUTCMonth(),s.getUTCDate(),s.getUTCHours(),s.getUTCMinutes(),s.getUTCSeconds(),0)}var f,g,c,u=s&&s.match(this.nonpunctuation)||[],s=new Date(0,0,0,0,0,0,0),v={},m=["hh","h","ii","i","ss","s","yyyy","yy","M","MM","m","mm","D","DD","d","dd","H","HH","p","P"],b={hh:function(t,e){return t.setUTCHours(e)},h:function(t,e){return t.setUTCHours(e)},HH:function(t,e){return t.setUTCHours(12==e?0:e)},H:function(t,e){return t.setUTCHours(12==e?0:e)},ii:function(t,e){return t.setUTCMinutes(e)},i:function(t,e){return t.setUTCMinutes(e)},ss:function(t,e){return t.setUTCSeconds(e)},s:function(t,e){return t.setUTCSeconds(e)},yyyy:function(t,e){return t.setUTCFullYear(e)},yy:function(t,e){return t.setUTCFullYear(2e3+e)},m:function(t,e){for(e-=1;0>e;)e+=12;for(e%=12,t.setUTCMonth(e);t.getUTCMonth()!=e;)t.setUTCDate(t.getUTCDate()-1);return t},d:function(t,e){return t.setUTCDate(e)},p:function(t,e){return t.setUTCHours(1==e?t.getUTCHours()+12:t.getUTCHours())}};if(b.M=b.MM=b.mm=b.m,b.dd=b.d,b.P=b.p,s=e(s.getFullYear(),s.getMonth(),s.getDate(),s.getHours(),s.getMinutes(),s.getSeconds()),u.length==a.parts.length){for(var p=0,w=a.parts.length;w>p;p++){if(f=parseInt(u[p],10),c=a.parts[p],isNaN(f))switch(c){case"MM":g=t(n[o].months).filter(function(){var t=this.slice(0,u[p].length),e=u[p].slice(0,t.length);return t==e}),f=t.inArray(g[0],n[o].months)+1;break;case"M":g=t(n[o].monthsShort).filter(function(){var t=this.slice(0,u[p].length),e=u[p].slice(0,t.length);return t==e}),f=t.inArray(g[0],n[o].monthsShort)+1;break;case"p":case"P":f=t.inArray(u[p].toLowerCase(),n[o].meridiem)}v[c]=f}for(var y,p=0;p<m.length;p++)y=m[p],y in v&&!isNaN(v[y])&&b[y](s,v[y])}return s},formatDate:function(e,i,a,o){if(null==e)return"";var r;if("standard"==o)r={yy:e.getUTCFullYear().toString().substring(2),yyyy:e.getUTCFullYear(),m:e.getUTCMonth()+1,M:n[a].monthsShort[e.getUTCMonth()],MM:n[a].months[e.getUTCMonth()],d:e.getUTCDate(),D:n[a].daysShort[e.getUTCDay()],DD:n[a].days[e.getUTCDay()],p:2==n[a].meridiem.length?n[a].meridiem[e.getUTCHours()<12?0:1]:"",h:e.getUTCHours(),i:e.getUTCMinutes(),s:e.getUTCSeconds()},r.H=0==r.h%12?12:r.h%12,r.HH=(r.H<10?"0":"")+r.H,r.P=r.p.toUpperCase(),r.hh=(r.h<10?"0":"")+r.h,r.ii=(r.i<10?"0":"")+r.i,r.ss=(r.s<10?"0":"")+r.s,r.dd=(r.d<10?"0":"")+r.d,r.mm=(r.m<10?"0":"")+r.m;else{if("php"!=o)throw new Error("Invalid format type.");r={y:e.getUTCFullYear().toString().substring(2),Y:e.getUTCFullYear(),F:n[a].months[e.getUTCMonth()],M:n[a].monthsShort[e.getUTCMonth()],n:e.getUTCMonth()+1,t:s.getDaysInMonth(e.getUTCFullYear(),e.getUTCMonth()),j:e.getUTCDate(),l:n[a].days[e.getUTCDay()],D:n[a].daysShort[e.getUTCDay()],w:e.getUTCDay(),N:0==e.getUTCDay()?7:e.getUTCDay(),S:e.getUTCDate()%10<=n[a].suffix.length?n[a].suffix[e.getUTCDate()%10-1]:"",a:2==n[a].meridiem.length?n[a].meridiem[e.getUTCHours()<12?0:1]:"",g:0==e.getUTCHours()%12?12:e.getUTCHours()%12,G:e.getUTCHours(),i:e.getUTCMinutes(),s:e.getUTCSeconds()},r.m=(r.n<10?"0":"")+r.n,r.d=(r.j<10?"0":"")+r.j,r.A=r.a.toString().toUpperCase(),r.h=(r.g<10?"0":"")+r.g,r.H=(r.G<10?"0":"")+r.G,r.i=(r.i<10?"0":"")+r.i,r.s=(r.s<10?"0":"")+r.s}for(var e=[],l=t.extend([],i.separators),c=0,d=i.parts.length;d>c;c++)l.length&&e.push(l.shift()),e.push(r[i.parts[c]]);return e.join("")},convertViewMode:function(t){switch(t){case 4:case"decade":t=4;break;case 3:case"year":t=3;break;case 2:case"month":t=2;break;case 1:case"day":t=1;break;case 0:case"hour":t=0}return t},headTemplate:'<thead><tr><th class="prev"><i class="fa fa-angle-left"/></th><th colspan="5" class="switch"></th><th class="next"><i class="fa fa-angle-right"/></th></tr></thead>',contTemplate:'<tbody><tr><td colspan="7"></td></tr></tbody>',footTemplate:'<tfoot><tr><th colspan="7" class="today"></th></tr></tfoot>'};s.template='<div class="datetimepicker"><div class="datetimepicker-minutes"><table class=" table-condensed">'+s.headTemplate+s.contTemplate+s.footTemplate+"</table>"+"</div>"+'<div class="datetimepicker-hours">'+'<table class=" table-condensed">'+s.headTemplate+s.contTemplate+s.footTemplate+"</table>"+"</div>"+'<div class="datetimepicker-days">'+'<table class=" table-condensed">'+s.headTemplate+"<tbody></tbody>"+s.footTemplate+"</table>"+"</div>"+'<div class="datetimepicker-months">'+'<table class="table-condensed">'+s.headTemplate+s.contTemplate+s.footTemplate+"</table>"+"</div>"+'<div class="datetimepicker-years">'+'<table class="table-condensed">'+s.headTemplate+s.contTemplate+s.footTemplate+"</table>"+"</div>"+"</div>",t.fn.datetimepicker.DPGlobal=s,t.fn.datetimepicker.noConflict=function(){return t.fn.datetimepicker=old,this},t(document).on("focus.datetimepicker.data-api click.datetimepicker.data-api",'[data-provide="datetimepicker"]',function(e){var i=t(this);i.data("datetimepicker")||(e.preventDefault(),i.datetimepicker("show"))}),t(function(){t('[data-provide="datetimepicker-inline"]').datetimepicker()})}(window.jQuery);