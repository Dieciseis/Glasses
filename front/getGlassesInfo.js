"use strict";
//向服务器请求眼镜图片和设计要素信息，接收服务器返回值并解析，显示在页面中
function getQueryString(name){//取网页链接请求参数的函数
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r!=null) return r[2];else return'';
}

window.onload = function(){//页面载入时触发
    var gid = getQueryString('gid');
    if(gid !== ' ') {
        var url = "http://www.deepbluecape.ink/glasses/back/getGlassesInfo.php?gid=" + gid;//请求眼镜信息
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.send();
        xhr.onreadystatechange = function () {
            // readyState == 4说明请求已完成
            if (xhr.readyState === 4 && xhr.status === 200 || xhr.status === 304) {
                // 从服务器获得数据
                if (xhr.responseText === false) {
                    alert("发生错误！");
                } else {
                    var temp = "{\"data\":" + xhr.responseText + "}",//整合json数据
                        json = eval('(' + temp + ')');//也可使用 JSON.parse,注意格式
                    console.log(json);
                    var args = [];
                    switch (json.data[0].frame_shape) {
                        case "0": args[0] = "方";break;
                        case "1": args[0] = "圆";break;
                        case "2": args[0] = "多边形";break;
                        default : args[0] = "未知";
                    }
                    switch (json.data[0].frame_thickness) {
                        case "0": args[1] = "粗";break;
                        case "1": args[1] = "细";break;
                        default : args[1] = "未知";
                    }
                    switch (json.data[0].frame_type) {
                        case "0": args[2] = "全框";break;
                        case "1": args[2] = "半框";break;
                        case "2": args[2] = "无框";break;
                        default : args[2] = "未知";
                    }
                    switch (json.data[0].frame_width) {
                        case "0": args[3] = "宽";break;
                        case "1": args[3] = "高";break;
                        default : args[3] = "未知";
                    }
                    switch (json.data[0].materials) {
                        case "0": args[4] = "金属";break;
                        case "1": args[4] = "塑料";break;
                        case "2": args[4] = "组合";break;
                        default : args[4] = "未知";
                    }
                    document.getElementById("gimg").innerHTML = "<img src='/glasses/back/fig/glasses/"+json.data[0].figName+"' alt='glassImage' class=\"img-thumbnail\" width='400' height='400'>";
                    document.getElementById("g1").innerHTML += args[0];
                    document.getElementById("g2").innerHTML += args[1];
                    document.getElementById("g3").innerHTML += args[2];
                    document.getElementById("g4").innerHTML += args[3];
                    document.getElementById("g5").innerHTML += args[4];
                }
            }
        }
    }
};