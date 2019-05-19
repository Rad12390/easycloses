/*
 Input Mask plugin for jquery
 http://github.com/RobinHerbots/jquery.inputmask
 Copyright (c) 2010 - 2014 Robin Herbots
 Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
 Version: 3.0.3
*/
(function(c){if(void 0===c.fn.inputmask){var a=function(a){var e=document.createElement("input");a="on"+a;var c=a in e;c||(e.setAttribute(a,"return;"),c="function"==typeof e[a]);return c},h=function(a,e,b){return(a=b.aliases[a])?(a.alias&&h(a.alias,void 0,b),c.extend(!0,b,a),c.extend(!0,b,e),!0):!1},d=function(a){function e(e){function c(a,b,e,l){this.matches=[];this.isGroup=a||!1;this.isOptional=b||!1;this.isQuantifier=e||!1;this.isAlternator=l||!1;this.quantifier={min:1,max:1}}function b(c,e,l){var d=
a.definitions[e];l=void 0!=l?l:c.matches.length;if(d&&!h){for(var f=d.prevalidator,p=f?f.length:0,g=1;g<d.cardinality;g++){var n=p>=g?f[g-1]:[],k=n.validator,n=n.cardinality;c.matches.splice(l++,0,{fn:k?"string"==typeof k?RegExp(k):new function(){this.test=k}:/./,cardinality:n?n:1,optionality:c.isOptional,casing:d.casing,def:d.definitionSymbol||e})}c.matches.splice(l++,0,{fn:d.validator?"string"==typeof d.validator?RegExp(d.validator):new function(){this.test=d.validator}:/./,cardinality:d.cardinality,
optionality:c.isOptional,casing:d.casing,def:d.definitionSymbol||e})}else c.matches.splice(l++,0,{fn:null,cardinality:0,optionality:c.isOptional,casing:null,def:e}),h=!1}for(var d=/(?:[?*+]|\{[0-9]+(?:,[0-9\+\*]*)?\})\??|[^.?*+^${[]()|\\]+|./g,h=!1,f=new c,l,p=[],g=[];l=d.exec(e);)switch(l=l[0],l.charAt(0)){case a.optionalmarker.end:case a.groupmarker.end:var n=p.pop();0<p.length?p[p.length-1].matches.push(n):f.matches.push(n);break;case a.optionalmarker.start:p.push(new c(!1,!0));break;case a.groupmarker.start:p.push(new c(!0));
break;case a.quantifiermarker.start:n=new c(!1,!1,!0);l=l.replace(/[{}]/g,"");var k=l.split(",");l=isNaN(k[0])?k[0]:parseInt(k[0]);k=1==k.length?l:isNaN(k[1])?k[1]:parseInt(k[1]);n.quantifier={min:l,max:k};if("*"==k||"+"==k)a.greedy=!1;if(0<p.length){k=p[p.length-1].matches;l=k.pop();if(!l.isGroup){var r=new c(!0);r.matches.push(l);l=r}k.push(l);k.push(n)}else l=f.matches.pop(),l.isGroup||(r=new c(!0),r.matches.push(l),l=r),f.matches.push(l),f.matches.push(n);break;case a.escapeChar:h=!0;break;case a.alternatormarker:break;
default:0<p.length?b(p[p.length-1],l):(0<f.matches.length&&(n=f.matches[f.matches.length-1],n.isGroup&&(n.isGroup=!1,b(n,a.groupmarker.start,0),b(n,a.groupmarker.end))),b(f,l))}0<f.matches.length&&g.push(f);return g}function b(d,h){a.numericInput&&(d=d.split("").reverse().join(""));if(void 0!=d&&""!=d){if(0<a.repeat||"*"==a.repeat||"+"==a.repeat)d=a.groupmarker.start+d+a.groupmarker.end+a.quantifiermarker.start+("*"==a.repeat?0:"+"==a.repeat?1:a.repeat)+","+a.repeat+a.quantifiermarker.end;void 0==
c.inputmask.masksCache[d]&&(c.inputmask.masksCache[d]={mask:d,maskToken:e(d),validPositions:{},_buffer:void 0,buffer:void 0,tests:{},metadata:h});return c.extend(!0,{},c.inputmask.masksCache[d])}}var d=[];c.isFunction(a.mask)&&(a.mask=a.mask.call(this,a));c.isArray(a.mask)?c.each(a.mask,function(a,c){void 0!=c.mask?d.push(b(c.mask.toString(),c)):d.push(b(c.toString()))}):(1==a.mask.length&&!1==a.greedy&&0!=a.repeat&&(a.placeholder=""),d=void 0!=a.mask.mask?b(a.mask.mask.toString(),a.mask):b(a.mask.toString()));
return d},f="function"===typeof ScriptEngineMajorVersion?ScriptEngineMajorVersion():10<=(new Function("/*@cc_on return @_jscript_version; @*/"))(),b=navigator.userAgent,g=null!==b.match(/iphone/i),k=null!==b.match(/android.*safari.*/i),m=null!==b.match(/android.*chrome.*/i),x=null!==b.match(/android.*firefox.*/i),O=/Kindle/i.test(b)||/Silk/i.test(b)||/KFTT/i.test(b)||/KFOT/i.test(b)||/KFJWA/i.test(b)||/KFJWI/i.test(b)||/KFSOWI/i.test(b)||/KFTHWA/i.test(b)||/KFTHWI/i.test(b)||/KFAPWA/i.test(b)||/KFAPWI/i.test(b),
P=a("paste")?"paste":a("input")?"input":"propertychange",v=function(a,e,b){function d(q,b,c){b=b||0;var l=[],h,f=0,g;do{if(!0===q&&a.validPositions[f]){var p=a.validPositions[f];g=p.match;h=p.locator.slice();l.push(null==g.fn?g.def:!0===c?p.input:e.placeholder.charAt(f%e.placeholder.length))}else h=M(f,!0,h,f-1),h=h[e.greedy||b>f?0:h.length-1],g=h.match,h=h.locator.slice(),l.push(null==g.fn?g.def:e.placeholder.charAt(f%e.placeholder.length));f++}while((void 0==I||f-1<I)&&null!=g.fn||null==g.fn&&""!=
g.def||b>=f);l.pop();return l}function h(q){var b=a;b.buffer=void 0;b.tests={};!0!==q&&(b._buffer=void 0,b.validPositions={},b.p=-1)}function r(q){var b=a;q=-1;for(var c in b.validPositions)b=parseInt(c),b>q&&(q=b);return q}function S(q,b,d,l){if(e.insertMode&&void 0!=a.validPositions[q]&&void 0==l){l=c.extend(!0,{},a.validPositions);for(var h=F(D());h>q&&0<=h;h--)if(G(h)){var f=F(h),g=a.validPositions[f];void 0!=g&&v(h).def==v(f).def&&void 0==a.validPositions[h]&&!1!==U(h,g.input,d,!0)&&delete a.validPositions[f]}if(void 0==
a.validPositions[q])a.validPositions[q]=b;else return a.validPositions=c.extend(!0,{},l),!1}else a.validPositions[q]=b;return!0}function v(q){return a.validPositions[q]?a.validPositions[q].match:M(q)[0].match}function M(q,b,c,e){function d(a,b,c,e){function l(c,e,u){var B=h;if(h==q&&void 0==c.matches)return f.push({match:c,locator:e.reverse()}),!0;if(void 0!=c.matches)if(c.isGroup&&!0!==u){if(c=l(a.matches[H+1],e))return!0}else if(c.isOptional){var p=c;if(c=d(c,b,e,u))c=f[f.length-1].match,(c=0==
p.matches.indexOf(c))&&(g=!0),h=B}else{if(!c.isAlternator)if(c.isQuantifier&&!0!==u)for(B=c,u=0<b.length&&!0!==u?b.shift():0;u<(isNaN(B.quantifier.max)?u+1:B.quantifier.max)&&h<=q;u++){if(p=a.matches[a.matches.indexOf(B)-1],c=l(p,[u].concat(e),!0))if(c=f[f.length-1].match,u>B.quantifier.min-1&&(c.optionalQuantifier=!0),c=0==p.matches.indexOf(c))if(u>B.quantifier.min-1){g=!0;h=q;break}else return!0;else return!0}else if(c=d(c,b,e,u))return!0}else h++}for(var H=0<b.length?b.shift():0;H<a.matches.length;H++)if(!0!==
a.matches[H].isQuantifier){var u=l(a.matches[H],[H].concat(c),e);if(u&&h==q)return u;if(h>q)break}}var l=a.maskToken,h=c?e:0;e=c||[0];var f=[],g=!1;if(!0!==b&&a.tests[q]&&!a.validPositions[q])return a.tests[q];if(void 0==c){for(b=q-1;void 0==(c=a.validPositions[b])&&-1<b;)b--;if(void 0!=c&&-1<b)h=b,e=c.locator.slice();else{for(b=q-1;void 0==(c=a.tests[b])&&-1<b;)b--;void 0!=c&&-1<b&&(h=b,e=c[0].locator.slice())}}for(b=e.shift();b<l.length&&!(d(l[b],e,[b])&&h==q||h>q);b++);(0==f.length||g&&2>f.length)&&
f.push({match:{fn:null,cardinality:0,optionality:!0,casing:null,def:""},locator:[]});return a.tests[q]=f}function y(){void 0==a._buffer&&(a._buffer=d(!1,1));return a._buffer}function l(){void 0==a.buffer&&(a.buffer=d(!0,r(),!0));return a.buffer}function s(a,b){for(var e=l(),d=a;d<b;d++)if(e[d]!=R(d)){var h=M(d,!1)[0];S(d,c.extend({},h,{input:E(e[d],h.match)}),!0)}}function E(a,b){switch(b.casing){case "upper":a=a.toUpperCase();break;case "lower":a=a.toLowerCase()}return a}function U(b,d,f,g){function p(b,
q,d,f){var u=!1;c.each(M(b,!d),function(H,g){for(var B=g.match,p=q?1:0,n="",k=l(),T=B.cardinality;T>p;T--)n+=void 0==a.validPositions[b-(T-1)]?R(b-(T-1)):a.validPositions[b-(T-1)].input;q&&(n+=q);u=null!=B.fn?B.fn.test(n,k,b,d,e):q!=B.def&&q!=e.skipOptionalPartCharacter||""==B.def?!1:{c:B.def,pos:b};if(!1!==u)return p=void 0!=u.c?u.c:q,p=p==e.skipOptionalPartCharacter?B.def:p,n=b,u.refreshFromBuffer?(k=u.refreshFromBuffer,d=!0,n=void 0!=u.pos?u.pos:b,g=M(n,!d)[0],!0===k?(a.validPositions={},s(0,l().length)):
s(k.start,k.end)):!0!==u&&u.pos!=b&&(n=u.pos,s(b,n),g=M(n,!d)[0]),0<H&&h(!0),S(n,c.extend({},g,{input:E(p,B)}),d,f)||(u=!1),!1});return u}f=!0===f;var n=p(b,d,f,g);if(!f&&(e.insertMode||void 0==a.validPositions[A(b)])&&!1===n&&!G(b))for(var k=b+1,r=A(b);k<=r;k++)if(n=p(k,d,f,g),!1!==n){b=k;break}!0===n&&(n={pos:b});return n}function G(a){a=v(a);return null!=a.fn?a.fn:!1}function D(){var b;I=t.prop("maxLength");-1==I&&(I=void 0);if(!1==e.greedy){b=r()+1;for(var c=v(b);null!=c.fn||""!=c.def;)c=v(++b),
!0!==c.optionality&&(c=M(b),c=c[c.length-1].match);b=d(!0,b).length;a.tests={}}else b=l().length;return void 0==I||b<I?b:I}function A(a){var b=D();if(a>=b)return b;for(;++a<b&&!G(a)&&(!0!==e.nojumps||e.nojumpsThreshold>a););return a}function F(a){if(0>=a)return 0;for(;0<--a&&!G(a););return a}function C(a,b,c){a._valueSet(b.join(""));void 0!=c&&w(a,c)}function R(a){var b=v(a);return null==b.fn?b.def:e.placeholder.charAt(a%e.placeholder.length)}function N(b,e,d,l,f){l=void 0!=l?l.slice():ea(b._valueGet()).split("");
h();e&&b._valueSet("");c.each(l,function(l,h){if(!0===f){var g=a.p,g=-1==g?g:F(g),p=-1==g?l:A(g);-1==c.inArray(h,y().slice(g+1,p))&&V.call(b,void 0,!0,h.charCodeAt(0),e,d,l)}else V.call(b,void 0,!0,h.charCodeAt(0),e,d,l),d=d||0<l&&l>a.p})}function $(a){return c.inputmask.escapeRegex.call(this,a)}function ea(a){return a.replace(RegExp("("+$(y().join(""))+")*$"),"")}function W(a){var b=l().slice(),c;for(c=b.length-1;0<=c;c--){var e=v(c);if((e.optionality||e.optionalQuantifier)&&b[c]==R(c))b.pop();else break}C(a,
b)}function fa(a,b){if(!a.data("_inputmask")||!0!==b&&a.hasClass("hasDatepicker"))return a[0]._valueGet();var d=c.map(l(),function(a,b){return G(b)&&U(b,a,!0)?a:null}),d=(z?d.reverse():d).join(""),h=(z?l().reverse():l()).join("");return c.isFunction(e.onUnMask)?e.onUnMask.call(a,h,d,e):d}function J(a){!z||"number"!=typeof a||e.greedy&&""==e.placeholder||(a=l().length-a);return a}function w(a,b,d){a=a.jquery&&0<a.length?a[0]:a;if("number"==typeof b){b=J(b);d=J(d);d="number"==typeof d?d:b;var l=c(a).data("_inputmask")||
{};l.caret={begin:b,end:d};c(a).data("_inputmask",l);c(a).is(":visible")&&(a.scrollLeft=a.scrollWidth,!1==e.insertMode&&b==d&&d++,a.setSelectionRange?(a.selectionStart=b,a.selectionEnd=d):a.createTextRange&&(a=a.createTextRange(),a.collapse(!0),a.moveEnd("character",d),a.moveStart("character",b),a.select()))}else return l=c(a).data("_inputmask"),!c(a).is(":visible")&&l&&void 0!=l.caret?(b=l.caret.begin,d=l.caret.end):a.setSelectionRange?(b=a.selectionStart,d=a.selectionEnd):document.selection&&document.selection.createRange&&
(a=document.selection.createRange(),b=0-a.duplicate().moveStart("character",-1E5),d=b+a.text.length),b=J(b),d=J(d),{begin:b,end:d}}function Q(a){if(c.isFunction(e.isComplete))return e.isComplete.call(t,a,e);if("*"!=e.repeat){var b=!1,d=F(D());if(r()==d)for(var b=!0,l=0;l<=d;l++){var h=G(l);if(h&&(void 0==a[l]||a[l]==R(l))||!h&&a[l]!=R(l)){b=!1;break}}return b}}function ga(a){a=c._data(a).events;c.each(a,function(a,b){c.each(b,function(a,b){if("inputmask"==b.namespace&&"setvalue"!=b.type){var c=b.handler;
b.handler=function(a){if(this.readOnly||this.disabled)a.preventDefault;else return c.apply(this,arguments)}}})})}function ha(a){function b(a){if(void 0==c.valHooks[a]||!0!=c.valHooks[a].inputmaskpatch){var d=c.valHooks[a]&&c.valHooks[a].get?c.valHooks[a].get:function(a){return a.value},e=c.valHooks[a]&&c.valHooks[a].set?c.valHooks[a].set:function(a,b){a.value=b;return a};c.valHooks[a]={get:function(a){var b=c(a);if(b.data("_inputmask")){if(b.data("_inputmask").opts.autoUnmask)return b.inputmask("unmaskedvalue");
a=d(a);b=(b=b.data("_inputmask").maskset._buffer)?b.join(""):"";return a!=b?a:""}return d(a)},set:function(a,b){var d=c(a),l=e(a,b);d.data("_inputmask")&&d.triggerHandler("setvalue.inputmask");return l},inputmaskpatch:!0}}}var d;Object.getOwnPropertyDescriptor&&(d=Object.getOwnPropertyDescriptor(a,"value"));if(d&&d.get){if(!a._valueGet){var e=d.get,l=d.set;a._valueGet=function(){return z?e.call(this).split("").reverse().join(""):e.call(this)};a._valueSet=function(a){l.call(this,z?a.split("").reverse().join(""):
a)};Object.defineProperty(a,"value",{get:function(){var a=c(this),b=c(this).data("_inputmask"),d=b.maskset;return b&&b.opts.autoUnmask?a.inputmask("unmaskedvalue"):e.call(this)!=d._buffer.join("")?e.call(this):""},set:function(a){l.call(this,a);c(this).triggerHandler("setvalue.inputmask")}})}}else document.__lookupGetter__&&a.__lookupGetter__("value")?a._valueGet||(e=a.__lookupGetter__("value"),l=a.__lookupSetter__("value"),a._valueGet=function(){return z?e.call(this).split("").reverse().join(""):
e.call(this)},a._valueSet=function(a){l.call(this,z?a.split("").reverse().join(""):a)},a.__defineGetter__("value",function(){var a=c(this),b=c(this).data("_inputmask"),d=b.maskset;return b&&b.opts.autoUnmask?a.inputmask("unmaskedvalue"):e.call(this)!=d._buffer.join("")?e.call(this):""}),a.__defineSetter__("value",function(a){l.call(this,a);c(this).triggerHandler("setvalue.inputmask")})):(a._valueGet||(a._valueGet=function(){return z?this.value.split("").reverse().join(""):this.value},a._valueSet=
function(a){this.value=z?a.split("").reverse().join(""):a}),b(a.type))}function aa(b,c,d){if(e.numericInput||z){switch(c){case e.keyCode.BACKSPACE:c=e.keyCode.DELETE;break;case e.keyCode.DELETE:c=e.keyCode.BACKSPACE}z&&(b=d.end,d.end=d.begin,d.begin=b)}d.begin==d.end?(b=c==e.keyCode.BACKSPACE?d.begin-1:d.begin,e.isNumeric&&""!=e.radixPoint&&l()[b]==e.radixPoint&&(d.begin=l().length-1==b?d.begin:c==e.keyCode.BACKSPACE?b:A(b),d.end=d.begin),c==e.keyCode.BACKSPACE?d.begin=F(d.begin):c==e.keyCode.DELETE&&
d.end++):1!=d.end-d.begin||e.insertMode||c==e.keyCode.BACKSPACE&&d.begin--;b=d.begin;var f=d.end;for(c=A(b-1);b<f;b++)delete a.validPositions[b];b=f;for(f=D();b<f;b++){var g=a.validPositions[b],p=a.validPositions[c];void 0!=g&&void 0==p&&(v(c).def==g.match.def&&!1!==U(c,g.input,!0)&&delete a.validPositions[b],c=A(c))}h(!0);c=A(-1);r()<c?a.p=c:a.p=d.begin}function X(b){Y=!1;var d=this,h=c(d),f=b.keyCode,p=w(d);f==e.keyCode.BACKSPACE||f==e.keyCode.DELETE||g&&127==f||b.ctrlKey&&88==f?(b.preventDefault(),
88==f&&(K=l().join("")),aa(d,f,p),C(d,l(),a.p),d._valueGet()==y().join("")&&h.trigger("cleared"),e.showTooltip&&h.prop("title",a.mask)):f==e.keyCode.END||f==e.keyCode.PAGE_DOWN?setTimeout(function(){var a=A(r());e.insertMode||a!=D()||b.shiftKey||a--;w(d,b.shiftKey?p.begin:a,a)},0):f==e.keyCode.HOME&&!b.shiftKey||f==e.keyCode.PAGE_UP?w(d,0,b.shiftKey?p.begin:0):f==e.keyCode.ESCAPE||90==f&&b.ctrlKey?(N(d,!0,!1,K.split("")),h.click()):f!=e.keyCode.INSERT||b.shiftKey||b.ctrlKey?!1!=e.insertMode||b.shiftKey||
(f==e.keyCode.RIGHT?setTimeout(function(){var a=w(d);w(d,a.begin)},0):f==e.keyCode.LEFT&&setTimeout(function(){var a=w(d);w(d,a.begin-1)},0)):(e.insertMode=!e.insertMode,w(d,e.insertMode||p.begin!=D()?p.begin:p.begin-1));var h=w(d),n=e.onKeyDown.call(this,b,l(),e);n&&!0===n.refreshFromBuffer&&(a.validPositions={},s(0,l().length),w(d,h.begin,h.end));ba=-1!=c.inArray(f,e.ignorables)}function V(b,d,f,g,p,n){if(void 0==f&&Y)return!1;Y=!0;var k=c(this);b=b||window.event;f=d?f:b.which||b.charCode||b.keyCode;
if(!(!0===d||b.ctrlKey&&b.altKey)&&(b.ctrlKey||b.metaKey||ba))return!0;if(f){!0!==d&&46==f&&!1==b.shiftKey&&","==e.radixPoint&&(f=44);var s,E;f=String.fromCharCode(f);d?(n=p?n:r()+1,s={begin:n,end:n}):s=w(this);if(n=z?1<s.begin-s.end||1==s.begin-s.end&&e.insertMode:1<s.end-s.begin||1==s.end-s.begin&&e.insertMode)a.undoPositions=c.extend(!0,{},a.validPositions),aa(this,e.keyCode.DELETE,s),e.insertMode||(e.insertMode=!e.insertMode,S(s.begin,void 0,p),e.insertMode=!e.insertMode),n=!e.multi;var v=l().join("").indexOf(e.radixPoint);
e.isNumeric&&!0!==d&&-1!=v&&(e.greedy&&s.begin<=v?(s.begin=F(s.begin),s.end=s.begin):f==e.radixPoint&&(s.begin=v,s.end=s.begin));a.writeOutBuffer=!0;s=s.begin;var m=U(s,f,p);!1!==m&&(!0!==m&&(s=void 0!=m.pos?m.pos:s,f=void 0!=m.c?m.c:f),h(!0),E=A(s),a.p=E);if(!1!==g){var t=this;setTimeout(function(){e.onKeyValidation.call(t,m,e)},0);if(a.writeOutBuffer&&!1!==m){var D=l();g=d?void 0:e.numericInput?s>v?F(E):f==e.radixPoint?E-1:F(E-1):E;C(this,D,g);!0!==d&&setTimeout(function(){!0===Q(D)&&k.trigger("complete");
Z=!0;k.trigger("input")},0)}else n&&(a.buffer=void 0,a.validPositions=a.undoPositions)}else n&&(a.buffer=void 0,a.validPositions=a.undoPositions);e.showTooltip&&k.prop("title",a.mask);b&&(b.preventDefault?b.preventDefault():b.returnValue=!1)}}function ca(b){var d=c(this),f=b.keyCode,g=l();(b=e.onKeyUp.call(this,b,g,e))&&!0===b.refreshFromBuffer&&(a.validPositions={},s(0,l().length));f==e.keyCode.TAB&&e.showMaskOnFocus&&(d.hasClass("focus.inputmask")&&0==this._valueGet().length?(h(),g=l(),C(this,g),
w(this,0),K=l().join("")):(C(this,g),g.join("")==y().join("")&&-1!=c.inArray(e.radixPoint,g)?(w(this,J(0)),d.click()):w(this,J(0),J(D()))))}function da(a){if(!0===Z&&"input"==a.type)return Z=!1,!0;var b=this,d=c(b);if("propertychange"==a.type&&b._valueGet().length<=D())return!0;setTimeout(function(){var a=c.isFunction(e.onBeforePaste)?e.onBeforePaste.call(b,b._valueGet(),e):b._valueGet();N(b,!1,!1,a.split(""),!0);C(b,l());!0===Q(l())&&d.trigger("complete");d.click()},0)}function ia(a){var b=c(this),
d=w(this),h=this._valueGet(),h=h.replace(RegExp("("+$(y().join(""))+")*"),"");d.begin>h.length&&(w(this,h.length),d=w(this));1!=l().length-h.length||h.charAt(d.begin)==l()[d.begin]||h.charAt(d.begin+1)==l()[d.begin]||G(d.begin)?(N(this,!1,!1,h.split("")),C(this,l()),!0===Q(l())&&b.trigger("complete"),b.click()):(a.keyCode=e.keyCode.BACKSPACE,X.call(this,a));a.preventDefault()}function ja(b){t=c(b);if(t.is(":input")){t.data("_inputmask",{maskset:a,opts:e,isRTL:!1});e.showTooltip&&t.prop("title",a.mask);
ha(b);e.numericInput&&(e.isNumeric=e.numericInput);("rtl"==b.dir||e.numericInput&&e.rightAlignNumerics||e.isNumeric&&e.rightAlignNumerics)&&t.css("text-align","right");if("rtl"==b.dir||e.numericInput){b.dir="ltr";t.removeAttr("dir");var d=t.data("_inputmask");d.isRTL=!0;t.data("_inputmask",d);z=!0}t.unbind(".inputmask");t.removeClass("focus.inputmask");t.closest("form").bind("submit",function(){K!=l().join("")&&t.change()}).bind("reset",function(){setTimeout(function(){t.trigger("setvalue")},0)});
t.bind("mouseenter.inputmask",function(){!c(this).hasClass("focus.inputmask")&&e.showMaskOnHover&&this._valueGet()!=l().join("")&&C(this,l())}).bind("blur.inputmask",function(){var a=c(this),b=this._valueGet(),d=l();a.removeClass("focus.inputmask");K!=l().join("")&&a.change();e.clearMaskOnLostFocus&&""!=b&&(b==y().join("")?this._valueSet(""):W(this));!1===Q(d)&&(a.trigger("incomplete"),e.clearIncomplete&&(h(),e.clearMaskOnLostFocus?this._valueSet(""):(d=y().slice(),C(this,d))))}).bind("focus.inputmask",
function(){var a=c(this),b=this._valueGet();e.showMaskOnFocus&&!a.hasClass("focus.inputmask")&&(!e.showMaskOnHover||e.showMaskOnHover&&""==b)&&this._valueGet()!=l().join("")&&C(this,l(),A(r()));a.addClass("focus.inputmask");K=l().join("")}).bind("mouseleave.inputmask",function(){var a=c(this);e.clearMaskOnLostFocus&&(a.hasClass("focus.inputmask")||this._valueGet()==a.attr("placeholder")||(this._valueGet()==y().join("")||""==this._valueGet()?this._valueSet(""):W(this)))}).bind("click.inputmask",function(){var a=
this;setTimeout(function(){var b=w(a),d=l();if(b.begin==b.end){var b=z?J(b.begin):b.begin,h=r(b),d=e.isNumeric?!1===e.skipRadixDance&&""!=e.radixPoint&&-1!=c.inArray(e.radixPoint,d)?e.numericInput?A(c.inArray(e.radixPoint,d)):c.inArray(e.radixPoint,d):A(h):A(h);b<d?G(b)?w(a,b):w(a,A(b)):w(a,d)}},0)}).bind("dblclick.inputmask",function(){var a=this;setTimeout(function(){w(a,0,A(r()))},0)}).bind(P+".inputmask dragdrop.inputmask drop.inputmask",da).bind("setvalue.inputmask",function(){N(this,!0);K=l().join("");
this._valueGet()==y().join("")&&this._valueSet("")}).bind("complete.inputmask",e.oncomplete).bind("incomplete.inputmask",e.onincomplete).bind("cleared.inputmask",e.oncleared);t.bind("keydown.inputmask",X).bind("keypress.inputmask",V).bind("keyup.inputmask",ca);if(k||x||m||O)if(t.attr("autocomplete","off").attr("autocorrect","off").attr("autocapitalize","off").attr("spellcheck",!1),x||O)t.unbind("keydown.inputmask",X).unbind("keypress.inputmask",V).unbind("keyup.inputmask",ca),"input"==P&&t.unbind(P+
".inputmask"),t.bind("input.inputmask",ia);f&&t.bind("input.inputmask",da);d=c.isFunction(e.onBeforeMask)?e.onBeforeMask.call(b,b._valueGet(),e):b._valueGet();N(b,!0,!1,d.split(""),!0);K=l().join("");var g;try{g=document.activeElement}catch(p){}g===b?(t.addClass("focus.inputmask"),w(b,A(r()))):e.clearMaskOnLostFocus?l().join("")==y().join("")?b._valueSet(""):W(b):C(b,l());ga(b)}}var z=!1,K=l().join(""),t,Y=!1,Z=!1,ba=!1,I;if(void 0!=b)switch(b.action){case "isComplete":return t=c(b.el),Q(b.buffer);
case "unmaskedvalue":return t=b.$input,z=b.$input.data("_inputmask").isRTL,fa(b.$input,b.skipDatepickerCheck);case "mask":ja(b.el);break;case "format":return t=c({}),t.data("_inputmask",{maskset:a,opts:e,isRTL:e.numericInput}),e.numericInput&&(e.isNumeric=e.numericInput,z=!0),b=b.value.split(""),N(t,!1,!1,z?b.reverse():b,!0),z?l().reverse().join(""):l().join("");case "isValid":return t=c({}),t.data("_inputmask",{maskset:a,opts:e,isRTL:e.numericInput}),e.numericInput&&(e.isNumeric=e.numericInput,z=
!0),b=b.value.split(""),N(t,!1,!0,z?b.reverse():b),Q(l())}},r=function(a,b,d){function h(b,e,g){b=b.jquery&&0<b.length?b[0]:b;if("number"==typeof e){e=f(e);g=f(g);g="number"==typeof g?g:e;if(b!=a){var n=c(b).data("_inputmask")||{};n.caret={begin:e,end:g};c(b).data("_inputmask",n)}c(b).is(":visible")&&(b.scrollLeft=b.scrollWidth,!1==d.insertMode&&e==g&&g++,b.setSelectionRange?(b.selectionStart=e,b.selectionEnd=g):b.createTextRange&&(b=b.createTextRange(),b.collapse(!0),b.moveEnd("character",g),b.moveStart("character",
e),b.select()))}else return c(b).is(":visible")||void 0==c(b).data("_inputmask").caret?b.setSelectionRange?(e=b.selectionStart,g=b.selectionEnd):document.selection&&document.selection.createRange&&(b=document.selection.createRange(),e=0-b.duplicate().moveStart("character",-1E5),g=e+b.text.length):(n=c(b).data("_inputmask"),e=n.caret.begin,g=n.caret.end),e=f(e),g=f(g),{begin:e,end:g}}function f(b){!r||"number"!=typeof b||d.greedy&&""==d.placeholder||(b=a.value.length-b);return b}function g(b,e){if("multiMaskScope"!=
b){var f=-1,r=-1,v=-1;c.each(e,function(a,b){var d=c(b).data("_inputmask").maskset,e=-1,l=0,g=h(b).begin,k;for(k in d.validPositions)d=parseInt(k),d>e&&(e=d),l++;if(l>f||l==f&&r>g&&v>e||l==f&&r==g&&v<e)f=l,r=g,m=a,v=e});c.isFunction(d.determineActiveMasksetIndex)&&(m=d.determineActiveMasksetIndex.call(k,b,e));var L=k.data("_inputmask-multi")||{activeMasksetIndex:0,elmasks:e};L.activeMasksetIndex=m;k.data("_inputmask-multi",L)}-1==["focus"].indexOf(b)&&a.value!=e[m]._valueGet()&&(L=""==c(e[m]).val()?
e[m]._valueGet():c(e[m]).val(),a.value=L);-1==["blur","focus"].indexOf(b)&&c(e[m]).hasClass("focus.inputmask")&&(L=h(e[m]),h(a,L.begin,L.end))}d.multi=!0;var k=c(a),r="rtl"==a.dir||d.numericInput,m=0,y=c.map(b,function(a,b){var e='<input type="text" ';k.attr("value")&&(e+='value="'+k.attr("value")+'" ');k.attr("dir")&&(e+='dir="'+k.attr("dir")+'" ');e=c(e+"/>")[0];v(c.extend(!0,{},a),d,{action:"mask",el:e});return e});k.data("_inputmask-multi",{activeMasksetIndex:0,elmasks:y});("rtl"==a.dir||d.numericInput&&
d.rightAlignNumerics||d.isNumeric&&d.rightAlignNumerics)&&k.css("text-align","right");a.dir="ltr";k.removeAttr("dir");""!=k.attr("value")&&g("init",y);k.bind("mouseenter blur focus mouseleave click dblclick keydown keypress keypress",function(b){var e=h(a),k,r=!0;if("keydown"==b.type){k=b.keyCode;if(k==d.keyCode.DOWN&&m<y.length-1)return m++,g("multiMaskScope",y),!1;if(k==d.keyCode.UP&&0<m)return m--,g("multiMaskScope",y),!1;if(b.ctrlKey||b.shiftKey||b.altKey)return!0}else if("keypress"==b.type&&
(b.ctrlKey||b.shiftKey||b.altKey))return!0;c.each(y,function(a,g){if("keydown"==b.type){k=b.keyCode;if(k==d.keyCode.BACKSPACE&&g._valueGet().length<e.begin)return;if(k==d.keyCode.TAB)r=!1;else{if(k==d.keyCode.RIGHT){h(g,e.begin+1,e.end+1);r=!1;return}if(k==d.keyCode.LEFT){h(g,e.begin-1,e.end-1);r=!1;return}}}if(-1!=["click"].indexOf(b.type)&&(h(g,f(e.begin),f(e.end)),e.begin!=e.end)){r=!1;return}-1!=["keydown"].indexOf(b.type)&&e.begin!=e.end&&h(g,e.begin,e.end);c(g).triggerHandler(b)});r&&setTimeout(function(){g(b.type,
y)},0)});k.bind(P+" dragdrop drop setvalue",function(b){h(a);setTimeout(function(){c.each(y,function(d,e){e._valueSet(a.value);c(e).triggerHandler(b)});setTimeout(function(){g(b.type,y)},0)},0)});(function(a){if(void 0==c.valHooks[a]||!0!=c.valHooks[a].inputmaskmultipatch){var b=c.valHooks[a]&&c.valHooks[a].get?c.valHooks[a].get:function(a){return a.value},d=c.valHooks[a]&&c.valHooks[a].set?c.valHooks[a].set:function(a,b){a.value=b;return a};c.valHooks[a]={get:function(a){var d=c(a);return d.data("_inputmask-multi")?
(a=d.data("_inputmask-multi"),b(a.elmasks[a.activeMasksetIndex])):b(a)},set:function(a,b){var e=c(a),h=d(a,b);e.data("_inputmask-multi")&&e.triggerHandler("setvalue");return h},inputmaskmultipatch:!0}}})(a.type)};c.inputmask={defaults:{placeholder:"_",optionalmarker:{start:"[",end:"]"},quantifiermarker:{start:"{",end:"}"},groupmarker:{start:"(",end:")"},alternatormarker:"|",escapeChar:"\\",mask:null,oncomplete:c.noop,onincomplete:c.noop,oncleared:c.noop,repeat:0,greedy:!0,autoUnmask:!1,clearMaskOnLostFocus:!0,
insertMode:!0,clearIncomplete:!1,aliases:{},onKeyUp:c.noop,onKeyDown:c.noop,onBeforeMask:void 0,onBeforePaste:void 0,onUnMask:void 0,showMaskOnFocus:!0,showMaskOnHover:!0,onKeyValidation:c.noop,skipOptionalPartCharacter:" ",showTooltip:!1,numericInput:!1,isNumeric:!1,radixPoint:"",skipRadixDance:!1,rightAlignNumerics:!0,definitions:{9:{validator:"[0-9]",cardinality:1,definitionSymbol:"*"},a:{validator:"[A-Za-z\u0410-\u044f\u0401\u0451]",cardinality:1,definitionSymbol:"*"},"*":{validator:"[A-Za-z\u0410-\u044f\u0401\u04510-9]",
cardinality:1}},keyCode:{ALT:18,BACKSPACE:8,CAPS_LOCK:20,COMMA:188,COMMAND:91,COMMAND_LEFT:91,COMMAND_RIGHT:93,CONTROL:17,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,INSERT:45,LEFT:37,MENU:93,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SHIFT:16,SPACE:32,TAB:9,UP:38,WINDOWS:91},ignorables:[8,9,13,19,27,33,34,35,36,37,38,39,40,45,46,93,112,113,114,115,116,117,118,119,120,121,122,123],
isComplete:void 0,multi:!1,nojumps:!1,nojumpsThreshold:0,determineActiveMasksetIndex:void 0},masksCache:{},escapeRegex:function(a){return a.replace(RegExp("(\\/|\\.|\\*|\\+|\\?|\\||\\(|\\)|\\[|\\]|\\{|\\}|\\\\)","gim"),"\\$1")},format:function(a,b){var f=c.extend(!0,{},c.inputmask.defaults,b);h(f.alias,b,f);return v(d(f),f,{action:"format",value:a})},isValid:function(a,b){var f=c.extend(!0,{},c.inputmask.defaults,b);h(f.alias,b,f);return v(d(f),f,{action:"isValid",value:a})}};c.fn.inputmask=function(a,
b){function f(a,b){var d=c(a),e;for(e in b){var h=d.data("inputmask-"+e.toLowerCase());void 0!=h&&(b[e]=h)}return b}var g=c.extend(!0,{},c.inputmask.defaults,b),k;if("string"===typeof a)switch(a){case "mask":return h(g.alias,b,g),k=d(g),0==k.length?this:this.each(function(){c.isArray(k)?r(this,k,f(this,g)):v(c.extend(!0,{},k),f(this,g),{action:"mask",el:this})});case "unmaskedvalue":var m=c(this);return m.data("_inputmask")?(k=m.data("_inputmask").maskset,g=m.data("_inputmask").opts,v(k,g,{action:"unmaskedvalue",
$input:m})):m.val();case "remove":return this.each(function(){var a=c(this);if(a.data("_inputmask")){k=a.data("_inputmask").maskset;g=a.data("_inputmask").opts;this._valueSet(v(k,g,{action:"unmaskedvalue",$input:a,skipDatepickerCheck:!0}));a.removeData("_inputmask");a.unbind(".inputmask");a.removeClass("focus.inputmask");var b;Object.getOwnPropertyDescriptor&&(b=Object.getOwnPropertyDescriptor(this,"value"));b&&b.get?this._valueGet&&Object.defineProperty(this,"value",{get:this._valueGet,set:this._valueSet}):
document.__lookupGetter__&&this.__lookupGetter__("value")&&this._valueGet&&(this.__defineGetter__("value",this._valueGet),this.__defineSetter__("value",this._valueSet));try{delete this._valueGet,delete this._valueSet}catch(d){this._valueSet=this._valueGet=void 0}}});case "getemptymask":return this.data("_inputmask")?(k=this.data("_inputmask").maskset,k._buffer.join("")):"";case "hasMaskedValue":return this.data("_inputmask")?!this.data("_inputmask").opts.autoUnmask:!1;case "isComplete":return this.data("_inputmask")?
(k=this.data("_inputmask").maskset,g=this.data("_inputmask").opts,v(k,g,{action:"isComplete",buffer:this[0]._valueGet().split(""),el:this})):!0;case "getmetadata":if(this.data("_inputmask"))return k=this.data("_inputmask").maskset,k.metadata;break;default:return h(a,b,g)||(g.mask=a),k=d(g),void 0==k?this:this.each(function(){c.isArray(k)?r(this,k,f(this,g)):v(c.extend(!0,{},k),f(this,g),{action:"mask",el:this})})}else{if("object"==typeof a)return g=c.extend(!0,{},c.inputmask.defaults,a),h(g.alias,
a,g),k=d(g),void 0==k?this:this.each(function(){c.isArray(k)?r(this,k,f(this,g)):v(c.extend(!0,{},k),f(this,g),{action:"mask",el:this})});if(void 0==a)return this.each(function(){var a=c(this).attr("data-inputmask");if(a&&""!=a)try{var a=a.replace(RegExp("'","g"),'"'),d=c.parseJSON("{"+a+"}");c.extend(!0,d,b);g=c.extend(!0,{},c.inputmask.defaults,d);h(g.alias,d,g);g.alias=void 0;c(this).inputmask(g)}catch(f){}})}}}})(jQuery);
(function(c){c.extend(c.inputmask.defaults.definitions,{A:{validator:"[A-Za-z]",cardinality:1,casing:"upper"},"#":{validator:"[A-Za-z\u0410-\u044f\u0401\u04510-9]",cardinality:1,casing:"upper"}});c.extend(c.inputmask.defaults.aliases,{url:{mask:"ir",placeholder:"",separator:"",defaultPrefix:"http://",regex:{urlpre1:/[fh]/,urlpre2:/(ft|ht)/,urlpre3:/(ftp|htt)/,urlpre4:/(ftp:|http|ftps)/,urlpre5:/(ftp:\/|ftps:|http:|https)/,urlpre6:/(ftp:\/\/|ftps:\/|http:\/|https:)/,urlpre7:/(ftp:\/\/|ftps:\/\/|http:\/\/|https:\/)/,
urlpre8:/(ftp:\/\/|ftps:\/\/|http:\/\/|https:\/\/)/},definitions:{i:{validator:function(a,c,d,f,b){return!0},cardinality:8,prevalidator:function(){for(var a=[],c=0;8>c;c++)a[c]=function(){var a=c;return{validator:function(c,b,h,k,m){if(m.regex["urlpre"+(a+1)]){var x=c;0<a+1-c.length&&(x=b.join("").substring(0,a+1-c.length)+""+x);c=m.regex["urlpre"+(a+1)].test(x);if(!k&&!c){h-=a;for(k=0;k<m.defaultPrefix.length;k++)b[h]=m.defaultPrefix[k],h++;for(k=0;k<x.length-1;k++)b[h]=x[k],h++;return{pos:h}}return c}return!1},
cardinality:a}}();return a}()},r:{validator:".",cardinality:50}},insertMode:!1,autoUnmask:!1},ip:{mask:"i[i[i]].i[i[i]].i[i[i]].i[i[i]]",definitions:{i:{validator:function(a,c,d,f,b){-1<d-1&&"."!=c[d-1]?(a=c[d-1]+a,a=-1<d-2&&"."!=c[d-2]?c[d-2]+a:"0"+a):a="00"+a;return/25[0-5]|2[0-4][0-9]|[01][0-9][0-9]/.test(a)},cardinality:1}}},email:{mask:"*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}.*{2,6}[.*{1,2}]",greedy:!1}})})(jQuery);
(function(c){c.extend(c.inputmask.defaults.definitions,{h:{validator:"[01][0-9]|2[0-3]",cardinality:2,prevalidator:[{validator:"[0-2]",cardinality:1}]},s:{validator:"[0-5][0-9]",cardinality:2,prevalidator:[{validator:"[0-5]",cardinality:1}]},d:{validator:"0[1-9]|[12][0-9]|3[01]",cardinality:2,prevalidator:[{validator:"[0-3]",cardinality:1}]},m:{validator:"0[1-9]|1[012]",cardinality:2,prevalidator:[{validator:"[01]",cardinality:1}]},y:{validator:"(19|20)\\d{2}",cardinality:4,prevalidator:[{validator:"[12]",
cardinality:1},{validator:"(19|20)",cardinality:2},{validator:"(19|20)\\d",cardinality:3}]}});c.extend(c.inputmask.defaults.aliases,{"dd/mm/yyyy":{mask:"1/2/y",placeholder:"dd/mm/yyyy",regex:{val1pre:/[0-3]/,val1:/0[1-9]|[12][0-9]|3[01]/,val2pre:function(a){a=c.inputmask.escapeRegex.call(this,a);return RegExp("((0[1-9]|[12][0-9]|3[01])"+a+"[01])")},val2:function(a){a=c.inputmask.escapeRegex.call(this,a);return RegExp("((0[1-9]|[12][0-9])"+a+"(0[1-9]|1[012]))|(30"+a+"(0[13-9]|1[012]))|(31"+a+"(0[13578]|1[02]))")}},
leapday:"29/02/",separator:"/",yearrange:{minyear:1900,maxyear:2099},isInYearRange:function(a,c,d){if(isNaN(a))return!1;var f=parseInt(a.concat(c.toString().slice(a.length)));a=parseInt(a.concat(d.toString().slice(a.length)));return(isNaN(f)?!1:c<=f&&f<=d)||(isNaN(a)?!1:c<=a&&a<=d)},determinebaseyear:function(a,c,d){var f=(new Date).getFullYear();if(a>f)return a;if(c<f){for(var f=c.toString().slice(0,2),b=c.toString().slice(2,4);c<f+d;)f--;c=f+b;return a>c?a:c}return f},onKeyUp:function(a,h,d){h=
c(this);a.ctrlKey&&a.keyCode==d.keyCode.RIGHT&&(a=new Date,h.val(a.getDate().toString()+(a.getMonth()+1).toString()+a.getFullYear().toString()))},definitions:{1:{validator:function(a,c,d,f,b){var g=b.regex.val1.test(a);return f||g||a.charAt(1)!=b.separator&&-1=="-./".indexOf(a.charAt(1))||!(g=b.regex.val1.test("0"+a.charAt(0)))?g:(c[d-1]="0",{refreshFromBuffer:{start:d-1,end:d},pos:d,c:a.charAt(0)})},cardinality:2,prevalidator:[{validator:function(a,c,d,f,b){isNaN(c[d+1])||(a+=c[d+1]);var g=1==a.length?
b.regex.val1pre.test(a):b.regex.val1.test(a);return f||g||!(g=b.regex.val1.test("0"+a))?g:(c[d]="0",d++,{pos:d})},cardinality:1}]},2:{validator:function(a,c,d,f,b){var g=b.mask.indexOf("2")==b.mask.length-1?c.join("").substr(5,3):c.join("").substr(0,3);-1!=g.indexOf(b.placeholder[0])&&(g="01"+b.separator);var k=b.regex.val2(b.separator).test(g+a);if(!(f||k||a.charAt(1)!=b.separator&&-1=="-./".indexOf(a.charAt(1)))&&(k=b.regex.val2(b.separator).test(g+"0"+a.charAt(0))))return c[d-1]="0",{refreshFromBuffer:{start:d-
1,end:d},pos:d,c:a.charAt(0)};if(b.mask.indexOf("2")==b.mask.length-1&&k){if(c.join("").substr(4,4)+a!=b.leapday)return!0;a=parseInt(c.join("").substr(0,4),10);return 0===a%4?0===a%100?0===a%400?!0:!1:!0:!1}return k},cardinality:2,prevalidator:[{validator:function(a,c,d,f,b){isNaN(c[d+1])||(a+=c[d+1]);var g=b.mask.indexOf("2")==b.mask.length-1?c.join("").substr(5,3):c.join("").substr(0,3);-1!=g.indexOf(b.placeholder[0])&&(g="01"+b.separator);var k=1==a.length?b.regex.val2pre(b.separator).test(g+a):
b.regex.val2(b.separator).test(g+a);return f||k||!(k=b.regex.val2(b.separator).test(g+"0"+a))?k:(c[d]="0",d++,{pos:d})},cardinality:1}]},y:{validator:function(a,c,d,f,b){if(b.isInYearRange(a,b.yearrange.minyear,b.yearrange.maxyear)){if(c.join("").substr(0,6)!=b.leapday)return!0;a=parseInt(a,10);return 0===a%4?0===a%100?0===a%400?!0:!1:!0:!1}return!1},cardinality:4,prevalidator:[{validator:function(a,c,d,f,b){var g=b.isInYearRange(a,b.yearrange.minyear,b.yearrange.maxyear);if(!f&&!g){f=b.determinebaseyear(b.yearrange.minyear,
b.yearrange.maxyear,a+"0").toString().slice(0,1);if(g=b.isInYearRange(f+a,b.yearrange.minyear,b.yearrange.maxyear))return c[d++]=f[0],{pos:d};f=b.determinebaseyear(b.yearrange.minyear,b.yearrange.maxyear,a+"0").toString().slice(0,2);if(g=b.isInYearRange(f+a,b.yearrange.minyear,b.yearrange.maxyear))return c[d++]=f[0],c[d++]=f[1],{pos:d}}return g},cardinality:1},{validator:function(a,c,d,f,b){var g=b.isInYearRange(a,b.yearrange.minyear,b.yearrange.maxyear);if(!f&&!g){f=b.determinebaseyear(b.yearrange.minyear,
b.yearrange.maxyear,a).toString().slice(0,2);if(g=b.isInYearRange(a[0]+f[1]+a[1],b.yearrange.minyear,b.yearrange.maxyear))return c[d++]=f[1],{pos:d};f=b.determinebaseyear(b.yearrange.minyear,b.yearrange.maxyear,a).toString().slice(0,2);b.isInYearRange(f+a,b.yearrange.minyear,b.yearrange.maxyear)?c.join("").substr(0,6)!=b.leapday?g=!0:(b=parseInt(a,10),g=0===b%4?0===b%100?0===b%400?!0:!1:!0:!1):g=!1;if(g)return c[d-1]=f[0],c[d++]=f[1],c[d++]=a[0],{refreshFromBuffer:{start:d-3,end:d},pos:d}}return g},
cardinality:2},{validator:function(a,c,d,f,b){return b.isInYearRange(a,b.yearrange.minyear,b.yearrange.maxyear)},cardinality:3}]}},insertMode:!1,autoUnmask:!1},"mm/dd/yyyy":{placeholder:"mm/dd/yyyy",alias:"dd/mm/yyyy",regex:{val2pre:function(a){a=c.inputmask.escapeRegex.call(this,a);return RegExp("((0[13-9]|1[012])"+a+"[0-3])|(02"+a+"[0-2])")},val2:function(a){a=c.inputmask.escapeRegex.call(this,a);return RegExp("((0[1-9]|1[012])"+a+"(0[1-9]|[12][0-9]))|((0[13-9]|1[012])"+a+"30)|((0[13578]|1[02])"+
a+"31)")},val1pre:/[01]/,val1:/0[1-9]|1[012]/},leapday:"02/29/",onKeyUp:function(a,h,d){h=c(this);a.ctrlKey&&a.keyCode==d.keyCode.RIGHT&&(a=new Date,h.val((a.getMonth()+1).toString()+a.getDate().toString()+a.getFullYear().toString()))}},"yyyy/mm/dd":{mask:"y/1/2",placeholder:"yyyy/mm/dd",alias:"mm/dd/yyyy",leapday:"/02/29",onKeyUp:function(a,h,d){h=c(this);a.ctrlKey&&a.keyCode==d.keyCode.RIGHT&&(a=new Date,h.val(a.getFullYear().toString()+(a.getMonth()+1).toString()+a.getDate().toString()))}},"dd.mm.yyyy":{mask:"1.2.y",
placeholder:"dd.mm.yyyy",leapday:"29.02.",separator:".",alias:"dd/mm/yyyy"},"dd-mm-yyyy":{mask:"1-2-y",placeholder:"dd-mm-yyyy",leapday:"29-02-",separator:"-",alias:"dd/mm/yyyy"},"mm.dd.yyyy":{mask:"1.2.y",placeholder:"mm.dd.yyyy",leapday:"02.29.",separator:".",alias:"mm/dd/yyyy"},"mm-dd-yyyy":{mask:"1-2-y",placeholder:"mm-dd-yyyy",leapday:"02-29-",separator:"-",alias:"mm/dd/yyyy"},"yyyy.mm.dd":{mask:"y.1.2",placeholder:"yyyy.mm.dd",leapday:".02.29",separator:".",alias:"yyyy/mm/dd"},"yyyy-mm-dd":{mask:"y-1-2",
placeholder:"yyyy-mm-dd",leapday:"-02-29",separator:"-",alias:"yyyy/mm/dd"},datetime:{mask:"1/2/y h:s",placeholder:"dd/mm/yyyy hh:mm",alias:"dd/mm/yyyy",regex:{hrspre:/[012]/,hrs24:/2[0-4]|1[3-9]/,hrs:/[01][0-9]|2[0-4]/,ampm:/^[a|p|A|P][m|M]/},timeseparator:":",hourFormat:"24",definitions:{h:{validator:function(a,c,d,f,b){if("24"==b.hourFormat&&24==parseInt(a,10))return c[d-1]="0",c[d]="0",{refreshFromBuffer:{start:d-1,end:d},c:"0"};var g=b.regex.hrs.test(a);return f||g||a.charAt(1)!=b.timeseparator&&
-1=="-.:".indexOf(a.charAt(1))||!(g=b.regex.hrs.test("0"+a.charAt(0)))?g&&"24"!==b.hourFormat&&b.regex.hrs24.test(a)?(a=parseInt(a,10),c[d+5]=24==a?"a":"p",c[d+6]="m",a-=12,10>a?(c[d]=a.toString(),c[d-1]="0"):(c[d]=a.toString().charAt(1),c[d-1]=a.toString().charAt(0)),{refreshFromBuffer:{start:d-1,end:d+6},c:c[d]}):g:(c[d-1]="0",c[d]=a.charAt(0),d++,{refreshFromBuffer:{start:d-2,end:d},pos:d,c:b.timeseparator})},cardinality:2,prevalidator:[{validator:function(a,c,d,f,b){var g=b.regex.hrspre.test(a);
return f||g||!(g=b.regex.hrs.test("0"+a))?g:(c[d]="0",d++,{pos:d})},cardinality:1}]},t:{validator:function(a,c,d,f,b){return b.regex.ampm.test(a+"m")},casing:"lower",cardinality:1}},insertMode:!1,autoUnmask:!1},datetime12:{mask:"1/2/y h:s t\\m",placeholder:"dd/mm/yyyy hh:mm xm",alias:"datetime",hourFormat:"12"},"hh:mm t":{mask:"h:s t\\m",placeholder:"hh:mm xm",alias:"datetime",hourFormat:"12"},"h:s t":{mask:"h:s t\\m",placeholder:"hh:mm xm",alias:"datetime",hourFormat:"12"},"hh:mm:ss":{mask:"h:s:s",
autoUnmask:!1},"hh:mm":{mask:"h:s",autoUnmask:!1},date:{alias:"dd/mm/yyyy"},"mm/yyyy":{mask:"1/y",placeholder:"mm/yyyy",leapday:"donotuse",separator:"/",alias:"mm/dd/yyyy"}})})(jQuery);
(function(c){c.extend(c.inputmask.defaults.aliases,{decimal:{mask:"~",placeholder:"",repeat:"*",greedy:!1,numericInput:!1,isNumeric:!0,digits:"*",groupSeparator:"",radixPoint:".",groupSize:3,autoGroup:!1,allowPlus:!0,allowMinus:!0,integerDigits:"*",defaultValue:"",prefix:"",suffix:"",postFormat:function(a,h,d,f){if(""==f.groupSeparator)return h;var b=a.slice();c.inArray(f.radixPoint,a);d||b.splice(h,0,"?");b=b.join("");if(f.autoGroup||d&&-1!=b.indexOf(f.groupSeparator)){for(var g=c.inputmask.escapeRegex.call(this,
f.groupSeparator),b=b.replace(RegExp(g,"g"),""),g=b.split(f.radixPoint),b=g[0],k=RegExp("([-+]?[\\d?]+)([\\d?]{"+f.groupSize+"})");k.test(b);)b=b.replace(k,"$1"+f.groupSeparator+"$2"),b=b.replace(f.groupSeparator+f.groupSeparator,f.groupSeparator);1<g.length&&(b+=f.radixPoint+g[1])}a.length=b.length;f=0;for(g=b.length;f<g;f++)a[f]=b.charAt(f);b=c.inArray("?",a);d||a.splice(b,1);return d?h:b},regex:{number:function(a){var h=c.inputmask.escapeRegex.call(this,a.radixPoint),d=isNaN(a.digits)?a.digits:
"{0,"+a.digits+"}",f=isNaN(a.integerDigits)?a.integerDigits:"{1,"+a.integerDigits+"}";return RegExp("^"+(a.allowPlus||a.allowMinus?"["+(a.allowPlus?"+":"")+(a.allowMinus?"-":"")+"]?":"")+"\\d"+f+"("+h+"\\d"+d+")?$")}},onKeyDown:function(a,h,d){var f=c(this);if(a.keyCode==d.keyCode.TAB){if(a=c.inArray(d.radixPoint,h),-1!=a){for(var b=f.data("_inputmask").masksets,f=f.data("_inputmask").activeMasksetIndex,g=1;g<=d.digits&&g<d.getMaskLength(b[f]._buffer,d.greedy,d.repeat,h,d);g++)if(void 0==h[a+g]||
""==h[a+g])h[a+g]="0";return{refreshFromBuffer:!0}}}else if(a.keyCode==d.keyCode.DELETE||a.keyCode==d.keyCode.BACKSPACE)return d.postFormat(h,0,!0,d),this._valueSet(h.join("")),{refreshFromBuffer:!0}},definitions:{"~":{validator:function(a,h,d,f,b){var g=c.extend({},b,{digits:f?"*":b.digits});if(""==a)return!1;if(!f&&1>=d&&"0"===h[0]&&/[\d-]/.test(a)&&1==h.join("").length)return h[0]="",{pos:0};var k=f?h.slice(0,d):h.slice();k.splice(d,0,a);var k=k.join(""),m=c.inputmask.escapeRegex.call(this,b.groupSeparator),
k=k.replace(RegExp(m,"g"),"");f&&k.lastIndexOf(b.radixPoint)==k.length-1&&(m=c.inputmask.escapeRegex.call(this,b.radixPoint),k=k.replace(RegExp(m,"g"),""));if(!f&&""==k)return!1;m=b.regex.number(g).test(k);if(!m&&(k+="0",m=b.regex.number(g).test(k),!m)){m=k.lastIndexOf(b.groupSeparator);for(m=k.length-m;3>=m;m++)k+="0";m=b.regex.number(g).test(k);if(!m&&!f&&a==b.radixPoint&&(m=b.regex.number(g).test("0"+k+"0")))return h[d]="0",d++,{pos:d}}return!1==m||f||a==b.radixPoint?m:{pos:b.postFormat(h,d,"-"==
a||"+"==a?!0:!1,b),refreshFromBuffer:!0}},cardinality:1,prevalidator:null}},insertMode:!0,autoUnmask:!1},integer:{regex:{number:function(a){var h=c.inputmask.escapeRegex.call(this,a.groupSeparator);return RegExp("^"+(a.allowPlus||a.allowMinus?"["+(a.allowPlus?"+":"")+(a.allowMinus?"-":"")+"]?":"")+"(\\d+|\\d{1,"+a.groupSize+"}(("+h+"\\d{"+a.groupSize+"})?)+)$")}},alias:"decimal"}})})(jQuery);
(function(c){c.extend(c.inputmask.defaults.aliases,{Regex:{mask:"r",greedy:!1,repeat:"*",regex:null,regexTokens:null,tokenizer:/\[\^?]?(?:[^\\\]]+|\\[\S\s]?)*]?|\\(?:0(?:[0-3][0-7]{0,2}|[4-7][0-7]?)?|[1-9][0-9]*|x[0-9A-Fa-f]{2}|u[0-9A-Fa-f]{4}|c[A-Za-z]|[\S\s]?)|\((?:\?[:=!]?)?|(?:[?*+]|\{[0-9]+(?:,[0-9]*)?\})\??|[^.?*+^${[()|\\]+|./g,quantifierFilter:/[0-9]+[^,]/,isComplete:function(a,c){return RegExp(c.regex).test(a.join(""))},definitions:{r:{validator:function(a,c,d,f,b){function g(a,b){this.matches=
[];this.isGroup=a||!1;this.isQuantifier=b||!1;this.quantifier={min:1,max:1};this.repeaterPart=void 0}function k(){var a=new g,c,d=[];for(b.regexTokens=[];c=b.tokenizer.exec(b.regex);)switch(c=c[0],c.charAt(0)){case "(":d.push(new g(!0));break;case ")":var e=d.pop();0<d.length?d[d.length-1].matches.push(e):a.matches.push(e);break;case "{":case "+":case "*":var f=new g(!1,!0);c=c.replace(/[{}]/g,"");e=c.split(",");c=isNaN(e[0])?e[0]:parseInt(e[0]);e=1==e.length?c:isNaN(e[1])?e[1]:parseInt(e[1]);f.quantifier=
{min:c,max:e};if(0<d.length){var h=d[d.length-1].matches;c=h.pop();c.isGroup||(e=new g(!0),e.matches.push(c),c=e);h.push(c);h.push(f)}else c=a.matches.pop(),c.isGroup||(e=new g(!0),e.matches.push(c),c=e),a.matches.push(c),a.matches.push(f);break;default:0<d.length?d[d.length-1].matches.push(c):a.matches.push(c)}0<a.matches.length&&b.regexTokens.push(a)}function m(a,b){var c=!1;b&&(x+="(",O++);for(var d=0;d<a.matches.length;d++){var f=a.matches[d];if(!0==f.isGroup)c=m(f,!0);else if(!0==f.isQuantifier){var g=
a.matches.indexOf(f),g=a.matches[g-1],h=x;if(isNaN(f.quantifier.max)){for(;f.repeaterPart&&f.repeaterPart!=x&&f.repeaterPart.length>x.length&&!(c=m(g,!0)););(c=c||m(g,!0))&&(f.repeaterPart=x);x=h+f.quantifier.max}else{for(var k=0,S=f.quantifier.max-1;k<S&&!(c=m(g,!0));k++);x=h+"{"+f.quantifier.min+","+f.quantifier.max+"}"}}else if(void 0!=f.matches)for(g=0;g<f.length&&!(c=m(f[g],b));g++);else{if("["==f[0]){c=x;c+=f;for(k=0;k<O;k++)c+=")";c=RegExp("^("+c+")$");c=c.test(P)}else for(g=0,h=f.length;g<
h;g++)if("\\"!=f[g]){c=x;c+=f.substr(0,g+1);c=c.replace(/\|$/,"");for(k=0;k<O;k++)c+=")";c=RegExp("^("+c+")$");if(c=c.test(P))break}x+=f}if(c)break}b&&(x+=")",O--);return c}null==b.regexTokens&&k();f=c.slice();var x="";c=!1;var O=0;f.splice(d,0,a);var P=f.join("");for(a=0;a<b.regexTokens.length&&!(g=b.regexTokens[a],c=m(g,g.isGroup));a++);return c},cardinality:1}}}})})(jQuery);
(function(c){c.extend(c.inputmask.defaults.aliases,{phone:{url:"phone-codes/phone-codes.json",mask:function(a){a.definitions={p:{validator:function(){return!1},cardinality:1},"#":{validator:"[0-9]",cardinality:1}};var h=[];c.ajax({url:a.url,async:!1,dataType:"json",success:function(a){h=a}});h.splice(0,0,"+p(ppp)ppp-pppp");return h},nojumps:!0,nojumpsThreshold:1},phonebe:{url:"phone-codes/phone-be.json",mask:function(a){a.definitions={p:{validator:function(){return!1},cardinality:1},"#":{validator:"[0-9]",
cardinality:1}};var h=[];c.ajax({url:a.url,async:!1,dataType:"json",success:function(a){h=a}});h.splice(0,0,"+32(ppp)ppp-pppp");return h},nojumps:!0,nojumpsThreshold:4}})})(jQuery);

/*!
 * jQuery Input Ip Address Control : v0.1beta (2010/11/09 16:15:43)
 * Copyright (c) 2010 jquery-input-ip-address-control@googlecode.com
 * Licensed under the MIT license and GPL licenses.
 *
 */
eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--){d[e(c)]=k[c]||e(c)}k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c])}}return p}('(l($){Q.1o.1t=l(){E=/\\b(?:(?:25[0-5]|2[0-4][0-9]|[1m]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[1m]?[0-9][0-9]?)\\b/;h E.1a(p.1i())};Q.1o.1S=l(){E=/\\b([A-16-9]{1,4}:){7}([A-16-9]{1,4})\\b/i;h E.1a(p.1i())};$.1X.1h({y:l(u,c){f(p.z==0)h;f(1k u==\'1f\'){c=(1k c==\'1f\')?c:u;h p.1d(l(){f(p.18){p.1s();p.18(u,c)}w f(p.1e){t C=p.1e();C.1L(S);C.1z(\'10\',c);C.1b(\'10\',u);C.1w()}})}w{f(p[0].18){u=p[0].1u;c=p[0].1C}w f(14.12&&14.12.19){t C=14.12.19();u=0-C.1D().1b(\'10\',-1E);c=u+C.1H.z}h{u:u,c:c}}},1B:l(s){s=$.1h({v:4},s);f(s.v==4){s.W=M I(\'[0-9]\',\'g\');s.r=\'R.R.R.R\'}f(s.v==6){s.W=M I(\'[A-16-9]\',\'1x\');s.r=\'x:x:x:x:x:x:x:x\'}s.D=s.r.K(\'\').Y();s.q=s.r.X(M I(s.D,\'g\'),\'\').K(\'\').Y();s.O=s.r.K(s.q).Y();h $(p).1d(l(){t a={k:T,n:T,o:T,d:T};a.d=$(p);f(a.d.m()==\'\'||!J(a.d.m()))a.d.m(s.r);a.d.1j(\'1Z\',(s.v==4?15:1c)).1j(\'1W\',(s.v==4?15:1c));l J(o){h 24("o.21"+s.v+"()")};l P(){a.k=a.d.y();a.o=J(L(a.d.m()))?L(a.d.m()):a.o;a.n=a.d.m().K(\'\')};l 1n(o){t G=o.K(s.q);1p(t j=0;j<G.z;j++){1M((s.O.z-G[j].z)>0)G[j]+=s.D}h G.H(s.q)};l L(o){t E=M I(s.O,\'g\');t 1g=M I(s.D,\'g\');h o.X(E,\'0\').X(1g,\'\')};l 11(e){1R(e.1Q){U 8:f(a.n[a.k.c-1]!=s.q){a.n[a.k.c-1]=s.D;a.d.m(a.n.H("")).m()}a.d.y(a.k.c-1);h B;V;U 13:U 1T:a.d.17();V;U 1P:f(a.n[a.k.c]!=s.q&&a.k.c<s.r.z){a.n[a.k.c]=s.D;a.d.m(a.n.H(""))}f(a.k.c<s.r.z)a.d.y(a.k.c+1);h B;V}h S};a.d.N(\'1O\',l(e){P();f($.1l.1K||$.1l.1V){h 11(e)}}).N(\'1U\',l(e){P();f(e.23||e.22||e.1Y)h S;w f((e.F>=20&&e.F<=1N)||e.F>1J){f(Q.1q(e.F).1y(s.W)){a.n[a.k.c]=Q.1q(e.F);f(!J(L(a.n.H(\'\')))){f((a.k.c==0||a.n[a.k.c-1]==s.q)){1p(t i=a.k.c+1;i<a.k.c+s.O.z;i++){a.n[i]=s.D}}w h B}a.d.m(a.n.H(\'\'));f(a.n[a.k.c+1]==s.q){a.d.y(a.k.c+2)}w{a.d.y(a.k.c+1)}h B}w f(s.q.1v(0)==e.F){t Z=a.d.m().1A(s.q,a.k.c);f(Z==-1)h B;f(a.n[a.k.c-1]==s.q)h B;a.k.c=Z;a.d.y(a.k.c+1);h B}w h B}h 11(e)}).N(\'17\',l(){f(a.d.m()==s.r)h S;t o=L($.1G(a.d.m()));f(J(o))a.d.m(o);w a.d.m(a.o)}).N(\'1s\',l(){1r(l(){P();f(a.d.m()!=s.r)a.d.m(1n(a.o));a.d.y(0)},0)}).N(\'1I\',l(e){a.d.m(\'\');1r(l(){a.d.17()},0)})})}})})(1F);',62,130,'||||||||||ctx||end|input||if||return|||cursor|function|val|buffer|ip|this|separator|label||var|begin||else|____|caret|length||false|range|place|rgx|which|part|join|RegExp|isIp|split|unmask|new|bind|partplace|loadCtx|String|___|true|null|case|break|rgxcase|replace|pop|pos|character|entryNoCharacter|selection||document||F0|blur|setSelectionRange|createRange|test|moveStart|39|each|createTextRange|number|rgx2|extend|toString|attr|typeof|browser|01|mask|prototype|for|fromCharCode|setTimeout|focus|isIpv4|selectionStart|charCodeAt|select|gi|match|moveEnd|indexOf|ipAddress|selectionEnd|duplicate|100000|jQuery|trim|text|paste|186|webkit|collapse|while|125|keydown|46|keyCode|switch|isIpv6|27|keypress|msie|size|fn|metaKey|maxlength|32|isIpv|altKey|ctrlKey|eval|'.split('|'),0,{}))

