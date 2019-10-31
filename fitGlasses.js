"use strict";

var face=[];//脸部坐标,0
var glasses=[];//眼镜坐标,1
var figUrl1;
var figUrl2;

var canvas;
var ctx;
var drawn = 0;
//测试用画点程序，测完删
function drawPlot(x,y){
    ctx.fillStyle = "rgba(235,0,0,0.5)";
    ctx.beginPath();
    ctx.arc(x,y,4,0,Math.PI*2,true);
    ctx.fill();
}

function getQueryString(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r!=null) return r[2];else return'';
}


window.onload = function init(){
    var fid = getQueryString("fid");
    var gid = getQueryString("gid");
    if(fid !==''&& gid !=='') {
        canvas = document.getElementById("show");
        ctx = canvas.getContext("2d");

        var url = "http://www.deepbluecape.ink/glasses/back/fitData.php?fid=" + fid + "&gid=" + gid;

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

                    figUrl1 = "back/fig/face/" + json.data[0].figName;drawn++;
                    figUrl2 = "back/fig/glasses/" + json.data[1].figName;drawn++;
                }
            }

            if(drawn>0) {
                var image1 = new Image();
                image1.src = figUrl1;
                image1.onload = function () {
                    ctx.drawImage(image1, 0, 0, image1.width, image1.height,0,0,canvas.width,canvas.height);
                    drawn--;
                    //截取起始坐标和长宽
                    var image2 = new Image();
                    image2.src = figUrl2;
                    image2.onload = function () {
                        var sx = glasses[0]*canvas.width;
                        var sy = glasses[1]*canvas.height;
                        var swidth = (glasses[2]-glasses[0])*canvas.width;
                        var sheight = (glasses[3]-glasses[1])*canvas.height;
                        //中心点
                        var f_center_x = (face[0]+face[2])/2*canvas.width;
                        var f_center_y = (face[1]+face[3])/2*canvas.height;
                        console.log(f_center_x,f_center_y);

                        var glass_center_x = (glasses[4]+glasses[6])/2*canvas.width;
                        var glass_center_y = (glasses[5]+glasses[7])/2*canvas.height;
                        console.log(glass_center_x,glass_center_y);

                        //贴图起始坐标和长宽
                        var width = 1.2* (f_center_x - face[0]*canvas.width)/(glass_center_x - glasses[4]*canvas.width)*(glasses[2] - glasses[0])*canvas.width;
                        var height = canvas.height * (glasses[3] - glasses[1])* width / ((glasses[2] - glasses[0])* canvas.width) ;//等比例缩放
                        var x = f_center_x - width/2;
                        var y = f_center_y - height/2;

                        ctx.drawImage(image2, sx,sy,swidth,sheight,x,y,width,height);
                        drawn--;
                    };
                };
            }
        }
    }
};


