(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["apiShop-integralgoods-main"],{"07ed":function(t,e,a){"use strict";var i=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-uni-view",[a("cu-custom",{attrs:{bgColor:"bg-white",isBack:!0}},[a("template",{attrs:{slot:"backText"},slot:"backText"},[t._v("返回")]),a("template",{attrs:{slot:"content"},slot:"content"},[t._v("积分商品列表")])],2),a("div",{staticClass:"newgoods"},[a("div",{staticClass:"sortlist"},t._l(t.listData,function(e,i){return a("div",{key:i,staticClass:"item",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.goodsDetail(e.goods_id)}}},[a("img",{attrs:{src:e.list_pic_url,alt:""}}),a("p",{staticClass:"name"},[t._v(t._s(e.goods_name))]),a("p",{staticClass:"price"},[t._v(t._s(e.exchange_integral)+"积分")])])}),0)])],1)},s=[];a.d(e,"a",function(){return i}),a.d(e,"b",function(){return s})},"31c9":function(t,e,a){"use strict";a.r(e);var i=a("07ed"),s=a("e9a9");for(var n in s)"default"!==n&&function(t){a.d(e,t,function(){return s[t]})}(n);a("ce44");var o=a("f0c5"),r=Object(o["a"])(s["default"],i["a"],i["b"],!1,null,"37241d5e",null);e["default"]=r.exports},5974:function(t,e,a){var i=a("b096");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var s=a("4f06").default;s("0225c076",i,!0,{sourceMap:!1,shadowMode:!1})},b096:function(t,e,a){e=t.exports=a("2350")(!1),e.push([t.i,'@charset "UTF-8";\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\n/* 页面左右间距 */\n/* 文字尺寸 */\n/*文字颜色*/\n/* 边框颜色 */\n/* 图片加载中颜色 */\n/* 行为相关颜色 */.newgoods .sortnav[data-v-37241d5e]{display:-webkit-box;display:-webkit-flex;display:flex;background:#fff;width:100%;height:%?78?%;line-height:%?78?%}.newgoods .sortnav div[data-v-37241d5e]{width:%?250?%;height:100%;text-align:center}.newgoods .sortnav .active[data-v-37241d5e]{color:#b4282d}.newgoods .sortnav .price[data-v-37241d5e]{background:url(https://imgt1.oss-cn-shanghai.aliyuncs.com/ecAllRes/images/no.png) %?165?% 50% no-repeat;background-size:%?15?% %?21?%}.newgoods .sortnav .active.desc[data-v-37241d5e]{background:url(https://imgt1.oss-cn-shanghai.aliyuncs.com/ecAllRes/images/down.png) %?165?% 50% no-repeat;background-size:%?15?% %?21?%}.newgoods .sortnav .active.asc[data-v-37241d5e]{background:url(https://imgt1.oss-cn-shanghai.aliyuncs.com/ecAllRes/images/up.png) %?165?% 50% no-repeat;background-size:%?15?% %?21?%}.newgoods .sortlist[data-v-37241d5e]{margin-top:%?20?%;display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;-webkit-flex-wrap:wrap;flex-wrap:wrap}.newgoods .sortlist .item[data-v-37241d5e]{width:%?372.5?%;margin-bottom:%?5?%;text-align:center;background:#fff;padding:%?15?% 0}.newgoods .sortlist .item img[data-v-37241d5e]{display:block;width:%?302?%;height:%?302?%;margin:0 auto}.newgoods .sortlist .item .name[data-v-37241d5e]{margin:%?15?% 0 %?22?% 0;text-align:center;padding:0 %?20?%;font-size:%?24?%}.newgoods .sortlist .item .price[data-v-37241d5e]{text-align:center;font-size:%?30?%;color:#b4282d}',""])},ce21:function(t,e,a){"use strict";var i=a("288e");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,a("96cf");var s=i(a("3b8d")),n=a("00c9"),o={onReachBottom:function(){if(this.page=this.page+1,this.page>this.pagetotal)return!1;this.getlistData()},created:function(){},mounted:function(){this.$root.$mp.query.isHot&&(this.isHot=this.$root.$mp.query.isHot),this.$root.$mp.query.isNew&&(this.isNew=this.$root.$mp.query.isNew),this.id=this.$root.$mp.query.id,void 0===this.id&&(this.id=""),this.getlistData(!0)},data:function(){return{id:"",page:1,order:"",isHot:"",isNew:"",nowIndex:0,navData:["综合","价格","分类"],listData:[],activeClass:""}},components:{},methods:{getlistData:function(){var t=(0,s.default)(regeneratorRuntime.mark(function t(){var e;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,(0,n.integralgoodsGoodsListApi)({page:this.page});case 2:e=t.sent,1==this.page&&(this.listData=e.data.info),this.pagetotal=e.data.pagetotal,this.page>1&&(this.listData=this.listData.concat(e.data.info));case 6:case"end":return t.stop()}},t,this)}));function e(){return t.apply(this,arguments)}return e}(),changeTab:function(t){this.nowIndex=t,1==t?(this.order="asc"==this.order?"desc":"asc",this.isHot="",this.page=1,this.activeClass,this.activeClass=this.order+" active"):2==t&&(this.isHot="asc"==this.isHot?"desc":"asc",this.order="",this.page=1,this.activeClass=this.isHot+" active"),this.getlistData(!0)},goodsDetail:function(t){uni.navigateTo({url:"/apiShop/goods/main?id="+t+"&ral=true"})}},computed:{}};e.default=o},ce44:function(t,e,a){"use strict";var i=a("5974"),s=a.n(i);s.a},e9a9:function(t,e,a){"use strict";a.r(e);var i=a("ce21"),s=a.n(i);for(var n in i)"default"!==n&&function(t){a.d(e,t,function(){return i[t]})}(n);e["default"]=s.a}}]);