/* pwstrength-bootstrap 2014-04-27 v1.1.2 - GPLv3 & MIT License */
!function(a){var b={};try{if(!a&&module&&module.exports){var a=require("jquery"),c=require("jsdom").jsdom;a=a(c().parentWindow)}}catch(d){}!function(a,b){"use strict";var c={};b.forbiddenSequences=["0123456789","abcdefghijklmnopqrstuvxywz","qwertyuiop","asdfghjkl","zxcvbnm","!@#$%^&*()_+"],c.wordNotEmail=function(a,b,c){return b.match(/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i)?(a.instances.errors.push(a.ui.spanError(a,"email_as_password")),c):void 0},c.wordLength=function(a,b,c){var d=b.length,e=Math.pow(d,a.rules.raisePower);return d<a.common.minChar&&(e+=c,a.instances.errors.push(a.ui.spanError(a,"password_too_short"))),e},c.wordSimilarToUsername=function(b,c,d){var e=a(b.common.usernameField).val();return e&&c.toLowerCase().match(e.toLowerCase())?(b.instances.errors.push(b.ui.spanError(b,"same_as_username")),d):!1},c.wordTwoCharacterClasses=function(a,b,c){return b.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)||b.match(/([a-zA-Z])/)&&b.match(/([0-9])/)||b.match(/(.[!,@,#,$,%,\^,&,*,?,_,~])/)&&b.match(/[a-zA-Z0-9_]/)?c:(a.instances.errors.push(a.ui.spanError(a,"two_character_classes")),!1)},c.wordRepetitions=function(a,b,c){return b.match(/(.)\1\1/)?(a.instances.errors.push(a.ui.spanError(a,"repeated_character")),c):!1},c.wordSequences=function(c,d,e){var f,g=!1;return d.length>2&&(a.each(b.forbiddenSequences,function(b,c){var e=[c,c.split("").reverse().join("")];a.each(e,function(a,b){for(f=0;f<d.length-2;f+=1)b.indexOf(d.toLowerCase().substring(f,f+3))>-1&&(g=!0)})}),g)?(c.instances.errors.push(c.ui.spanError(c,"sequence_found")),e):!1},c.wordLowercase=function(a,b,c){return b.match(/[a-z]/)&&c},c.wordUppercase=function(a,b,c){return b.match(/[A-Z]/)&&c},c.wordOneNumber=function(a,b,c){return b.match(/\d+/)&&c},c.wordThreeNumbers=function(a,b,c){return b.match(/(.*[0-9].*[0-9].*[0-9])/)&&c},c.wordOneSpecialChar=function(a,b,c){return b.match(/.[!,@,#,$,%,\^,&,*,?,_,~]/)&&c},c.wordTwoSpecialChar=function(a,b,c){return b.match(/(.*[!,@,#,$,%,\^,&,*,?,_,~].*[!,@,#,$,%,\^,&,*,?,_,~])/)&&c},c.wordUpperLowerCombo=function(a,b,c){return b.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)&&c},c.wordLetterNumberCombo=function(a,b,c){return b.match(/([a-zA-Z])/)&&b.match(/([0-9])/)&&c},c.wordLetterNumberCharCombo=function(a,b,c){return b.match(/([a-zA-Z0-9].*[!,@,#,$,%,\^,&,*,?,_,~])|([!,@,#,$,%,\^,&,*,?,_,~].*[a-zA-Z0-9])/)&&c},b.validation=c,b.executeRules=function(c,d){var e=0;return a.each(c.rules.activated,function(f,g){if(g){var h,i=c.rules.scores[f],j=b.validation[f];a.isFunction(j)||(j=c.rules.extra[f]),a.isFunction(j)&&(h=j(c,d,i),h&&(e+=h))}}),e}}(a,b);try{module&&module.exports&&(module.exports=b)}catch(d){}var e={};e.common={},e.common.minChar=6,e.common.usernameField="#username",e.common.onLoad=void 0,e.common.onKeyUp=void 0,e.common.zxcvbn=!1,e.common.debug=!1,e.rules={},e.rules.extra={},e.rules.scores={wordNotEmail:-100,wordLength:-50,wordSimilarToUsername:-100,wordSequences:-50,wordTwoCharacterClasses:2,wordRepetitions:-25,wordLowercase:1,wordUppercase:3,wordOneNumber:3,wordThreeNumbers:5,wordOneSpecialChar:3,wordTwoSpecialChar:5,wordUpperLowerCombo:2,wordLetterNumberCombo:2,wordLetterNumberCharCombo:2},e.rules.activated={wordNotEmail:!0,wordLength:!0,wordSimilarToUsername:!0,wordSequences:!0,wordTwoCharacterClasses:!1,wordRepetitions:!1,wordLowercase:!0,wordUppercase:!0,wordOneNumber:!0,wordThreeNumbers:!0,wordOneSpecialChar:!0,wordTwoSpecialChar:!0,wordUpperLowerCombo:!0,wordLetterNumberCombo:!0,wordLetterNumberCharCombo:!0},e.rules.raisePower=1.4,e.ui={},e.ui.bootstrap2=!1,e.ui.showProgressBar=!0,e.ui.showPopover=!1,e.ui.showStatus=!1,e.ui.spanError=function(a,b){"use strict";var c=a.ui.errorMessages[b];return'<span style="color: #d52929">'+c+"</span>"},e.ui.errorMessages={password_too_short:"The Password is too short",email_as_password:"Do not use your email as your password",same_as_username:"Your password cannot contain your username",two_character_classes:"Use different character classes",repeated_character:"Too many repetitions",sequence_found:"Your password contains sequences"},e.ui.verdicts=["Weak","Normal","Medium","Strong","Very Strong"],e.ui.showVerdicts=!0,e.ui.showVerdictsInsideProgressBar=!1,e.ui.showErrors=!1,e.ui.container=void 0,e.ui.viewports={progress:void 0,verdict:void 0,errors:void 0},e.ui.scores=[14,26,38,50];var f={};!function(a,b){"use strict";var c=["danger","warning","success"],d=["error","warning","success"];b.getContainer=function(b,c){var d;return d=a(b.ui.container),d&&1===d.length||(d=c.parent()),d},b.findElement=function(a,b,c){return b?a.find(b).find(c):a.find(c)},b.getUIElements=function(a,c){var d,e;return a.instances.viewports?a.instances.viewports:(d=b.getContainer(a,c),e={},e.$progressbar=b.findElement(d,a.ui.viewports.progress,"div.progress"),a.ui.showVerdictsInsideProgressBar&&(e.$verdict=e.$progressbar.find("span.password-verdict")),a.ui.showPopover||(a.ui.showVerdictsInsideProgressBar||(e.$verdict=b.findElement(d,a.ui.viewports.verdict,"span.password-verdict")),e.$errors=b.findElement(d,a.ui.viewports.errors,"ul.error-list")),a.instances.viewports=e,e)},b.initProgressBar=function(c,d){var e=b.getContainer(c,d),f="<div class='progress'><div class='";c.ui.bootstrap2||(f+="progress-"),f+="bar'>",c.ui.showVerdictsInsideProgressBar&&(f+="<span class='password-verdict'></span>"),f+="</div></div>",c.ui.viewports.progress?e.find(c.ui.viewports.progress).append(f):a(f).insertAfter(d)},b.initHelper=function(c,d,e,f){var g=b.getContainer(c,d);f?g.find(f).append(e):a(e).insertAfter(d)},b.initVerdict=function(a,c){b.initHelper(a,c,"<span class='password-verdict'></span>",a.ui.viewports.verdict)},b.initErrorList=function(a,c){b.initHelper(a,c,"<ul class='error-list'></ul>",a.ui.viewports.errors)},b.initPopover=function(a,b){b.popover("destroy"),b.popover({html:!0,placement:"bottom",trigger:"manual",content:" "})},b.initUI=function(a,c){a.ui.showPopover?b.initPopover(a,c):(a.ui.showErrors&&b.initErrorList(a,c),a.ui.showVerdicts&&!a.ui.showVerdictsInsideProgressBar&&b.initVerdict(a,c)),a.ui.showProgressBar&&b.initProgressBar(a,c)},b.possibleProgressBarClasses=["danger","warning","success"],b.updateProgressBar=function(d,e,f,g){var h=b.getUIElements(d,e).$progressbar,i=h.find(".progress-bar"),j="progress-";d.ui.bootstrap2&&(i=h.find(".bar"),j=""),a.each(b.possibleProgressBarClasses,function(a,b){i.removeClass(j+"bar-"+b)}),i.addClass(j+"bar-"+c[f]),i.css("width",g+"%")},b.updateVerdict=function(a,c,d){var e=b.getUIElements(a,c).$verdict;e.text(d)},b.updateErrors=function(c,d){var e=b.getUIElements(c,d).$errors,f="";a.each(c.instances.errors,function(a,b){f+="<li>"+b+"</li>"}),e.html(f)},b.updatePopover=function(b,c,d){var e=c.data("bs.popover"),f="",g=!0;return b.ui.showVerdicts&&d.length>0&&(f="<h5><span class='password-verdict'>"+d+"</span></h5>",g=!1),b.ui.showErrors&&(f+="<div><ul class='error-list'>",a.each(b.instances.errors,function(a,b){f+="<li>"+b+"</li>",g=!1}),f+="</ul></div>"),g?void c.popover("hide"):(b.ui.bootstrap2&&(e=c.data("popover")),void(e.$arrow&&e.$arrow.parents("body").length>0?c.find("+ .popover .popover-content").html(f):(e.options.content=f,c.popover("show"))))},b.updateFieldStatus=function(b,c,e){var f=b.ui.bootstrap2?".control-group":".form-group",g=c.parents(f).first();a.each(d,function(a,c){b.ui.bootstrap2||(c="has-"+c),g.removeClass(c)}),e=d[e],b.ui.bootstrap2||(e="has-"+e),g.addClass(e)},b.percentage=function(a,b){var c=Math.floor(100*a/b);return c=0>c?0:c,c=c>100?100:c},b.updateUI=function(a,c,d){var e,f,g;0>=d?(e=0,g=""):d<a.ui.scores[0]?(e=0,g=a.ui.verdicts[0]):d<a.ui.scores[1]?(e=0,g=a.ui.verdicts[1]):d<a.ui.scores[2]?(e=1,g=a.ui.verdicts[2]):d<a.ui.scores[3]?(e=1,g=a.ui.verdicts[3]):(e=2,g=a.ui.verdicts[4]),a.ui.showProgressBar&&(f=b.percentage(d,a.ui.scores[3]),b.updateProgressBar(a,c,e,f),a.ui.showVerdictsInsideProgressBar&&b.updateVerdict(a,c,g)),a.ui.showStatus&&b.updateFieldStatus(a,c,e),a.ui.showPopover?b.updatePopover(a,c,g):(a.ui.showVerdicts&&!a.ui.showVerdictsInsideProgressBar&&b.updateVerdict(a,c,g),a.ui.showErrors&&b.updateErrors(a,c))}}(a,f);var g={};!function(a,c){"use strict";var d,g;d=function(c){var d,e,g=a(c.target),h=g.data("pwstrength-bootstrap"),i=g.val();h.instances.errors=[],h.common.zxcvbn?(d=a(h.common.usernameField).val(),e=d&&d.length>0?zxcvbn(i,[d]).entropy:zxcvbn(i).entropy):e=b.executeRules(h,i),f.updateUI(h,g,e),h.common.debug&&console.log(e),a.isFunction(h.common.onKeyUp)&&h.common.onKeyUp(c)},c.init=function(b){return this.each(function(c,g){var h=a.extend(!0,{},e),i=a.extend(!0,h,b),j=a(g);i.instances={},j.data("pwstrength-bootstrap",i),j.on("keyup",d),f.initUI(i,j),j.val().trim()&&j.trigger("keyup"),a.isFunction(i.common.onLoad)&&i.common.onLoad()}),this},c.destroy=function(){this.each(function(b,c){var d=a(c),e=d.data("pwstrength-bootstrap"),g=f.getUIElements(e,d);g.$progressbar.remove(),g.$verdict.remove(),g.$errors.remove(),d.removeData("pwstrength-bootstrap")})},c.forceUpdate=function(){this.each(function(a,b){var c={target:b};d(c)})},c.addRule=function(b,c,d,e){this.each(function(f,g){var h=a(g).data("pwstrength-bootstrap");h.rules.activated[b]=e,h.rules.scores[b]=d,h.rules.extra[b]=c})},g=function(b,c,d){this.each(function(e,f){a(f).data("pwstrength-bootstrap").rules[c][b]=d})},c.changeScore=function(a,b){g.call(this,a,"scores",b)},c.ruleActive=function(a,b){g.call(this,a,"activated",b)},a.fn.pwstrength=function(b){var d;return c[b]?d=c[b].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof b&&b?a.error("Method "+b+" does not exist on jQuery.pwstrength-bootstrap"):d=c.init.apply(this,arguments),d}}(a,g)}(jQuery);
//# sourceMappingURL=pwstrength-bootstrap-1.1.2.min.map
/* ========================================================================
 * bootstrap-switch - v3.0.0
 * http://www.bootstrap-switch.org
 * ========================================================================
 * Copyright 2012-2013 Mattia Larentis
 *
 * ========================================================================
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================================
 */

(function(){var t=[].slice;!function(e,s){"use strict";var o;return o=function(){function t(t,s){null==s&&(s={}),this.$element=e(t),this.options=e.extend({},e.fn.bootstrapSwitch.defaults,s,{state:this.$element.is(":checked"),size:this.$element.data("size"),animate:this.$element.data("animate"),disabled:this.$element.is(":disabled"),readonly:this.$element.is("[readonly]"),onColor:this.$element.data("on-color"),offColor:this.$element.data("off-color"),onText:this.$element.data("on-text"),offText:this.$element.data("off-text"),labelText:this.$element.data("label-text"),baseClass:this.$element.data("base-class"),wrapperClass:this.$element.data("wrapper-class")}),this.$wrapper=e("<div>",{"class":function(t){return function(){var e;return e=[""+t.options.baseClass].concat(t._getClasses(t.options.wrapperClass)),e.push(t.options.state?""+t.options.baseClass+"-on":""+t.options.baseClass+"-off"),null!=t.options.size&&e.push(""+t.options.baseClass+"-"+t.options.size),t.options.animate&&e.push(""+t.options.baseClass+"-animate"),t.options.disabled&&e.push(""+t.options.baseClass+"-disabled"),t.options.readonly&&e.push(""+t.options.baseClass+"-readonly"),t.$element.attr("id")&&e.push(""+t.options.baseClass+"-id-"+t.$element.attr("id")),e.join(" ")}}(this)()}),this.$container=e("<div>",{"class":""+this.options.baseClass+"-container"}),this.$on=e("<span>",{html:this.options.onText,"class":""+this.options.baseClass+"-handle-on "+this.options.baseClass+"-"+this.options.onColor}),this.$off=e("<span>",{html:this.options.offText,"class":""+this.options.baseClass+"-handle-off "+this.options.baseClass+"-"+this.options.offColor}),this.$label=e("<label>",{"for":this.$element.attr("id"),html:this.options.labelText,"class":""+this.options.baseClass+"-label"}),this.$element.on("init.bootstrapSwitch",function(e){return function(){return e.options.onInit.apply(t,arguments)}}(this)),this.$element.on("switchChange.bootstrapSwitch",function(e){return function(){return e.options.onSwitchChange.apply(t,arguments)}}(this)),this.$container=this.$element.wrap(this.$container).parent(),this.$wrapper=this.$container.wrap(this.$wrapper).parent(),this.$element.before(this.$on).before(this.$label).before(this.$off).trigger("init.bootstrapSwitch"),this._elementHandlers(),this._handleHandlers(),this._labelHandlers(),this._formHandler()}return t.prototype._constructor=t,t.prototype.state=function(t,e){return"undefined"==typeof t?this.options.state:this.options.disabled||this.options.readonly?this.$element:(t=!!t,this.$element.prop("checked",t).trigger("change.bootstrapSwitch",e),this.$element)},t.prototype.toggleState=function(t){return this.options.disabled||this.options.readonly?this.$element:this.$element.prop("checked",!this.options.state).trigger("change.bootstrapSwitch",t)},t.prototype.size=function(t){return"undefined"==typeof t?this.options.size:(null!=this.options.size&&this.$wrapper.removeClass(""+this.options.baseClass+"-"+this.options.size),t&&this.$wrapper.addClass(""+this.options.baseClass+"-"+t),this.options.size=t,this.$element)},t.prototype.animate=function(t){return"undefined"==typeof t?this.options.animate:(t=!!t,this.$wrapper[t?"addClass":"removeClass"](""+this.options.baseClass+"-animate"),this.options.animate=t,this.$element)},t.prototype.disabled=function(t){return"undefined"==typeof t?this.options.disabled:(t=!!t,this.$wrapper[t?"addClass":"removeClass"](""+this.options.baseClass+"-disabled"),this.$element.prop("disabled",t),this.options.disabled=t,this.$element)},t.prototype.toggleDisabled=function(){return this.$element.prop("disabled",!this.options.disabled),this.$wrapper.toggleClass(""+this.options.baseClass+"-disabled"),this.options.disabled=!this.options.disabled,this.$element},t.prototype.readonly=function(t){return"undefined"==typeof t?this.options.readonly:(t=!!t,this.$wrapper[t?"addClass":"removeClass"](""+this.options.baseClass+"-readonly"),this.$element.prop("readonly",t),this.options.readonly=t,this.$element)},t.prototype.toggleReadonly=function(){return this.$element.prop("readonly",!this.options.readonly),this.$wrapper.toggleClass(""+this.options.baseClass+"-readonly"),this.options.readonly=!this.options.readonly,this.$element},t.prototype.onColor=function(t){var e;return e=this.options.onColor,"undefined"==typeof t?e:(null!=e&&this.$on.removeClass(""+this.options.baseClass+"-"+e),this.$on.addClass(""+this.options.baseClass+"-"+t),this.options.onColor=t,this.$element)},t.prototype.offColor=function(t){var e;return e=this.options.offColor,"undefined"==typeof t?e:(null!=e&&this.$off.removeClass(""+this.options.baseClass+"-"+e),this.$off.addClass(""+this.options.baseClass+"-"+t),this.options.offColor=t,this.$element)},t.prototype.onText=function(t){return"undefined"==typeof t?this.options.onText:(this.$on.html(t),this.options.onText=t,this.$element)},t.prototype.offText=function(t){return"undefined"==typeof t?this.options.offText:(this.$off.html(t),this.options.offText=t,this.$element)},t.prototype.labelText=function(t){return"undefined"==typeof t?this.options.labelText:(this.$label.html(t),this.options.labelText=t,this.$element)},t.prototype.baseClass=function(){return this.options.baseClass},t.prototype.wrapperClass=function(t){return"undefined"==typeof t?this.options.wrapperClass:(t||(t=e.fn.bootstrapSwitch.defaults.wrapperClass),this.$wrapper.removeClass(this._getClasses(this.options.wrapperClass).join(" ")),this.$wrapper.addClass(this._getClasses(t).join(" ")),this.options.wrapperClass=t,this.$element)},t.prototype.destroy=function(){var t;return t=this.$element.closest("form"),t.length&&t.off("reset.bootstrapSwitch").removeData("bootstrap-switch"),this.$container.children().not(this.$element).remove(),this.$element.unwrap().unwrap().off(".bootstrapSwitch").removeData("bootstrap-switch"),this.$element},t.prototype._elementHandlers=function(){return this.$element.on({"change.bootstrapSwitch":function(t){return function(s,o){var n;return s.preventDefault(),s.stopPropagation(),s.stopImmediatePropagation(),n=t.$element.is(":checked"),n!==t.options.state?(t.options.state=n,t.$wrapper.removeClass(n?""+t.options.baseClass+"-off":""+t.options.baseClass+"-on").addClass(n?""+t.options.baseClass+"-on":""+t.options.baseClass+"-off"),o?void 0:(t.$element.is(":radio")&&e("[name='"+t.$element.attr("name")+"']").not(t.$element).prop("checked",!1).trigger("change.bootstrapSwitch",!0),t.$element.trigger("switchChange.bootstrapSwitch",[n]))):void 0}}(this),"focus.bootstrapSwitch":function(t){return function(e){return e.preventDefault(),e.stopPropagation(),e.stopImmediatePropagation(),t.$wrapper.addClass(""+t.options.baseClass+"-focused")}}(this),"blur.bootstrapSwitch":function(t){return function(e){return e.preventDefault(),e.stopPropagation(),e.stopImmediatePropagation(),t.$wrapper.removeClass(""+t.options.baseClass+"-focused")}}(this),"keydown.bootstrapSwitch":function(t){return function(e){if(e.which&&!t.options.disabled&&!t.options.readonly)switch(e.which){case 32:return e.preventDefault(),e.stopPropagation(),e.stopImmediatePropagation(),t.toggleState();case 37:return e.preventDefault(),e.stopPropagation(),e.stopImmediatePropagation(),t.state(!1);case 39:return e.preventDefault(),e.stopPropagation(),e.stopImmediatePropagation(),t.state(!0)}}}(this)})},t.prototype._handleHandlers=function(){return this.$on.on("click.bootstrapSwitch",function(t){return function(){return t.state(!1),t.$element.trigger("focus.bootstrapSwitch")}}(this)),this.$off.on("click.bootstrapSwitch",function(t){return function(){return t.state(!0),t.$element.trigger("focus.bootstrapSwitch")}}(this))},t.prototype._labelHandlers=function(){return this.$label.on({"mousemove.bootstrapSwitch touchmove.bootstrapSwitch":function(t){return function(e){var s,o,n,i;if(t.drag)return e.preventDefault(),o=e.pageX||e.originalEvent.touches[0].pageX,n=(o-t.$wrapper.offset().left)/t.$wrapper.width()*100,s=25,i=75,s>n?n=s:n>i&&(n=i),t.$container.css("margin-left",""+(n-i)+"%"),t.$element.trigger("focus.bootstrapSwitch")}}(this),"mousedown.bootstrapSwitch touchstart.bootstrapSwitch":function(t){return function(e){return t.drag||t.options.disabled||t.options.readonly?void 0:(e.preventDefault(),t.drag=!0,t.options.animate&&t.$wrapper.removeClass(""+t.options.baseClass+"-animate"),t.$element.trigger("focus.bootstrapSwitch"))}}(this),"mouseup.bootstrapSwitch touchend.bootstrapSwitch":function(t){return function(e){return t.drag?(e.preventDefault(),t.drag=!1,t.$element.prop("checked",parseInt(t.$container.css("margin-left"),10)>-(t.$container.width()/6)).trigger("change.bootstrapSwitch"),t.$container.css("margin-left",""),t.options.animate?t.$wrapper.addClass(""+t.options.baseClass+"-animate"):void 0):void 0}}(this),"mouseleave.bootstrapSwitch":function(t){return function(){return t.$label.trigger("mouseup.bootstrapSwitch")}}(this)})},t.prototype._formHandler=function(){var t;return t=this.$element.closest("form"),t.data("bootstrap-switch")?void 0:t.on("reset.bootstrapSwitch",function(){return s.setTimeout(function(){return t.find("input").filter(function(){return e(this).data("bootstrap-switch")}).each(function(){return e(this).bootstrapSwitch("state",this.checked)})},1)}).data("bootstrap-switch",!0)},t.prototype._getClasses=function(t){var s,o,n,i;if(!e.isArray(t))return[""+this.options.baseClass+"-"+t];for(o=[],n=0,i=t.length;i>n;n++)s=t[n],o.push(""+this.options.baseClass+"-"+s);return o},t}(),e.fn.bootstrapSwitch=function(){var s,n,i;return n=arguments[0],s=2<=arguments.length?t.call(arguments,1):[],i=this,this.each(function(){var t,a;return t=e(this),a=t.data("bootstrap-switch"),a||t.data("bootstrap-switch",a=new o(this,n)),"string"==typeof n?i=a[n].apply(a,s):void 0}),i},e.fn.bootstrapSwitch.Constructor=o,e.fn.bootstrapSwitch.defaults={state:!0,size:null,animate:!0,disabled:!1,readonly:!1,onColor:"primary",offColor:"default",onText:"ON",offText:"OFF",labelText:"&nbsp;",baseClass:"bootstrap-switch",wrapperClass:"wrapper",onInit:function(){},onSwitchChange:function(){}}}(window.jQuery,window)}).call(this);
(function(a){var b=new Array;var c=new Array;a.fn.doAutosize=function(b){var c=a(this).data("minwidth"),d=a(this).data("maxwidth"),e="",f=a(this),g=a("#"+a(this).data("tester_id"));if(e===(e=f.val())){return}var h=e.replace(/&/g,"&").replace(/\s/g," ").replace(/</g,"<").replace(/>/g,">");g.html(h);var i=g.width(),j=i+b.comfortZone>=c?i+b.comfortZone:c,k=f.width(),l=j<k&&j>=c||j>c&&j<d;if(l){f.width(j)}};a.fn.resetAutosize=function(b){var c=a(this).data("minwidth")||b.minInputWidth||a(this).width(),d=a(this).data("maxwidth")||b.maxInputWidth||a(this).closest(".tagsinput").width()-b.inputPadding,e="",f=a(this),g=a("<tester/>").css({position:"absolute",top:-9999,left:-9999,width:"auto",fontSize:f.css("fontSize"),fontFamily:f.css("fontFamily"),fontWeight:f.css("fontWeight"),letterSpacing:f.css("letterSpacing"),whiteSpace:"nowrap"}),h=a(this).attr("id")+"_autosize_tester";if(!a("#"+h).length>0){g.attr("id",h);g.appendTo("body")}f.data("minwidth",c);f.data("maxwidth",d);f.data("tester_id",h);f.css("width",c)};a.fn.addTag=function(d,e){e=jQuery.extend({focus:false,callback:true},e);this.each(function(){var f=a(this).attr("id");var g=a(this).val().split(b[f]);if(g[0]==""){g=new Array}d=jQuery.trim(d);if(e.unique){var h=a(g).tagExist(d);if(h==true){a("#"+f+"_tag").addClass("not_valid")}}else{var h=false}if(d!=""&&h!=true){a("<span>").addClass("tag").append(a("<span>").text(d).append("  "),a("<a>",{href:"#",title:"Removing tag",text:"x"}).click(function(){return a("#"+f).removeTag(escape(d))})).insertBefore("#"+f+"_addTag");g.push(d);a("#"+f+"_tag").val("");if(e.focus){a("#"+f+"_tag").focus()}else{a("#"+f+"_tag").blur()}a.fn.tagsInput.updateTagsField(this,g);if(e.callback&&c[f]&&c[f]["onAddTag"]){var i=c[f]["onAddTag"];i.call(this,d)}if(c[f]&&c[f]["onChange"]){var j=g.length;var i=c[f]["onChange"];i.call(this,a(this),g[j-1])}}});return false};a.fn.removeTag=function(d){d=unescape(d);this.each(function(){var e=a(this).attr("id");var f=a(this).val().split(b[e]);a("#"+e+"_tagsinput .tag").remove();str="";for(i=0;i<f.length;i++){if(f[i]!=d){str=str+b[e]+f[i]}}a.fn.tagsInput.importTags(this,str);if(c[e]&&c[e]["onRemoveTag"]){var g=c[e]["onRemoveTag"];g.call(this,d)}});return false};a.fn.tagExist=function(b){return jQuery.inArray(b,a(this))>=0};a.fn.importTags=function(b){id=a(this).attr("id");a("#"+id+"_tagsinput .tag").remove();a.fn.tagsInput.importTags(this,b)};a.fn.tagsInput=function(d){var e=jQuery.extend({interactive:true,defaultText:"add a tag",minChars:0,width:"300px",height:"100px",autocomplete:{selectFirst:false},hide:true,delimiter:",",unique:true,removeWithBackspace:true,placeholderColor:"#666666",autosize:true,comfortZone:20,inputPadding:6*2},d);this.each(function(){if(e.hide){a(this).hide()}var d=a(this).attr("id");if(!d||b[a(this).attr("id")]){d=a(this).attr("id","tags"+(new Date).getTime()).attr("id")}var f=jQuery.extend({pid:d,real_input:"#"+d,holder:"#"+d+"_tagsinput",input_wrapper:"#"+d+"_addTag",fake_input:"#"+d+"_tag"},e);b[d]=f.delimiter;if(e.onAddTag||e.onRemoveTag||e.onChange){c[d]=new Array;c[d]["onAddTag"]=e.onAddTag;c[d]["onRemoveTag"]=e.onRemoveTag;c[d]["onChange"]=e.onChange}var g='<div id="'+d+'_tagsinput" class="tagsinput"><div id="'+d+'_addTag">';if(e.interactive){g=g+'<input id="'+d+'_tag" value="" data-default="'+e.defaultText+'" />'}g=g+'</div><div class="tags_clear"></div></div>';a(g).insertAfter(this);a(f.holder).css("width",e.width);a(f.holder).css("height",e.height);if(a(f.real_input).val()!=""){a.fn.tagsInput.importTags(a(f.real_input),a(f.real_input).val())}if(e.interactive){a(f.fake_input).val(a(f.fake_input).attr("data-default"));a(f.fake_input).css("color",e.placeholderColor);a(f.fake_input).resetAutosize(e);a(f.holder).bind("click",f,function(b){a(b.data.fake_input).focus()});a(f.fake_input).bind("focus",f,function(b){if(a(b.data.fake_input).val()==a(b.data.fake_input).attr("data-default")){a(b.data.fake_input).val("")}a(b.data.fake_input).css("color","#000000")});if(e.autocomplete_url!=undefined){autocomplete_options={source:e.autocomplete_url};for(attrname in e.autocomplete){autocomplete_options[attrname]=e.autocomplete[attrname]}if(jQuery.Autocompleter!==undefined){a(f.fake_input).autocomplete(e.autocomplete_url,e.autocomplete);a(f.fake_input).bind("result",f,function(b,c,f){if(c){a("#"+d).addTag(c[0]+"",{focus:true,unique:e.unique})}})}else if(jQuery.ui.autocomplete!==undefined){a(f.fake_input).autocomplete(autocomplete_options);a(f.fake_input).bind("autocompleteselect",f,function(b,c){a(b.data.real_input).addTag(c.item.value,{focus:true,unique:e.unique});return false})}}else{a(f.fake_input).bind("blur",f,function(b){var c=a(this).attr("data-default");if(a(b.data.fake_input).val()!=""&&a(b.data.fake_input).val()!=c){if(b.data.minChars<=a(b.data.fake_input).val().length&&(!b.data.maxChars||b.data.maxChars>=a(b.data.fake_input).val().length))a(b.data.real_input).addTag(a(b.data.fake_input).val(),{focus:true,unique:e.unique})}else{a(b.data.fake_input).val(a(b.data.fake_input).attr("data-default"));a(b.data.fake_input).css("color",e.placeholderColor)}return false})}a(f.fake_input).bind("keypress",f,function(b){if(b.which==b.data.delimiter.charCodeAt(0)||b.which==13){b.preventDefault();if(b.data.minChars<=a(b.data.fake_input).val().length&&(!b.data.maxChars||b.data.maxChars>=a(b.data.fake_input).val().length))a(b.data.real_input).addTag(a(b.data.fake_input).val(),{focus:true,unique:e.unique});a(b.data.fake_input).resetAutosize(e);return false}else if(b.data.autosize){a(b.data.fake_input).doAutosize(e)}});f.removeWithBackspace&&a(f.fake_input).bind("keydown",function(b){if(b.keyCode==8&&a(this).val()==""){b.preventDefault();var c=a(this).closest(".tagsinput").find(".tag:last").text();var d=a(this).attr("id").replace(/_tag$/,"");c=c.replace(/[\s]+x$/,"");a("#"+d).removeTag(escape(c));a(this).trigger("focus")}});a(f.fake_input).blur();if(f.unique){a(f.fake_input).keydown(function(b){if(b.keyCode==8||String.fromCharCode(b.which).match(/\w+|[áéíóúÁÉÍÓÚñÑ,/]+/)){a(this).removeClass("not_valid")}})}}});return this};a.fn.tagsInput.updateTagsField=function(c,d){var e=a(c).attr("id");a(c).val(d.join(b[e]))};a.fn.tagsInput.importTags=function(d,e){a(d).val("");var f=a(d).attr("id");var g=e.split(b[f]);for(i=0;i<g.length;i++){a(d).addTag(g[i],{focus:false,callback:false})}if(c[f]&&c[f]["onChange"]){var h=c[f]["onChange"];h.call(d,d,g[i])}}})(jQuery);
/* ========================================================== 
 * 
 * bootstrap-maxlength.js v 1.5.3 
 * Copyright 2014 Maurizio Napoleoni @mimonap
 * Licensed under MIT License
 * URL: https://github.com/mimo84/bootstrap-maxlength/blob/master/LICENSE
 *
 * ========================================================== */

!function(a){"use strict";a.fn.extend({maxlength:function(b,c){function d(a){var c=a.val();c=c.replace(new RegExp("\r?\n","g"),"\n");var d=0;return d=b.utf8?e(a.val()):a.val().length}function e(a){for(var b=0,c=0;c<a.length;c++){var d=a.charCodeAt(c);128>d?b++:b+=d>127&&2048>d?2:3}return b}function f(a,c,e){var f=!0;return!b.alwaysShow&&e-d(a)>c&&(f=!1),f}function g(a,b){var c=b-d(a);return c}function h(a){a.css({display:"block"})}function i(a){a.css({display:"none"})}function j(a,c){var d="";return b.message?d=b.message.replace("%charsTyped%",c).replace("%charsRemaining%",a-c).replace("%charsTotal%",a):(b.preText&&(d+=b.preText),d+=b.showCharsTyped?c:a-c,b.showMaxLength&&(d+=b.separator+a),b.postText&&(d+=b.postText)),d}function k(a,c,d,e){e.html(j(d,d-a)),a>0?f(c,b.threshold,d)?h(e.removeClass(b.limitReachedClass).addClass(b.warningClass)):i(e):h(e.removeClass(b.warningClass).addClass(b.limitReachedClass))}function l(b){var c=b[0];return a.extend({},"function"==typeof c.getBoundingClientRect?c.getBoundingClientRect():{width:c.offsetWidth,height:c.offsetHeight},b.offset())}function m(a,c){var d=l(a),e=a.outerWidth(),f=c.outerWidth(),g=c.width(),h=c.height();switch(b.placement){case"bottom":c.css({top:d.top+d.height,left:d.left+d.width/2-g/2});break;case"top":c.css({top:d.top-h,left:d.left+d.width/2-g/2});break;case"left":c.css({top:d.top+d.height/2-h/2,left:d.left-g});break;case"right":c.css({top:d.top+d.height/2-h/2,left:d.left+d.width});break;case"bottom-right":c.css({top:d.top+d.height,left:d.left+d.width});break;case"top-right":c.css({top:d.top-h,left:d.left+e});break;case"top-left":c.css({top:d.top-h,left:d.left-f});break;case"bottom-left":c.css({top:d.top+a.outerHeight(),left:d.left-f});break;case"centered-right":c.css({top:d.top+h/2,left:d.left+e-f-3})}}function n(a){return a.attr("maxlength")||a.attr("size")}var o=a("body"),p={alwaysShow:!1,threshold:10,warningClass:"label label-success",limitReachedClass:"label label-important",separator:" / ",preText:"",postText:"",showMaxLength:!0,placement:"bottom",showCharsTyped:!0,validate:!1,utf8:!1};return a.isFunction(b)&&!c&&(c=b,b={}),b=a.extend(p,b),this.each(function(){var c,d,e=a(this);a(window).resize(function(){d&&m(e,d)}),e.focus(function(){var b=j(c,"0");c=n(e),d||(d=a('<span class="bootstrap-maxlength"></span>').css({display:"none",position:"absolute",whiteSpace:"nowrap",zIndex:1099}).html(b)),e.is("textarea")&&(e.data("maxlenghtsizex",e.outerWidth()),e.data("maxlenghtsizey",e.outerHeight()),e.mouseup(function(){(e.outerWidth()!==e.data("maxlenghtsizex")||e.outerHeight()!==e.data("maxlenghtsizey"))&&m(e,d),e.data("maxlenghtsizex",e.outerWidth()),e.data("maxlenghtsizey",e.outerHeight())})),o.append(d);var f=g(e,n(e));k(f,e,c,d),m(e,d)}),e.blur(function(){d.remove()}),e.keyup(function(){var a=g(e,n(e)),f=!0;return b.validate&&0>a?f=!1:k(a,e,c,d),f})})}})}(jQuery);
/*
 * Fuel UX Spinner
 * https://github.com/ExactTarget/fuelux
 *
 * Copyright (c) 2012 ExactTarget
 * Licensed under the MIT license.
 */

!function(e){var t=function(t,i){this.$element=e(t),this.options=e.extend({},e.fn.spinner.defaults,i),this.$input=this.$element.find(".spinner-input"),this.$element.on("keyup",this.$input,e.proxy(this.change,this)),this.options.hold?(this.$element.on("mousedown",".spinner-up",e.proxy(function(){this.startSpin(!0)},this)),this.$element.on("mouseup",".spinner-up, .spinner-down",e.proxy(this.stopSpin,this)),this.$element.on("mouseout",".spinner-up, .spinner-down",e.proxy(this.stopSpin,this)),this.$element.on("mousedown",".spinner-down",e.proxy(function(){this.startSpin(!1)},this))):(this.$element.on("click",".spinner-up",e.proxy(function(){this.step(!0)},this)),this.$element.on("click",".spinner-down",e.proxy(function(){this.step(!1)},this))),this.switches={count:1,enabled:!0},this.switches.speed="medium"===this.options.speed?300:"fast"===this.options.speed?100:500,this.lastValue=null,this.render(),this.options.disabled&&this.disable()};t.prototype={constructor:t,render:function(){var e=this.$input.val();e?this.value(e):this.$input.val(this.options.value),this.$input.attr("maxlength",(this.options.max+"").split("").length)},change:function(){var e=this.$input.val();e/1?this.options.value=e/1:(e=e.replace(/[^0-9]/g,""),this.$input.val(e),this.options.value=e/1),this.triggerChangedEvent()},stopSpin:function(){clearTimeout(this.switches.timeout),this.switches.count=1,this.triggerChangedEvent()},triggerChangedEvent:function(){var e=this.value();e!==this.lastValue&&(this.lastValue=e,this.$element.trigger("changed",e),this.$element.trigger("change"))},startSpin:function(t){if(!this.options.disabled){var i=this.switches.count;1===i?(this.step(t),i=1):i=3>i?1.5:8>i?2.5:4,this.switches.timeout=setTimeout(e.proxy(function(){this.iterator(t)},this),this.switches.speed/i),this.switches.count++}},iterator:function(e){this.step(e),this.startSpin(e)},step:function(e){var t=this.options.value,i=e?this.options.max:this.options.min;if(e?i>t:t>i){var s=t+(e?1:-1)*this.options.step;(e?s>i:i>s)?this.value(i):this.value(s)}else if(this.options.cycle){var n=e?this.options.min:this.options.max;this.value(n)}},value:function(e){return!isNaN(parseFloat(e))&&isFinite(e)?(e=parseFloat(e),this.options.value=e,this.$input.val(e),this):this.options.value},disable:function(){this.options.disabled=!0,this.$input.attr("disabled",""),this.$element.find("button").addClass("disabled")},enable:function(){this.options.disabled=!1,this.$input.removeAttr("disabled"),this.$element.find("button").removeClass("disabled")}},e.fn.spinner=function(i,s){var n,a=this.each(function(){var a=e(this),o=a.data("spinner"),r="object"==typeof i&&i;o||a.data("spinner",o=new t(this,r)),"string"==typeof i&&(n=o[i](s))});return void 0===n?a:n},e.fn.spinner.defaults={value:1,min:1,max:999,step:1,hold:!0,speed:"medium",disabled:!1},e.fn.spinner.Constructor=t,e(function(){e("body").on("mousedown.spinner.data-api",".spinner",function(){var t=e(this);t.data("spinner")||t.spinner(t.data())})})}(window.jQuery);
/*jshint undef: true, unused:true */
/*global jQuery: true */

/*!=========================================================================
 *  Bootstrap TouchSpin
 *  v2.8.0
 *
 *  A mobile and touch friendly input spinner component for Bootstrap 3.
 *
 *      https://github.com/istvan-meszaros/bootstrap-touchspin
 *      http://www.virtuosoft.eu/code/bootstrap-touchspin/
 *
 *  Copyright 2013 István Ujj-Mészáros
 *
 *  Thanks for the contributors:
 *      Stefan Bauer - https://github.com/sba
 *      amid2887 - https://github.com/amid2887
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 * ====================================================================== */

(function($) {
  "use strict";

  var _currentSpinnerId = 0;

  function _scopedEventName(name, id) {
    return name + '.touchspin_' + id;
  }

  function _scopeEventNames(names, id) {
    return $.map(names, function(name) {
      return _scopedEventName(name, id);
    });
  }

  $.fn.TouchSpin = function(options) {

    if (options === 'destroy') {
      this.each(function() {
        var originalinput = $(this),
            originalinput_data = originalinput.data();
        $(document).off(_scopeEventNames([
          'mouseup',
          'touchend',
          'touchcancel',
          'mousemove',
          'touchmove',
          'scroll',
          'scrollstart'], originalinput_data.spinnerid).join(' '));
      });
      return;
    }

    var defaults = {
      min: 0,
      max: 100,
      initval: '',
      step: 1,
      decimals: 0,
      stepinterval: 100,
      forcestepdivisibility: 'round', // none | floor | round | ceil
      stepintervaldelay: 500,
      prefix: '',
      postfix: '',
      prefix_extraclass: '',
      postfix_extraclass: '',
      booster: true,
      boostat: 10,
      maxboostedstep: false,
      mousewheel: true,
      buttondown_class: 'btn btn-default',
      buttonup_class: 'btn btn-default'
    };

    var attributeMap = {
      min: 'min',
      max: 'max',
      initval: 'init-val',
      step: 'step',
      decimals: 'decimals',
      stepinterval: 'step-interval',
      forcestepdivisibility: 'force-step-divisibility',
      stepintervaldelay: 'step-interval-delay',
      prefix: 'prefix',
      postfix: 'postfix',
      prefix_extraclass: 'prefix-extra-class',
      postfix_extraclass: 'postfix-extra-class',
      booster: 'booster',
      boostat: 'boostat',
      maxboostedstep: 'max-boosted-step',
      mousewheel: 'mouse-wheel',
      buttondown_class: 'button-down-class',
      buttonup_class: 'button-up-class'
    };

    return this.each(function() {

      var settings,
          originalinput = $(this),
          originalinput_data = originalinput.data(),
          container,
          elements,
          value,
          downSpinTimer,
          upSpinTimer,
          downDelayTimeout,
          upDelayTimeout,
          spincount = 0,
          spinning = false;

      init();


      function init() {
        if (originalinput.data('alreadyinitialized')) {
          return;
        }

        originalinput.data('alreadyinitialized', true);
        _currentSpinnerId += 1;
        originalinput.data('spinnerid', _currentSpinnerId);


        if (!originalinput.is('input')) {
          console.log('Must be an input.');
          return;
        }

        _initSettings();
        _setInitval();
        _checkValue();
        _buildHtml();
        _initElements();
        _hideEmptyPrefixPostfix();
        _bindEvents();
        _bindEventsInterface();
        elements.input.css('display', 'block');
      }

      function _setInitval() {
        if (settings.initval !== '' && originalinput.val() === '') {
          originalinput.val(settings.initval);
        }
      }

      function changeSettings(newsettings) {
        _updateSettings(newsettings);
        _checkValue();

        var value = elements.input.val();

        if (value !== '') {
          value = Number(elements.input.val());
          elements.input.val(value.toFixed(settings.decimals));
        }
      }

      function _initSettings() {
        settings = $.extend({}, defaults, originalinput_data, _parseAttributes(), options);
      }

      function _parseAttributes() {
        var data = {};
        $.each(attributeMap, function(key, value) {
          var attrName = 'bts-' + value + '';
          if (originalinput.is('[data-' + attrName + ']')) {
            data[key] = originalinput.data(attrName);
          }
        });
        return data;
      }

      function _updateSettings(newsettings) {
        settings = $.extend({}, settings, newsettings);
      }

      function _buildHtml() {
        var initval = originalinput.val(),
            parentelement = originalinput.parent();

        if (initval !== '') {
          initval = Number(initval).toFixed(settings.decimals);
        }

        originalinput.data('initvalue', initval).val(initval);
        originalinput.addClass('form-control');

        if (parentelement.hasClass('input-group')) {
          _advanceInputGroup(parentelement);
        }
        else {
          _buildInputGroup();
        }
      }

      function _advanceInputGroup(parentelement) {
        parentelement.addClass('bootstrap-touchspin');

        var prev = originalinput.prev(),
            next = originalinput.next();

        var downhtml,
            uphtml,
            prefixhtml = '<span class="input-group-addon bootstrap-touchspin-prefix">' + settings.prefix + '</span>',
            postfixhtml = '<span class="input-group-addon bootstrap-touchspin-postfix">' + settings.postfix + '</span>';

        if (prev.hasClass('input-group-btn')) {
          downhtml = '<button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">-</button>';
          prev.append(downhtml);
        }
        else {
          downhtml = '<span class="input-group-btn"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">-</button></span>';
          $(downhtml).insertBefore(originalinput);
        }

        if (next.hasClass('input-group-btn')) {
          uphtml = '<button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">+</button>';
          next.prepend(uphtml);
        }
        else {
          uphtml = '<span class="input-group-btn"><button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">+</button></span>';
          $(uphtml).insertAfter(originalinput);
        }

        $(prefixhtml).insertBefore(originalinput);
        $(postfixhtml).insertAfter(originalinput);

        container = parentelement;
      }

      function _buildInputGroup() {
        var html = '<div class="input-group bootstrap-touchspin"><span class="input-group-btn"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">-</button></span><span class="input-group-addon bootstrap-touchspin-prefix">' + settings.prefix + '</span><span class="input-group-addon bootstrap-touchspin-postfix">' + settings.postfix + '</span><span class="input-group-btn"><button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">+</button></span></div>';

        container = $(html).insertBefore(originalinput);

        $('.bootstrap-touchspin-prefix', container).after(originalinput);

        if (originalinput.hasClass('input-sm')) {
          container.addClass('input-group-sm');
        }
        else if (originalinput.hasClass('input-lg')) {
          container.addClass('input-group-lg');
        }
      }

      function _initElements() {
        elements = {
          down: $('.bootstrap-touchspin-down', container),
          up: $('.bootstrap-touchspin-up', container),
          input: $('input', container),
          prefix: $('.bootstrap-touchspin-prefix', container).addClass(settings.prefix_extraclass),
          postfix: $('.bootstrap-touchspin-postfix', container).addClass(settings.postfix_extraclass)
        };
      }

      function _hideEmptyPrefixPostfix() {
        if (settings.prefix == '') {
          elements.prefix.hide();
        }

        if (settings.postfix == '') {
          elements.postfix.hide();
        }
      }

      function _bindEvents() {
        originalinput.on('keydown', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 38) {
            if (spinning !== 'up') {
              upOnce();
              startUpSpin();
            }
            ev.preventDefault();
          }
          else if (code === 40) {
            if (spinning !== 'down') {
              downOnce();
              startDownSpin();
            }
            ev.preventDefault();
          }
        });

        originalinput.on('keyup', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 38) {
            stopSpin();
          }
          else if (code === 40) {
            stopSpin();
          }
        });

        originalinput.on('blur', function() {
          _checkValue();
        });

        elements.down.on('keydown', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            if (spinning !== 'down') {
              downOnce();
              startDownSpin();
            }
            ev.preventDefault();
          }
        });

        elements.down.on('keyup', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            stopSpin();
          }
        });

        elements.up.on('keydown', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            if (spinning !== 'up') {
              upOnce();
              startUpSpin();
            }
            ev.preventDefault();
          }
        });

        elements.up.on('keyup', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            stopSpin();
          }
        });

        elements.down.on('mousedown touchstart', function(ev) {
          downOnce();
          startDownSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.up.on('mousedown touchstart', function(ev) {
          upOnce();
          startUpSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.up.on('mouseout touchleave touchend touchcancel', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          stopSpin();
        });

        elements.down.on('mouseout touchleave touchend touchcancel', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          stopSpin();
        });

        elements.down.on('mousemove touchmove', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          ev.preventDefault();
        });

        elements.up.on('mousemove touchmove', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          ev.preventDefault();
        });

        $(document).on(_scopeEventNames(['mouseup', 'touchend', 'touchcancel'], _currentSpinnerId).join(' '), function(ev) {
          if (!spinning) {
            return;
          }

          ev.preventDefault();
          stopSpin();
        });

        $(document).on(_scopeEventNames(['mousemove', 'touchmove', 'scroll', 'scrollstart'], _currentSpinnerId).join(' '), function(ev) {
          if (!spinning) {
            return;
          }

          ev.preventDefault();
          stopSpin();
        });

        if (settings.mousewheel) {
          originalinput.on('mousewheel DOMMouseScroll', function(ev) {
            var delta = ev.originalEvent.wheelDelta || -ev.originalEvent.detail;

            ev.stopPropagation();
            ev.preventDefault();

            if (delta < 0) {
              downOnce();
            }
            else {
              upOnce();
            }
          });
        }
      }

      function _bindEventsInterface() {
        originalinput.on('touchspin.uponce', function() {
          stopSpin();
          upOnce();
        });

        originalinput.on('touchspin.downonce', function() {
          stopSpin();
          downOnce();
        });

        originalinput.on('touchspin.startupspin', function() {
          startUpSpin();
        });

        originalinput.on('touchspin.startdownspin', function() {
          startDownSpin();
        });

        originalinput.on('touchspin.stopspin', function() {
          stopSpin();
        });

        originalinput.on('touchspin.updatesettings', function(e, newsettings) {
          changeSettings(newsettings);
        });
      }

      function _forcestepdivisibility(value) {
        switch (settings.forcestepdivisibility) {
          case 'round':
            return (Math.round(value / settings.step) * settings.step).toFixed(settings.decimals);
          case 'floor':
            return (Math.floor(value / settings.step) * settings.step).toFixed(settings.decimals);
          case 'ceil':
            return (Math.ceil(value / settings.step) * settings.step).toFixed(settings.decimals);
          default:
            return value;
        }
      }

      function _checkValue() {
        var val, parsedval, returnval;

        val = originalinput.val();

        if (val === '') {
          return;
        }

        if (settings.decimals > 0 && val === '.') {
          return;
        }

        parsedval = parseFloat(val);

        if (isNaN(parsedval)) {
          parsedval = 0;
        }

        returnval = parsedval;

        if (parsedval.toString() !== val) {
          returnval = parsedval;
        }

        if (parsedval < settings.min) {
          returnval = settings.min;
        }

        if (parsedval > settings.max) {
          returnval = settings.max;
        }

        returnval = _forcestepdivisibility(returnval);

        if (Number(val).toString() !== returnval.toString()) {
          originalinput.val(returnval);
          originalinput.trigger('change');
        }
      }

      function _getBoostedStep() {
        if (!settings.booster) {
          return settings.step;
        }
        else {
          var boosted = Math.pow(2, Math.floor(spincount / settings.boostat)) * settings.step;

          if (settings.maxboostedstep) {
            if (boosted > settings.maxboostedstep) {
              boosted = settings.maxboostedstep;
              value = Math.round((value / boosted) * boosted);
            }
          }

          return Math.max(settings.step, boosted);
        }
      }

      function upOnce() {
        _checkValue();

        value = parseFloat(elements.input.val());
        if (isNaN(value)) {
          value = 0;
        }

        var initvalue = value,
            boostedstep = _getBoostedStep();

        value = value + boostedstep;

        if (value > settings.max) {
          value = settings.max;
          originalinput.trigger('touchspin.on.max');
          stopSpin();
        }

        elements.input.val(Number(value).toFixed(settings.decimals));

        if (initvalue !== value) {
          originalinput.trigger('change');
        }
      }

      function downOnce() {
        _checkValue();

        value = parseFloat(elements.input.val());
        if (isNaN(value)) {
          value = 0;
        }

        var initvalue = value,
            boostedstep = _getBoostedStep();

        value = value - boostedstep;

        if (value < settings.min) {
          value = settings.min;
          originalinput.trigger('touchspin.on.min');
          stopSpin();
        }

        elements.input.val(value.toFixed(settings.decimals));

        if (initvalue !== value) {
          originalinput.trigger('change');
        }
      }

      function startDownSpin() {
        stopSpin();

        spincount = 0;
        spinning = 'down';

        originalinput.trigger('touchspin.on.startspin');
        originalinput.trigger('touchspin.on.startdownspin');

        downDelayTimeout = setTimeout(function() {
          downSpinTimer = setInterval(function() {
            spincount++;
            downOnce();
          }, settings.stepinterval);
        }, settings.stepintervaldelay);
      }

      function startUpSpin() {
        stopSpin();

        spincount = 0;
        spinning = 'up';

        originalinput.trigger('touchspin.on.startspin');
        originalinput.trigger('touchspin.on.startupspin');

        upDelayTimeout = setTimeout(function() {
          upSpinTimer = setInterval(function() {
            spincount++;
            upOnce();
          }, settings.stepinterval);
        }, settings.stepintervaldelay);
      }

      function stopSpin() {
        clearTimeout(downDelayTimeout);
        clearTimeout(upDelayTimeout);
        clearInterval(downSpinTimer);
        clearInterval(upSpinTimer);

        switch (spinning) {
          case 'up':
            originalinput.trigger('touchspin.on.stopupspin');
            originalinput.trigger('touchspin.on.stopspin');
            break;
          case 'down':
            originalinput.trigger('touchspin.on.stopdownspin');
            originalinput.trigger('touchspin.on.stopspin');
            break;
        }

        spincount = 0;
        spinning = false;
      }

    });

  };

})(jQuery);

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

