"use strict"
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
            var photo;
            var photoW = canvas.width;
            var photoH = canvas.height;

            var fileInput = evt.target.file;
            if(fileInput.length > 0){
                //window url
                var windowURL = window.URL || window.webkitURL;
                //picture url
                var picURL = windowURL.createObjectURL(fileInput[0]);

                photo = new Image();
                photo.src = picURL;
                photo.onload = function() {
                    //draw photo into canvas when ready
                    ctx.drawImage(photo, 0, 0, photoW, photoH);
                };
                //释放picURL
                var arr = picURL.split('/');
                var imgUrl = arr[arr.length - 1];
                windowURL.revokeObjectURL(imgUrl);
            }
        }
    );
    markPhoto();
};