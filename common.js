"use strict"
//存放多个js文件中通用函数的文件。内容比较少时可以直接在各js中直接写，多的时候建议放在common.js中，需要时引入，减少功能实现js中代码量
//获取链接中附带的参数
function getQueryString(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r!=null) return r[2];else return'';
}