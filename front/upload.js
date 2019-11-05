"use strict";
var res;
var canvas;
var ctx;
function getQueryString(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r!=null) return r[2];else return'';
}

function markPhoto(){
    canvas.addEventListener("click", function(event) {
        getMousePos(canvas, event);
    });

}

function getMousePos(canvas, event) {
    //1
    var rect = canvas.getBoundingClientRect();
    //2
    var x = Math.round((event.clientX - rect.left * (canvas.width / rect.width))/canvas.width*1000)/1000;
    var y = Math.round((event.clientY - rect.top * (canvas.height / rect.height))/canvas.height*1000)/1000;
    if(x >= 0 && y >= 0)
        document.getElementById("marks").innerHTML="坐标： X："+ x +" "+ "Y："+y;
}



window.onload = function init(){
    var result = getQueryString("res");
    if(result === "0" ){
        window.alert("录入失败！");
    }else{
        if(result === "1"){
            window.alert("录入成功！");
        }
    }
    canvas = document.getElementById("photo");
    ctx = canvas.getContext("2d");

    $("#upLoadImage").change(
        function uploadPhoto(evt){
            var file = document.querySelector('input[type=file]').files[0];// 获取选择的文件，这里是图片类型
            var reader = new FileReader();
            reader.readAsDataURL(file); //读取文件并将文件以URL的形式保存在resulr属性中 base64格式
            reader.onload = function(e) { // 文件读取完成时触发
                let result = e.target.result;
                var image = new Image();
                image.src = result;
                image.onload = function(){
                    ctx.drawImage(image, 0, 0, image.width, image.height,0,0,canvas.width,canvas.height);
                }
            }
        }
    );
    markPhoto();
};