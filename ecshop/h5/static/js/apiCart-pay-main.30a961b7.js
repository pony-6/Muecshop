(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["apiCart-pay-main"],{"0ad2":function(e,t,n){"use strict";var a=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("v-uni-view",[n("cu-custom",{attrs:{bgColor:"bg-white",isBack:!0}},[n("template",{attrs:{slot:"backText"},slot:"backText"},[e._v("返回")]),n("template",{attrs:{slot:"content"},slot:"content"},[e._v("支付")])],2),n("div",{staticClass:"container"},[n("div",{staticClass:"total"},[n("div",{staticClass:"label"},[e._v("订单号")]),n("div",{staticClass:"txt"},[e._v(e._s(e.orderId))])]),n("div",{staticClass:"total"},[n("div",{staticClass:"label"},[e._v("订单金额")]),n("div",{staticClass:"txt"},[e._v("¥ "+e._s(e.actualPrice))])]),n("div",{staticClass:"pay-list"},[n("div",{staticClass:"h"},[e._v("请选择支付方式")]),n("div",{staticClass:"b"},["on"==e.hfive_is_use?n("div",{staticClass:"item",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.paytype="wxpayh5"}}},[n("div",{staticClass:"checkbox",class:["wxpayh5"===e.paytype?"checked":""]}),n("div",{staticClass:"icon-wechat"}),n("div",{staticClass:"name"},[e._v("微信支付")])]):e._e(),"on"==e.zhifu_is_use?n("div",{staticClass:"item",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.paytype="malipaynew"}}},[n("div",{staticClass:"checkbox",class:["malipaynew"===e.paytype?"checked":""]}),n("div",{staticClass:"icon-Alipay"}),n("div",{staticClass:"name"},[e._v("支付宝")])]):e._e(),n("div",{staticClass:"item",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.paytype="deposit"}}},[n("div",{staticClass:"checkbox",class:["deposit"===e.paytype?"checked":""]}),n("div",{staticClass:"icon-balance"}),n("div",{staticClass:"name"},[e._v("预存款支付 (余额："+e._s(e.advance)+")")])])])]),n("div",{staticClass:"pay-btn",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.startPay.apply(void 0,arguments)}}},[e._v("确定")])])],1)},i=[];n.d(t,"a",function(){return a}),n.d(t,"b",function(){return i})},"278a":function(e,t,n){t=e.exports=n("2350")(!1),t.push([e.i,'@charset "UTF-8";\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\n/* 页面左右间距 */\n/* 文字尺寸 */\n/*文字颜色*/\n/* 边框颜色 */\n/* 图片加载中颜色 */\n/* 行为相关颜色 */uni-page-body[data-v-0b5855d0]{min-height:100vh;width:100%;background:#f4f4f4}.container[data-v-0b5855d0]{padding-top:%?20?%;-webkit-box-pack:start;-webkit-justify-content:flex-start;justify-content:flex-start}.total[data-v-0b5855d0]{height:%?104?%;background:#fff;width:92%;line-height:%?104?%;padding-left:%?30?%;padding-right:%?30?%}.total .label[data-v-0b5855d0]{float:left}.total .txt[data-v-0b5855d0]{float:right}.pay-list[data-v-0b5855d0]{margin-top:%?30?%;height:auto;width:100%;overflow:hidden}.pay-list .h[data-v-0b5855d0]{width:100%;height:%?50?%;line-height:%?50?%;margin-left:%?31.25?%;margin-bottom:%?31.25?%}.pay-list .item[data-v-0b5855d0]{height:%?108?%;padding-left:%?31.25?%;background:#fff;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;border-bottom:.01rem solid #f4f4f4;border-top:.01rem solid #f4f4f4}.pay-list .checkbox[data-v-0b5855d0]{background:url(https://imgt1.oss-cn-shanghai.aliyuncs.com/ecAllRes/images/checkbox-sed.png) 0 %?-448?% no-repeat;background-size:%?38?% %?486?%;width:%?40?%;height:%?40?%;display:inline-block;vertical-align:middle;margin-right:%?30?%}.pay-list .checkbox.checked[data-v-0b5855d0]{background:url(https://imgt1.oss-cn-shanghai.aliyuncs.com/ecAllRes/images/checkbox-sed.png) 0 %?-192?% no-repeat;background-size:%?38?% %?486?%}.pay-list .icon-wechat[data-v-0b5855d0]{display:inline-block;vertical-align:middle;background-image:url(https://imgt1.oss-cn-shanghai.aliyuncs.com/ecAllRes/images/wechat.png);background-repeat:no-repeat;background-size:20px 20px;margin-right:11.25px;width:20px;margin-left:4px;height:20px}.pay-list .icon-balance[data-v-0b5855d0]{display:inline-block;vertical-align:middle;background-image:url(https://imgt1.oss-cn-shanghai.aliyuncs.com/ecAllRes/images/balance.png);background-repeat:no-repeat;background-size:20px 20px;margin-right:11.25px;width:20px;margin-left:4px;height:20px}.pay-list .icon-Alipay[data-v-0b5855d0]{display:inline-block;vertical-align:middle;background-image:url(https://imgt1.oss-cn-shanghai.aliyuncs.com/ecAllRes/images/alipay.png);background-repeat:no-repeat;background-size:20px 20px;margin-right:11.25px;width:20px;margin-left:4px;height:20px}.pay-list .icon[data-v-0b5855d0]{display:inline-block;vertical-align:middle;margin-right:%?10.5?%;width:%?56.25?%;height:%?56.25?%}.pay-list .name[data-v-0b5855d0]{display:inline-block;vertical-align:middle;height:%?56.25?%;line-height:%?56.25?%}.pay-btn[data-v-0b5855d0]{position:fixed;left:0;bottom:0;height:%?100?%;width:100%;text-align:center;line-height:%?100?%;background:#b4282d;color:#fff;font-size:%?30?%}.tips[data-v-0b5855d0]{height:%?40?%;width:92%;font-size:%?24?%;color:#999;line-height:%?40?%;padding-left:%?30?%;padding-right:%?30?%}body.?%PAGE?%[data-v-0b5855d0]{background:#f4f4f4}',""])},"6bef":function(e,t,n){var a=n("278a");"string"===typeof a&&(a=[[e.i,a,""]]),a.locals&&(e.exports=a.locals);var i=n("4f06").default;i("2f837425",a,!0,{sourceMap:!1,shadowMode:!1})},"7c21":function(e,t,n){"use strict";n.r(t);var a=n("0ad2"),i=n("9ae5");for(var r in i)"default"!==r&&function(e){n.d(t,e,function(){return i[e]})}(r);n("bf8f");var o=n("f0c5"),s=Object(o["a"])(i["default"],a["a"],a["b"],!1,null,"0b5855d0",null);t["default"]=s.exports},"9ae5":function(e,t,n){"use strict";n.r(t);var a=n("d69c"),i=n.n(a);for(var r in a)"default"!==r&&function(e){n.d(t,e,function(){return a[e]})}(r);t["default"]=i.a},bf8f:function(e,t,n){"use strict";var a=n("6bef"),i=n.n(a);i.a},d69c:function(e,t,n){"use strict";(function(e){var a=n("288e");Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0,n("c5f6"),n("ac6a"),n("96cf");var i=a(n("3b8d"));n("4917");var r=a(n("bd86")),o=n("00c9"),s=n("a19f"),c={onShow:function(){n("f1af");(0,s.toLogin)(),uni.getStorageSync("bonusName",""),uni.getStorageSync("bonus_id",""),this.advance=uni.getStorageSync("advance"),this.iswechatH5=this.is_weixn(),this.orderId=this.$root.$mp.query.orderId,this.code=this.$root.$mp.query.code,this.openid_h5=uni.getStorageSync("openid_h5"),this.code&&(this.getOpenid_h5(),this.openid_h5=uni.getStorageSync("openid_h5"),this.wxpayh5Pay())},mounted:function(){this.orderId=this.$root.$mp.query.orderId,this.orderDetail(),this.getPayment(),this.GetAdvance(),this.getPaymentIsUse()},data:function(){var e;return e={orderId:0,actualPrice:0,advance:"",payParams:[],balance_amount:0,pay_id:"",paytype:"wxpayh5",alipaywap:"",goods_name:""},(0,r.default)(e,"advance",0),(0,r.default)(e,"small_is_use",""),(0,r.default)(e,"hfive_is_use",""),(0,r.default)(e,"app_is_use",""),(0,r.default)(e,"zhifu_is_use",""),(0,r.default)(e,"iswechatH5",""),(0,r.default)(e,"openid",""),(0,r.default)(e,"code",""),(0,r.default)(e,"openid_h5",""),e},methods:{is_weixn:function(){var e=navigator.userAgent.toLowerCase();return"micromessenger"==e.match(/MicroMessenger/i)},orderDetail:function(){var e=(0,i.default)(regeneratorRuntime.mark(function e(){var t;return regeneratorRuntime.wrap(function(e){while(1)switch(e.prev=e.next){case 0:return e.next=2,(0,o.payDetailApi)({order_id:this.$root.$mp.query.orderId});case 2:if(t=e.sent,!1!==t.data.pay_detail){e.next=7;break}return uni.showToast({title:"不存在此订单",icon:"none",duration:2e3}),uni.navigateTo({url:"/pages/index/main"}),e.abrupt("return",!1);case 7:this.actualPrice=t.data.info.total_fee,this.order=t.data.info,this.order_id=this.$root.$mp.query.orderId,this.payment_list=t.data.info.payment_list,this.alipaywap=t.data.other.url,this.goods_name=t.data.other.goods_name;case 13:case"end":return e.stop()}},e,this)}));function t(){return e.apply(this,arguments)}return t}(),selectPayment:function(e){this.pay_id=e},GetAdvance:function(){var e=(0,i.default)(regeneratorRuntime.mark(function e(){var t;return regeneratorRuntime.wrap(function(e){while(1)switch(e.prev=e.next){case 0:return e.next=2,(0,o.memberInfoApi)({});case 2:t=e.sent,this.advance=t.data.info.user_money;case 4:case"end":return e.stop()}},e,this)}));function t(){return e.apply(this,arguments)}return t}(),doPay:function(){var e=(0,i.default)(regeneratorRuntime.mark(function e(){var t,n=this;return regeneratorRuntime.wrap(function(e){while(1)switch(e.prev=e.next){case 0:if("balance"!==this.pay_id){e.next=5;break}return e.next=3,(0,o.doPaymentBalanceApi)({order_id:this.$root.$mp.query.orderId,pay_id:this.pay_id});case 3:t=e.sent,t.data.dopay?(uni.showToast({title:"订单支付成功",icon:"none",duration:1e3}),setTimeout(function(){uni.redirectTo({url:"/apiMember/orderdetail/main?id="+n.$root.$mp.query.orderId})},1e3)):(uni.showToast({title:t.data.info,icon:"none",duration:1e3}),setTimeout(function(){uni.redirectTo({url:"/apiMember/orderdetail/main?id="+n.$root.$mp.query.orderId})},1e3));case 5:case"end":return e.stop()}},e,this)}));function t(){return e.apply(this,arguments)}return t}(),getPayment:function(){var e=(0,i.default)(regeneratorRuntime.mark(function e(){var t,n=this;return regeneratorRuntime.wrap(function(e){while(1)switch(e.prev=e.next){case 0:return e.next=2,(0,o.getPaymentListApi)({});case 2:t=e.sent,0===t.data.info.length?uni.showToast({title:"请配置支付方式",icon:"none",duration:2e3}):this.pay_id=t.data.info[0].pay_code,t.data.info.forEach(function(e){"balance"===e.pay_code&&(n.balance=!0,n.balance_amount=e.amount_txt),"alipay"===e.pay_code&&(n.alipay=!0),"wxpay"===e.pay_code&&(n.wxpay=!0)});case 5:case"end":return e.stop()}},e,this)}));function t(){return e.apply(this,arguments)}return t}(),startPay:function(){var e=(0,i.default)(regeneratorRuntime.mark(function e(){return regeneratorRuntime.wrap(function(e){while(1)switch(e.prev=e.next){case 0:e.t0=this.paytype,e.next="deposit"===e.t0?3:"kaiyu"===e.t0?6:"wxpaywxapp"===e.t0?9:"malipaymyapp"===e.t0?12:"wxpayh5"===e.t0?15:"malipaynew"===e.t0?18:"wxpayappplus"===e.t0?21:"malipayappplus"===e.t0?24:27;break;case 3:return this.depositPay(),e.abrupt("return",!1);case 6:return this.cardPay(),e.abrupt("return",!1);case 9:return this.wxpaywxappPay(),e.abrupt("return",!1);case 12:return this.malipaymyappPay(),e.abrupt("return",!1);case 15:return this.wxpayh5Pay(),e.abrupt("return",!1);case 18:return this.malipaynewPay(),e.abrupt("return",!1);case 21:return this.wxpayappplusPay(),e.abrupt("return",!1);case 24:return this.malipayappplusPay(),e.abrupt("return",!1);case 27:return e.abrupt("return",!1);case 29:case"end":return e.stop()}},e,this)}));function t(){return e.apply(this,arguments)}return t}(),depositPay:function(){var e=(0,i.default)(regeneratorRuntime.mark(function e(){var t;return regeneratorRuntime.wrap(function(e){while(1)switch(e.prev=e.next){case 0:if(!(Number(this.advance)<Number(this.actualPrice))){e.next=3;break}return uni.showToast({icon:"none",title:"余额不足"}),e.abrupt("return",!1);case 3:return e.next=5,(0,o.doPaymentBalanceApi)({pay_id:this.paytype,order_id:this.orderId,money:this.actualPrice});case 5:if(t=e.sent,!1!==t.data.dopay){e.next=9;break}return uni.redirectTo({url:"/apiCart/payresult/main?status=0&orderId="+this.orderId+"&money="+this.actualPrice+"&paytype="+this.paytype}),e.abrupt("return",!1);case 9:uni.redirectTo({url:"/apiCart/payresult/main?status=1&orderId="+this.orderId+"&money="+this.actualPrice+"&paytype="+this.paytype});case 10:case"end":return e.stop()}},e,this)}));function t(){return e.apply(this,arguments)}return t}(),wxpaywxappPay:function(){var t=(0,i.default)(regeneratorRuntime.mark(function t(){var n,a;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,(0,o.payDopaymentApi)({pay_type:"wxpayMp",order_id:this.orderId,money:this.actualPrice,openid:uni.getStorageSync("openId")});case 2:if(n=t.sent,!1!==n.data.res){t.next=6;break}return uni.showToast({icon:"none",title:n.data.msg}),t.abrupt("return",!1);case 6:this.payParams=n,a=this,uni.requestPayment({provider:"wxpay",timeStamp:n.data.timeStamp,nonceStr:n.data.nonceStr,package:n.data.package,signType:"MD5",paySign:n.data.paySign,success:function(e){uni.redirectTo({url:"/apiCart/payresult/main?status=1&orderId="+a.orderId+"&money="+a.actualPrice})},fail:function(t){e.log(a.orderId),uni.redirectTo({url:"/apiCart/payresult/main?status=0&orderId="+a.orderId+"&money="+a.actualPrice})}});case 9:case"end":return t.stop()}},t,this)}));function n(){return t.apply(this,arguments)}return n}(),malipaymyappPay:function(){var t=(0,i.default)(regeneratorRuntime.mark(function t(){var n,a;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,(0,o.payDopaymentApi)({paytype:this.paytype,orderId:this.orderId,money:this.actualPrice});case 2:n=t.sent,e.log(n),this.payParams=n,a=this,this.payParams.trade_no&&uni.tradePay({tradeNO:this.payParams.trade_no,success:function(t){e.log(t),uni.redirectTo({url:"/apiCart/payresult/main?status=1&orderId="+a.orderId+"&money="+a.actualPrice+"&paytype="+this.paytype})},fail:function(t){uni.redirectTo({url:"/apiCart/payresult/main?status=0&orderId="+a.orderId+"&money="+a.actualPrice+"&paytype="+this.paytype}),e.log(t)}});case 7:case"end":return t.stop()}},t,this)}));function n(){return t.apply(this,arguments)}return n}(),onBridgeReady:function(){var e=(0,i.default)(regeneratorRuntime.mark(function e(t,n){var a;return regeneratorRuntime.wrap(function(e){while(1)switch(e.prev=e.next){case 0:a=this,WeixinJSBridge.invoke("getBrandWCPayRequest",t,function(e){"get_brand_wcpay_request:ok"==e.err_msg?window.location.href=n+"status=1&orderId="+a.orderId:window.location.href=n+"status=0&orderId="+a.orderId});case 2:case"end":return e.stop()}},e,this)}));function t(t,n){return e.apply(this,arguments)}return t}(),wxpayh5Pay:function(){var e=(0,i.default)(regeneratorRuntime.mark(function e(){var t,n,a,i;return regeneratorRuntime.wrap(function(e){while(1)switch(e.prev=e.next){case 0:if(!this.iswechatH5){e.next=12;break}if(this.openid_h5){e.next=7;break}return e.next=4,(0,o.getWxCodeApi)({order_id:this.orderId});case 4:return t=e.sent,window.location.href=t.data.url,e.abrupt("return",!1);case 7:return e.next=9,(0,o.wechatPayApi)({order_id:this.orderId,money:this.actualPrice,openid:this.openid_h5,code:this.code});case 9:return n=e.sent,!0===n.data.res&&(a={appId:n.data.result.appId,timeStamp:n.data.result.timeStamp,nonceStr:n.data.result.nonceStr,package:n.data.result.package,signType:"MD5",paySign:n.data.result.paySign},this.onBridgeReady(a,n.data.url)),e.abrupt("return",!0);case 12:return e.next=14,(0,o.payDopaymentApi)({pay_id:this.paytype,order_id:this.orderId,money:this.actualPrice,pay_type:"wxpayH5"});case 14:if(i=e.sent,!1!==i.data.res){e.next=18;break}return uni.showToast({title:i.data.msg,icon:"none",duration:1500}),e.abrupt("return",!1);case 18:!0===i.data.res&&(window.location.href=i.data.url);case 19:case"end":return e.stop()}},e,this)}));function t(){return e.apply(this,arguments)}return t}(),malipaynewPay:function(){var t=(0,i.default)(regeneratorRuntime.mark(function t(){var n;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:e.log(window.location.href),n=this.alipaywap+"&order_sn="+this.orderId+"&body="+this.goods_name+"&money="+this.actualPrice+"&quit_url="+window.location.href,e.log(n),location.href=n;case 4:case"end":return t.stop()}},t,this)}));function n(){return t.apply(this,arguments)}return n}(),wxpayappplusPay:function(){var t=(0,i.default)(regeneratorRuntime.mark(function t(){var n;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:return e.log("微信APP支付"),t.next=3,(0,o.payDopaymentApi)({order_id:this.orderId,money:this.actualPrice,pay_type:"wxpayApp"});case 3:n=t.sent,uni.requestPayment({provider:"wxpay",orderInfo:n.data.response,success:function(t){e.log("支付成功"),e.log(t)},fail:function(t){e.log("失败"),e.log(t)}});case 5:case"end":return t.stop()}},t,this)}));function n(){return t.apply(this,arguments)}return n}(),malipayappplusPay:function(){var t=(0,i.default)(regeneratorRuntime.mark(function t(){var n,a;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:return e.log("支付宝支付"),t.next=3,(0,o.payDopaymentApi)({order_id:this.orderId,money:this.actualPrice,pay_type:"alipayApp"});case 3:n=t.sent,a=this,uni.requestPayment({provider:"alipay",orderInfo:n.data.response.payInfo,success:function(t){e.log(t),e.log("成功支付"),e.log(a.orderId),e.log("-------"),uni.redirectTo({url:"/apiCart/payresult/main?status=1&orderId="+a.orderId})},fail:function(t){e.log(t),e.log("支付失败")}});case 6:case"end":return t.stop()}},t,this)}));function n(){return t.apply(this,arguments)}return n}(),getWechatOpenid:function(){var t=(0,i.default)(regeneratorRuntime.mark(function t(){var n=this;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:uni.showLoading({title:"登录中...",mask:!0,success:function(e){}}),uni.login({success:function(){var t=(0,i.default)(regeneratorRuntime.mark(function t(a){var i;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:if(!a.code){t.next=8;break}return n.code=a.code,e.log("code："+n.code),t.next=5,(0,o.loginWechatApi)({code:n.code,platform:"MP-WEIXIN-MOBILE"});case 5:i=t.sent,!0===i.data.res&&uni.setStorageSync("openId",i.data.openid),uni.setStorageSync("openId",i.data.openid);case 8:case"end":return t.stop()}},t,this)}));function a(e){return t.apply(this,arguments)}return a}()});case 2:case"end":return t.stop()}},t,this)}));function n(){return t.apply(this,arguments)}return n}(),getPaymentIsUse:function(){var e=(0,i.default)(regeneratorRuntime.mark(function e(){var t;return regeneratorRuntime.wrap(function(e){while(1)switch(e.prev=e.next){case 0:return e.next=2,(0,o.getPaymentIsUseApi)({});case 2:t=e.sent,this.small_is_use=t.data.small_is_use,this.hfive_is_use=t.data.hfive_is_use,this.app_is_use=t.data.app_is_use,this.zhifu_is_use=t.data.zhifu_is_use;case 7:case"end":return e.stop()}},e,this)}));function t(){return e.apply(this,arguments)}return t}(),getOpenid_h5:function(){var e=(0,i.default)(regeneratorRuntime.mark(function e(){var t;return regeneratorRuntime.wrap(function(e){while(1)switch(e.prev=e.next){case 0:return e.next=2,(0,o.getOpenid)({code:this.code});case 2:t=e.sent,uni.setStorageSync("openid_h5",t.data.openid_h5);case 4:case"end":return e.stop()}},e,this)}));function t(){return e.apply(this,arguments)}return t}()}};t.default=c}).call(this,n("5a52")["default"])},f1af:function(e,t,n){"use strict";(function(a){var i,r=n("288e"),o=r(n("f499"));n("a481");var s=r(n("bd86"));n("28a5"),n("4917"),function(a,r){i=function(){return r(a)}.call(t,n,t,e),void 0===i||(e.exports=i)}(window,function(e,t){if(!e.jWeixin){var n,i,r={config:"preVerifyJSAPI",onMenuShareTimeline:"menu:share:timeline",onMenuShareAppMessage:"menu:share:appmessage",onMenuShareQQ:"menu:share:qq",onMenuShareWeibo:"menu:share:weiboApp",onMenuShareQZone:"menu:share:QZone",previewImage:"imagePreview",getLocation:"geoLocation",openProductSpecificView:"openProductViewWithPid",addCard:"batchAddCard",openCard:"batchViewCard",chooseWXPay:"getBrandWCPayRequest",openEnterpriseRedPacket:"getRecevieBizHongBaoRequest",startSearchBeacons:"startMonitoringBeacons",stopSearchBeacons:"stopMonitoringBeacons",onSearchBeacons:"onBeaconsInRange",consumeAndShareCard:"consumedShareCard",openAddress:"editAddress"},c=function(){var e={};for(var t in r)e[r[t]]=t;return e}(),d=e.document,u=d.title,p=navigator.userAgent.toLowerCase(),l=navigator.platform.toLowerCase(),f=!(!l.match("mac")&&!l.match("win")),h=-1!=p.indexOf("wxdebugger"),m=-1!=p.indexOf("micromessenger"),g=-1!=p.indexOf("android"),y=-1!=p.indexOf("iphone")||-1!=p.indexOf("ipad"),v=(i=p.match(/micromessenger\/(\d+\.\d+\.\d+)/)||p.match(/micromessenger\/(\d+\.\d+)/))?i[1]:"",w={initStartTime:E(),initEndTime:0,preVerifyStartTime:0,preVerifyEndTime:0},_={version:1,appId:"",initTime:0,preVerifyTime:0,networkType:"",isPreVerifyOk:1,systemType:y?1:g?2:-1,clientVersion:v,url:encodeURIComponent(location.href)},b={},x={_completes:[]},k={state:0,data:{}};N(function(){w.initEndTime=E()});var I=!1,S=[],P=(n={config:function(t){O("config",b=t);var n=!1!==b.check;N(function(){if(n)A(r.config,{verifyJsApiList:L(b.jsApiList),verifyOpenTagList:L(b.openTagList)},function(){x._complete=function(e){w.preVerifyEndTime=E(),k.state=1,k.data=e},x.success=function(e){_.isPreVerifyOk=0},x.fail=function(e){x._fail?x._fail(e):k.state=-1};var e=x._completes;return e.push(function(){!function(){if(!(f||h||b.debug||v<"6.0.2"||_.systemType<0)){var e=new Image;_.appId=b.appId,_.initTime=w.initEndTime-w.initStartTime,_.preVerifyTime=w.preVerifyEndTime-w.preVerifyStartTime,P.getNetworkType({isInnerInvoke:!0,success:function(t){_.networkType=t.networkType;var n="https://open.weixin.qq.com/sdk/report?v="+_.version+"&o="+_.isPreVerifyOk+"&s="+_.systemType+"&c="+_.clientVersion+"&a="+_.appId+"&n="+_.networkType+"&i="+_.initTime+"&p="+_.preVerifyTime+"&u="+_.url;e.src=n}})}}()}),x.complete=function(t){for(var n=0,a=e.length;n<a;++n)e[n]();x._completes=[]},x}()),w.preVerifyStartTime=E();else{k.state=1;for(var e=x._completes,t=0,a=e.length;t<a;++t)e[t]();x._completes=[]}}),P.invoke||(P.invoke=function(t,n,a){e.WeixinJSBridge&&WeixinJSBridge.invoke(t,R(n),a)},P.on=function(t,n){e.WeixinJSBridge&&WeixinJSBridge.on(t,n)})},ready:function(e){0!=k.state?e():(x._completes.push(e),!m&&b.debug&&e())},error:function(e){v<"6.0.2"||(-1==k.state?e(k.data):x._fail=e)},checkJsApi:function(e){A("checkJsApi",{jsApiList:L(e.jsApiList)},(e._complete=function(e){if(g){var t=e.checkResult;t&&(e.checkResult=JSON.parse(t))}e=function(e){var t=e.checkResult;for(var n in t){var a=c[n];a&&(t[a]=t[n],delete t[n])}return e}(e)},e))},onMenuShareTimeline:function(e){M(r.onMenuShareTimeline,{complete:function(){A("shareTimeline",{title:e.title||u,desc:e.title||u,img_url:e.imgUrl||"",link:e.link||location.href,type:e.type||"link",data_url:e.dataUrl||""},e)}},e)},onMenuShareAppMessage:function(e){M(r.onMenuShareAppMessage,{complete:function(t){"favorite"===t.scene?A("sendAppMessage",{title:e.title||u,desc:e.desc||"",link:e.link||location.href,img_url:e.imgUrl||"",type:e.type||"link",data_url:e.dataUrl||""}):A("sendAppMessage",{title:e.title||u,desc:e.desc||"",link:e.link||location.href,img_url:e.imgUrl||"",type:e.type||"link",data_url:e.dataUrl||""},e)}},e)},onMenuShareQQ:function(e){M(r.onMenuShareQQ,{complete:function(){A("shareQQ",{title:e.title||u,desc:e.desc||"",img_url:e.imgUrl||"",link:e.link||location.href},e)}},e)},onMenuShareWeibo:function(e){M(r.onMenuShareWeibo,{complete:function(){A("shareWeiboApp",{title:e.title||u,desc:e.desc||"",img_url:e.imgUrl||"",link:e.link||location.href},e)}},e)},onMenuShareQZone:function(e){M(r.onMenuShareQZone,{complete:function(){A("shareQZone",{title:e.title||u,desc:e.desc||"",img_url:e.imgUrl||"",link:e.link||location.href},e)}},e)},updateTimelineShareData:function(e){A("updateTimelineShareData",{title:e.title,link:e.link,imgUrl:e.imgUrl},e)},updateAppMessageShareData:function(e){A("updateAppMessageShareData",{title:e.title,desc:e.desc,link:e.link,imgUrl:e.imgUrl},e)},startRecord:function(e){A("startRecord",{},e)},stopRecord:function(e){A("stopRecord",{},e)},onVoiceRecordEnd:function(e){M("onVoiceRecordEnd",e)},playVoice:function(e){A("playVoice",{localId:e.localId},e)},pauseVoice:function(e){A("pauseVoice",{localId:e.localId},e)},stopVoice:function(e){A("stopVoice",{localId:e.localId},e)},onVoicePlayEnd:function(e){M("onVoicePlayEnd",e)},uploadVoice:function(e){A("uploadVoice",{localId:e.localId,isShowProgressTips:0==e.isShowProgressTips?0:1},e)},downloadVoice:function(e){A("downloadVoice",{serverId:e.serverId,isShowProgressTips:0==e.isShowProgressTips?0:1},e)},translateVoice:function(e){A("translateVoice",{localId:e.localId,isShowProgressTips:0==e.isShowProgressTips?0:1},e)},chooseImage:function(e){A("chooseImage",{scene:"1|2",count:e.count||9,sizeType:e.sizeType||["original","compressed"],sourceType:e.sourceType||["album","camera"]},(e._complete=function(e){if(g){var t=e.localIds;try{t&&(e.localIds=JSON.parse(t))}catch(e){}}},e))},getLocation:function(e){},previewImage:function(e){A(r.previewImage,{current:e.current,urls:e.urls},e)},uploadImage:function(e){A("uploadImage",{localId:e.localId,isShowProgressTips:0==e.isShowProgressTips?0:1},e)},downloadImage:function(e){A("downloadImage",{serverId:e.serverId,isShowProgressTips:0==e.isShowProgressTips?0:1},e)},getLocalImgData:function(e){!1===I?(I=!0,A("getLocalImgData",{localId:e.localId},(e._complete=function(e){if(I=!1,0<S.length){var t=S.shift();wx.getLocalImgData(t)}},e))):S.push(e)},getNetworkType:function(e){A("getNetworkType",{},(e._complete=function(e){e=function(e){var t=e.errMsg;e.errMsg="getNetworkType:ok";var n=e.subtype;if(delete e.subtype,n)e.networkType=n;else{var a=t.indexOf(":"),i=t.substring(a+1);switch(i){case"wifi":case"edge":case"wwan":e.networkType=i;break;default:e.errMsg="getNetworkType:fail"}}return e}(e)},e))},openLocation:function(e){A("openLocation",{latitude:e.latitude,longitude:e.longitude,name:e.name||"",address:e.address||"",scale:e.scale||28,infoUrl:e.infoUrl||""},e)}},(0,s.default)(n,"getLocation",function(e){A(r.getLocation,{type:(e=e||{}).type||"wgs84"},(e._complete=function(e){delete e.type},e))}),(0,s.default)(n,"hideOptionMenu",function(e){A("hideOptionMenu",{},e)}),(0,s.default)(n,"showOptionMenu",function(e){A("showOptionMenu",{},e)}),(0,s.default)(n,"closeWindow",function(e){A("closeWindow",{},e=e||{})}),(0,s.default)(n,"hideMenuItems",function(e){A("hideMenuItems",{menuList:e.menuList},e)}),(0,s.default)(n,"showMenuItems",function(e){A("showMenuItems",{menuList:e.menuList},e)}),(0,s.default)(n,"hideAllNonBaseMenuItem",function(e){A("hideAllNonBaseMenuItem",{},e)}),(0,s.default)(n,"showAllNonBaseMenuItem",function(e){A("showAllNonBaseMenuItem",{},e)}),(0,s.default)(n,"scanQRCode",function(e){A("scanQRCode",{needResult:(e=e||{}).needResult||0,scanType:e.scanType||["qrCode","barCode"]},(e._complete=function(e){if(y){var t=e.resultStr;if(t){var n=JSON.parse(t);e.resultStr=n&&n.scan_code&&n.scan_code.scan_result}}},e))}),(0,s.default)(n,"openAddress",function(e){A(r.openAddress,{},(e._complete=function(e){e=function(e){return e.postalCode=e.addressPostalCode,delete e.addressPostalCode,e.provinceName=e.proviceFirstStageName,delete e.proviceFirstStageName,e.cityName=e.addressCitySecondStageName,delete e.addressCitySecondStageName,e.countryName=e.addressCountiesThirdStageName,delete e.addressCountiesThirdStageName,e.detailInfo=e.addressDetailInfo,delete e.addressDetailInfo,e}(e)},e))}),(0,s.default)(n,"openProductSpecificView",function(e){A(r.openProductSpecificView,{pid:e.productId,view_type:e.viewType||0,ext_info:e.extInfo},e)}),(0,s.default)(n,"addCard",function(e){for(var t=e.cardList,n=[],a=0,i=t.length;a<i;++a){var o=t[a],s={card_id:o.cardId,card_ext:o.cardExt};n.push(s)}A(r.addCard,{card_list:n},(e._complete=function(e){var t=e.card_list;if(t){for(var n=0,a=(t=JSON.parse(t)).length;n<a;++n){var i=t[n];i.cardId=i.card_id,i.cardExt=i.card_ext,i.isSuccess=!!i.is_succ,delete i.card_id,delete i.card_ext,delete i.is_succ}e.cardList=t,delete e.card_list}},e))}),(0,s.default)(n,"chooseCard",function(e){A("chooseCard",{app_id:b.appId,location_id:e.shopId||"",sign_type:e.signType||"SHA1",card_id:e.cardId||"",card_type:e.cardType||"",card_sign:e.cardSign,time_stamp:e.timestamp+"",nonce_str:e.nonceStr},(e._complete=function(e){e.cardList=e.choose_card_info,delete e.choose_card_info},e))}),(0,s.default)(n,"openCard",function(e){for(var t=e.cardList,n=[],a=0,i=t.length;a<i;++a){var o=t[a],s={card_id:o.cardId,code:o.code};n.push(s)}A(r.openCard,{card_list:n},e)}),(0,s.default)(n,"consumeAndShareCard",function(e){A(r.consumeAndShareCard,{consumedCardId:e.cardId,consumedCode:e.code},e)}),(0,s.default)(n,"chooseWXPay",function(e){A(r.chooseWXPay,V(e),e)}),(0,s.default)(n,"openEnterpriseRedPacket",function(e){A(r.openEnterpriseRedPacket,V(e),e)}),(0,s.default)(n,"startSearchBeacons",function(e){A(r.startSearchBeacons,{ticket:e.ticket},e)}),(0,s.default)(n,"stopSearchBeacons",function(e){A(r.stopSearchBeacons,{},e)}),(0,s.default)(n,"onSearchBeacons",function(e){M(r.onSearchBeacons,e)}),(0,s.default)(n,"openEnterpriseChat",function(e){A("openEnterpriseChat",{useridlist:e.userIds,chatname:e.groupName},e)}),(0,s.default)(n,"launchMiniProgram",function(e){A("launchMiniProgram",{targetAppId:e.targetAppId,path:function(e){if("string"==typeof e&&0<e.length){var t=e.split("?")[0],n=e.split("?")[1];return t+=".html",void 0!==n?t+"?"+n:t}}(e.path),envVersion:e.envVersion},e)}),(0,s.default)(n,"openBusinessView",function(e){A("openBusinessView",{businessType:e.businessType,queryString:e.queryString||"",envVersion:e.envVersion},(e._complete=function(e){if(g){var t=e.extraData;if(t)try{e.extraData=JSON.parse(t)}catch(t){e.extraData={}}}},e))}),(0,s.default)(n,"miniProgram",{navigateBack:function(e){e=e||{},N(function(){A("invokeMiniProgramAPI",{name:"navigateBack",arg:{delta:e.delta||1}},e)})},navigateTo:function(e){N(function(){A("invokeMiniProgramAPI",{name:"navigateTo",arg:{url:e.url}},e)})},redirectTo:function(e){N(function(){A("invokeMiniProgramAPI",{name:"redirectTo",arg:{url:e.url}},e)})},switchTab:function(e){N(function(){A("invokeMiniProgramAPI",{name:"switchTab",arg:{url:e.url}},e)})},reLaunch:function(e){N(function(){A("invokeMiniProgramAPI",{name:"reLaunch",arg:{url:e.url}},e)})},postMessage:function(e){N(function(){A("invokeMiniProgramAPI",{name:"postMessage",arg:e.data||{}},e)})},getEnv:function(t){N(function(){t({miniprogram:"miniprogram"===e.__wxjs_environment})})}}),n),T=1,C={};return d.addEventListener("error",function(e){if(!g){var t=e.target,n=t.tagName,a=t.src;if(("IMG"==n||"VIDEO"==n||"AUDIO"==n||"SOURCE"==n)&&-1!=a.indexOf("wxlocalresource://")){e.preventDefault(),e.stopPropagation();var i=t["wx-id"];if(i||(i=T++,t["wx-id"]=i),C[i])return;C[i]=!0,wx.ready(function(){wx.getLocalImgData({localId:a,success:function(e){t.src=e.localData}})})}}},!0),d.addEventListener("load",function(e){if(!g){var t=e.target,n=t.tagName;if(t.src,"IMG"==n||"VIDEO"==n||"AUDIO"==n||"SOURCE"==n){var a=t["wx-id"];a&&(C[a]=!1)}}},!0),t&&(e.wx=e.jWeixin=P),P}function A(t,n,a){e.WeixinJSBridge?WeixinJSBridge.invoke(t,R(n),function(e){B(t,e,a)}):O(t,a)}function M(t,n,a){e.WeixinJSBridge?WeixinJSBridge.on(t,function(e){a&&a.trigger&&a.trigger(e),B(t,e,n)}):O(t,a||n)}function R(e){return(e=e||{}).appId=b.appId,e.verifyAppId=b.appId,e.verifySignType="sha1",e.verifyTimestamp=b.timestamp+"",e.verifyNonceStr=b.nonceStr,e.verifySignature=b.signature,e}function V(e){return{timeStamp:e.timestamp+"",nonceStr:e.nonceStr,package:e.package,paySign:e.paySign,signType:e.signType||"SHA1"}}function B(e,t,n){"openEnterpriseChat"!=e&&"openBusinessView"!==e||(t.errCode=t.err_code),delete t.err_code,delete t.err_desc,delete t.err_detail;var a=t.errMsg;a||(a=t.err_msg,delete t.err_msg,a=function(e,t){var n=e,a=c[n];a&&(n=a);var i="ok";if(t){var r=t.indexOf(":");"confirm"==(i=t.substring(r+1))&&(i="ok"),"failed"==i&&(i="fail"),-1!=i.indexOf("failed_")&&(i=i.substring(7)),-1!=i.indexOf("fail_")&&(i=i.substring(5)),"access denied"!=(i=(i=i.replace(/_/g," ")).toLowerCase())&&"no permission to execute"!=i||(i="permission denied"),"config"==n&&"function not exist"==i&&(i="ok"),""==i&&(i="fail")}return n+":"+i}(e,a),t.errMsg=a),(n=n||{})._complete&&(n._complete(t),delete n._complete),a=t.errMsg||"",b.debug&&!n.isInnerInvoke&&alert((0,o.default)(t));var i=a.indexOf(":");switch(a.substring(i+1)){case"ok":n.success&&n.success(t);break;case"cancel":n.cancel&&n.cancel(t);break;default:n.fail&&n.fail(t)}n.complete&&n.complete(t)}function L(e){if(e){for(var t=0,n=e.length;t<n;++t){var a=e[t],i=r[a];i&&(e[t]=i)}return e}}function O(e,t){if(!(!b.debug||t&&t.isInnerInvoke)){var n=c[e];n&&(e=n),t&&t._complete&&delete t._complete,a.log('"'+e+'",',t||"")}}function E(){return(new Date).getTime()}function N(t){m&&(e.WeixinJSBridge?t():d.addEventListener&&d.addEventListener("WeixinJSBridgeReady",t,!1))}})}).call(this,n("5a52")["default"])}}]);