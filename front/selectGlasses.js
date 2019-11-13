"use strict";
var fid;
var figUrl1;
var canvas;
var ctx;
var drawn = 0;

function getQueryString(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r!=null) return r[2];else return'';
}

function fitGlasses(g){//实现佩戴效果贴图的函数，点击眼镜图片触发
    var face=[];//脸部坐标,0
    var glasses=[];//眼镜坐标,1
    var figUrl2;
    var figUrl3;

    canvas = document.getElementById("photo");
    ctx = canvas.getContext("2d");

    var url = "http://www.deepbluecape.ink/glasses/back/fitData.php?fid=" + fid + "&gid=" + g;//请求贴图相关坐标

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
                face[0] = parseFloat(json.data[0].left_eye_x);
                face[1] = parseFloat(json.data[0].left_eye_y);
                face[2] = parseFloat(json.data[0].right_eye_x);
                face[3] = parseFloat(json.data[0].right_eye_y);
                console.log(face);

                glasses[0] = parseFloat(json.data[1].left_ear_x);
                glasses[1] = parseFloat(json.data[1].left_ear_y);
                glasses[2] = parseFloat(json.data[1].right_ear_x);
                glasses[3] = parseFloat(json.data[1].right_ear_y);
                glasses[4] = parseFloat(json.data[1].left_eye_x);
                glasses[5] = parseFloat(json.data[1].left_eye_y);
                glasses[6] = parseFloat(json.data[1].right_eye_x);
                glasses[7] = parseFloat(json.data[1].right_eye_y);
                console.log(glasses);

                figUrl2 = "back/fig/face/" + json.data[0].figName;
                drawn++;
                figUrl3 = "back/fig/glasses/" + json.data[1].figName;
                drawn++;
            }
        }

        if (drawn > 0) {
            var image1 = new Image();
            image1.src = figUrl2;
            image1.onload = function () {
                ctx.drawImage(image1, 0, 0, image1.width, image1.height, 0, 0, canvas.width, canvas.height);
                drawn--;
                //截取起始坐标和长宽
                var image2 = new Image();
                image2.src = figUrl3;
                image2.onload = function () {
                    var sx = glasses[0] * canvas.width;
                    var sy = glasses[1] * canvas.height;
                    var swidth = (glasses[2] - glasses[0]) * canvas.width;
                    var sheight = (glasses[3] - glasses[1]) * canvas.height;
                    //中心点
                    var f_center_x = (face[0] + face[2]) / 2 * canvas.width;
                    var f_center_y = (face[1] + face[3]) / 2 * canvas.height;
                    console.log(f_center_x, f_center_y);

                    var glass_center_x = (glasses[4] + glasses[6]) / 2 * canvas.width;
                    var glass_center_y = (glasses[5] + glasses[7]) / 2 * canvas.height;
                    console.log(glass_center_x, glass_center_y);

                    //贴图起始坐标和长宽
                    var width = 1.2 * (f_center_x - face[0] * canvas.width) / (glass_center_x - glasses[4] * canvas.width) * (glasses[2] - glasses[0]) * canvas.width;
                    var height = canvas.height * (glasses[3] - glasses[1]) * width / ((glasses[2] - glasses[0]) * canvas.width);//等比例缩放
                    var x = f_center_x - width / 2;
                    var y = f_center_y - height / 2;

                    ctx.drawImage(image2, sx, sy, swidth, sheight, x, y, width, height);
                    drawn--;
                };
            };
        }
    }
}

function changeCondition(json){//改变筛选条件，重置推荐序列
    var style1 = document.getElementById("style1");
    var style2 = document.getElementById("style2");
    var style3 = document.getElementById("style3");
    var style4 = document.getElementById("style4");
    var style_mat = [];

    for(i=0;i<style1.length;i++) {//下拉框的长度就是它的选项数.
        if(style1[i].selected === true) {
            style_mat[0] = style1[i].value;//获取当前选择项的值.
        }
        if(style2[i].selected === true) {
            style_mat[1] = style2[i].value;//获取当前选择项的值.
        }
        if(style3[i].selected === true) {
            style_mat[2] = style3[i].value;//获取当前选择项的值.
        }
        if(style4[i].selected === true) {
            style_mat[3] = style4[i].value;//获取当前选择项的值.
        }
    }
    var glasses = '';
    var chosen = 0;
    for(var i = 0;i < 17;i++){
        //判断当前眼镜是否符合要求（写成递归函数可能会更好，但我不想写了）
        var gid = parseInt(json.data[i].gid);
        if(style_mat[0] === "0" || style_mat[0] === json.data[i].style1){
            if(style_mat[1] === "0" || style_mat[1] === json.data[i].style2){
                if(style_mat[2] === "0" || style_mat[2] === json.data[i].style3){
                    if(style_mat[3] === "0" || style_mat[3] === json.data[i].style4){
                        chosen = 1;
                    }else{
                        chosen = 0;
                    }
                }else{
                    chosen = 0;
                }
            }else{
                chosen = 0;
            }
        }else{
            chosen = 0;
        }
        if(chosen ===1)
            glasses += "<div id='g" + gid + "' class=\"col-md-3\">" +
                "<img src='/glasses/back/fig/glasses/" + (gid - 6) + ".png' alt='glassImage' class=\"img-thumbnail\" width='100' height='100' onclick='fitGlasses("+gid+")'>" +
                "<br><a href='http://www.deepbluecape.ink/glasses/glassesInfo.html?gid=" + gid + "'>details>></a>" +
                "</div>"
    }
    console.log(glasses);
    document.getElementById("showGlasses").innerHTML = glasses;//插入眼镜推荐序列
}

window.onload = function(){
    fid = getQueryString("fid");
    if(fid !== ' ') {
        canvas = document.getElementById("photo");
        ctx = canvas.getContext("2d");

        var url = "http://www.deepbluecape.ink/glasses/back/selectGlasses.php?fid=" + fid + "&&fig=1";//请求人脸照片名
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
                    console.log(xhr.responseText);
                    figUrl1 = "back/fig/face/" + xhr.responseText;
                    drawn++;
                }
            }
            if (drawn > 0) {
                var image1 = new Image();
                image1.src = figUrl1;
                image1.onload = function () {
                    ctx.drawImage(image1, 0, 0, image1.width, image1.height);
                    drawn--;
                    var url = "http://www.deepbluecape.ink/glasses/back/selectGlasses.php?fid=" + fid;//请求眼镜推荐序列
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', url, true);
                    xhr.send();
                    xhr.onreadystatechange = function () {
                        // readyState == 4说明请求已完成
                        if (xhr.readyState === 4 && xhr.status === 200 || xhr.status === 304) {
                            // 从服务器获得数据
                            if (xhr.responseText === false) {
                                alert("发生错误！");
                            } else{
                                var temp = "{\"data\":" + xhr.responseText + "}",//整合json数据
                                    json = eval('(' + temp + ')');//也可使用 JSON.parse,注意格式
                                console.log(json);
                                changeCondition(json);
                                document.getElementById("style1").onchange = function(){
                                    changeCondition(json);
                                };
                                document.getElementById("style2").onchange = function(){
                                    changeCondition(json);
                                };
                                document.getElementById("style3").onchange = function(){
                                    changeCondition(json);
                                };
                                document.getElementById("style4").onchange = function(){
                                    changeCondition(json);
                                };
                            }
                        }
                    }
                }
            }
        }
    }
};