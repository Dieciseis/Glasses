"use strict";
var fid;
function newWindow(){
    window.open("selectGlasses.html?fid="+fid);
}

function sendImage() {
        var url = "http://www.deepbluecape.ink/glasses/back/getFaceInfo.php";//请求13项人脸感性评价数据
        var input = document.querySelector("#upLoadImage");

        var formData = new FormData();
        formData.append("file", input.files[0]);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.send(formData);
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
                     console.log(json);
                     fid = json.data[0].fid;
                     document.getElementById("face_size").value = json.data[0].face_size;
                     document.getElementById("p1").innerHTML = json.data[0].face_size;
                     document.getElementById("face_width").value = json.data[0].face_width;
                    document.getElementById("p2").innerHTML = json.data[0].face_width;
                     document.getElementById("face_shape").value = json.data[0].face_shape;
                    document.getElementById("p3").innerHTML = json.data[0].face_shape;
                     document.getElementById("eye_size").value = json.data[0].eye_size;
                    document.getElementById("p4").innerHTML = json.data[0].eye_size;
                     document.getElementById("eye_shape").value = json.data[0].eye_shape;
                    document.getElementById("p5").innerHTML = json.data[0].eye_shape;
                     document.getElementById("eye_length").value = json.data[0].eye_length;
                    document.getElementById("p6").innerHTML = json.data[0].eye_length;
                     document.getElementById("nose_length").value = json.data[0].nose_length;
                    document.getElementById("p7").innerHTML = json.data[0].nose_length;
                     document.getElementById("nose_width").value = json.data[0].nose_width;
                    document.getElementById("p8").innerHTML = json.data[0].nose_width;
                     document.getElementById("mouth_thick").value = json.data[0].mouth_thick;
                    document.getElementById("p9").innerHTML = json.data[0].mouth_thick;
                     document.getElementById("mouth_width").value = json.data[0].mouth_width;
                    document.getElementById("p10").innerHTML = json.data[0].mouth_width;
                     document.getElementById("eye_distance").value = json.data[0].eye_distance;
                    document.getElementById("p11").innerHTML = json.data[0].eye_distance;
                     document.getElementById("forehead").value = json.data[0].forehead;
                    document.getElementById("p12").innerHTML = json.data[0].forehead;
                     document.getElementById("facial_feature").value = json.data[0].facial_feature;
                    document.getElementById("p13").innerHTML = json.data[0].facial_feature;
                    document.getElementById("getRecommend").innerHTML = "<button type=\"button\" class=\"btn btn-default\" onclick='newWindow()'>个性推荐</button>";
                }
            }
        }
}