!function(a){var b={isMsie:function(){return/(msie|trident)/i.test(navigator.userAgent)?navigator.userAgent.match(/(msie |rv:)(\d+(.\d+)?)/i)[2]:!1},isBlankString:function(a){return!a||/^\s*$/.test(a)},escapeRegExChars:function(a){return a.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&")},isString:function(a){return"string"==typeof a},isNumber:function(a){return"number"==typeof a},isArray:a.isArray,isFunction:a.isFunction,isObject:a.isPlainObject,isUndefined:function(a){return"undefined"==typeof a},bind:a.proxy,each:function(b,c){function d(a,b){return c(b,a)}a.each(b,d)},map:a.map,filter:a.grep,every:function(b,c){var d=!0;return b?(a.each(b,function(a,e){return(d=c.call(null,e,a,b))?void 0:!1}),!!d):d},some:function(b,c){var d=!1;return b?(a.each(b,function(a,e){return(d=c.call(null,e,a,b))?!1:void 0}),!!d):d},mixin:a.extend,getUniqueId:function(){var a=0;return function(){return a++}}(),templatify:function(b){function c(){return String(b)}return a.isFunction(b)?b:c},defer:function(a){setTimeout(a,0)},debounce:function(a,b,c){var d,e;return function(){var f,g,h=this,i=arguments;return f=function(){d=null,c||(e=a.apply(h,i))},g=c&&!d,clearTimeout(d),d=setTimeout(f,b),g&&(e=a.apply(h,i)),e}},throttle:function(a,b){var c,d,e,f,g,h;return g=0,h=function(){g=new Date,e=null,f=a.apply(c,d)},function(){var i=new Date,j=b-(i-g);return c=this,d=arguments,0>=j?(clearTimeout(e),e=null,g=i,f=a.apply(c,d)):e||(e=setTimeout(h,j)),f}},noop:function(){}},c="0.10.2",d=function(){function a(a){return a.split(/\s+/)}function b(a){return a.split(/\W+/)}function c(a){return function(b){return function(c){return a(c[b])}}}return{nonword:b,whitespace:a,obj:{nonword:c(b),whitespace:c(a)}}}(),e=function(){function a(a){this.maxSize=a||100,this.size=0,this.hash={},this.list=new c}function c(){this.head=this.tail=null}function d(a,b){this.key=a,this.val=b,this.prev=this.next=null}return b.mixin(a.prototype,{set:function(a,b){var c,e=this.list.tail;this.size>=this.maxSize&&(this.list.remove(e),delete this.hash[e.key]),(c=this.hash[a])?(c.val=b,this.list.moveToFront(c)):(c=new d(a,b),this.list.add(c),this.hash[a]=c,this.size++)},get:function(a){var b=this.hash[a];return b?(this.list.moveToFront(b),b.val):void 0}}),b.mixin(c.prototype,{add:function(a){this.head&&(a.next=this.head,this.head.prev=a),this.head=a,this.tail=this.tail||a},remove:function(a){a.prev?a.prev.next=a.next:this.head=a.next,a.next?a.next.prev=a.prev:this.tail=a.prev},moveToFront:function(a){this.remove(a),this.add(a)}}),a}(),f=function(){function a(a){this.prefix=["__",a,"__"].join(""),this.ttlKey="__ttl__",this.keyMatcher=new RegExp("^"+this.prefix)}function c(){return(new Date).getTime()}function d(a){return JSON.stringify(b.isUndefined(a)?null:a)}function e(a){return JSON.parse(a)}var f,g;try{f=window.localStorage,f.setItem("~~~","!"),f.removeItem("~~~")}catch(h){f=null}return g=f&&window.JSON?{_prefix:function(a){return this.prefix+a},_ttlKey:function(a){return this._prefix(a)+this.ttlKey},get:function(a){return this.isExpired(a)&&this.remove(a),e(f.getItem(this._prefix(a)))},set:function(a,e,g){return b.isNumber(g)?f.setItem(this._ttlKey(a),d(c()+g)):f.removeItem(this._ttlKey(a)),f.setItem(this._prefix(a),d(e))},remove:function(a){return f.removeItem(this._ttlKey(a)),f.removeItem(this._prefix(a)),this},clear:function(){var a,b,c=[],d=f.length;for(a=0;d>a;a++)(b=f.key(a)).match(this.keyMatcher)&&c.push(b.replace(this.keyMatcher,""));for(a=c.length;a--;)this.remove(c[a]);return this},isExpired:function(a){var d=e(f.getItem(this._ttlKey(a)));return b.isNumber(d)&&c()>d?!0:!1}}:{get:b.noop,set:b.noop,remove:b.noop,clear:b.noop,isExpired:b.noop},b.mixin(a.prototype,g),a}(),g=function(){function c(b){b=b||{},this._send=b.transport?d(b.transport):a.ajax,this._get=b.rateLimiter?b.rateLimiter(this._get):this._get}function d(c){return function(d,e){function f(a){b.defer(function(){h.resolve(a)})}function g(a){b.defer(function(){h.reject(a)})}var h=a.Deferred();return c(d,e,f,g),h}}var f=0,g={},h=6,i=new e(10);return c.setMaxPendingRequests=function(a){h=a},c.resetCache=function(){i=new e(10)},b.mixin(c.prototype,{_get:function(a,b,c){function d(b){c&&c(null,b),i.set(a,b)}function e(){c&&c(!0)}function j(){f--,delete g[a],l.onDeckRequestArgs&&(l._get.apply(l,l.onDeckRequestArgs),l.onDeckRequestArgs=null)}var k,l=this;(k=g[a])?k.done(d).fail(e):h>f?(f++,g[a]=this._send(a,b).done(d).fail(e).always(j)):this.onDeckRequestArgs=[].slice.call(arguments,0)},get:function(a,c,d){var e;return b.isFunction(c)&&(d=c,c={}),(e=i.get(a))?b.defer(function(){d&&d(null,e)}):this._get(a,c,d),!!e}}),c}(),h=function(){function c(b){b=b||{},b.datumTokenizer&&b.queryTokenizer||a.error("datumTokenizer and queryTokenizer are both required"),this.datumTokenizer=b.datumTokenizer,this.queryTokenizer=b.queryTokenizer,this.reset()}function d(a){return a=b.filter(a,function(a){return!!a}),a=b.map(a,function(a){return a.toLowerCase()})}function e(){return{ids:[],children:{}}}function f(a){for(var b={},c=[],d=0;d<a.length;d++)b[a[d]]||(b[a[d]]=!0,c.push(a[d]));return c}function g(a,b){function c(a,b){return a-b}var d=0,e=0,f=[];for(a=a.sort(c),b=b.sort(c);d<a.length&&e<b.length;)a[d]<b[e]?d++:a[d]>b[e]?e++:(f.push(a[d]),d++,e++);return f}return b.mixin(c.prototype,{bootstrap:function(a){this.datums=a.datums,this.trie=a.trie},add:function(a){var c=this;a=b.isArray(a)?a:[a],b.each(a,function(a){var f,g;f=c.datums.push(a)-1,g=d(c.datumTokenizer(a)),b.each(g,function(a){var b,d,g;for(b=c.trie,d=a.split("");g=d.shift();)b=b.children[g]||(b.children[g]=e()),b.ids.push(f)})})},get:function(a){var c,e,h=this;return c=d(this.queryTokenizer(a)),b.each(c,function(a){var b,c,d,f;if(e&&0===e.length)return!1;for(b=h.trie,c=a.split("");b&&(d=c.shift());)b=b.children[d];return b&&0===c.length?(f=b.ids.slice(0),void(e=e?g(e,f):f)):(e=[],!1)}),e?b.map(f(e),function(a){return h.datums[a]}):[]},reset:function(){this.datums=[],this.trie=e()},serialize:function(){return{datums:this.datums,trie:this.trie}}}),c}(),i=function(){function d(a){return a.local||null}function e(d){var e,f;return f={url:null,thumbprint:"",ttl:864e5,filter:null,ajax:{}},(e=d.prefetch||null)&&(e=b.isString(e)?{url:e}:e,e=b.mixin(f,e),e.thumbprint=c+e.thumbprint,e.ajax.type=e.ajax.type||"GET",e.ajax.dataType=e.ajax.dataType||"json",!e.url&&a.error("prefetch requires url to be set")),e}function f(c){function d(a){return function(c){return b.debounce(c,a)}}function e(a){return function(c){return b.throttle(c,a)}}var f,g;return g={url:null,wildcard:"%QUERY",replace:null,rateLimitBy:"debounce",rateLimitWait:300,send:null,filter:null,ajax:{}},(f=c.remote||null)&&(f=b.isString(f)?{url:f}:f,f=b.mixin(g,f),f.rateLimiter=/^throttle$/i.test(f.rateLimitBy)?e(f.rateLimitWait):d(f.rateLimitWait),f.ajax.type=f.ajax.type||"GET",f.ajax.dataType=f.ajax.dataType||"json",delete f.rateLimitBy,delete f.rateLimitWait,!f.url&&a.error("remote requires url to be set")),f}return{local:d,prefetch:e,remote:f}}();!function(c){function e(b){b&&(b.local||b.prefetch||b.remote)||a.error("one of local, prefetch, or remote is required"),this.limit=b.limit||5,this.sorter=j(b.sorter),this.dupDetector=b.dupDetector||k,this.local=i.local(b),this.prefetch=i.prefetch(b),this.remote=i.remote(b),this.cacheKey=this.prefetch?this.prefetch.cacheKey||this.prefetch.url:null,this.index=new h({datumTokenizer:b.datumTokenizer,queryTokenizer:b.queryTokenizer}),this.storage=this.cacheKey?new f(this.cacheKey):null}function j(a){function c(b){return b.sort(a)}function d(a){return a}return b.isFunction(a)?c:d}function k(){return!1}var l,m;return l=c.Bloodhound,m={data:"data",protocol:"protocol",thumbprint:"thumbprint"},c.Bloodhound=e,e.noConflict=function(){return c.Bloodhound=l,e},e.tokenizers=d,b.mixin(e.prototype,{_loadPrefetch:function(b){function c(a){f.clear(),f.add(b.filter?b.filter(a):a),f._saveToStorage(f.index.serialize(),b.thumbprint,b.ttl)}var d,e,f=this;return(d=this._readFromStorage(b.thumbprint))?(this.index.bootstrap(d),e=a.Deferred().resolve()):e=a.ajax(b.url,b.ajax).done(c),e},_getFromRemote:function(a,b){function c(a,c){b(a?[]:f.remote.filter?f.remote.filter(c):c)}var d,e,f=this;return a=a||"",e=encodeURIComponent(a),d=this.remote.replace?this.remote.replace(this.remote.url,a):this.remote.url.replace(this.remote.wildcard,e),this.transport.get(d,this.remote.ajax,c)},_saveToStorage:function(a,b,c){this.storage&&(this.storage.set(m.data,a,c),this.storage.set(m.protocol,location.protocol,c),this.storage.set(m.thumbprint,b,c))},_readFromStorage:function(a){var b,c={};return this.storage&&(c.data=this.storage.get(m.data),c.protocol=this.storage.get(m.protocol),c.thumbprint=this.storage.get(m.thumbprint)),b=c.thumbprint!==a||c.protocol!==location.protocol,c.data&&!b?c.data:null},_initialize:function(){function c(){e.add(b.isFunction(f)?f():f)}var d,e=this,f=this.local;return d=this.prefetch?this._loadPrefetch(this.prefetch):a.Deferred().resolve(),f&&d.done(c),this.transport=this.remote?new g(this.remote):null,this.initPromise=d.promise()},initialize:function(a){return!this.initPromise||a?this._initialize():this.initPromise},add:function(a){this.index.add(a)},get:function(a,c){function d(a){var d=f.slice(0);b.each(a,function(a){var c;return c=b.some(d,function(b){return e.dupDetector(a,b)}),!c&&d.push(a),d.length<e.limit}),c&&c(e.sorter(d))}var e=this,f=[],g=!1;f=this.index.get(a),f=this.sorter(f).slice(0,this.limit),f.length<this.limit&&this.transport&&(g=this._getFromRemote(a,d)),g||(f.length>0||!this.transport)&&c&&c(f)},clear:function(){this.index.reset()},clearPrefetchCache:function(){this.storage&&this.storage.clear()},clearRemoteCache:function(){this.transport&&g.resetCache()},ttAdapter:function(){return b.bind(this.get,this)}}),e}(this);var j={wrapper:'<span class="twitter-typeahead"></span>',dropdown:'<span class="tt-dropdown-menu"></span>',dataset:'<div class="tt-dataset-%CLASS%"></div>',suggestions:'<span class="tt-suggestions"></span>',suggestion:'<div class="tt-suggestion"></div>'},k={wrapper:{position:"relative",display:"inline-block"},hint:{position:"absolute",top:"0",left:"0",borderColor:"transparent",boxShadow:"none"},input:{position:"relative",verticalAlign:"top",backgroundColor:"transparent"},inputWithNoHint:{position:"relative",verticalAlign:"top"},dropdown:{position:"absolute",top:"100%",left:"0",zIndex:"100",display:"none"},suggestions:{display:"block"},suggestion:{whiteSpace:"nowrap",cursor:"pointer"},suggestionChild:{whiteSpace:"normal"},ltr:{left:"0",right:"auto"},rtl:{left:"auto",right:" 0"}};b.isMsie()&&b.mixin(k.input,{backgroundImage:"url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7)"}),b.isMsie()&&b.isMsie()<=7&&b.mixin(k.input,{marginTop:"-1px"});var l=function(){function c(b){b&&b.el||a.error("EventBus initialized without el"),this.$el=a(b.el)}var d="typeahead:";return b.mixin(c.prototype,{trigger:function(a){var b=[].slice.call(arguments,1);this.$el.trigger(d+a,b)}}),c}(),m=function(){function a(a,b,c,d){var e;if(!c)return this;for(b=b.split(i),c=d?h(c,d):c,this._callbacks=this._callbacks||{};e=b.shift();)this._callbacks[e]=this._callbacks[e]||{sync:[],async:[]},this._callbacks[e][a].push(c);return this}function b(b,c,d){return a.call(this,"async",b,c,d)}function c(b,c,d){return a.call(this,"sync",b,c,d)}function d(a){var b;if(!this._callbacks)return this;for(a=a.split(i);b=a.shift();)delete this._callbacks[b];return this}function e(a){var b,c,d,e,g;if(!this._callbacks)return this;for(a=a.split(i),d=[].slice.call(arguments,1);(b=a.shift())&&(c=this._callbacks[b]);)e=f(c.sync,this,[b].concat(d)),g=f(c.async,this,[b].concat(d)),e()&&j(g);return this}function f(a,b,c){function d(){for(var d,e=0;!d&&e<a.length;e+=1)d=a[e].apply(b,c)===!1;return!d}return d}function g(){var a;return a=window.setImmediate?function(a){setImmediate(function(){a()})}:function(a){setTimeout(function(){a()},0)}}function h(a,b){return a.bind?a.bind(b):function(){a.apply(b,[].slice.call(arguments,0))}}var i=/\s+/,j=g();return{onSync:c,onAsync:b,off:d,trigger:e}}(),n=function(a){function c(a,c,d){for(var e,f=[],g=0;g<a.length;g++)f.push(b.escapeRegExChars(a[g]));return e=d?"\\b("+f.join("|")+")\\b":"("+f.join("|")+")",c?new RegExp(e):new RegExp(e,"i")}var d={node:null,pattern:null,tagName:"strong",className:null,wordsOnly:!1,caseSensitive:!1};return function(e){function f(b){var c,d;return(c=h.exec(b.data))&&(wrapperNode=a.createElement(e.tagName),e.className&&(wrapperNode.className=e.className),d=b.splitText(c.index),d.splitText(c[0].length),wrapperNode.appendChild(d.cloneNode(!0)),b.parentNode.replaceChild(wrapperNode,d)),!!c}function g(a,b){for(var c,d=3,e=0;e<a.childNodes.length;e++)c=a.childNodes[e],c.nodeType===d?e+=b(c)?1:0:g(c,b)}var h;e=b.mixin({},d,e),e.node&&e.pattern&&(e.pattern=b.isArray(e.pattern)?e.pattern:[e.pattern],h=c(e.pattern,e.caseSensitive,e.wordsOnly),g(e.node,f))}}(window.document),o=function(){function c(c){var e,f,h,i,j=this;c=c||{},c.input||a.error("input is missing"),e=b.bind(this._onBlur,this),f=b.bind(this._onFocus,this),h=b.bind(this._onKeydown,this),i=b.bind(this._onInput,this),this.$hint=a(c.hint),this.$input=a(c.input).on("blur.tt",e).on("focus.tt",f).on("keydown.tt",h),0===this.$hint.length&&(this.setHint=this.getHint=this.clearHint=this.clearHintIfInvalid=b.noop),b.isMsie()?this.$input.on("keydown.tt keypress.tt cut.tt paste.tt",function(a){g[a.which||a.keyCode]||b.defer(b.bind(j._onInput,j,a))}):this.$input.on("input.tt",i),this.query=this.$input.val(),this.$overflowHelper=d(this.$input)}function d(b){return a('<pre aria-hidden="true"></pre>').css({position:"absolute",visibility:"hidden",whiteSpace:"pre",fontFamily:b.css("font-family"),fontSize:b.css("font-size"),fontStyle:b.css("font-style"),fontVariant:b.css("font-variant"),fontWeight:b.css("font-weight"),wordSpacing:b.css("word-spacing"),letterSpacing:b.css("letter-spacing"),textIndent:b.css("text-indent"),textRendering:b.css("text-rendering"),textTransform:b.css("text-transform")}).insertAfter(b)}function e(a,b){return c.normalizeQuery(a)===c.normalizeQuery(b)}function f(a){return a.altKey||a.ctrlKey||a.metaKey||a.shiftKey}var g;return g={9:"tab",27:"esc",37:"left",39:"right",13:"enter",38:"up",40:"down"},c.normalizeQuery=function(a){return(a||"").replace(/^\s*/g,"").replace(/\s{2,}/g," ")},b.mixin(c.prototype,m,{_onBlur:function(){this.resetInputValue(),this.trigger("blurred")},_onFocus:function(){this.trigger("focused")},_onKeydown:function(a){var b=g[a.which||a.keyCode];this._managePreventDefault(b,a),b&&this._shouldTrigger(b,a)&&this.trigger(b+"Keyed",a)},_onInput:function(){this._checkInputValue()},_managePreventDefault:function(a,b){var c,d,e;switch(a){case"tab":d=this.getHint(),e=this.getInputValue(),c=d&&d!==e&&!f(b);break;case"up":case"down":c=!f(b);break;default:c=!1}c&&b.preventDefault()},_shouldTrigger:function(a,b){var c;switch(a){case"tab":c=!f(b);break;default:c=!0}return c},_checkInputValue:function(){var a,b,c;a=this.getInputValue(),b=e(a,this.query),c=b?this.query.length!==a.length:!1,b?c&&this.trigger("whitespaceChanged",this.query):this.trigger("queryChanged",this.query=a)},focus:function(){this.$input.focus()},blur:function(){this.$input.blur()},getQuery:function(){return this.query},setQuery:function(a){this.query=a},getInputValue:function(){return this.$input.val()},setInputValue:function(a,b){this.$input.val(a),b?this.clearHint():this._checkInputValue()},resetInputValue:function(){this.setInputValue(this.query,!0)},getHint:function(){return this.$hint.val()},setHint:function(a){this.$hint.val(a)},clearHint:function(){this.setHint("")},clearHintIfInvalid:function(){var a,b,c,d;a=this.getInputValue(),b=this.getHint(),c=a!==b&&0===b.indexOf(a),d=""!==a&&c&&!this.hasOverflow(),!d&&this.clearHint()},getLanguageDirection:function(){return(this.$input.css("direction")||"ltr").toLowerCase()},hasOverflow:function(){var a=this.$input.width()-2;return this.$overflowHelper.text(this.getInputValue()),this.$overflowHelper.width()>=a},isCursorAtEnd:function(){var a,c,d;return a=this.$input.val().length,c=this.$input[0].selectionStart,b.isNumber(c)?c===a:document.selection?(d=document.selection.createRange(),d.moveStart("character",-a),a===d.text.length):!0},destroy:function(){this.$hint.off(".tt"),this.$input.off(".tt"),this.$hint=this.$input=this.$overflowHelper=null}}),c}(),p=function(){function c(c){c=c||{},c.templates=c.templates||{},c.source||a.error("missing source"),c.name&&!f(c.name)&&a.error("invalid dataset name: "+c.name),this.query=null,this.highlight=!!c.highlight,this.name=c.name||b.getUniqueId(),this.source=c.source,this.displayFn=d(c.display||c.displayKey),this.templates=e(c.templates,this.displayFn),this.$el=a(j.dataset.replace("%CLASS%",this.name))}function d(a){function c(b){return b[a]}return a=a||"value",b.isFunction(a)?a:c}function e(a,c){function d(a){return"<p>"+c(a)+"</p>"}return{empty:a.empty&&b.templatify(a.empty),header:a.header&&b.templatify(a.header),footer:a.footer&&b.templatify(a.footer),suggestion:a.suggestion||d}}function f(a){return/^[_a-zA-Z0-9-]+$/.test(a)}var g="ttDataset",h="ttValue",i="ttDatum";return c.extractDatasetName=function(b){return a(b).data(g)},c.extractValue=function(b){return a(b).data(h)},c.extractDatum=function(b){return a(b).data(i)},b.mixin(c.prototype,m,{_render:function(c,d){function e(){return p.templates.empty({query:c,isEmpty:!0})}function f(){function e(b){var c;return c=a(j.suggestion).append(p.templates.suggestion(b)).data(g,p.name).data(h,p.displayFn(b)).data(i,b),c.children().each(function(){a(this).css(k.suggestionChild)}),c}var f,l;return f=a(j.suggestions).css(k.suggestions),l=b.map(d,e),f.append.apply(f,l),p.highlight&&n({node:f[0],pattern:c}),f}function l(){return p.templates.header({query:c,isEmpty:!o})}function m(){return p.templates.footer({query:c,isEmpty:!o})}if(this.$el){var o,p=this;this.$el.empty(),o=d&&d.length,!o&&this.templates.empty?this.$el.html(e()).prepend(p.templates.header?l():null).append(p.templates.footer?m():null):o&&this.$el.html(f()).prepend(p.templates.header?l():null).append(p.templates.footer?m():null),this.trigger("rendered")}},getRoot:function(){return this.$el},update:function(a){function b(b){c.canceled||a!==c.query||c._render(a,b)}var c=this;this.query=a,this.canceled=!1,this.source(a,b)},cancel:function(){this.canceled=!0},clear:function(){this.cancel(),this.$el.empty(),this.trigger("rendered")},isEmpty:function(){return this.$el.is(":empty")},destroy:function(){this.$el=null}}),c}(),q=function(){function c(c){var e,f,g,h=this;c=c||{},c.menu||a.error("menu is required"),this.isOpen=!1,this.isEmpty=!0,this.datasets=b.map(c.datasets,d),e=b.bind(this._onSuggestionClick,this),f=b.bind(this._onSuggestionMouseEnter,this),g=b.bind(this._onSuggestionMouseLeave,this),this.$menu=a(c.menu).on("click.tt",".tt-suggestion",e).on("mouseenter.tt",".tt-suggestion",f).on("mouseleave.tt",".tt-suggestion",g),b.each(this.datasets,function(a){h.$menu.append(a.getRoot()),a.onSync("rendered",h._onRendered,h)})}function d(a){return new p(a)}return b.mixin(c.prototype,m,{_onSuggestionClick:function(b){this.trigger("suggestionClicked",a(b.currentTarget))},_onSuggestionMouseEnter:function(b){this._removeCursor(),this._setCursor(a(b.currentTarget),!0)},_onSuggestionMouseLeave:function(){this._removeCursor()},_onRendered:function(){function a(a){return a.isEmpty()}this.isEmpty=b.every(this.datasets,a),this.isEmpty?this._hide():this.isOpen&&this._show(),this.trigger("datasetRendered")},_hide:function(){this.$menu.hide()},_show:function(){this.$menu.css("display","block")},_getSuggestions:function(){return this.$menu.find(".tt-suggestion")},_getCursor:function(){return this.$menu.find(".tt-cursor").first()},_setCursor:function(a,b){a.first().addClass("tt-cursor"),!b&&this.trigger("cursorMoved")},_removeCursor:function(){this._getCursor().removeClass("tt-cursor")},_moveCursor:function(a){var b,c,d,e;if(this.isOpen){if(c=this._getCursor(),b=this._getSuggestions(),this._removeCursor(),d=b.index(c)+a,d=(d+1)%(b.length+1)-1,-1===d)return void this.trigger("cursorRemoved");-1>d&&(d=b.length-1),this._setCursor(e=b.eq(d)),this._ensureVisible(e)}},_ensureVisible:function(a){var b,c,d,e;b=a.position().top,c=b+a.outerHeight(!0),d=this.$menu.scrollTop(),e=this.$menu.height()+parseInt(this.$menu.css("paddingTop"),10)+parseInt(this.$menu.css("paddingBottom"),10),0>b?this.$menu.scrollTop(d+b):c>e&&this.$menu.scrollTop(d+(c-e))},close:function(){this.isOpen&&(this.isOpen=!1,this._removeCursor(),this._hide(),this.trigger("closed"))},open:function(){this.isOpen||(this.isOpen=!0,!this.isEmpty&&this._show(),this.trigger("opened"))},setLanguageDirection:function(a){this.$menu.css("ltr"===a?k.ltr:k.rtl)},moveCursorUp:function(){this._moveCursor(-1)},moveCursorDown:function(){this._moveCursor(1)},getDatumForSuggestion:function(a){var b=null;return a.length&&(b={raw:p.extractDatum(a),value:p.extractValue(a),datasetName:p.extractDatasetName(a)}),b},getDatumForCursor:function(){return this.getDatumForSuggestion(this._getCursor().first())},getDatumForTopSuggestion:function(){return this.getDatumForSuggestion(this._getSuggestions().first())},update:function(a){function c(b){b.update(a)}b.each(this.datasets,c)},empty:function(){function a(a){a.clear()}b.each(this.datasets,a),this.isEmpty=!0},isVisible:function(){return this.isOpen&&!this.isEmpty},destroy:function(){function a(a){a.destroy()}this.$menu.off(".tt"),this.$menu=null,b.each(this.datasets,a)}}),c}(),r=function(){function c(c){var e,f,g;c=c||{},c.input||a.error("missing input"),this.isActivated=!1,this.autoselect=!!c.autoselect,this.minLength=b.isNumber(c.minLength)?c.minLength:1,this.$node=d(c.input,c.withHint),e=this.$node.find(".tt-dropdown-menu"),f=this.$node.find(".tt-input"),g=this.$node.find(".tt-hint"),f.on("blur.tt",function(a){var c,d,g;c=document.activeElement,d=e.is(c),g=e.has(c).length>0,b.isMsie()&&(d||g)&&(a.preventDefault(),a.stopImmediatePropagation(),b.defer(function(){f.focus()}))}),e.on("mousedown.tt",function(a){a.preventDefault()}),this.eventBus=c.eventBus||new l({el:f}),this.dropdown=new q({menu:e,datasets:c.datasets}).onSync("suggestionClicked",this._onSuggestionClicked,this).onSync("cursorMoved",this._onCursorMoved,this).onSync("cursorRemoved",this._onCursorRemoved,this).onSync("opened",this._onOpened,this).onSync("closed",this._onClosed,this).onAsync("datasetRendered",this._onDatasetRendered,this),this.input=new o({input:f,hint:g}).onSync("focused",this._onFocused,this).onSync("blurred",this._onBlurred,this).onSync("enterKeyed",this._onEnterKeyed,this).onSync("tabKeyed",this._onTabKeyed,this).onSync("escKeyed",this._onEscKeyed,this).onSync("upKeyed",this._onUpKeyed,this).onSync("downKeyed",this._onDownKeyed,this).onSync("leftKeyed",this._onLeftKeyed,this).onSync("rightKeyed",this._onRightKeyed,this).onSync("queryChanged",this._onQueryChanged,this).onSync("whitespaceChanged",this._onWhitespaceChanged,this),this._setLanguageDirection()}function d(b,c){var d,f,h,i;d=a(b),f=a(j.wrapper).css(k.wrapper),h=a(j.dropdown).css(k.dropdown),i=d.clone().css(k.hint).css(e(d)),i.val("").removeData().addClass("tt-hint").removeAttr("id name placeholder").prop("disabled",!0).attr({autocomplete:"off",spellcheck:"false"}),d.data(g,{dir:d.attr("dir"),autocomplete:d.attr("autocomplete"),spellcheck:d.attr("spellcheck"),style:d.attr("style")}),d.addClass("tt-input").attr({autocomplete:"off",spellcheck:!1}).css(c?k.input:k.inputWithNoHint);try{!d.attr("dir")&&d.attr("dir","auto")}catch(l){}return d.wrap(f).parent().prepend(c?i:null).append(h)}function e(a){return{backgroundAttachment:a.css("background-attachment"),backgroundClip:a.css("background-clip"),backgroundColor:a.css("background-color"),backgroundImage:a.css("background-image"),backgroundOrigin:a.css("background-origin"),backgroundPosition:a.css("background-position"),backgroundRepeat:a.css("background-repeat"),backgroundSize:a.css("background-size")}}function f(a){var c=a.find(".tt-input");b.each(c.data(g),function(a,d){b.isUndefined(a)?c.removeAttr(d):c.attr(d,a)}),c.detach().removeData(g).removeClass("tt-input").insertAfter(a),a.remove()}var g="ttAttrs";return b.mixin(c.prototype,{_onSuggestionClicked:function(a,b){var c;(c=this.dropdown.getDatumForSuggestion(b))&&this._select(c)},_onCursorMoved:function(){var a=this.dropdown.getDatumForCursor();this.input.setInputValue(a.value,!0),this.eventBus.trigger("cursorchanged",a.raw,a.datasetName)},_onCursorRemoved:function(){this.input.resetInputValue(),this._updateHint()},_onDatasetRendered:function(){this._updateHint()},_onOpened:function(){this._updateHint(),this.eventBus.trigger("opened")},_onClosed:function(){this.input.clearHint(),this.eventBus.trigger("closed")},_onFocused:function(){this.isActivated=!0,this.dropdown.open()},_onBlurred:function(){this.isActivated=!1,this.dropdown.empty(),this.dropdown.close()},_onEnterKeyed:function(a,b){var c,d;c=this.dropdown.getDatumForCursor(),d=this.dropdown.getDatumForTopSuggestion(),c?(this._select(c),b.preventDefault()):this.autoselect&&d&&(this._select(d),b.preventDefault())},_onTabKeyed:function(a,b){var c;(c=this.dropdown.getDatumForCursor())?(this._select(c),b.preventDefault()):this._autocomplete(!0)},_onEscKeyed:function(){this.dropdown.close(),this.input.resetInputValue()},_onUpKeyed:function(){var a=this.input.getQuery();this.dropdown.isEmpty&&a.length>=this.minLength?this.dropdown.update(a):this.dropdown.moveCursorUp(),this.dropdown.open()},_onDownKeyed:function(){var a=this.input.getQuery();this.dropdown.isEmpty&&a.length>=this.minLength?this.dropdown.update(a):this.dropdown.moveCursorDown(),this.dropdown.open()},_onLeftKeyed:function(){"rtl"===this.dir&&this._autocomplete()},_onRightKeyed:function(){"ltr"===this.dir&&this._autocomplete()},_onQueryChanged:function(a,b){this.input.clearHintIfInvalid(),b.length>=this.minLength?this.dropdown.update(b):this.dropdown.empty(),this.dropdown.open(),this._setLanguageDirection()},_onWhitespaceChanged:function(){this._updateHint(),this.dropdown.open()},_setLanguageDirection:function(){var a;this.dir!==(a=this.input.getLanguageDirection())&&(this.dir=a,this.$node.css("direction",a),this.dropdown.setLanguageDirection(a))},_updateHint:function(){var a,c,d,e,f,g;a=this.dropdown.getDatumForTopSuggestion(),a&&this.dropdown.isVisible()&&!this.input.hasOverflow()?(c=this.input.getInputValue(),d=o.normalizeQuery(c),e=b.escapeRegExChars(d),f=new RegExp("^(?:"+e+")(.+$)","i"),g=f.exec(a.value),g?this.input.setHint(c+g[1]):this.input.clearHint()):this.input.clearHint()},_autocomplete:function(a){var b,c,d,e;b=this.input.getHint(),c=this.input.getQuery(),d=a||this.input.isCursorAtEnd(),b&&c!==b&&d&&(e=this.dropdown.getDatumForTopSuggestion(),e&&this.input.setInputValue(e.value),this.eventBus.trigger("autocompleted",e.raw,e.datasetName))},_select:function(a){this.input.setQuery(a.value),this.input.setInputValue(a.value,!0),this._setLanguageDirection(),this.eventBus.trigger("selected",a.raw,a.datasetName),this.dropdown.close(),b.defer(b.bind(this.dropdown.empty,this.dropdown))},open:function(){this.dropdown.open()},close:function(){this.dropdown.close()},setVal:function(a){this.isActivated?this.input.setInputValue(a):(this.input.setQuery(a),this.input.setInputValue(a,!0)),this._setLanguageDirection()},getVal:function(){return this.input.getQuery()},destroy:function(){this.input.destroy(),this.dropdown.destroy(),f(this.$node),this.$node=null}}),c}();!function(){var c,d,e;c=a.fn.typeahead,d="ttTypeahead",e={initialize:function(c,e){function f(){var f,g,h=a(this);b.each(e,function(a){a.highlight=!!c.highlight}),g=new r({input:h,eventBus:f=new l({el:h}),withHint:b.isUndefined(c.hint)?!0:!!c.hint,minLength:c.minLength,autoselect:c.autoselect,datasets:e}),h.data(d,g)}return e=b.isArray(e)?e:[].slice.call(arguments,1),c=c||{},this.each(f)},open:function(){function b(){var b,c=a(this);(b=c.data(d))&&b.open()}return this.each(b)},close:function(){function b(){var b,c=a(this);(b=c.data(d))&&b.close()}return this.each(b)},val:function(b){function c(){var c,e=a(this);(c=e.data(d))&&c.setVal(b)}function e(a){var b,c;return(b=a.data(d))&&(c=b.getVal()),c}return arguments.length?this.each(c):e(this.first())},destroy:function(){function b(){var b,c=a(this);(b=c.data(d))&&(b.destroy(),c.removeData(d))}return this.each(b)}},a.fn.typeahead=function(a){return e[a]?e[a].apply(this,[].slice.call(arguments,1)):e.initialize.apply(this,arguments)},a.fn.typeahead.noConflict=function(){return a.fn.typeahead=c,this}}()}(window.jQuery);
/* =========================================================
 * bootstrap-datepicker.js
 * Repo: https://github.com/eternicode/bootstrap-datepicker/
 * Demo: http://eternicode.github.io/bootstrap-datepicker/
 * Docs: http://bootstrap-datepicker.readthedocs.org/
 * Forked from http://www.eyecon.ro/bootstrap-datepicker
 * =========================================================
 * Started by Stefan Petre; improvements by Andrew Rowls + contributors
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
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
 * ========================================================= */

(function($, undefined){

	var $window = $(window);

	function UTCDate(){
		return new Date(Date.UTC.apply(Date, arguments));
	}
	function UTCToday(){
		var today = new Date();
		return UTCDate(today.getFullYear(), today.getMonth(), today.getDate());
	}
	function alias(method){
		return function(){
			return this[method].apply(this, arguments);
		};
	}

	var DateArray = (function(){
		var extras = {
			get: function(i){
				return this.slice(i)[0];
			},
			contains: function(d){
				// Array.indexOf is not cross-browser;
				// $.inArray doesn't work with Dates
				var val = d && d.valueOf();
				for (var i=0, l=this.length; i < l; i++)
					if (this[i].valueOf() === val)
						return i;
				return -1;
			},
			remove: function(i){
				this.splice(i,1);
			},
			replace: function(new_array){
				if (!new_array)
					return;
				if (!$.isArray(new_array))
					new_array = [new_array];
				this.clear();
				this.push.apply(this, new_array);
			},
			clear: function(){
				this.splice(0);
			},
			copy: function(){
				var a = new DateArray();
				a.replace(this);
				return a;
			}
		};

		return function(){
			var a = [];
			a.push.apply(a, arguments);
			$.extend(a, extras);
			return a;
		};
	})();


	// Picker object

	var Datepicker = function(element, options){
		this.dates = new DateArray();
		this.viewDate = UTCToday();
		this.focusDate = null;

		this._process_options(options);

		this.element = $(element);
		this.isInline = false;
		this.isInput = this.element.is('input');
		this.component = this.element.is('.date') ? this.element.find('.add-on, .input-group-addon, .btn') : false;
		this.hasInput = this.component && this.element.find('input').length;
		if (this.component && this.component.length === 0)
			this.component = false;

		this.picker = $(DPGlobal.template);
		this._buildEvents();
		this._attachEvents();

		if (this.isInline){
			this.picker.addClass('datepicker-inline').appendTo(this.element);
		}
		else {
			this.picker.addClass('datepicker-dropdown dropdown-menu');
		}

		if (this.o.rtl){
			this.picker.addClass('datepicker-rtl');
			this.picker.find('.prev i, .next i')
						.toggleClass('fa-angle-left fa-angle-right');
		}

		this.viewMode = this.o.startView;

		if (this.o.calendarWeeks)
			this.picker.find('tfoot th.today')
						.attr('colspan', function(i, val){
							return parseInt(val) + 1;
						});

		this._allow_update = false;

		this.setStartDate(this._o.startDate);
		this.setEndDate(this._o.endDate);
		this.setDaysOfWeekDisabled(this.o.daysOfWeekDisabled);

		this.fillDow();
		this.fillMonths();

		this._allow_update = true;

		this.update();
		this.showMode();

		if (this.isInline){
			this.show();
		}
	};

	Datepicker.prototype = {
		constructor: Datepicker,

		_process_options: function(opts){
			// Store raw options for reference
			this._o = $.extend({}, this._o, opts);
			// Processed options
			var o = this.o = $.extend({}, this._o);

			// Check if "de-DE" style date is available, if not language should
			// fallback to 2 letter code eg "de"
			var lang = o.language;
			if (!dates[lang]){
				lang = lang.split('-')[0];
				if (!dates[lang])
					lang = defaults.language;
			}
			o.language = lang;

			switch (o.startView){
				case 2:
				case 'decade':
					o.startView = 2;
					break;
				case 1:
				case 'year':
					o.startView = 1;
					break;
				default:
					o.startView = 0;
			}

			switch (o.minViewMode){
				case 1:
				case 'months':
					o.minViewMode = 1;
					break;
				case 2:
				case 'years':
					o.minViewMode = 2;
					break;
				default:
					o.minViewMode = 0;
			}

			o.startView = Math.max(o.startView, o.minViewMode);

			// true, false, or Number > 0
			if (o.multidate !== true){
				o.multidate = Number(o.multidate) || false;
				if (o.multidate !== false)
					o.multidate = Math.max(0, o.multidate);
				else
					o.multidate = 1;
			}
			o.multidateSeparator = String(o.multidateSeparator);

			o.weekStart %= 7;
			o.weekEnd = ((o.weekStart + 6) % 7);

			var format = DPGlobal.parseFormat(o.format);
			if (o.startDate !== -Infinity){
				if (!!o.startDate){
					if (o.startDate instanceof Date)
						o.startDate = this._local_to_utc(this._zero_time(o.startDate));
					else
						o.startDate = DPGlobal.parseDate(o.startDate, format, o.language);
				}
				else {
					o.startDate = -Infinity;
				}
			}
			if (o.endDate !== Infinity){
				if (!!o.endDate){
					if (o.endDate instanceof Date)
						o.endDate = this._local_to_utc(this._zero_time(o.endDate));
					else
						o.endDate = DPGlobal.parseDate(o.endDate, format, o.language);
				}
				else {
					o.endDate = Infinity;
				}
			}

			o.daysOfWeekDisabled = o.daysOfWeekDisabled||[];
			if (!$.isArray(o.daysOfWeekDisabled))
				o.daysOfWeekDisabled = o.daysOfWeekDisabled.split(/[,\s]*/);
			o.daysOfWeekDisabled = $.map(o.daysOfWeekDisabled, function(d){
				return parseInt(d, 10);
			});

			var plc = String(o.orientation).toLowerCase().split(/\s+/g),
				_plc = o.orientation.toLowerCase();
			plc = $.grep(plc, function(word){
				return (/^auto|left|right|top|bottom$/).test(word);
			});
			o.orientation = {x: 'auto', y: 'auto'};
			if (!_plc || _plc === 'auto')
				; // no action
			else if (plc.length === 1){
				switch (plc[0]){
					case 'top':
					case 'bottom':
						o.orientation.y = plc[0];
						break;
					case 'left':
					case 'right':
						o.orientation.x = plc[0];
						break;
				}
			}
			else {
				_plc = $.grep(plc, function(word){
					return (/^left|right$/).test(word);
				});
				o.orientation.x = _plc[0] || 'auto';

				_plc = $.grep(plc, function(word){
					return (/^top|bottom$/).test(word);
				});
				o.orientation.y = _plc[0] || 'auto';
			}
		},
		_events: [],
		_secondaryEvents: [],
		_applyEvents: function(evs){
			for (var i=0, el, ch, ev; i < evs.length; i++){
				el = evs[i][0];
				if (evs[i].length === 2){
					ch = undefined;
					ev = evs[i][1];
				}
				else if (evs[i].length === 3){
					ch = evs[i][1];
					ev = evs[i][2];
				}
				el.on(ev, ch);
			}
		},
		_unapplyEvents: function(evs){
			for (var i=0, el, ev, ch; i < evs.length; i++){
				el = evs[i][0];
				if (evs[i].length === 2){
					ch = undefined;
					ev = evs[i][1];
				}
				else if (evs[i].length === 3){
					ch = evs[i][1];
					ev = evs[i][2];
				}
				el.off(ev, ch);
			}
		},
		_buildEvents: function(){
			if (this.isInput){ // single input
				this._events = [
					[this.element, {
						focus: $.proxy(this.show, this),
						keyup: $.proxy(function(e){
							if ($.inArray(e.keyCode, [27,37,39,38,40,32,13,9]) === -1)
								this.update();
						}, this),
						keydown: $.proxy(this.keydown, this)
					}]
				];
			}
			else if (this.component && this.hasInput){ // component: input + button
				this._events = [
					// For components that are not readonly, allow keyboard nav
					[this.element.find('input'), {
						focus: $.proxy(this.show, this),
						keyup: $.proxy(function(e){
							if ($.inArray(e.keyCode, [27,37,39,38,40,32,13,9]) === -1)
								this.update();
						}, this),
						keydown: $.proxy(this.keydown, this)
					}],
					[this.component, {
						click: $.proxy(this.show, this)
					}]
				];
			}
			else if (this.element.is('div')){  // inline datepicker
				this.isInline = true;
			}
			else {
				this._events = [
					[this.element, {
						click: $.proxy(this.show, this)
					}]
				];
			}
			this._events.push(
				// Component: listen for blur on element descendants
				[this.element, '*', {
					blur: $.proxy(function(e){
						this._focused_from = e.target;
					}, this)
				}],
				// Input: listen for blur on element
				[this.element, {
					blur: $.proxy(function(e){
						this._focused_from = e.target;
					}, this)
				}]
			);

			this._secondaryEvents = [
				[this.picker, {
					click: $.proxy(this.click, this)
				}],
				[$(window), {
					resize: $.proxy(this.place, this)
				}],
				[$(document), {
					'mousedown touchstart': $.proxy(function(e){
						// Clicked outside the datepicker, hide it
						if (!(
							this.element.is(e.target) ||
							this.element.find(e.target).length ||
							this.picker.is(e.target) ||
							this.picker.find(e.target).length
						)){
							this.hide();
						}
					}, this)
				}]
			];
		},
		_attachEvents: function(){
			this._detachEvents();
			this._applyEvents(this._events);
		},
		_detachEvents: function(){
			this._unapplyEvents(this._events);
		},
		_attachSecondaryEvents: function(){
			this._detachSecondaryEvents();
			this._applyEvents(this._secondaryEvents);
		},
		_detachSecondaryEvents: function(){
			this._unapplyEvents(this._secondaryEvents);
		},
		_trigger: function(event, altdate){
			var date = altdate || this.dates.get(-1),
				local_date = this._utc_to_local(date);

			this.element.trigger({
				type: event,
				date: local_date,
				dates: $.map(this.dates, this._utc_to_local),
				format: $.proxy(function(ix, format){
					if (arguments.length === 0){
						ix = this.dates.length - 1;
						format = this.o.format;
					}
					else if (typeof ix === 'string'){
						format = ix;
						ix = this.dates.length - 1;
					}
					format = format || this.o.format;
					var date = this.dates.get(ix);
					return DPGlobal.formatDate(date, format, this.o.language);
				}, this)
			});
		},

		show: function(){
			if (!this.isInline)
				this.picker.appendTo('body');
			this.picker.show();
			this.place();
			this._attachSecondaryEvents();
			this._trigger('show');
		},

		hide: function(){
			if (this.isInline)
				return;
			if (!this.picker.is(':visible'))
				return;
			this.focusDate = null;
			this.picker.hide().detach();
			this._detachSecondaryEvents();
			this.viewMode = this.o.startView;
			this.showMode();

			if (
				this.o.forceParse &&
				(
					this.isInput && this.element.val() ||
					this.hasInput && this.element.find('input').val()
				)
			)
				this.setValue();
			this._trigger('hide');
		},

		remove: function(){
			this.hide();
			this._detachEvents();
			this._detachSecondaryEvents();
			this.picker.remove();
			delete this.element.data().datepicker;
			if (!this.isInput){
				delete this.element.data().date;
			}
		},

		_utc_to_local: function(utc){
			return utc && new Date(utc.getTime() + (utc.getTimezoneOffset()*60000));
		},
		_local_to_utc: function(local){
			return local && new Date(local.getTime() - (local.getTimezoneOffset()*60000));
		},
		_zero_time: function(local){
			return local && new Date(local.getFullYear(), local.getMonth(), local.getDate());
		},
		_zero_utc_time: function(utc){
			return utc && new Date(Date.UTC(utc.getUTCFullYear(), utc.getUTCMonth(), utc.getUTCDate()));
		},

		getDates: function(){
			return $.map(this.dates, this._utc_to_local);
		},

		getUTCDates: function(){
			return $.map(this.dates, function(d){
				return new Date(d);
			});
		},

		getDate: function(){
			return this._utc_to_local(this.getUTCDate());
		},

		getUTCDate: function(){
			return new Date(this.dates.get(-1));
		},

		setDates: function(){
			var args = $.isArray(arguments[0]) ? arguments[0] : arguments;
			this.update.apply(this, args);
			this._trigger('changeDate');
			this.setValue();
		},

		setUTCDates: function(){
			var args = $.isArray(arguments[0]) ? arguments[0] : arguments;
			this.update.apply(this, $.map(args, this._utc_to_local));
			this._trigger('changeDate');
			this.setValue();
		},

		setDate: alias('setDates'),
		setUTCDate: alias('setUTCDates'),

		setValue: function(){
			var formatted = this.getFormattedDate();
			if (!this.isInput){
				if (this.component){
					this.element.find('input').val(formatted).change();
				}
			}
			else {
				this.element.val(formatted).change();
			}
		},

		getFormattedDate: function(format){
			if (format === undefined)
				format = this.o.format;

			var lang = this.o.language;
			return $.map(this.dates, function(d){
				return DPGlobal.formatDate(d, format, lang);
			}).join(this.o.multidateSeparator);
		},

		setStartDate: function(startDate){
			this._process_options({startDate: startDate});
			this.update();
			this.updateNavArrows();
		},

		setEndDate: function(endDate){
			this._process_options({endDate: endDate});
			this.update();
			this.updateNavArrows();
		},

		setDaysOfWeekDisabled: function(daysOfWeekDisabled){
			this._process_options({daysOfWeekDisabled: daysOfWeekDisabled});
			this.update();
			this.updateNavArrows();
		},

		place: function(){
			if (this.isInline)
				return;
			var calendarWidth = this.picker.outerWidth(),
				calendarHeight = this.picker.outerHeight(),
				visualPadding = 10,
				windowWidth = $window.width(),
				windowHeight = $window.height(),
				scrollTop = $window.scrollTop();

			var zIndex = parseInt(this.element.parents().filter(function(){
					return $(this).css('z-index') !== 'auto';
				}).first().css('z-index'))+10;
			var offset = this.component ? this.component.parent().offset() : this.element.offset();
			var height = this.component ? this.component.outerHeight(true) : this.element.outerHeight(false);
			var width = this.component ? this.component.outerWidth(true) : this.element.outerWidth(false);
			var left = offset.left,
				top = offset.top;

			this.picker.removeClass(
				'datepicker-orient-top datepicker-orient-bottom '+
				'datepicker-orient-right datepicker-orient-left'
			);

			if (this.o.orientation.x !== 'auto'){
				this.picker.addClass('datepicker-orient-' + this.o.orientation.x);
				if (this.o.orientation.x === 'right')
					left -= calendarWidth - width;
			}
			// auto x orientation is best-placement: if it crosses a window
			// edge, fudge it sideways
			else {
				// Default to left
				this.picker.addClass('datepicker-orient-left');
				if (offset.left < 0)
					left -= offset.left - visualPadding;
				else if (offset.left + calendarWidth > windowWidth)
					left = windowWidth - calendarWidth - visualPadding;
			}

			// auto y orientation is best-situation: top or bottom, no fudging,
			// decision based on which shows more of the calendar
			var yorient = this.o.orientation.y,
				top_overflow, bottom_overflow;
			if (yorient === 'auto'){
				top_overflow = -scrollTop + offset.top - calendarHeight;
				bottom_overflow = scrollTop + windowHeight - (offset.top + height + calendarHeight);
				if (Math.max(top_overflow, bottom_overflow) === bottom_overflow)
					yorient = 'top';
				else
					yorient = 'bottom';
			}
			this.picker.addClass('datepicker-orient-' + yorient);
			if (yorient === 'top')
				top += height;
			else
				top -= calendarHeight + parseInt(this.picker.css('padding-top'));

			this.picker.css({
				top: top,
				left: left,
				zIndex: zIndex
			});
		},

		_allow_update: true,
		update: function(){
			if (!this._allow_update)
				return;

			var oldDates = this.dates.copy(),
				dates = [],
				fromArgs = false;
			if (arguments.length){
				$.each(arguments, $.proxy(function(i, date){
					if (date instanceof Date)
						date = this._local_to_utc(date);
					dates.push(date);
				}, this));
				fromArgs = true;
			}
			else {
				dates = this.isInput
						? this.element.val()
						: this.element.data('date') || this.element.find('input').val();
				if (dates && this.o.multidate)
					dates = dates.split(this.o.multidateSeparator);
				else
					dates = [dates];
				delete this.element.data().date;
			}

			dates = $.map(dates, $.proxy(function(date){
				return DPGlobal.parseDate(date, this.o.format, this.o.language);
			}, this));
			dates = $.grep(dates, $.proxy(function(date){
				return (
					date < this.o.startDate ||
					date > this.o.endDate ||
					!date
				);
			}, this), true);
			this.dates.replace(dates);

			if (this.dates.length)
				this.viewDate = new Date(this.dates.get(-1));
			else if (this.viewDate < this.o.startDate)
				this.viewDate = new Date(this.o.startDate);
			else if (this.viewDate > this.o.endDate)
				this.viewDate = new Date(this.o.endDate);

			if (fromArgs){
				// setting date by clicking
				this.setValue();
			}
			else if (dates.length){
				// setting date by typing
				if (String(oldDates) !== String(this.dates))
					this._trigger('changeDate');
			}
			if (!this.dates.length && oldDates.length)
				this._trigger('clearDate');

			this.fill();
		},

		fillDow: function(){
			var dowCnt = this.o.weekStart,
				html = '<tr>';
			if (this.o.calendarWeeks){
				var cell = '<th class="cw">&nbsp;</th>';
				html += cell;
				this.picker.find('.datepicker-days thead tr:first-child').prepend(cell);
			}
			while (dowCnt < this.o.weekStart + 7){
				html += '<th class="dow">'+dates[this.o.language].daysMin[(dowCnt++)%7]+'</th>';
			}
			html += '</tr>';
			this.picker.find('.datepicker-days thead').append(html);
		},

		fillMonths: function(){
			var html = '',
			i = 0;
			while (i < 12){
				html += '<span class="month">'+dates[this.o.language].monthsShort[i++]+'</span>';
			}
			this.picker.find('.datepicker-months td').html(html);
		},

		setRange: function(range){
			if (!range || !range.length)
				delete this.range;
			else
				this.range = $.map(range, function(d){
					return d.valueOf();
				});
			this.fill();
		},

		getClassNames: function(date){
			var cls = [],
				year = this.viewDate.getUTCFullYear(),
				month = this.viewDate.getUTCMonth(),
				today = new Date();
			if (date.getUTCFullYear() < year || (date.getUTCFullYear() === year && date.getUTCMonth() < month)){
				cls.push('old');
			}
			else if (date.getUTCFullYear() > year || (date.getUTCFullYear() === year && date.getUTCMonth() > month)){
				cls.push('new');
			}
			if (this.focusDate && date.valueOf() === this.focusDate.valueOf())
				cls.push('focused');
			// Compare internal UTC date with local today, not UTC today
			if (this.o.todayHighlight &&
				date.getUTCFullYear() === today.getFullYear() &&
				date.getUTCMonth() === today.getMonth() &&
				date.getUTCDate() === today.getDate()){
				cls.push('today');
			}
			if (this.dates.contains(date) !== -1)
				cls.push('active');
			if (date.valueOf() < this.o.startDate || date.valueOf() > this.o.endDate ||
				$.inArray(date.getUTCDay(), this.o.daysOfWeekDisabled) !== -1){
				cls.push('disabled');
			}
			if (this.range){
				if (date > this.range[0] && date < this.range[this.range.length-1]){
					cls.push('range');
				}
				if ($.inArray(date.valueOf(), this.range) !== -1){
					cls.push('selected');
				}
			}
			return cls;
		},

		fill: function(){
			var d = new Date(this.viewDate),
				year = d.getUTCFullYear(),
				month = d.getUTCMonth(),
				startYear = this.o.startDate !== -Infinity ? this.o.startDate.getUTCFullYear() : -Infinity,
				startMonth = this.o.startDate !== -Infinity ? this.o.startDate.getUTCMonth() : -Infinity,
				endYear = this.o.endDate !== Infinity ? this.o.endDate.getUTCFullYear() : Infinity,
				endMonth = this.o.endDate !== Infinity ? this.o.endDate.getUTCMonth() : Infinity,
				todaytxt = dates[this.o.language].today || dates['en'].today || '',
				cleartxt = dates[this.o.language].clear || dates['en'].clear || '',
				tooltip;
			this.picker.find('.datepicker-days thead th.datepicker-switch')
						.text(dates[this.o.language].months[month]+' '+year);
			this.picker.find('tfoot th.today')
						.text(todaytxt)
						.toggle(this.o.todayBtn !== false);
			this.picker.find('tfoot th.clear')
						.text(cleartxt)
						.toggle(this.o.clearBtn !== false);
			this.updateNavArrows();
			this.fillMonths();
			var prevMonth = UTCDate(year, month-1, 28),
				day = DPGlobal.getDaysInMonth(prevMonth.getUTCFullYear(), prevMonth.getUTCMonth());
			prevMonth.setUTCDate(day);
			prevMonth.setUTCDate(day - (prevMonth.getUTCDay() - this.o.weekStart + 7)%7);
			var nextMonth = new Date(prevMonth);
			nextMonth.setUTCDate(nextMonth.getUTCDate() + 42);
			nextMonth = nextMonth.valueOf();
			var html = [];
			var clsName;
			while (prevMonth.valueOf() < nextMonth){
				if (prevMonth.getUTCDay() === this.o.weekStart){
					html.push('<tr>');
					if (this.o.calendarWeeks){
						// ISO 8601: First week contains first thursday.
						// ISO also states week starts on Monday, but we can be more abstract here.
						var
							// Start of current week: based on weekstart/current date
							ws = new Date(+prevMonth + (this.o.weekStart - prevMonth.getUTCDay() - 7) % 7 * 864e5),
							// Thursday of this week
							th = new Date(Number(ws) + (7 + 4 - ws.getUTCDay()) % 7 * 864e5),
							// First Thursday of year, year from thursday
							yth = new Date(Number(yth = UTCDate(th.getUTCFullYear(), 0, 1)) + (7 + 4 - yth.getUTCDay())%7*864e5),
							// Calendar week: ms between thursdays, div ms per day, div 7 days
							calWeek =  (th - yth) / 864e5 / 7 + 1;
						html.push('<td class="cw">'+ calWeek +'</td>');

					}
				}
				clsName = this.getClassNames(prevMonth);
				clsName.push('day');

				if (this.o.beforeShowDay !== $.noop){
					var before = this.o.beforeShowDay(this._utc_to_local(prevMonth));
					if (before === undefined)
						before = {};
					else if (typeof(before) === 'boolean')
						before = {enabled: before};
					else if (typeof(before) === 'string')
						before = {classes: before};
					if (before.enabled === false)
						clsName.push('disabled');
					if (before.classes)
						clsName = clsName.concat(before.classes.split(/\s+/));
					if (before.tooltip)
						tooltip = before.tooltip;
				}

				clsName = $.unique(clsName);
				html.push('<td class="'+clsName.join(' ')+'"' + (tooltip ? ' title="'+tooltip+'"' : '') + '>'+prevMonth.getUTCDate() + '</td>');
				if (prevMonth.getUTCDay() === this.o.weekEnd){
					html.push('</tr>');
				}
				prevMonth.setUTCDate(prevMonth.getUTCDate()+1);
			}
			this.picker.find('.datepicker-days tbody').empty().append(html.join(''));

			var months = this.picker.find('.datepicker-months')
						.find('th:eq(1)')
							.text(year)
							.end()
						.find('span').removeClass('active');

			$.each(this.dates, function(i, d){
				if (d.getUTCFullYear() === year)
					months.eq(d.getUTCMonth()).addClass('active');
			});

			if (year < startYear || year > endYear){
				months.addClass('disabled');
			}
			if (year === startYear){
				months.slice(0, startMonth).addClass('disabled');
			}
			if (year === endYear){
				months.slice(endMonth+1).addClass('disabled');
			}

			html = '';
			year = parseInt(year/10, 10) * 10;
			var yearCont = this.picker.find('.datepicker-years')
								.find('th:eq(1)')
									.text(year + '-' + (year + 9))
									.end()
								.find('td');
			year -= 1;
			var years = $.map(this.dates, function(d){
					return d.getUTCFullYear();
				}),
				classes;
			for (var i = -1; i < 11; i++){
				classes = ['year'];
				if (i === -1)
					classes.push('old');
				else if (i === 10)
					classes.push('new');
				if ($.inArray(year, years) !== -1)
					classes.push('active');
				if (year < startYear || year > endYear)
					classes.push('disabled');
				html += '<span class="' + classes.join(' ') + '">'+year+'</span>';
				year += 1;
			}
			yearCont.html(html);
		},

		updateNavArrows: function(){
			if (!this._allow_update)
				return;

			var d = new Date(this.viewDate),
				year = d.getUTCFullYear(),
				month = d.getUTCMonth();
			switch (this.viewMode){
				case 0:
					if (this.o.startDate !== -Infinity && year <= this.o.startDate.getUTCFullYear() && month <= this.o.startDate.getUTCMonth()){
						this.picker.find('.prev').css({visibility: 'hidden'});
					}
					else {
						this.picker.find('.prev').css({visibility: 'visible'});
					}
					if (this.o.endDate !== Infinity && year >= this.o.endDate.getUTCFullYear() && month >= this.o.endDate.getUTCMonth()){
						this.picker.find('.next').css({visibility: 'hidden'});
					}
					else {
						this.picker.find('.next').css({visibility: 'visible'});
					}
					break;
				case 1:
				case 2:
					if (this.o.startDate !== -Infinity && year <= this.o.startDate.getUTCFullYear()){
						this.picker.find('.prev').css({visibility: 'hidden'});
					}
					else {
						this.picker.find('.prev').css({visibility: 'visible'});
					}
					if (this.o.endDate !== Infinity && year >= this.o.endDate.getUTCFullYear()){
						this.picker.find('.next').css({visibility: 'hidden'});
					}
					else {
						this.picker.find('.next').css({visibility: 'visible'});
					}
					break;
			}
		},

		click: function(e){
			e.preventDefault();
			var target = $(e.target).closest('span, td, th'),
				year, month, day;
			if (target.length === 1){
				switch (target[0].nodeName.toLowerCase()){
					case 'th':
						switch (target[0].className){
							case 'datepicker-switch':
								this.showMode(1);
								break;
							case 'prev':
							case 'next':
								var dir = DPGlobal.modes[this.viewMode].navStep * (target[0].className === 'prev' ? -1 : 1);
								switch (this.viewMode){
									case 0:
										this.viewDate = this.moveMonth(this.viewDate, dir);
										this._trigger('changeMonth', this.viewDate);
										break;
									case 1:
									case 2:
										this.viewDate = this.moveYear(this.viewDate, dir);
										if (this.viewMode === 1)
											this._trigger('changeYear', this.viewDate);
										break;
								}
								this.fill();
								break;
							case 'today':
								var date = new Date();
								date = UTCDate(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0);

								this.showMode(-2);
								var which = this.o.todayBtn === 'linked' ? null : 'view';
								this._setDate(date, which);
								break;
							case 'clear':
								var element;
								if (this.isInput)
									element = this.element;
								else if (this.component)
									element = this.element.find('input');
								if (element)
									element.val("").change();
								this.update();
								this._trigger('changeDate');
								if (this.o.autoclose)
									this.hide();
								break;
						}
						break;
					case 'span':
						if (!target.is('.disabled')){
							this.viewDate.setUTCDate(1);
							if (target.is('.month')){
								day = 1;
								month = target.parent().find('span').index(target);
								year = this.viewDate.getUTCFullYear();
								this.viewDate.setUTCMonth(month);
								this._trigger('changeMonth', this.viewDate);
								if (this.o.minViewMode === 1){
									this._setDate(UTCDate(year, month, day));
								}
							}
							else {
								day = 1;
								month = 0;
								year = parseInt(target.text(), 10)||0;
								this.viewDate.setUTCFullYear(year);
								this._trigger('changeYear', this.viewDate);
								if (this.o.minViewMode === 2){
									this._setDate(UTCDate(year, month, day));
								}
							}
							this.showMode(-1);
							this.fill();
						}
						break;
					case 'td':
						if (target.is('.day') && !target.is('.disabled')){
							day = parseInt(target.text(), 10)||1;
							year = this.viewDate.getUTCFullYear();
							month = this.viewDate.getUTCMonth();
							if (target.is('.old')){
								if (month === 0){
									month = 11;
									year -= 1;
								}
								else {
									month -= 1;
								}
							}
							else if (target.is('.new')){
								if (month === 11){
									month = 0;
									year += 1;
								}
								else {
									month += 1;
								}
							}
							this._setDate(UTCDate(year, month, day));
						}
						break;
				}
			}
			if (this.picker.is(':visible') && this._focused_from){
				$(this._focused_from).focus();
			}
			delete this._focused_from;
		},

		_toggle_multidate: function(date){
			var ix = this.dates.contains(date);
			if (!date){
				this.dates.clear();
			}
			else if (ix !== -1){
				this.dates.remove(ix);
			}
			else {
				this.dates.push(date);
			}
			if (typeof this.o.multidate === 'number')
				while (this.dates.length > this.o.multidate)
					this.dates.remove(0);
		},

		_setDate: function(date, which){
			if (!which || which === 'date')
				this._toggle_multidate(date && new Date(date));
			if (!which || which  === 'view')
				this.viewDate = date && new Date(date);

			this.fill();
			this.setValue();
			this._trigger('changeDate');
			var element;
			if (this.isInput){
				element = this.element;
			}
			else if (this.component){
				element = this.element.find('input');
			}
			if (element){
				element.change();
			}
			if (this.o.autoclose && (!which || which === 'date')){
				this.hide();
			}
		},

		moveMonth: function(date, dir){
			if (!date)
				return undefined;
			if (!dir)
				return date;
			var new_date = new Date(date.valueOf()),
				day = new_date.getUTCDate(),
				month = new_date.getUTCMonth(),
				mag = Math.abs(dir),
				new_month, test;
			dir = dir > 0 ? 1 : -1;
			if (mag === 1){
				test = dir === -1
					// If going back one month, make sure month is not current month
					// (eg, Mar 31 -> Feb 31 == Feb 28, not Mar 02)
					? function(){
						return new_date.getUTCMonth() === month;
					}
					// If going forward one month, make sure month is as expected
					// (eg, Jan 31 -> Feb 31 == Feb 28, not Mar 02)
					: function(){
						return new_date.getUTCMonth() !== new_month;
					};
				new_month = month + dir;
				new_date.setUTCMonth(new_month);
				// Dec -> Jan (12) or Jan -> Dec (-1) -- limit expected date to 0-11
				if (new_month < 0 || new_month > 11)
					new_month = (new_month + 12) % 12;
			}
			else {
				// For magnitudes >1, move one month at a time...
				for (var i=0; i < mag; i++)
					// ...which might decrease the day (eg, Jan 31 to Feb 28, etc)...
					new_date = this.moveMonth(new_date, dir);
				// ...then reset the day, keeping it in the new month
				new_month = new_date.getUTCMonth();
				new_date.setUTCDate(day);
				test = function(){
					return new_month !== new_date.getUTCMonth();
				};
			}
			// Common date-resetting loop -- if date is beyond end of month, make it
			// end of month
			while (test()){
				new_date.setUTCDate(--day);
				new_date.setUTCMonth(new_month);
			}
			return new_date;
		},

		moveYear: function(date, dir){
			return this.moveMonth(date, dir*12);
		},

		dateWithinRange: function(date){
			return date >= this.o.startDate && date <= this.o.endDate;
		},

		keydown: function(e){
			if (this.picker.is(':not(:visible)')){
				if (e.keyCode === 27) // allow escape to hide and re-show picker
					this.show();
				return;
			}
			var dateChanged = false,
				dir, newDate, newViewDate,
				focusDate = this.focusDate || this.viewDate;
			switch (e.keyCode){
				case 27: // escape
					if (this.focusDate){
						this.focusDate = null;
						this.viewDate = this.dates.get(-1) || this.viewDate;
						this.fill();
					}
					else
						this.hide();
					e.preventDefault();
					break;
				case 37: // left
				case 39: // right
					if (!this.o.keyboardNavigation)
						break;
					dir = e.keyCode === 37 ? -1 : 1;
					if (e.ctrlKey){
						newDate = this.moveYear(this.dates.get(-1) || UTCToday(), dir);
						newViewDate = this.moveYear(focusDate, dir);
						this._trigger('changeYear', this.viewDate);
					}
					else if (e.shiftKey){
						newDate = this.moveMonth(this.dates.get(-1) || UTCToday(), dir);
						newViewDate = this.moveMonth(focusDate, dir);
						this._trigger('changeMonth', this.viewDate);
					}
					else {
						newDate = new Date(this.dates.get(-1) || UTCToday());
						newDate.setUTCDate(newDate.getUTCDate() + dir);
						newViewDate = new Date(focusDate);
						newViewDate.setUTCDate(focusDate.getUTCDate() + dir);
					}
					if (this.dateWithinRange(newDate)){
						this.focusDate = this.viewDate = newViewDate;
						this.setValue();
						this.fill();
						e.preventDefault();
					}
					break;
				case 38: // up
				case 40: // down
					if (!this.o.keyboardNavigation)
						break;
					dir = e.keyCode === 38 ? -1 : 1;
					if (e.ctrlKey){
						newDate = this.moveYear(this.dates.get(-1) || UTCToday(), dir);
						newViewDate = this.moveYear(focusDate, dir);
						this._trigger('changeYear', this.viewDate);
					}
					else if (e.shiftKey){
						newDate = this.moveMonth(this.dates.get(-1) || UTCToday(), dir);
						newViewDate = this.moveMonth(focusDate, dir);
						this._trigger('changeMonth', this.viewDate);
					}
					else {
						newDate = new Date(this.dates.get(-1) || UTCToday());
						newDate.setUTCDate(newDate.getUTCDate() + dir * 7);
						newViewDate = new Date(focusDate);
						newViewDate.setUTCDate(focusDate.getUTCDate() + dir * 7);
					}
					if (this.dateWithinRange(newDate)){
						this.focusDate = this.viewDate = newViewDate;
						this.setValue();
						this.fill();
						e.preventDefault();
					}
					break;
				case 32: // spacebar
					// Spacebar is used in manually typing dates in some formats.
					// As such, its behavior should not be hijacked.
					break;
				case 13: // enter
					focusDate = this.focusDate || this.dates.get(-1) || this.viewDate;
					this._toggle_multidate(focusDate);
					dateChanged = true;
					this.focusDate = null;
					this.viewDate = this.dates.get(-1) || this.viewDate;
					this.setValue();
					this.fill();
					if (this.picker.is(':visible')){
						e.preventDefault();
						if (this.o.autoclose)
							this.hide();
					}
					break;
				case 9: // tab
					this.focusDate = null;
					this.viewDate = this.dates.get(-1) || this.viewDate;
					this.fill();
					this.hide();
					break;
			}
			if (dateChanged){
				if (this.dates.length)
					this._trigger('changeDate');
				else
					this._trigger('clearDate');
				var element;
				if (this.isInput){
					element = this.element;
				}
				else if (this.component){
					element = this.element.find('input');
				}
				if (element){
					element.change();
				}
			}
		},

		showMode: function(dir){
			if (dir){
				this.viewMode = Math.max(this.o.minViewMode, Math.min(2, this.viewMode + dir));
			}
			this.picker
				.find('>div')
				.hide()
				.filter('.datepicker-'+DPGlobal.modes[this.viewMode].clsName)
					.css('display', 'block');
			this.updateNavArrows();
		}
	};

	var DateRangePicker = function(element, options){
		this.element = $(element);
		this.inputs = $.map(options.inputs, function(i){
			return i.jquery ? i[0] : i;
		});
		delete options.inputs;

		$(this.inputs)
			.datepicker(options)
			.bind('changeDate', $.proxy(this.dateUpdated, this));

		this.pickers = $.map(this.inputs, function(i){
			return $(i).data('datepicker');
		});
		this.updateDates();
	};
	DateRangePicker.prototype = {
		updateDates: function(){
			this.dates = $.map(this.pickers, function(i){
				return i.getUTCDate();
			});
			this.updateRanges();
		},
		updateRanges: function(){
			var range = $.map(this.dates, function(d){
				return d.valueOf();
			});
			$.each(this.pickers, function(i, p){
				p.setRange(range);
			});
		},
		dateUpdated: function(e){
			// `this.updating` is a workaround for preventing infinite recursion
			// between `changeDate` triggering and `setUTCDate` calling.  Until
			// there is a better mechanism.
			if (this.updating)
				return;
			this.updating = true;

			var dp = $(e.target).data('datepicker'),
				new_date = dp.getUTCDate(),
				i = $.inArray(e.target, this.inputs),
				l = this.inputs.length;
			if (i === -1)
				return;

			$.each(this.pickers, function(i, p){
				if (!p.getUTCDate())
					p.setUTCDate(new_date);
			});

			if (new_date < this.dates[i]){
				// Date being moved earlier/left
				while (i >= 0 && new_date < this.dates[i]){
					this.pickers[i--].setUTCDate(new_date);
				}
			}
			else if (new_date > this.dates[i]){
				// Date being moved later/right
				while (i < l && new_date > this.dates[i]){
					this.pickers[i++].setUTCDate(new_date);
				}
			}
			this.updateDates();

			delete this.updating;
		},
		remove: function(){
			$.map(this.pickers, function(p){ p.remove(); });
			delete this.element.data().datepicker;
		}
	};

	function opts_from_el(el, prefix){
		// Derive options from element data-attrs
		var data = $(el).data(),
			out = {}, inkey,
			replace = new RegExp('^' + prefix.toLowerCase() + '([A-Z])');
		prefix = new RegExp('^' + prefix.toLowerCase());
		function re_lower(_,a){
			return a.toLowerCase();
		}
		for (var key in data)
			if (prefix.test(key)){
				inkey = key.replace(replace, re_lower);
				out[inkey] = data[key];
			}
		return out;
	}

	function opts_from_locale(lang){
		// Derive options from locale plugins
		var out = {};
		// Check if "de-DE" style date is available, if not language should
		// fallback to 2 letter code eg "de"
		if (!dates[lang]){
			lang = lang.split('-')[0];
			if (!dates[lang])
				return;
		}
		var d = dates[lang];
		$.each(locale_opts, function(i,k){
			if (k in d)
				out[k] = d[k];
		});
		return out;
	}

	var old = $.fn.datepicker;
	$.fn.datepicker = function(option){
		var args = Array.apply(null, arguments);
		args.shift();
		var internal_return;
		this.each(function(){
			var $this = $(this),
				data = $this.data('datepicker'),
				options = typeof option === 'object' && option;
			if (!data){
				var elopts = opts_from_el(this, 'date'),
					// Preliminary otions
					xopts = $.extend({}, defaults, elopts, options),
					locopts = opts_from_locale(xopts.language),
					// Options priority: js args, data-attrs, locales, defaults
					opts = $.extend({}, defaults, locopts, elopts, options);
				if ($this.is('.input-daterange') || opts.inputs){
					var ropts = {
						inputs: opts.inputs || $this.find('input').toArray()
					};
					$this.data('datepicker', (data = new DateRangePicker(this, $.extend(opts, ropts))));
				}
				else {
					$this.data('datepicker', (data = new Datepicker(this, opts)));
				}
			}
			if (typeof option === 'string' && typeof data[option] === 'function'){
				internal_return = data[option].apply(data, args);
				if (internal_return !== undefined)
					return false;
			}
		});
		if (internal_return !== undefined)
			return internal_return;
		else
			return this;
	};

	var defaults = $.fn.datepicker.defaults = {
		autoclose: false,
		beforeShowDay: $.noop,
		calendarWeeks: false,
		clearBtn: false,
		daysOfWeekDisabled: [],
		endDate: Infinity,
		forceParse: true,
		format: 'mm/dd/yyyy',
		keyboardNavigation: true,
		language: 'en',
		minViewMode: 0,
		multidate: false,
		multidateSeparator: ',',
		orientation: "auto",
		rtl: false,
		startDate: -Infinity,
		startView: 0,
		todayBtn: false,
		todayHighlight: false,
		weekStart: 0
	};
	var locale_opts = $.fn.datepicker.locale_opts = [
		'format',
		'rtl',
		'weekStart'
	];
	$.fn.datepicker.Constructor = Datepicker;
	var dates = $.fn.datepicker.dates = {
		en: {
			days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
			daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
			daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
			months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
			monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
			today: "Today",
			clear: "Clear"
		}
	};

	var DPGlobal = {
		modes: [
			{
				clsName: 'days',
				navFnc: 'Month',
				navStep: 1
			},
			{
				clsName: 'months',
				navFnc: 'FullYear',
				navStep: 1
			},
			{
				clsName: 'years',
				navFnc: 'FullYear',
				navStep: 10
		}],
		isLeapYear: function(year){
			return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
		},
		getDaysInMonth: function(year, month){
			return [31, (DPGlobal.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
		},
		validParts: /dd?|DD?|mm?|MM?|yy(?:yy)?/g,
		nonpunctuation: /[^ -\/:-@\[\u3400-\u9fff-`{-~\t\n\r]+/g,
		parseFormat: function(format){
			// IE treats \0 as a string end in inputs (truncating the value),
			// so it's a bad format delimiter, anyway
			var separators = format.replace(this.validParts, '\0').split('\0'),
				parts = format.match(this.validParts);
			if (!separators || !separators.length || !parts || parts.length === 0){
				throw new Error("Invalid date format.");
			}
			return {separators: separators, parts: parts};
		},
		parseDate: function(date, format, language){
			if (!date)
				return undefined;
			if (date instanceof Date)
				return date;
			if (typeof format === 'string')
				format = DPGlobal.parseFormat(format);
			var part_re = /([\-+]\d+)([dmwy])/,
				parts = date.match(/([\-+]\d+)([dmwy])/g),
				part, dir, i;
			if (/^[\-+]\d+[dmwy]([\s,]+[\-+]\d+[dmwy])*$/.test(date)){
				date = new Date();
				for (i=0; i < parts.length; i++){
					part = part_re.exec(parts[i]);
					dir = parseInt(part[1]);
					switch (part[2]){
						case 'd':
							date.setUTCDate(date.getUTCDate() + dir);
							break;
						case 'm':
							date = Datepicker.prototype.moveMonth.call(Datepicker.prototype, date, dir);
							break;
						case 'w':
							date.setUTCDate(date.getUTCDate() + dir * 7);
							break;
						case 'y':
							date = Datepicker.prototype.moveYear.call(Datepicker.prototype, date, dir);
							break;
					}
				}
				return UTCDate(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), 0, 0, 0);
			}
			parts = date && date.match(this.nonpunctuation) || [];
			date = new Date();
			var parsed = {},
				setters_order = ['yyyy', 'yy', 'M', 'MM', 'm', 'mm', 'd', 'dd'],
				setters_map = {
					yyyy: function(d,v){
						return d.setUTCFullYear(v);
					},
					yy: function(d,v){
						return d.setUTCFullYear(2000+v);
					},
					m: function(d,v){
						if (isNaN(d))
							return d;
						v -= 1;
						while (v < 0) v += 12;
						v %= 12;
						d.setUTCMonth(v);
						while (d.getUTCMonth() !== v)
							d.setUTCDate(d.getUTCDate()-1);
						return d;
					},
					d: function(d,v){
						return d.setUTCDate(v);
					}
				},
				val, filtered;
			setters_map['M'] = setters_map['MM'] = setters_map['mm'] = setters_map['m'];
			setters_map['dd'] = setters_map['d'];
			date = UTCDate(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0);
			var fparts = format.parts.slice();
			// Remove noop parts
			if (parts.length !== fparts.length){
				fparts = $(fparts).filter(function(i,p){
					return $.inArray(p, setters_order) !== -1;
				}).toArray();
			}
			// Process remainder
			function match_part(){
				var m = this.slice(0, parts[i].length),
					p = parts[i].slice(0, m.length);
				return m === p;
			}
			if (parts.length === fparts.length){
				var cnt;
				for (i=0, cnt = fparts.length; i < cnt; i++){
					val = parseInt(parts[i], 10);
					part = fparts[i];
					if (isNaN(val)){
						switch (part){
							case 'MM':
								filtered = $(dates[language].months).filter(match_part);
								val = $.inArray(filtered[0], dates[language].months) + 1;
								break;
							case 'M':
								filtered = $(dates[language].monthsShort).filter(match_part);
								val = $.inArray(filtered[0], dates[language].monthsShort) + 1;
								break;
						}
					}
					parsed[part] = val;
				}
				var _date, s;
				for (i=0; i < setters_order.length; i++){
					s = setters_order[i];
					if (s in parsed && !isNaN(parsed[s])){
						_date = new Date(date);
						setters_map[s](_date, parsed[s]);
						if (!isNaN(_date))
							date = _date;
					}
				}
			}
			return date;
		},
		formatDate: function(date, format, language){
			if (!date)
				return '';
			if (typeof format === 'string')
				format = DPGlobal.parseFormat(format);
			var val = {
				d: date.getUTCDate(),
				D: dates[language].daysShort[date.getUTCDay()],
				DD: dates[language].days[date.getUTCDay()],
				m: date.getUTCMonth() + 1,
				M: dates[language].monthsShort[date.getUTCMonth()],
				MM: dates[language].months[date.getUTCMonth()],
				yy: date.getUTCFullYear().toString().substring(2),
				yyyy: date.getUTCFullYear()
			};
			val.dd = (val.d < 10 ? '0' : '') + val.d;
			val.mm = (val.m < 10 ? '0' : '') + val.m;
			date = [];
			var seps = $.extend([], format.separators);
			for (var i=0, cnt = format.parts.length; i <= cnt; i++){
				if (seps.length)
					date.push(seps.shift());
				date.push(val[format.parts[i]]);
			}
			return date.join('');
		},
		headTemplate: '<thead>'+
							'<tr>'+
								'<th class="prev"><i class="fa fa-angle-left"></i></th>'+
								'<th colspan="5" class="datepicker-switch"></th>'+
								'<th class="next"><i class="fa fa-angle-right"></i></th>'+
							'</tr>'+
						'</thead>',
		contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>',
		footTemplate: '<tfoot>'+
							'<tr>'+
								'<th colspan="7" class="today"></th>'+
							'</tr>'+
							'<tr>'+
								'<th colspan="7" class="clear"></th>'+
							'</tr>'+
						'</tfoot>'
	};
	DPGlobal.template = '<div class="datepicker">'+
							'<div class="datepicker-days">'+
								'<table class=" table-condensed">'+
									DPGlobal.headTemplate+
									'<tbody></tbody>'+
									DPGlobal.footTemplate+
								'</table>'+
							'</div>'+
							'<div class="datepicker-months">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
									DPGlobal.footTemplate+
								'</table>'+
							'</div>'+
							'<div class="datepicker-years">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
									DPGlobal.footTemplate+
								'</table>'+
							'</div>'+
						'</div>';

	$.fn.datepicker.DPGlobal = DPGlobal;


	/* DATEPICKER NO CONFLICT
	* =================== */

	$.fn.datepicker.noConflict = function(){
		$.fn.datepicker = old;
		return this;
	};


	/* DATEPICKER DATA-API
	* ================== */

	$(document).on(
		'focus.datepicker.data-api click.datepicker.data-api',
		'[data-provide="datepicker"]',
		function(e){
			var $this = $(this);
			if ($this.data('datepicker'))
				return;
			e.preventDefault();
			// component click requires us to explicitly show it
			$this.datepicker('show');
		}
	);
	$(function(){
		$('[data-provide="datepicker-inline"]').datepicker();
	});

}(window.jQuery));

!function(t){function e(){return new Date(Date.UTC.apply(Date,arguments))}var i=function(e,i){var a=this;this.element=t(e),this.language=i.language||this.element.data("date-language")||"en",this.language=this.language in n?this.language:"en",this.isRTL=n[this.language].rtl||"rtl"==t("body").css("direction"),this.formatType=i.formatType||this.element.data("format-type")||"standard",this.format=s.parseFormat(i.format||this.element.data("date-format")||n[this.language].format||s.getDefaultFormat(this.formatType,"input"),this.formatType),this.isInline=!1,this.isVisible=!1,this.isInput=this.element.is("input"),this.component=this.element.is(".date")?this.element.find(".date-set").parent():!1,this.componentReset=this.element.is(".date")?this.element.find(".date-reset").parent():!1,this.hasInput=this.component&&this.element.find("input").length,this.component&&0===this.component.length&&(this.component=!1),this.linkField=i.linkField||this.element.data("link-field")||!1,this.linkFormat=s.parseFormat(i.linkFormat||this.element.data("link-format")||s.getDefaultFormat(this.formatType,"link"),this.formatType),this.minuteStep=i.minuteStep||this.element.data("minute-step")||5,this.pickerPosition=i.pickerPosition||this.element.data("picker-position")||"bottom-right",this.showMeridian=i.showMeridian||this.element.data("show-meridian")||!1,this.initialDate=i.initialDate||new Date,this._attachEvents(),this.formatViewType="datetime","formatViewType"in i?this.formatViewType=i.formatViewType:"formatViewType"in this.element.data()&&(this.formatViewType=this.element.data("formatViewType")),this.minView=0,"minView"in i?this.minView=i.minView:"minView"in this.element.data()&&(this.minView=this.element.data("min-view")),this.minView=s.convertViewMode(this.minView),this.maxView=s.modes.length-1,"maxView"in i?this.maxView=i.maxView:"maxView"in this.element.data()&&(this.maxView=this.element.data("max-view")),this.maxView=s.convertViewMode(this.maxView),this.wheelViewModeNavigation=!1,"wheelViewModeNavigation"in i?this.wheelViewModeNavigation=i.wheelViewModeNavigation:"wheelViewModeNavigation"in this.element.data()&&(this.wheelViewModeNavigation=this.element.data("view-mode-wheel-navigation")),this.wheelViewModeNavigationInverseDirection=!1,"wheelViewModeNavigationInverseDirection"in i?this.wheelViewModeNavigationInverseDirection=i.wheelViewModeNavigationInverseDirection:"wheelViewModeNavigationInverseDirection"in this.element.data()&&(this.wheelViewModeNavigationInverseDirection=this.element.data("view-mode-wheel-navigation-inverse-dir")),this.wheelViewModeNavigationDelay=100,"wheelViewModeNavigationDelay"in i?this.wheelViewModeNavigationDelay=i.wheelViewModeNavigationDelay:"wheelViewModeNavigationDelay"in this.element.data()&&(this.wheelViewModeNavigationDelay=this.element.data("view-mode-wheel-navigation-delay")),this.startViewMode=2,"startView"in i?this.startViewMode=i.startView:"startView"in this.element.data()&&(this.startViewMode=this.element.data("start-view")),this.startViewMode=s.convertViewMode(this.startViewMode),this.viewMode=this.startViewMode,this.viewSelect=this.minView,"viewSelect"in i?this.viewSelect=i.viewSelect:"viewSelect"in this.element.data()&&(this.viewSelect=this.element.data("view-select")),this.viewSelect=s.convertViewMode(this.viewSelect),this.forceParse=!0,"forceParse"in i?this.forceParse=i.forceParse:"dateForceParse"in this.element.data()&&(this.forceParse=this.element.data("date-force-parse")),this.picker=t(s.template).appendTo(this.isInline?this.element:"body").on({click:t.proxy(this.click,this),mousedown:t.proxy(this.mousedown,this)}),this.wheelViewModeNavigation&&(t.fn.mousewheel?this.picker.on({mousewheel:t.proxy(this.mousewheel,this)}):console.log("Mouse Wheel event is not supported. Please include the jQuery Mouse Wheel plugin before enabling this option")),this.isInline?this.picker.addClass("datetimepicker-inline"):this.picker.addClass("datetimepicker-dropdown-"+this.pickerPosition+" dropdown-menu"),this.isRTL&&(this.picker.addClass("datetimepicker-rtl"),this.picker.find(".prev i, .next i").toggleClass("fa-arrow-left fa-arrow-right")),t(document).on("mousedown",function(e){0===t(e.target).closest(".datetimepicker").length&&a.hide()}),this.autoclose=!1,"autoclose"in i?this.autoclose=i.autoclose:"dateAutoclose"in this.element.data()&&(this.autoclose=this.element.data("date-autoclose")),this.keyboardNavigation=!0,"keyboardNavigation"in i?this.keyboardNavigation=i.keyboardNavigation:"dateKeyboardNavigation"in this.element.data()&&(this.keyboardNavigation=this.element.data("date-keyboard-navigation")),this.todayBtn=i.todayBtn||this.element.data("date-today-btn")||!1,this.todayHighlight=i.todayHighlight||this.element.data("date-today-highlight")||!1,this.weekStart=(i.weekStart||this.element.data("date-weekstart")||n[this.language].weekStart||0)%7,this.weekEnd=(this.weekStart+6)%7,this.startDate=-1/0,this.endDate=1/0,this.daysOfWeekDisabled=[],this.setStartDate(i.startDate||this.element.data("date-startdate")),this.setEndDate(i.endDate||this.element.data("date-enddate")),this.setDaysOfWeekDisabled(i.daysOfWeekDisabled||this.element.data("date-days-of-week-disabled")),this.fillDow(),this.fillMonths(),this.update(),this.showMode(),this.isInline&&this.show()};i.prototype={constructor:i,_events:[],_attachEvents:function(){this._detachEvents(),this.isInput?this._events=[[this.element,{focus:t.proxy(this.show,this),keyup:t.proxy(this.update,this),keydown:t.proxy(this.keydown,this)}]]:this.component&&this.hasInput?(this._events=[[this.element.find("input"),{focus:t.proxy(this.show,this),keyup:t.proxy(this.update,this),keydown:t.proxy(this.keydown,this)}],[this.component,{click:t.proxy(this.show,this)}]],this.componentReset&&this._events.push([this.componentReset,{click:t.proxy(this.reset,this)}])):this.element.is("div")?this.isInline=!0:this._events=[[this.element,{click:t.proxy(this.show,this)}]];for(var e,i,n=0;n<this._events.length;n++)e=this._events[n][0],i=this._events[n][1],e.on(i)},_detachEvents:function(){for(var t,e,i=0;i<this._events.length;i++)t=this._events[i][0],e=this._events[i][1],t.off(e);this._events=[]},show:function(e){this.picker.show(),this.height=this.component?this.component.outerHeight():this.element.outerHeight(),this.forceParse&&this.update(),this.place(),t(window).on("resize",t.proxy(this.place,this)),e&&(e.stopPropagation(),e.preventDefault()),this.isVisible=!0,this.element.trigger({type:"show",date:this.date})},hide:function(){this.isVisible&&(this.isInline||(this.picker.hide(),t(window).off("resize",this.place),this.viewMode=this.startViewMode,this.showMode(),this.isInput||t(document).off("mousedown",this.hide),this.forceParse&&(this.isInput&&this.element.val()||this.hasInput&&this.element.find("input").val())&&this.setValue(),this.isVisible=!1,this.element.trigger({type:"hide",date:this.date})))},remove:function(){this._detachEvents(),this.picker.remove(),delete this.picker,delete this.element.data().datetimepicker},getDate:function(){var t=this.getUTCDate();return new Date(t.getTime()+6e4*t.getTimezoneOffset())},getUTCDate:function(){return this.date},setDate:function(t){this.setUTCDate(new Date(t.getTime()-6e4*t.getTimezoneOffset()))},setUTCDate:function(t){t>=this.startDate&&t<=this.endDate?(this.date=t,this.setValue(),this.viewDate=this.date,this.fill()):this.element.trigger({type:"outOfRange",date:t,startDate:this.startDate,endDate:this.endDate})},setFormat:function(t){this.format=s.parseFormat(t,this.formatType);var e;this.isInput?e=this.element:this.component&&(e=this.element.find("input")),e&&e.val()&&this.setValue()},setValue:function(){var e=this.getFormattedDate();this.isInput?this.element.val(e):(this.component&&this.element.find("input").val(e),this.element.data("date",e)),this.linkField&&t("#"+this.linkField).val(this.getFormattedDate(this.linkFormat))},getFormattedDate:function(t){return void 0==t&&(t=this.format),s.formatDate(this.date,t,this.language,this.formatType)},setStartDate:function(t){this.startDate=t||-1/0,this.startDate!==-1/0&&(this.startDate=s.parseDate(this.startDate,this.format,this.language,this.formatType)),this.update(),this.updateNavArrows()},setEndDate:function(t){this.endDate=t||1/0,1/0!==this.endDate&&(this.endDate=s.parseDate(this.endDate,this.format,this.language,this.formatType)),this.update(),this.updateNavArrows()},setDaysOfWeekDisabled:function(e){this.daysOfWeekDisabled=e||[],t.isArray(this.daysOfWeekDisabled)||(this.daysOfWeekDisabled=this.daysOfWeekDisabled.split(/,\s*/)),this.daysOfWeekDisabled=t.map(this.daysOfWeekDisabled,function(t){return parseInt(t,10)}),this.update(),this.updateNavArrows()},place:function(){if(!this.isInline){var e,i,n,s=parseInt(this.element.parents().filter(function(){return"auto"!=t(this).css("z-index")}).first().css("z-index"))+10;this.component?(e=this.component.offset(),n=e.left,("bottom-left"==this.pickerPosition||"top-left"==this.pickerPosition)&&(n+=this.component.outerWidth()-this.picker.outerWidth())):(e=this.element.offset(),n=e.left),i="top-left"==this.pickerPosition||"top-right"==this.pickerPosition?e.top-this.picker.outerHeight():e.top+this.height,this.picker.css({top:i,left:n,zIndex:s})}},update:function(){var t,e=!1;arguments&&arguments.length&&("string"==typeof arguments[0]||arguments[0]instanceof Date)?(t=arguments[0],e=!0):t=this.element.data("date")||(this.isInput?this.element.val():this.element.find("input").val())||this.initialDate,t||(t=new Date,e=!1),this.date=s.parseDate(t,this.format,this.language,this.formatType),e&&this.setValue(),this.viewDate=this.date<this.startDate?new Date(this.startDate):this.date>this.endDate?new Date(this.endDate):new Date(this.date),this.fill()},fillDow:function(){for(var t=this.weekStart,e="<tr>";t<this.weekStart+7;)e+='<th class="dow">'+n[this.language].daysMin[t++%7]+"</th>";e+="</tr>",this.picker.find(".datetimepicker-days thead").append(e)},fillMonths:function(){for(var t="",e=0;12>e;)t+='<span class="month">'+n[this.language].monthsShort[e++]+"</span>";this.picker.find(".datetimepicker-months td").html(t)},fill:function(){if(null!=this.date&&null!=this.viewDate){var i=new Date(this.viewDate),a=i.getUTCFullYear(),o=i.getUTCMonth(),r=i.getUTCDate(),l=i.getUTCHours(),c=i.getUTCMinutes(),d=this.startDate!==-1/0?this.startDate.getUTCFullYear():-1/0,h=this.startDate!==-1/0?this.startDate.getUTCMonth():-1/0,u=1/0!==this.endDate?this.endDate.getUTCFullYear():1/0,p=1/0!==this.endDate?this.endDate.getUTCMonth():1/0,f=new e(this.date.getUTCFullYear(),this.date.getUTCMonth(),this.date.getUTCDate()).valueOf(),g=new Date;if(this.picker.find(".datetimepicker-days thead th:eq(1)").text(n[this.language].months[o]+" "+a),"time"==this.formatViewType){var v=l%12?l%12:12,m=(10>v?"0":"")+v,b=(10>c?"0":"")+c,w=n[this.language].meridiem[12>l?0:1];this.picker.find(".datetimepicker-hours thead th:eq(1)").text(m+":"+b+" "+w.toUpperCase()),this.picker.find(".datetimepicker-minutes thead th:eq(1)").text(m+":"+b+" "+w.toUpperCase())}else this.picker.find(".datetimepicker-hours thead th:eq(1)").text(r+" "+n[this.language].months[o]+" "+a),this.picker.find(".datetimepicker-minutes thead th:eq(1)").text(r+" "+n[this.language].months[o]+" "+a);this.picker.find("tfoot th.today").text(n[this.language].today).toggle(this.todayBtn!==!1),this.updateNavArrows(),this.fillMonths();var y=e(a,o-1,28,0,0,0,0),x=s.getDaysInMonth(y.getUTCFullYear(),y.getUTCMonth());y.setUTCDate(x),y.setUTCDate(x-(y.getUTCDay()-this.weekStart+7)%7);var T=new Date(y);T.setUTCDate(T.getUTCDate()+42),T=T.valueOf();for(var C,S=[];y.valueOf()<T;)y.getUTCDay()==this.weekStart&&S.push("<tr>"),C="",y.getUTCFullYear()<a||y.getUTCFullYear()==a&&y.getUTCMonth()<o?C+=" old":(y.getUTCFullYear()>a||y.getUTCFullYear()==a&&y.getUTCMonth()>o)&&(C+=" new"),this.todayHighlight&&y.getUTCFullYear()==g.getFullYear()&&y.getUTCMonth()==g.getMonth()&&y.getUTCDate()==g.getDate()&&(C+=" today"),y.valueOf()==f&&(C+=" active"),(y.valueOf()+864e5<=this.startDate||y.valueOf()>this.endDate||-1!==t.inArray(y.getUTCDay(),this.daysOfWeekDisabled))&&(C+=" disabled"),S.push('<td class="day'+C+'">'+y.getUTCDate()+"</td>"),y.getUTCDay()==this.weekEnd&&S.push("</tr>"),y.setUTCDate(y.getUTCDate()+1);this.picker.find(".datetimepicker-days tbody").empty().append(S.join("")),S=[];for(var k="",_="",$="",D=0;24>D;D++){var I=e(a,o,r,D);C="",I.valueOf()+36e5<=this.startDate||I.valueOf()>this.endDate?C+=" disabled":l==D&&(C+=" active"),this.showMeridian&&2==n[this.language].meridiem.length?(_=12>D?n[this.language].meridiem[0]:n[this.language].meridiem[1],_!=$&&(""!=$&&S.push("</fieldset>"),S.push('<fieldset class="hour"><legend>'+_.toUpperCase()+"</legend>")),$=_,k=D%12?D%12:12,S.push('<span class="hour'+C+" hour_"+(12>D?"am":"pm")+'">'+k+"</span>"),23==D&&S.push("</fieldset>")):(k=D+":00",S.push('<span class="hour'+C+'">'+k+"</span>"))}this.picker.find(".datetimepicker-hours td").html(S.join("")),S=[],k="",_="",$="";for(var D=0;60>D;D+=this.minuteStep){var I=e(a,o,r,l,D,0);C="",I.valueOf()<this.startDate||I.valueOf()>this.endDate?C+=" disabled":Math.floor(c/this.minuteStep)==Math.floor(D/this.minuteStep)&&(C+=" active"),this.showMeridian&&2==n[this.language].meridiem.length?(_=12>l?n[this.language].meridiem[0]:n[this.language].meridiem[1],_!=$&&(""!=$&&S.push("</fieldset>"),S.push('<fieldset class="minute"><legend>'+_.toUpperCase()+"</legend>")),$=_,k=l%12?l%12:12,S.push('<span class="minute'+C+'">'+k+":"+(10>D?"0"+D:D)+"</span>"),59==D&&S.push("</fieldset>")):(k=D+":00",S.push('<span class="minute'+C+'">'+l+":"+(10>D?"0"+D:D)+"</span>"))}this.picker.find(".datetimepicker-minutes td").html(S.join(""));var M=this.date.getUTCFullYear(),E=this.picker.find(".datetimepicker-months").find("th:eq(1)").text(a).end().find("span").removeClass("active");M==a&&E.eq(this.date.getUTCMonth()).addClass("active"),(d>a||a>u)&&E.addClass("disabled"),a==d&&E.slice(0,h).addClass("disabled"),a==u&&E.slice(p+1).addClass("disabled"),S="",a=10*parseInt(a/10,10);var A=this.picker.find(".datetimepicker-years").find("th:eq(1)").text(a+"-"+(a+9)).end().find("td");a-=1;for(var D=-1;11>D;D++)S+='<span class="year'+(-1==D||10==D?" old":"")+(M==a?" active":"")+(d>a||a>u?" disabled":"")+'">'+a+"</span>",a+=1;A.html(S),this.place()}},updateNavArrows:function(){var t=new Date(this.viewDate),e=t.getUTCFullYear(),i=t.getUTCMonth(),n=t.getUTCDate(),s=t.getUTCHours();switch(this.viewMode){case 0:this.startDate!==-1/0&&e<=this.startDate.getUTCFullYear()&&i<=this.startDate.getUTCMonth()&&n<=this.startDate.getUTCDate()&&s<=this.startDate.getUTCHours()?this.picker.find(".prev").css({visibility:"hidden"}):this.picker.find(".prev").css({visibility:"visible"}),1/0!==this.endDate&&e>=this.endDate.getUTCFullYear()&&i>=this.endDate.getUTCMonth()&&n>=this.endDate.getUTCDate()&&s>=this.endDate.getUTCHours()?this.picker.find(".next").css({visibility:"hidden"}):this.picker.find(".next").css({visibility:"visible"});break;case 1:this.startDate!==-1/0&&e<=this.startDate.getUTCFullYear()&&i<=this.startDate.getUTCMonth()&&n<=this.startDate.getUTCDate()?this.picker.find(".prev").css({visibility:"hidden"}):this.picker.find(".prev").css({visibility:"visible"}),1/0!==this.endDate&&e>=this.endDate.getUTCFullYear()&&i>=this.endDate.getUTCMonth()&&n>=this.endDate.getUTCDate()?this.picker.find(".next").css({visibility:"hidden"}):this.picker.find(".next").css({visibility:"visible"});break;case 2:this.startDate!==-1/0&&e<=this.startDate.getUTCFullYear()&&i<=this.startDate.getUTCMonth()?this.picker.find(".prev").css({visibility:"hidden"}):this.picker.find(".prev").css({visibility:"visible"}),1/0!==this.endDate&&e>=this.endDate.getUTCFullYear()&&i>=this.endDate.getUTCMonth()?this.picker.find(".next").css({visibility:"hidden"}):this.picker.find(".next").css({visibility:"visible"});break;case 3:case 4:this.startDate!==-1/0&&e<=this.startDate.getUTCFullYear()?this.picker.find(".prev").css({visibility:"hidden"}):this.picker.find(".prev").css({visibility:"visible"}),1/0!==this.endDate&&e>=this.endDate.getUTCFullYear()?this.picker.find(".next").css({visibility:"hidden"}):this.picker.find(".next").css({visibility:"visible"})}},mousewheel:function(e){if(e.preventDefault(),e.stopPropagation(),!this.wheelPause){this.wheelPause=!0;var i=e.originalEvent,n=i.wheelDelta,s=n>0?1:0===n?0:-1;this.wheelViewModeNavigationInverseDirection&&(s=-s),this.showMode(s),setTimeout(t.proxy(function(){this.wheelPause=!1},this),this.wheelViewModeNavigationDelay)}},click:function(i){i.stopPropagation(),i.preventDefault();var n=t(i.target).closest("span, td, th, legend");if(1==n.length){if(n.is(".disabled"))return this.element.trigger({type:"outOfRange",date:this.viewDate,startDate:this.startDate,endDate:this.endDate}),void 0;switch(n[0].nodeName.toLowerCase()){case"th":switch(n[0].className){case"switch":this.showMode(1);break;case"prev":case"next":var a=s.modes[this.viewMode].navStep*("prev"==n[0].className?-1:1);switch(this.viewMode){case 0:this.viewDate=this.moveHour(this.viewDate,a);break;case 1:this.viewDate=this.moveDate(this.viewDate,a);break;case 2:this.viewDate=this.moveMonth(this.viewDate,a);break;case 3:case 4:this.viewDate=this.moveYear(this.viewDate,a)}this.fill();break;case"today":var o=new Date;o=e(o.getFullYear(),o.getMonth(),o.getDate(),o.getHours(),o.getMinutes(),o.getSeconds(),0),this.viewMode=this.startViewMode,this.showMode(0),this._setDate(o),this.fill(),this.autoclose&&this.hide()}break;case"span":if(!n.is(".disabled")){var r=this.viewDate.getUTCFullYear(),l=this.viewDate.getUTCMonth(),c=this.viewDate.getUTCDate(),d=this.viewDate.getUTCHours(),h=this.viewDate.getUTCMinutes(),u=this.viewDate.getUTCSeconds();if(n.is(".month")?(this.viewDate.setUTCDate(1),l=n.parent().find("span").index(n),c=this.viewDate.getUTCDate(),this.viewDate.setUTCMonth(l),this.element.trigger({type:"changeMonth",date:this.viewDate}),this.viewSelect>=3&&this._setDate(e(r,l,c,d,h,u,0))):n.is(".year")?(this.viewDate.setUTCDate(1),r=parseInt(n.text(),10)||0,this.viewDate.setUTCFullYear(r),this.element.trigger({type:"changeYear",date:this.viewDate}),this.viewSelect>=4&&this._setDate(e(r,l,c,d,h,u,0))):n.is(".hour")?(d=parseInt(n.text(),10)||0,(n.hasClass("hour_am")||n.hasClass("hour_pm"))&&(12==d&&n.hasClass("hour_am")?d=0:12!=d&&n.hasClass("hour_pm")&&(d+=12)),this.viewDate.setUTCHours(d),this.element.trigger({type:"changeHour",date:this.viewDate}),this.viewSelect>=1&&this._setDate(e(r,l,c,d,h,u,0))):n.is(".minute")&&(h=parseInt(n.text().substr(n.text().indexOf(":")+1),10)||0,this.viewDate.setUTCMinutes(h),this.element.trigger({type:"changeMinute",date:this.viewDate}),this.viewSelect>=0&&this._setDate(e(r,l,c,d,h,u,0))),0!=this.viewMode){var p=this.viewMode;this.showMode(-1),this.fill(),p==this.viewMode&&this.autoclose&&this.hide()}else this.fill(),this.autoclose&&this.hide()}break;case"td":if(n.is(".day")&&!n.is(".disabled")){var c=parseInt(n.text(),10)||1,r=this.viewDate.getUTCFullYear(),l=this.viewDate.getUTCMonth(),d=this.viewDate.getUTCHours(),h=this.viewDate.getUTCMinutes(),u=this.viewDate.getUTCSeconds();n.is(".old")?0===l?(l=11,r-=1):l-=1:n.is(".new")&&(11==l?(l=0,r+=1):l+=1),this.viewDate.setUTCFullYear(r),this.viewDate.setUTCMonth(l),this.viewDate.setUTCDate(c),this.element.trigger({type:"changeDay",date:this.viewDate}),this.viewSelect>=2&&this._setDate(e(r,l,c,d,h,u,0))}var p=this.viewMode;this.showMode(-1),this.fill(),p==this.viewMode&&this.autoclose&&this.hide()}}},_setDate:function(t,e){e&&"date"!=e||(this.date=t),e&&"view"!=e||(this.viewDate=t),this.fill(),this.setValue();var i;this.isInput?i=this.element:this.component&&(i=this.element.find("input")),i&&(i.change(),this.autoclose&&(!e||"date"==e)),this.element.trigger({type:"changeDate",date:this.date})},moveMinute:function(t,e){if(!e)return t;var i=new Date(t.valueOf());return i.setUTCMinutes(i.getUTCMinutes()+e*this.minuteStep),i},moveHour:function(t,e){if(!e)return t;var i=new Date(t.valueOf());return i.setUTCHours(i.getUTCHours()+e),i},moveDate:function(t,e){if(!e)return t;var i=new Date(t.valueOf());return i.setUTCDate(i.getUTCDate()+e),i},moveMonth:function(t,e){if(!e)return t;var i,n,s=new Date(t.valueOf()),a=s.getUTCDate(),o=s.getUTCMonth(),r=Math.abs(e);if(e=e>0?1:-1,1==r)n=-1==e?function(){return s.getUTCMonth()==o}:function(){return s.getUTCMonth()!=i},i=o+e,s.setUTCMonth(i),(0>i||i>11)&&(i=(i+12)%12);else{for(var l=0;r>l;l++)s=this.moveMonth(s,e);i=s.getUTCMonth(),s.setUTCDate(a),n=function(){return i!=s.getUTCMonth()}}for(;n();)s.setUTCDate(--a),s.setUTCMonth(i);return s},moveYear:function(t,e){return this.moveMonth(t,12*e)},dateWithinRange:function(t){return t>=this.startDate&&t<=this.endDate},keydown:function(t){if(this.picker.is(":not(:visible)"))return 27==t.keyCode&&this.show(),void 0;var e,i,n,s=!1;switch(t.keyCode){case 27:this.hide(),t.preventDefault();break;case 37:case 39:if(!this.keyboardNavigation)break;e=37==t.keyCode?-1:1,viewMode=this.viewMode,t.ctrlKey?viewMode+=2:t.shiftKey&&(viewMode+=1),4==viewMode?(i=this.moveYear(this.date,e),n=this.moveYear(this.viewDate,e)):3==viewMode?(i=this.moveMonth(this.date,e),n=this.moveMonth(this.viewDate,e)):2==viewMode?(i=this.moveDate(this.date,e),n=this.moveDate(this.viewDate,e)):1==viewMode?(i=this.moveHour(this.date,e),n=this.moveHour(this.viewDate,e)):0==viewMode&&(i=this.moveMinute(this.date,e),n=this.moveMinute(this.viewDate,e)),this.dateWithinRange(i)&&(this.date=i,this.viewDate=n,this.setValue(),this.update(),t.preventDefault(),s=!0);break;case 38:case 40:if(!this.keyboardNavigation)break;e=38==t.keyCode?-1:1,viewMode=this.viewMode,t.ctrlKey?viewMode+=2:t.shiftKey&&(viewMode+=1),4==viewMode?(i=this.moveYear(this.date,e),n=this.moveYear(this.viewDate,e)):3==viewMode?(i=this.moveMonth(this.date,e),n=this.moveMonth(this.viewDate,e)):2==viewMode?(i=this.moveDate(this.date,7*e),n=this.moveDate(this.viewDate,7*e)):1==viewMode?this.showMeridian?(i=this.moveHour(this.date,6*e),n=this.moveHour(this.viewDate,6*e)):(i=this.moveHour(this.date,4*e),n=this.moveHour(this.viewDate,4*e)):0==viewMode&&(i=this.moveMinute(this.date,4*e),n=this.moveMinute(this.viewDate,4*e)),this.dateWithinRange(i)&&(this.date=i,this.viewDate=n,this.setValue(),this.update(),t.preventDefault(),s=!0);break;case 13:if(0!=this.viewMode){var a=this.viewMode;this.showMode(-1),this.fill(),a==this.viewMode&&this.autoclose&&this.hide()}else this.fill(),this.autoclose&&this.hide();t.preventDefault();break;case 9:this.hide()}if(s){var o;this.isInput?o=this.element:this.component&&(o=this.element.find("input")),o&&o.change(),this.element.trigger({type:"changeDate",date:this.date})}},showMode:function(t){if(t){var e=Math.max(0,Math.min(s.modes.length-1,this.viewMode+t));e>=this.minView&&e<=this.maxView&&(this.element.trigger({type:"changeMode",date:this.viewDate,oldViewMode:this.viewMode,newViewMode:e}),this.viewMode=e)}this.picker.find(">div").hide().filter(".datetimepicker-"+s.modes[this.viewMode].clsName).css("display","block"),this.updateNavArrows()},reset:function(){this._setDate(null,"date")}},t.fn.datetimepicker=function(e){var n=Array.apply(null,arguments);return n.shift(),this.each(function(){var s=t(this),a=s.data("datetimepicker"),o="object"==typeof e&&e;a||s.data("datetimepicker",a=new i(this,t.extend({},t.fn.datetimepicker.defaults,o))),"string"==typeof e&&"function"==typeof a[e]&&a[e].apply(a,n)})},t.fn.datetimepicker.defaults={},t.fn.datetimepicker.Constructor=i;var n=t.fn.datetimepicker.dates={en:{days:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],daysShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat","Sun"],daysMin:["Su","Mo","Tu","We","Th","Fr","Sa","Su"],months:["January","February","March","April","May","June","July","August","September","October","November","December"],monthsShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],meridiem:["am","pm"],suffix:["st","nd","rd","th"],today:"Today"}},s={modes:[{clsName:"minutes",navFnc:"Hours",navStep:1},{clsName:"hours",navFnc:"Date",navStep:1},{clsName:"days",navFnc:"Month",navStep:1},{clsName:"months",navFnc:"FullYear",navStep:1},{clsName:"years",navFnc:"FullYear",navStep:10}],isLeapYear:function(t){return 0===t%4&&0!==t%100||0===t%400},getDaysInMonth:function(t,e){return[31,s.isLeapYear(t)?29:28,31,30,31,30,31,31,30,31,30,31][e]},getDefaultFormat:function(t,e){if("standard"==t)return"input"==e?"yyyy-mm-dd hh:ii":"yyyy-mm-dd hh:ii:ss";if("php"==t)return"input"==e?"Y-m-d H:i":"Y-m-d H:i:s";throw new Error("Invalid format type.")},validParts:function(t){if("standard"==t)return/hh?|HH?|p|P|ii?|ss?|dd?|DD?|mm?|MM?|yy(?:yy)?/g;if("php"==t)return/[dDjlNwzFmMnStyYaABgGhHis]/g;throw new Error("Invalid format type.")},nonpunctuation:/[^ -\/:-@\[-`{-~\t\n\rTZ]+/g,parseFormat:function(t,e){var i=t.replace(this.validParts(e),"\0").split("\0"),n=t.match(this.validParts(e));if(!i||!i.length||!n||0==n.length)throw new Error("Invalid date format.");return{separators:i,parts:n}},parseDate:function(s,a,o,r){if(s instanceof Date){var l=new Date(s.valueOf()-6e4*s.getTimezoneOffset());return l.setMilliseconds(0),l}if(/^\d{4}\-\d{1,2}\-\d{1,2}$/.test(s)&&(a=this.parseFormat("yyyy-mm-dd",r)),/^\d{4}\-\d{1,2}\-\d{1,2}[T ]\d{1,2}\:\d{1,2}$/.test(s)&&(a=this.parseFormat("yyyy-mm-dd hh:ii",r)),/^\d{4}\-\d{1,2}\-\d{1,2}[T ]\d{1,2}\:\d{1,2}\:\d{1,2}[Z]{0,1}$/.test(s)&&(a=this.parseFormat("yyyy-mm-dd hh:ii:ss",r)),/^[-+]\d+[dmwy]([\s,]+[-+]\d+[dmwy])*$/.test(s)){var c,d,h=/([-+]\d+)([dmwy])/,u=s.match(/([-+]\d+)([dmwy])/g);s=new Date;for(var p=0;p<u.length;p++)switch(c=h.exec(u[p]),d=parseInt(c[1]),c[2]){case"d":s.setUTCDate(s.getUTCDate()+d);break;case"m":s=i.prototype.moveMonth.call(i.prototype,s,d);break;case"w":s.setUTCDate(s.getUTCDate()+7*d);break;case"y":s=i.prototype.moveYear.call(i.prototype,s,d)}return e(s.getUTCFullYear(),s.getUTCMonth(),s.getUTCDate(),s.getUTCHours(),s.getUTCMinutes(),s.getUTCSeconds(),0)}var f,g,c,u=s&&s.match(this.nonpunctuation)||[],s=new Date(0,0,0,0,0,0,0),v={},m=["hh","h","ii","i","ss","s","yyyy","yy","M","MM","m","mm","D","DD","d","dd","H","HH","p","P"],b={hh:function(t,e){return t.setUTCHours(e)},h:function(t,e){return t.setUTCHours(e)},HH:function(t,e){return t.setUTCHours(12==e?0:e)},H:function(t,e){return t.setUTCHours(12==e?0:e)},ii:function(t,e){return t.setUTCMinutes(e)},i:function(t,e){return t.setUTCMinutes(e)},ss:function(t,e){return t.setUTCSeconds(e)},s:function(t,e){return t.setUTCSeconds(e)},yyyy:function(t,e){return t.setUTCFullYear(e)},yy:function(t,e){return t.setUTCFullYear(2e3+e)},m:function(t,e){for(e-=1;0>e;)e+=12;for(e%=12,t.setUTCMonth(e);t.getUTCMonth()!=e;)t.setUTCDate(t.getUTCDate()-1);return t},d:function(t,e){return t.setUTCDate(e)},p:function(t,e){return t.setUTCHours(1==e?t.getUTCHours()+12:t.getUTCHours())}};if(b.M=b.MM=b.mm=b.m,b.dd=b.d,b.P=b.p,s=e(s.getFullYear(),s.getMonth(),s.getDate(),s.getHours(),s.getMinutes(),s.getSeconds()),u.length==a.parts.length){for(var p=0,w=a.parts.length;w>p;p++){if(f=parseInt(u[p],10),c=a.parts[p],isNaN(f))switch(c){case"MM":g=t(n[o].months).filter(function(){var t=this.slice(0,u[p].length),e=u[p].slice(0,t.length);return t==e}),f=t.inArray(g[0],n[o].months)+1;break;case"M":g=t(n[o].monthsShort).filter(function(){var t=this.slice(0,u[p].length),e=u[p].slice(0,t.length);return t==e}),f=t.inArray(g[0],n[o].monthsShort)+1;break;case"p":case"P":f=t.inArray(u[p].toLowerCase(),n[o].meridiem)}v[c]=f}for(var y,p=0;p<m.length;p++)y=m[p],y in v&&!isNaN(v[y])&&b[y](s,v[y])}return s},formatDate:function(e,i,a,o){if(null==e)return"";var r;if("standard"==o)r={yy:e.getUTCFullYear().toString().substring(2),yyyy:e.getUTCFullYear(),m:e.getUTCMonth()+1,M:n[a].monthsShort[e.getUTCMonth()],MM:n[a].months[e.getUTCMonth()],d:e.getUTCDate(),D:n[a].daysShort[e.getUTCDay()],DD:n[a].days[e.getUTCDay()],p:2==n[a].meridiem.length?n[a].meridiem[e.getUTCHours()<12?0:1]:"",h:e.getUTCHours(),i:e.getUTCMinutes(),s:e.getUTCSeconds()},r.H=0==r.h%12?12:r.h%12,r.HH=(r.H<10?"0":"")+r.H,r.P=r.p.toUpperCase(),r.hh=(r.h<10?"0":"")+r.h,r.ii=(r.i<10?"0":"")+r.i,r.ss=(r.s<10?"0":"")+r.s,r.dd=(r.d<10?"0":"")+r.d,r.mm=(r.m<10?"0":"")+r.m;else{if("php"!=o)throw new Error("Invalid format type.");r={y:e.getUTCFullYear().toString().substring(2),Y:e.getUTCFullYear(),F:n[a].months[e.getUTCMonth()],M:n[a].monthsShort[e.getUTCMonth()],n:e.getUTCMonth()+1,t:s.getDaysInMonth(e.getUTCFullYear(),e.getUTCMonth()),j:e.getUTCDate(),l:n[a].days[e.getUTCDay()],D:n[a].daysShort[e.getUTCDay()],w:e.getUTCDay(),N:0==e.getUTCDay()?7:e.getUTCDay(),S:e.getUTCDate()%10<=n[a].suffix.length?n[a].suffix[e.getUTCDate()%10-1]:"",a:2==n[a].meridiem.length?n[a].meridiem[e.getUTCHours()<12?0:1]:"",g:0==e.getUTCHours()%12?12:e.getUTCHours()%12,G:e.getUTCHours(),i:e.getUTCMinutes(),s:e.getUTCSeconds()},r.m=(r.n<10?"0":"")+r.n,r.d=(r.j<10?"0":"")+r.j,r.A=r.a.toString().toUpperCase(),r.h=(r.g<10?"0":"")+r.g,r.H=(r.G<10?"0":"")+r.G,r.i=(r.i<10?"0":"")+r.i,r.s=(r.s<10?"0":"")+r.s}for(var e=[],l=t.extend([],i.separators),c=0,d=i.parts.length;d>c;c++)l.length&&e.push(l.shift()),e.push(r[i.parts[c]]);return e.join("")},convertViewMode:function(t){switch(t){case 4:case"decade":t=4;break;case 3:case"year":t=3;break;case 2:case"month":t=2;break;case 1:case"day":t=1;break;case 0:case"hour":t=0}return t},headTemplate:'<thead><tr><th class="prev"><i class="fa fa-angle-left"/></th><th colspan="5" class="switch"></th><th class="next"><i class="fa fa-angle-right"/></th></tr></thead>',contTemplate:'<tbody><tr><td colspan="7"></td></tr></tbody>',footTemplate:'<tfoot><tr><th colspan="7" class="today"></th></tr></tfoot>'};s.template='<div class="datetimepicker"><div class="datetimepicker-minutes"><table class=" table-condensed">'+s.headTemplate+s.contTemplate+s.footTemplate+"</table>"+"</div>"+'<div class="datetimepicker-hours">'+'<table class=" table-condensed">'+s.headTemplate+s.contTemplate+s.footTemplate+"</table>"+"</div>"+'<div class="datetimepicker-days">'+'<table class=" table-condensed">'+s.headTemplate+"<tbody></tbody>"+s.footTemplate+"</table>"+"</div>"+'<div class="datetimepicker-months">'+'<table class="table-condensed">'+s.headTemplate+s.contTemplate+s.footTemplate+"</table>"+"</div>"+'<div class="datetimepicker-years">'+'<table class="table-condensed">'+s.headTemplate+s.contTemplate+s.footTemplate+"</table>"+"</div>"+"</div>",t.fn.datetimepicker.DPGlobal=s,t.fn.datetimepicker.noConflict=function(){return t.fn.datetimepicker=old,this},t(document).on("focus.datetimepicker.data-api click.datetimepicker.data-api",'[data-provide="datetimepicker"]',function(e){var i=t(this);i.data("datetimepicker")||(e.preventDefault(),i.datetimepicker("show"))}),t(function(){t('[data-provide="datetimepicker-inline"]').datetimepicker()})}(window.jQuery);
var ComponentsFormTools = function () {

    var handleTwitterTypeahead = function() {

        // Example #1
        // instantiate the bloodhound suggestion engine
        var numbers = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.num); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          local: [
            { num: 'metronic' },
            { num: 'keenthemes' },
            { num: 'metronic theme' },
            { num: 'metronic template' },
            { num: 'keenthemes team' }
          ]
        });
         
        // initialize the bloodhound suggestion engine
        numbers.initialize();
         
        // instantiate the typeahead UI
        if (Metronic.isRTL()) {
          $('#typeahead_example_1').attr("dir", "rtl");  
        }
        $('#typeahead_example_1').typeahead(null, {
          displayKey: 'num',
          hint: (Metronic.isRTL() ? false : true),
          source: numbers.ttAdapter()
        });

        // Example #2
        var countries = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.name); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          limit: 10,
          prefetch: {
            url: 'demo/typeahead_countries.json',
            filter: function(list) {
              return $.map(list, function(country) { return { name: country }; });
            }
          }
        });
 
        countries.initialize();
         
        if (Metronic.isRTL()) {
          $('#typeahead_example_2').attr("dir", "rtl");  
        } 
        $('#typeahead_example_2').typeahead(null, {
          name: 'typeahead_example_2',
          displayKey: 'name',
          hint: (Metronic.isRTL() ? false : true),
          source: countries.ttAdapter()
        });

        // Example #3
        var custom = new Bloodhound({
          datumTokenizer: function(d) { return d.tokens; },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          remote: 'demo/typeahead_custom.php?query=%QUERY'
        });
         
        custom.initialize();
         
        if (Metronic.isRTL()) {
          $('#typeahead_example_3').attr("dir", "rtl");  
        }  
        $('#typeahead_example_3').typeahead(null, {
          name: 'datypeahead_example_3',
          displayKey: 'name',
          source: custom.ttAdapter(),
          hint: (Metronic.isRTL() ? false : true),
          templates: {
            suggestion: Handlebars.compile([
              '<div class="media">',
                    '<div class="pull-left">',
                        '<div class="media-object">',
                            '<img src="{{img}}" width="50" height="50"/>',
                        '</div>',
                    '</div>',
                    '<div class="media-body">',
                        '<h4 class="media-heading">{{value}}</h4>',
                        '<p>{{desc}}</p>',
                    '</div>',
              '</div>',
            ].join(''))
          }
        });

        // Example #4

        var nba = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.team); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          prefetch: 'demo/typeahead_nba.json'
        });
         
        var nhl = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.team); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          prefetch: 'demo/typeahead_nhl.json'
        });
         
        nba.initialize();
        nhl.initialize();
         
        if (Metronic.isRTL()) {
          $('#typeahead_example_4').attr("dir", "rtl");  
        }
        $('#typeahead_example_4').typeahead({
          hint: (Metronic.isRTL() ? false : true),
          highlight: true
        },
        {
          name: 'nba',
          displayKey: 'team',
          source: nba.ttAdapter(),
          templates: {
                header: '<h3>NBA Teams</h3>'
          }
        },
        {
          name: 'nhl',
          displayKey: 'team',
          source: nhl.ttAdapter(),
          templates: {
                header: '<h3>NHL Teams</h3>'
          }
        });

    }

    var handleTwitterTypeaheadModal = function() {

        // Example #1
        // instantiate the bloodhound suggestion engine
        var numbers = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.num); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          local: [
            { num: 'metronic' },
            { num: 'keenthemes' },
            { num: 'metronic theme' },
            { num: 'metronic template' },
            { num: 'keenthemes team' }
          ]
        });
         
        // initialize the bloodhound suggestion engine
        numbers.initialize();
         
        // instantiate the typeahead UI
        if (Metronic.isRTL()) {
          $('#typeahead_example_modal_1').attr("dir", "rtl");  
        }
        $('#typeahead_example_modal_1').typeahead(null, {
          displayKey: 'num',
          hint: (Metronic.isRTL() ? false : true),
          source: numbers.ttAdapter()
        });

        // Example #2
        var countries = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.name); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          limit: 10,
          prefetch: {
            url: 'demo/typeahead_countries.json',
            filter: function(list) {
              return $.map(list, function(country) { return { name: country }; });
            }
          }
        });
 
        countries.initialize();
         
        if (Metronic.isRTL()) {
          $('#typeahead_example_modal_2').attr("dir", "rtl");  
        }
        $('#typeahead_example_modal_2').typeahead(null, {
          name: 'typeahead_example_modal_2',
          displayKey: 'name',
          hint: (Metronic.isRTL() ? false : true),
          source: countries.ttAdapter()
        });

        // Example #3
        var custom = new Bloodhound({
          datumTokenizer: function(d) { return d.tokens; },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          remote: 'demo/typeahead_custom.php?query=%QUERY'
        });
         
        custom.initialize();
         
        if (Metronic.isRTL()) {
          $('#typeahead_example_modal_3').attr("dir", "rtl");  
        }
        $('#typeahead_example_modal_3').typeahead(null, {
          name: 'datypeahead_example_modal_3',
          displayKey: 'name',
          hint: (Metronic.isRTL() ? false : true),
          source: custom.ttAdapter(),
          templates: {
            suggestion: Handlebars.compile([
              '<div class="media">',
                    '<div class="pull-left">',
                        '<div class="media-object">',
                            '<img src="{{img}}" width="50" height="50"/>',
                        '</div>',
                    '</div>',
                    '<div class="media-body">',
                        '<h4 class="media-heading">{{value}}</h4>',
                        '<p>{{desc}}</p>',
                    '</div>',
              '</div>',
            ].join(''))
          }
        });

        // Example #4

        var nba = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.team); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          limit: 3,
          prefetch: 'demo/typeahead_nba.json'
        });
         
        var nhl = new Bloodhound({
          datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.team); },
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          limit: 3,
          prefetch: 'demo/typeahead_nhl.json'
        });
         
        nba.initialize();
        nhl.initialize();
         
        $('#typeahead_example_modal_4').typeahead({
            hint: (Metronic.isRTL() ? false : true),
            highlight: true
        },
        {
          name: 'nba',
          displayKey: 'team',
          source: nba.ttAdapter(),
          templates: {
                header: '<h3>NBA Teams</h3>'
          }
        },
        {
          name: 'nhl',
          displayKey: 'team',
          source: nhl.ttAdapter(),
          templates: {
                header: '<h3>NHL Teams</h3>'
          }
        });

    }

    var handleBootstrapSwitch = function() {

        $('.switch-radio1').on('switch-change', function () {
            $('.switch-radio1').bootstrapSwitch('toggleRadioState');
        });

        // or
        $('.switch-radio1').on('switch-change', function () {
            $('.switch-radio1').bootstrapSwitch('toggleRadioStateAllowUncheck');
        });

        // or
        $('.switch-radio1').on('switch-change', function () {
            $('.switch-radio1').bootstrapSwitch('toggleRadioStateAllowUncheck', false);
        });

    }

    var handleBootstrapTouchSpin = function() {

        $("#touchspin_demo1").TouchSpin({          
            buttondown_class: 'btn green',
            buttonup_class: 'btn green',
            min: -1000000000,
            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
            prefix: '$'
        }); 
        
        $("#touchspin_demo2").TouchSpin({
            buttondown_class: 'btn blue',
            buttonup_class: 'btn blue',
            min: 0,
            max: 100,
            step: 0.1,
            decimals: 2,
            boostat: 5,
            maxboostedstep: 10,
            postfix: '%'
        });         

        $("#touchspin_demo3").TouchSpin({          
            buttondown_class: 'btn green',
            buttonup_class: 'btn green',
            prefix: "$",
            postfix: "%"
        });
    }

    var handleBootstrapMaxlength = function() {
        $('#maxlength_defaultconfig').maxlength({
            limitReachedClass: "label label-danger",
        })
    
        $('#maxlength_thresholdconfig').maxlength({
            limitReachedClass: "label label-danger",
            threshold: 20
        });

        $('#maxlength_alloptions').maxlength({
            alwaysShow: true,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' chars available.',
            validate: true
        });

        $('#maxlength_textarea').maxlength({
            limitReachedClass: "label label-danger",
            alwaysShow: true
        });

        $('#maxlength_placement').maxlength({
            limitReachedClass: "label label-danger",
            alwaysShow: true,
            placement: Metronic.isRTL() ? 'top-right' : 'top-left'
        });
    }

    var handleSpinners = function () {
        $('#spinner1').spinner();
        $('#spinner2').spinner({disabled: true});
        $('#spinner3').spinner({value:0, min: 0, max: 10});
        $('#spinner4').spinner({value:0, step: 5, min: 0, max: 200});
    }
    
    var handleTagsInput = function () {
        if (!jQuery().tagsInput) {
            return;
        }
        $('#tags_1').tagsInput({
            width: 'auto',
            'onAddTag': function () {
                //alert(1);
            },
        });
        $('#tags_2').tagsInput({
            width: 300
        });
    }
    
    var handleInputMasks = function () {
        $.extend($.inputmask.defaults, {
            'autounmask': true
        });

        $("#mask_date").inputmask("d/m/y", {
            autoUnmask: true
        }); //direct mask        
        $("#mask_date1").inputmask("d/m/y", {
            "placeholder": "*"
        }); //change the placeholder
        $("#mask_date2").inputmask("d/m/y", {
            "placeholder": "dd/mm/yyyy"
        }); //multi-char placeholder
        $("#mask_phone").inputmask("mask", {
            "mask": "(999) 999-9999"
        }); //specifying fn & options
        $("#mask_tin").inputmask({
            "mask": "99-9999999",
            placeholder: "" // remove underscores from the input mask
        }); //specifying options only
        $("#mask_number").inputmask({
            "mask": "9",
            "repeat": 10,
            "greedy": false
        }); // ~ mask "9" or mask "99" or ... mask "9999999999"
        $("#mask_decimal").inputmask('decimal', {
            rightAlignNumerics: false
        }); //disables the right alignment of the decimal input
        $("#mask_currency").inputmask('€ 999.999.999,99', {
            numericInput: true
        }); //123456  =>  € ___.__1.234,56

        $("#mask_currency2").inputmask('€ 999,999,999.99', {
            numericInput: true,
            rightAlignNumerics: false,
            greedy: false
        }); //123456  =>  € ___.__1.234,56
        $("#mask_ssn").inputmask("999-99-9999", {
            placeholder: " ",
            clearMaskOnLostFocus: true
        }); //default
    }

    var handleIPAddressInput = function () {
        $('#input_ipv4').ipAddress();
        $('#input_ipv6').ipAddress({
            v: 6
        });
    }

    var handlePasswordStrengthChecker = function () {
        var initialized = false;
        var input = $("#password_strength");

        input.keydown(function () {
            if (initialized === false) {
                // set base options
                input.pwstrength({
                    raisePower: 1.4,
                    minChar: 8,
                    verdicts: ["Weak", "Normal", "Medium", "Strong", "Very Strong"],
                    scores: [17, 26, 40, 50, 60]
                });

                // add your own rule to calculate the password strength
                input.pwstrength("addRule", "demoRule", function (options, word, score) {
                    return word.match(/[a-z].[0-9]/) && score;
                }, 10, true);

                // set as initialized 
                initialized = true;
            }
        });
    }

    var handleUsernameAvailabilityChecker1 = function () {
        var input = $("#username1_input");

        $("#username1_checker").click(function (e) {
            var pop = $(this);

            if (input.val() === "") {
                input.closest('.form-group').removeClass('has-success').addClass('has-error');

                pop.popover('destroy');
                pop.popover({
                    'placement': (Metronic.isRTL() ? 'left' : 'right'),
                    'html': true,
                    'container': 'body',
                    'content': 'Please enter a username to check its availability.',
                });
                // add error class to the popover
                pop.data('bs.popover').tip().addClass('error');
                // set last poped popover to be closed on click(see Metronic.js => handlePopovers function)     
                Metronic.setLastPopedPopover(pop);
                pop.popover('show');
                e.stopPropagation(); // prevent closing the popover

                return;
            }

            var btn = $(this);

            btn.attr('disabled', true);

            input.attr("readonly", true).
            attr("disabled", true).
            addClass("spinner");

            $.post('demo/username_checker.php', {
                username: input.val()
            }, function (res) {
                btn.attr('disabled', false);

                input.attr("readonly", false).
                attr("disabled", false).
                removeClass("spinner");

                if (res.status == 'OK') {
                    input.closest('.form-group').removeClass('has-error').addClass('has-success');

                    pop.popover('destroy');
                    pop.popover({
                        'html': true,
                        'placement': (Metronic.isRTL() ? 'left' : 'right'),
                        'container': 'body',
                        'content': res.message,
                    });
                    pop.popover('show');
                    pop.data('bs.popover').tip().removeClass('error').addClass('success');
                } else {
                    input.closest('.form-group').removeClass('has-success').addClass('has-error');

                    pop.popover('destroy');
                    pop.popover({
                        'html': true,
                        'placement': (Metronic.isRTL() ? 'left' : 'right'),
                        'container': 'body',
                        'content': res.message,
                    });
                    pop.popover('show');
                    pop.data('bs.popover').tip().removeClass('success').addClass('error');
                    Metronic.setLastPopedPopover(pop);
                }

            }, 'json');

        });
    }

    var handleUsernameAvailabilityChecker2 = function () {
        $("#username2_input").change(function () {
            var input = $(this);

            if (input.val() === "") {
                return;
            }

            input.attr("readonly", true).
            attr("disabled", true).
            addClass("spinner");

            $.post('demo/username_checker.php', {
                username: input.val()
            }, function (res) {
                input.attr("readonly", false).
                attr("disabled", false).
                removeClass("spinner");

                // change popover font color based on the result
                if (res.status == 'OK') {
                    input.closest('.form-group').removeClass('has-error').addClass('has-success');
                    $('.icon-exclamation-sign', input.closest('.form-group')).remove();
                    input.before('<i class="icon-ok"></i>');
                    input.data('bs.popover').tip().removeClass('error').addClass('success');
                } else {
                    input.closest('.form-group').removeClass('has-success').addClass('has-error');
                    $('.icon-ok', input.closest('.form-group')).remove();
                    input.before('<i class="icon-exclamation-sign"></i>');

                    input.popover('destroy');
                    input.popover({
                        'html': true,
                        'placement': (Metronic.isRTL() ? 'left' : 'right'),
                        'container': 'body',
                        'content': res.message,
                    });
                    input.popover('show');
                    input.data('bs.popover').tip().removeClass('success').addClass('error');

                    Metronic.setLastPopedPopover(input);
                }

            }, 'json');

        });
    }

    return {
        //main function to initiate the module
        init: function () {
            handleTwitterTypeahead();
            handleTwitterTypeaheadModal();

            handleBootstrapSwitch();
            handleBootstrapTouchSpin();
            handleBootstrapMaxlength();
            handleSpinners();
            handleTagsInput();
            handleInputMasks();
            handleIPAddressInput();
            handlePasswordStrengthChecker();
            handleUsernameAvailabilityChecker1();
            handleUsernameAvailabilityChecker2();
        }
    };

}();
/*! jQuery Validation Plugin - v1.12.0 - 4/1/2014
 * http://jqueryvalidation.org/
 * Copyright (c) 2014 Jörn Zaefferer; Licensed MIT */
