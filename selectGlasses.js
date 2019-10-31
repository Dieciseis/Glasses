var face=[];//脸部坐标,0
var glasses=[];//眼镜id数组时
var figUrl1;
var figUrl2=[];

var canvas;
var ctx;
var drawn = 0;

function getQueryString(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r!=null) return r[2];else return'';
}

window.onload = function(){
    var fid = getQueryString("fid");
    if(fid !== ' ') {
        canvas = document.getElementById("photo");
        ctx = canvas.getContext("2d");

        var url = "http://www.deepbluecape.ink/glasses/back/selectGlasses.php?fid=" + fid;
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
                    var temp = "{\"data\":" + xhr.responseText + "}",//整合json数据
                        json = eval('(' + temp + ')');//也可使用 JSON.parse,注意格式
                    face[0] = parseFloat(json.data[0].left_eye_x);
                    face[1] = parseFloat(json.data[0].left_eye_y);
                    face[2] = parseFloat(json.data[0].right_eye_x);
                    face[3] = parseFloat(json.data[0].right_eye_y);


                    figUrl1 = "back/fig/face/" + json.data[0].figName;
                    drawn++;
                }
            }
            if (drawn > 0) {
                var image1 = new Image();
                image1.src = figUrl1;
                image1.onload = function () {
                    ctx.drawImage(image1, 0, 0, image1.width, image1.height);
                    drawn--;
                }
            }

        }
    }
};