(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["apiShop-topicdetail-main"],{"35df":function(t,i,e){var n=e("ffa0");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var a=e("4f06").default;a("ee535776",n,!0,{sourceMap:!1,shadowMode:!1})},"5d4e":function(t,i,e){"use strict";var n=e("288e");Object.defineProperty(i,"__esModule",{value:!0}),i.default=void 0,e("a481"),e("96cf");var a=n(e("3b8d")),o=e("00c9"),c={created:function(){},mounted:function(){this.id=this.$root.$mp.query.id,this.getListData()},data:function(){return{recommendList:[],id:"",goods_desc:"",topicdetaillength:""}},components:{},methods:{topicdetail:function(t){uni.navigateTo({url:"/apiShop/topicdetail/main?id="+t})},getListData:function(){var t=(0,a.default)(regeneratorRuntime.mark(function t(){var i;return regeneratorRuntime.wrap(function(t){while(1)switch(t.prev=t.next){case 0:return t.next=2,(0,o.topicdetailListApi)({id:this.id});case 2:i=t.sent,this.goods_desc=i.data.content.replace(/\<img/gi,'<img style="max-width:100%;height:auto;vertical-align: bottom;" '),this.topicdetaillength=this.goods_desc.length,this.recommendList=i.recommendList;case 6:case"end":return t.stop()}},t,this)}));function i(){return t.apply(this,arguments)}return i}()},computed:{}};i.default=c},"86df":function(t,i,e){"use strict";var n=e("35df"),a=e.n(n);a.a},"8d5f":function(t,i,e){"use strict";e.r(i);var n=e("d612"),a=e("a8a5");for(var o in a)"default"!==o&&function(t){e.d(i,t,function(){return a[t]})}(o);e("86df");var c=e("f0c5"),s=Object(c["a"])(a["default"],n["a"],n["b"],!1,null,"24cf1d83",null);i["default"]=s.exports},a8a5:function(t,i,e){"use strict";e.r(i);var n=e("5d4e"),a=e.n(n);for(var o in n)"default"!==o&&function(t){e.d(i,t,function(){return n[t]})}(o);i["default"]=a.a},d612:function(t,i,e){"use strict";var n=function(){var t=this,i=t.$createElement,e=t._self._c||i;return e("v-uni-view",[e("cu-custom",{attrs:{bgColor:"bg-white",isBack:!0}},[e("template",{attrs:{slot:"backText"},slot:"backText"},[t._v("返回")]),e("template",{attrs:{slot:"content"},slot:"content"},[t._v("文章详情")])],2),e("div",{staticClass:"topicdetail"},[e("div",{staticClass:"content"},[0!=t.topicdetaillength?e("div",{staticClass:"detail"},[e("v-uni-rich-text",{attrs:{nodes:t.goods_desc}})],1):e("div",{staticStyle:{"text-align":"center"}},[t._v("该文章一篇空白,什么都没有~~")])])])],1)},a=[];e.d(i,"a",function(){return n}),e.d(i,"b",function(){return a})},ffa0:function(t,i,e){i=t.exports=e("2350")(!1),i.push([t.i,'@charset "UTF-8";\n/**\r\n * 这里是uni-app内置的常用样式变量\r\n *\r\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\r\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\r\n *\r\n */\n/**\r\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\r\n *\r\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\r\n */\n/* 页面左右间距 */\n/* 文字尺寸 */\n/*文字颜色*/\n/* 边框颜色 */\n/* 图片加载中颜色 */\n/* 行为相关颜色 */.topicdetail .list[data-v-24cf1d83]{width:%?690?%;height:auto;margin:0 %?30?%}.topicdetail .list .title[data-v-24cf1d83]{text-align:center;background:#f4f4f4;font-size:%?30?%;color:#999;padding:%?30?% 0}.topicdetail .list .item[data-v-24cf1d83]{width:100%;padding:%?24?% %?24?% %?30?% %?24?%;margin-bottom:%?30?%;background:#fff;box-sizing:border-box}.topicdetail .list .item img[data-v-24cf1d83]{height:%?278?%;width:%?642?%;display:block}.topicdetail .list .item p[data-v-24cf1d83]{display:block;margin-top:%?30?%;font-size:%?28?%}',""])}}]);