!function(){function a(a){return a.replace(/<.[^<>]*?>/g," ").replace(/&nbsp;|&#160;/gi," ").replace(/[.(),;:!?%#$'\"_+=\/\-“”’]*/g,"")}jQuery.validator.addMethod("maxWords",function(b,c,d){return this.optional(c)||a(b).match(/\b\w+\b/g).length<=d},jQuery.validator.format("Please enter {0} words or less.")),jQuery.validator.addMethod("minWords",function(b,c,d){return this.optional(c)||a(b).match(/\b\w+\b/g).length>=d},jQuery.validator.format("Please enter at least {0} words.")),jQuery.validator.addMethod("rangeWords",function(b,c,d){var e=a(b),f=/\b\w+\b/g;return this.optional(c)||e.match(f).length>=d[0]&&e.match(f).length<=d[1]},jQuery.validator.format("Please enter between {0} and {1} words."))}(),jQuery.validator.addMethod("accept",function(a,b,c){var d,e,f="string"==typeof c?c.replace(/\s/g,"").replace(/,/g,"|"):"image/*",g=this.optional(b);if(g)return g;if("file"===jQuery(b).attr("type")&&(f=f.replace(/\*/g,".*"),b.files&&b.files.length))for(d=0;d<b.files.length;d++)if(e=b.files[d],!e.type.match(new RegExp(".?("+f+")$","i")))return!1;return!0},jQuery.validator.format("Please enter a value with a valid mimetype.")),jQuery.validator.addMethod("alphanumeric",function(a,b){return this.optional(b)||/^\w+$/i.test(a)},"Letters, numbers, and underscores only please"),jQuery.validator.addMethod("bankaccountNL",function(a,b){if(this.optional(b))return!0;if(!/^[0-9]{9}|([0-9]{2} ){3}[0-9]{3}$/.test(a))return!1;var c,d,e,f=a.replace(/ /g,""),g=0,h=f.length;for(c=0;h>c;c++)d=h-c,e=f.substring(c,c+1),g+=d*e;return g%11===0},"Please specify a valid bank account number"),jQuery.validator.addMethod("bankorgiroaccountNL",function(a,b){return this.optional(b)||$.validator.methods.bankaccountNL.call(this,a,b)||$.validator.methods.giroaccountNL.call(this,a,b)},"Please specify a valid bank or giro account number"),jQuery.validator.addMethod("bic",function(a,b){return this.optional(b)||/^([A-Z]{6}[A-Z2-9][A-NP-Z1-2])(X{3}|[A-WY-Z0-9][A-Z0-9]{2})?$/.test(a)},"Please specify a valid BIC code"),jQuery.validator.addMethod("cifES",function(a){"use strict";var b,c,d,e,f,g,h=[];if(a=a.toUpperCase(),!a.match("((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)"))return!1;for(d=0;9>d;d++)h[d]=parseInt(a.charAt(d),10);for(c=h[2]+h[4]+h[6],e=1;8>e;e+=2)f=(2*h[e]).toString(),g=f.charAt(1),c+=parseInt(f.charAt(0),10)+(""===g?0:parseInt(g,10));return/^[ABCDEFGHJNPQRSUVW]{1}/.test(a)?(c+="",b=10-parseInt(c.charAt(c.length-1),10),a+=b,h[8].toString()===String.fromCharCode(64+b)||h[8].toString()===a.charAt(a.length-1)):!1},"Please specify a valid CIF number."),jQuery.validator.addMethod("creditcardtypes",function(a,b,c){if(/[^0-9\-]+/.test(a))return!1;a=a.replace(/\D/g,"");var d=0;return c.mastercard&&(d|=1),c.visa&&(d|=2),c.amex&&(d|=4),c.dinersclub&&(d|=8),c.enroute&&(d|=16),c.discover&&(d|=32),c.jcb&&(d|=64),c.unknown&&(d|=128),c.all&&(d=255),1&d&&/^(5[12345])/.test(a)?16===a.length:2&d&&/^(4)/.test(a)?16===a.length:4&d&&/^(3[47])/.test(a)?15===a.length:8&d&&/^(3(0[012345]|[68]))/.test(a)?14===a.length:16&d&&/^(2(014|149))/.test(a)?15===a.length:32&d&&/^(6011)/.test(a)?16===a.length:64&d&&/^(3)/.test(a)?16===a.length:64&d&&/^(2131|1800)/.test(a)?15===a.length:128&d?!0:!1},"Please enter a valid credit card number."),jQuery.validator.addMethod("currency",function(a,b,c){var d,e="string"==typeof c,f=e?c:c[0],g=e?!0:c[1];return f=f.replace(/,/g,""),f=g?f+"]":f+"]?",d="^["+f+"([1-9]{1}[0-9]{0,2}(\\,[0-9]{3})*(\\.[0-9]{0,2})?|[1-9]{1}[0-9]{0,}(\\.[0-9]{0,2})?|0(\\.[0-9]{0,2})?|(\\.[0-9]{1,2})?)$",d=new RegExp(d),this.optional(b)||d.test(a)},"Please specify a valid currency"),jQuery.validator.addMethod("dateITA",function(a,b){var c,d,e,f,g,h=!1,i=/^\d{1,2}\/\d{1,2}\/\d{4}$/;return i.test(a)?(c=a.split("/"),d=parseInt(c[0],10),e=parseInt(c[1],10),f=parseInt(c[2],10),g=new Date(f,e-1,d,12,0,0,0),h=g.getFullYear()===f&&g.getMonth()===e-1&&g.getDate()===d?!0:!1):h=!1,this.optional(b)||h},"Please enter a correct date"),jQuery.validator.addMethod("dateNL",function(a,b){return this.optional(b)||/^(0?[1-9]|[12]\d|3[01])[\.\/\-](0?[1-9]|1[012])[\.\/\-]([12]\d)?(\d\d)$/.test(a)},"Please enter a correct date"),jQuery.validator.addMethod("extension",function(a,b,c){return c="string"==typeof c?c.replace(/,/g,"|"):"png|jpe?g|gif",this.optional(b)||a.match(new RegExp(".("+c+")$","i"))},jQuery.validator.format("Please enter a value with a valid extension.")),jQuery.validator.addMethod("giroaccountNL",function(a,b){return this.optional(b)||/^[0-9]{1,7}$/.test(a)},"Please specify a valid giro account number"),jQuery.validator.addMethod("iban",function(a,b){if(this.optional(b))return!0;var c,d,e,f,g,h,i,j,k,l=a.replace(/ /g,"").toUpperCase(),m="",n=!0,o="",p="";if(!/^([a-zA-Z0-9]{4} ){2,8}[a-zA-Z0-9]{1,4}|[a-zA-Z0-9]{12,34}$/.test(l))return!1;if(c=l.substring(0,2),h={AL:"\\d{8}[\\dA-Z]{16}",AD:"\\d{8}[\\dA-Z]{12}",AT:"\\d{16}",AZ:"[\\dA-Z]{4}\\d{20}",BE:"\\d{12}",BH:"[A-Z]{4}[\\dA-Z]{14}",BA:"\\d{16}",BR:"\\d{23}[A-Z][\\dA-Z]",BG:"[A-Z]{4}\\d{6}[\\dA-Z]{8}",CR:"\\d{17}",HR:"\\d{17}",CY:"\\d{8}[\\dA-Z]{16}",CZ:"\\d{20}",DK:"\\d{14}",DO:"[A-Z]{4}\\d{20}",EE:"\\d{16}",FO:"\\d{14}",FI:"\\d{14}",FR:"\\d{10}[\\dA-Z]{11}\\d{2}",GE:"[\\dA-Z]{2}\\d{16}",DE:"\\d{18}",GI:"[A-Z]{4}[\\dA-Z]{15}",GR:"\\d{7}[\\dA-Z]{16}",GL:"\\d{14}",GT:"[\\dA-Z]{4}[\\dA-Z]{20}",HU:"\\d{24}",IS:"\\d{22}",IE:"[\\dA-Z]{4}\\d{14}",IL:"\\d{19}",IT:"[A-Z]\\d{10}[\\dA-Z]{12}",KZ:"\\d{3}[\\dA-Z]{13}",KW:"[A-Z]{4}[\\dA-Z]{22}",LV:"[A-Z]{4}[\\dA-Z]{13}",LB:"\\d{4}[\\dA-Z]{20}",LI:"\\d{5}[\\dA-Z]{12}",LT:"\\d{16}",LU:"\\d{3}[\\dA-Z]{13}",MK:"\\d{3}[\\dA-Z]{10}\\d{2}",MT:"[A-Z]{4}\\d{5}[\\dA-Z]{18}",MR:"\\d{23}",MU:"[A-Z]{4}\\d{19}[A-Z]{3}",MC:"\\d{10}[\\dA-Z]{11}\\d{2}",MD:"[\\dA-Z]{2}\\d{18}",ME:"\\d{18}",NL:"[A-Z]{4}\\d{10}",NO:"\\d{11}",PK:"[\\dA-Z]{4}\\d{16}",PS:"[\\dA-Z]{4}\\d{21}",PL:"\\d{24}",PT:"\\d{21}",RO:"[A-Z]{4}[\\dA-Z]{16}",SM:"[A-Z]\\d{10}[\\dA-Z]{12}",SA:"\\d{2}[\\dA-Z]{18}",RS:"\\d{18}",SK:"\\d{20}",SI:"\\d{15}",ES:"\\d{20}",SE:"\\d{20}",CH:"\\d{5}[\\dA-Z]{12}",TN:"\\d{20}",TR:"\\d{5}[\\dA-Z]{17}",AE:"\\d{3}\\d{16}",GB:"[A-Z]{4}\\d{14}",VG:"[\\dA-Z]{4}\\d{16}"},g=h[c],"undefined"!=typeof g&&(i=new RegExp("^[A-Z]{2}\\d{2}"+g+"$",""),!i.test(l)))return!1;for(d=l.substring(4,l.length)+l.substring(0,4),j=0;j<d.length;j++)e=d.charAt(j),"0"!==e&&(n=!1),n||(m+="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ".indexOf(e));for(k=0;k<m.length;k++)f=m.charAt(k),p=""+o+f,o=p%97;return 1===o},"Please specify a valid IBAN"),jQuery.validator.addMethod("integer",function(a,b){return this.optional(b)||/^-?\d+$/.test(a)},"A positive or negative non-decimal number please"),jQuery.validator.addMethod("ipv4",function(a,b){return this.optional(b)||/^(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)$/i.test(a)},"Please enter a valid IP v4 address."),jQuery.validator.addMethod("ipv6",function(a,b){return this.optional(b)||/^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/i.test(a)},"Please enter a valid IP v6 address."),jQuery.validator.addMethod("lettersonly",function(a,b){return this.optional(b)||/^[a-z]+$/i.test(a)},"Letters only please"),jQuery.validator.addMethod("letterswithbasicpunc",function(a,b){return this.optional(b)||/^[a-z\-.,()'"\s]+$/i.test(a)},"Letters or punctuation only please"),jQuery.validator.addMethod("mobileNL",function(a,b){return this.optional(b)||/^((\+|00(\s|\s?\-\s?)?)31(\s|\s?\-\s?)?(\(0\)[\-\s]?)?|0)6((\s|\s?\-\s?)?[0-9]){8}$/.test(a)},"Please specify a valid mobile number"),jQuery.validator.addMethod("mobileUK",function(a,b){return a=a.replace(/\(|\)|\s+|-/g,""),this.optional(b)||a.length>9&&a.match(/^(?:(?:(?:00\s?|\+)44\s?|0)7(?:[1345789]\d{2}|624)\s?\d{3}\s?\d{3})$/)},"Please specify a valid mobile number"),jQuery.validator.addMethod("nieES",function(a){"use strict";return a=a.toUpperCase(),a.match("((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)")?/^[T]{1}/.test(a)?a[8]===/^[T]{1}[A-Z0-9]{8}$/.test(a):/^[XYZ]{1}/.test(a)?a[8]==="TRWAGMYFPDXBNJZSQVHLCKE".charAt(a.replace("X","0").replace("Y","1").replace("Z","2").substring(0,8)%23):!1:!1},"Please specify a valid NIE number."),jQuery.validator.addMethod("nifES",function(a){"use strict";return a=a.toUpperCase(),a.match("((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)")?/^[0-9]{8}[A-Z]{1}$/.test(a)?"TRWAGMYFPDXBNJZSQVHLCKE".charAt(a.substring(8,0)%23)===a.charAt(8):/^[KLM]{1}/.test(a)?a[8]===String.fromCharCode(64):!1:!1},"Please specify a valid NIF number."),jQuery.validator.addMethod("nowhitespace",function(a,b){return this.optional(b)||/^\S+$/i.test(a)},"No white space please"),jQuery.validator.addMethod("pattern",function(a,b,c){return this.optional(b)?!0:("string"==typeof c&&(c=new RegExp(c)),c.test(a))},"Invalid format."),jQuery.validator.addMethod("phoneNL",function(a,b){return this.optional(b)||/^((\+|00(\s|\s?\-\s?)?)31(\s|\s?\-\s?)?(\(0\)[\-\s]?)?|0)[1-9]((\s|\s?\-\s?)?[0-9]){8}$/.test(a)},"Please specify a valid phone number."),jQuery.validator.addMethod("phoneUK",function(a,b){return a=a.replace(/\(|\)|\s+|-/g,""),this.optional(b)||a.length>9&&a.match(/^(?:(?:(?:00\s?|\+)44\s?)|(?:\(?0))(?:\d{2}\)?\s?\d{4}\s?\d{4}|\d{3}\)?\s?\d{3}\s?\d{3,4}|\d{4}\)?\s?(?:\d{5}|\d{3}\s?\d{3})|\d{5}\)?\s?\d{4,5})$/)},"Please specify a valid phone number"),jQuery.validator.addMethod("phoneUS",function(a,b){return a=a.replace(/\s+/g,""),this.optional(b)||a.length>9&&a.match(/^(\+?1-?)?(\([2-9]([02-9]\d|1[02-9])\)|[2-9]([02-9]\d|1[02-9]))-?[2-9]([02-9]\d|1[02-9])-?\d{4}$/)},"Please specify a valid phone number"),jQuery.validator.addMethod("phonesUK",function(a,b){return a=a.replace(/\(|\)|\s+|-/g,""),this.optional(b)||a.length>9&&a.match(/^(?:(?:(?:00\s?|\+)44\s?|0)(?:1\d{8,9}|[23]\d{9}|7(?:[1345789]\d{8}|624\d{6})))$/)},"Please specify a valid uk phone number"),jQuery.validator.addMethod("postalcodeNL",function(a,b){return this.optional(b)||/^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/.test(a)},"Please specify a valid postal code"),jQuery.validator.addMethod("postcodeUK",function(a,b){return this.optional(b)||/^((([A-PR-UWYZ][0-9])|([A-PR-UWYZ][0-9][0-9])|([A-PR-UWYZ][A-HK-Y][0-9])|([A-PR-UWYZ][A-HK-Y][0-9][0-9])|([A-PR-UWYZ][0-9][A-HJKSTUW])|([A-PR-UWYZ][A-HK-Y][0-9][ABEHMNPRVWXY]))\s?([0-9][ABD-HJLNP-UW-Z]{2})|(GIR)\s?(0AA))$/i.test(a)},"Please specify a valid UK postcode"),jQuery.validator.addMethod("require_from_group",function(a,b,c){var d=$(c[1],b.form),e=d.eq(0),f=e.data("valid_req_grp")?e.data("valid_req_grp"):$.extend({},this),g=d.filter(function(){return f.elementValue(this)}).length>=c[0];return e.data("valid_req_grp",f),$(b).data("being_validated")||(d.data("being_validated",!0),d.each(function(){f.element(this)}),d.data("being_validated",!1)),g},jQuery.validator.format("Please fill at least {0} of these fields.")),jQuery.validator.addMethod("skip_or_fill_minimum",function(a,b,c){var d=$(c[1],b.form),e=d.eq(0),f=e.data("valid_skip")?e.data("valid_skip"):$.extend({},this),g=d.filter(function(){return f.elementValue(this)}).length,h=0===g||g>=c[0];return e.data("valid_skip",f),$(b).data("being_validated")||(d.data("being_validated",!0),d.each(function(){f.element(this)}),d.data("being_validated",!1)),h},jQuery.validator.format("Please either skip these fields or fill at least {0} of them.")),jQuery.validator.addMethod("strippedminlength",function(a,b,c){return jQuery(a).text().length>=c},jQuery.validator.format("Please enter at least {0} characters")),jQuery.validator.addMethod("time",function(a,b){return this.optional(b)||/^([01]\d|2[0-3])(:[0-5]\d){1,2}$/.test(a)},"Please enter a valid time, between 00:00 and 23:59"),jQuery.validator.addMethod("time12h",function(a,b){return this.optional(b)||/^((0?[1-9]|1[012])(:[0-5]\d){1,2}(\ ?[AP]M))$/i.test(a)},"Please enter a valid time in 12-hour am/pm format"),jQuery.validator.addMethod("url2",function(a,b){return this.optional(b)||/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)*(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(a)},jQuery.validator.messages.url),jQuery.validator.addMethod("vinUS",function(a){if(17!==a.length)return!1;var b,c,d,e,f,g,h=["A","B","C","D","E","F","G","H","J","K","L","M","N","P","R","S","T","U","V","W","X","Y","Z"],i=[1,2,3,4,5,6,7,8,1,2,3,4,5,7,9,2,3,4,5,6,7,8,9],j=[8,7,6,5,4,3,2,10,0,9,8,7,6,5,4,3,2],k=0;for(b=0;17>b;b++){if(e=j[b],d=a.slice(b,b+1),8===b&&(g=d),isNaN(d)){for(c=0;c<h.length;c++)if(d.toUpperCase()===h[c]){d=i[c],d*=e,isNaN(g)&&8===c&&(g=h[c]);break}}else d*=e;k+=d}return f=k%11,10===f&&(f="X"),f===g?!0:!1},"The specified vehicle identification number (VIN) is invalid."),jQuery.validator.addMethod("zipcodeUS",function(a,b){return this.optional(b)||/^\d{5}-\d{4}$|^\d{5}$/.test(a)},"The specified US ZIP Code is invalid"),jQuery.validator.addMethod("ziprange",function(a,b){return this.optional(b)||/^90[2-5]\d\{2\}-\d{4}$/.test(a)},"Your ZIP-code must be in the range 902xx-xxxx to 905-xx-xxxx");