(window.webpackWcBlocksJsonp=window.webpackWcBlocksJsonp||[]).push([[33],{271:function(e,t,n){"use strict";var c=n(21),i=n.n(c),r=n(172);t.a=function(e){return function(t){return function(n){var c=Object(r.a)(e,n);return React.createElement(t,i()({},n,c))}}}},272:function(e,t,n){"use strict";var c=n(21),i=n.n(c),r=n(26),o=n.n(r),a=n(8),s=n.n(a),l=(n(9),n(273),["children","className","headingLevel"]);t.a=function(e){var t=e.children,n=e.className,c=e.headingLevel,r=o()(e,l),a=s()("wc-block-components-title",n),u="h".concat(c);return React.createElement(u,i()({className:a},r),t)}},273:function(e,t){},277:function(e,t){},282:function(e,t,n){"use strict";var c=n(1);t.a=function(e){var t=e.defaultTitle,n=void 0===t?Object(c.__)("Step","woo-gutenberg-products-block"):t,i=e.defaultDescription,r=void 0===i?Object(c.__)("Step description text.","woo-gutenberg-products-block"):i,o=e.defaultShowStepNumber;return{title:{type:"string",default:n},description:{type:"string",default:r},showStepNumber:{type:"boolean",default:void 0===o||o}}}},291:function(e,t,n){"use strict";var c=n(8),i=n.n(c),r=(n(9),n(272)),o=(n(277),function(e){var t=e.title,n=e.stepHeadingContent;return React.createElement("div",{className:"wc-block-components-checkout-step__heading"},React.createElement(r.a,{"aria-hidden":"true",className:"wc-block-components-checkout-step__title",headingLevel:"2"},t),!!n&&React.createElement("span",{className:"wc-block-components-checkout-step__heading-content"},n))});t.a=function(e){var t=e.id,n=e.className,c=e.title,r=e.legend,a=e.description,s=e.children,l=e.disabled,u=void 0!==l&&l,d=e.showStepNumber,b=void 0===d||d,p=e.stepHeadingContent,f=void 0===p?function(){}:p,h=r||c?"fieldset":"div";return React.createElement(h,{className:i()(n,"wc-block-components-checkout-step",{"wc-block-components-checkout-step--with-step-number":b,"wc-block-components-checkout-step--disabled":u}),id:t,disabled:u},!(!r&&!c)&&React.createElement("legend",{className:"screen-reader-text"},r||c),!!c&&React.createElement(o,{title:c,stepHeadingContent:f()}),React.createElement("div",{className:"wc-block-components-checkout-step__container"},!!a&&React.createElement("p",{className:"wc-block-components-checkout-step__description"},a),React.createElement("div",{className:"wc-block-components-checkout-step__content"},s)))}},310:function(e,t,n){"use strict";n.d(t,"a",(function(){return p}));var c=n(4),i=n.n(c),r=n(26),o=n.n(r),a=n(2),s=n(0),l=n(81),u=n(66),d=["email"];function b(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var c=Object.getOwnPropertySymbols(e);t&&(c=c.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,c)}return n}var p=function(){var e=Object(l.b)().needsShipping,t=Object(u.b)(),n=t.billingData,c=t.setBillingData,r=t.shippingAddress,p=t.setShippingAddress,f=t.shippingAsBilling,h=t.setShippingAsBilling,m=Object(s.useRef)(f),g=Object(s.useRef)(n),O=Object(s.useCallback)((function(e){p(e),f&&c(e)}),[f,p,c]),v=Object(s.useCallback)((function(t){c(t),e||p(t)}),[e,p,c]);Object(s.useEffect)((function(){if(m.current!==f){if(f)g.current=n,c(r);else{var e=g.current,t=(e.email,o()(e,d));c(function(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?b(Object(n),!0).forEach((function(t){i()(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):b(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}({},t))}m.current=f}}),[f,c,r,n]);var j=Object(s.useCallback)((function(e){c({email:e})}),[c]),w=Object(s.useCallback)((function(e){c({phone:e})}),[c]),k=Object(s.useCallback)((function(e){O({phone:e})}),[O]);return{defaultAddressFields:a.defaultAddressFields,shippingFields:r,setShippingFields:O,billingFields:n,setBillingFields:v,setEmail:j,setPhone:w,setShippingPhone:k,shippingAsBilling:f,setShippingAsBilling:h,showShippingFields:e,showBillingFields:!e||!m.current}}},311:function(e,t,n){"use strict";var c=n(1),i=n(325);t.a=function(e){var t=e.id,n=void 0===t?"phone":t,r=e.isRequired,o=void 0!==r&&r,a=e.value,s=void 0===a?"":a,l=e.onChange;return React.createElement(i.a,{id:n,type:"tel",autoComplete:"tel",required:o,label:o?Object(c.__)("Phone","woo-gutenberg-products-block"):Object(c.__)("Phone (optional)","woo-gutenberg-products-block"),value:s,onChange:l})}},361:function(e,t,n){"use strict";var c=n(13),i=n(18),r=n(0),o=n(6),a=n(8),s=n.n(a),l=n(38),u=Object(r.createContext)(!1),d=u.Consumer,b=u.Provider,p=["BUTTON","FIELDSET","INPUT","OPTGROUP","OPTION","SELECT","TEXTAREA"];function f(e){var t=e.className,n=e.children,a=Object(i.a)(e,["className","children"]),u=Object(r.useRef)(),d=function(){l.focus.focusable.find(u.current).forEach((function(e){Object(o.includes)(p,e.nodeName)&&e.setAttribute("disabled",""),"A"===e.nodeName&&e.setAttribute("tabindex",-1);var t=e.getAttribute("tabindex");null!==t&&"-1"!==t&&e.removeAttribute("tabindex"),e.hasAttribute("contenteditable")&&e.setAttribute("contenteditable","false")}))},f=Object(r.useCallback)(Object(o.debounce)(d,{leading:!0}),[]);return Object(r.useLayoutEffect)((function(){d();var e=new window.MutationObserver(f);return e.observe(u.current,{childList:!0,attributes:!0,subtree:!0}),function(){e.disconnect(),f.cancel()}}),[]),Object(r.createElement)(b,{value:!0},Object(r.createElement)("div",Object(c.a)({ref:u,className:s()(t,"components-disabled")},a),n))}f.Consumer=d,t.a=f},404:function(e,t,n){"use strict";n.r(t);var c=n(8),i=n.n(c),r=n(271),o=n(291),a=n(61),s=n(310),l=n(0),u=n(361),d=n(53),b=n(43),p=n(373),f=n(311),h=function(e){var t=e.showCompanyField,n=void 0!==t&&t,c=e.showApartmentField,i=void 0!==c&&c,r=e.showPhoneField,o=void 0!==r&&r,a=e.requireCompanyField,h=void 0!==a&&a,m=e.requirePhoneField,g=void 0!==m&&m,O=Object(s.a)(),v=O.defaultAddressFields,j=O.billingFields,w=O.setBillingFields,k=O.setPhone,y=Object(d.a)().dispatchCheckoutEvent,E=Object(b.a)().isEditor;Object(l.useEffect)((function(){o||k("")}),[o,k]);var P=Object(l.useMemo)((function(){return{company:{hidden:!n,required:h},address_2:{hidden:!i}}}),[n,h,i]),F=E?u.a:l.Fragment;return React.createElement(F,null,React.createElement(p.a,{id:"billing",type:"billing",onChange:function(e){w(e),y("set-billing-address")},values:j,fields:Object.keys(v),fieldConfig:P}),o&&React.createElement(f.a,{isRequired:g,value:j.phone,onChange:function(e){k(e),y("set-phone-number",{step:"billing"})}}))},m=n(4),g=n.n(m),O=n(1),v=n(282);function j(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var c=Object.getOwnPropertySymbols(e);t&&(c=c.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,c)}return n}function w(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?j(Object(n),!0).forEach((function(t){g()(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):j(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}var k=w(w({},Object(v.a)({defaultTitle:Object(O.__)("Billing address","woo-gutenberg-products-block"),defaultDescription:Object(O.__)("Enter the address that matches your card or payment method.","woo-gutenberg-products-block")})),{},{className:{type:"string",default:""},lock:{type:"object",default:{move:!0,remove:!0}}}),y=n(173);t.default=Object(r.a)(k)((function(e){var t=e.title,n=e.description,c=e.showStepNumber,r=e.children,l=e.className,u=Object(a.b)().isProcessing,d=Object(s.a)().showBillingFields,b=Object(y.b)(),p=b.requireCompanyField,f=b.requirePhoneField,m=b.showApartmentField,g=b.showCompanyField,O=b.showPhoneField;return d?React.createElement(o.a,{id:"billing-fields",disabled:u,className:i()("wc-block-checkout__billing-fields",l),title:t,description:n,showStepNumber:c},React.createElement(h,{requireCompanyField:p,showApartmentField:m,showCompanyField:g,showPhoneField:O,requirePhoneField:f}),r):null}))}}]);