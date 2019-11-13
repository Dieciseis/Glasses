<?php
header("content-Type: text/html; charset=utf-8");//字符编码设置
//根据请求的fid和gid，返回对应人脸和眼镜佩戴效果需要的坐标数据。
//返回数据格式：json格式的数组，每行数据包含人脸X1,Y1,X2,Y2,眼镜X1,Y1,X2,Y2,X3,Y3,X4,Y4（只有一行，前端解析返回数据第一行即可）
require_once'DBC.php';
$db = new DBC;

$gid = $_GET['gid'];
$fid = $_GET['fid'];

if($gid != null||$fid != null) {
// 创建连接
    $conn = new mysqli($db->servername, $db->username, $db->password, $db->dbname);
// 检测连接
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //查面部数据
    $sql1 = "SELECT `left_eye_x`,`left_eye_y`,`right_eye_x`,`right_eye_y`,`figName` FROM faces WHERE fid=" . $fid . ";";
    //查眼镜数据
    $sql2 = "SELECT `left_ear_x`,`left_ear_y`,`right_ear_x`,`right_ear_y`,`left_eye_x`,`left_eye_y`,`right_eye_x`,`right_eye_y`,`figName` FROM glasses WHERE gid=" . $gid . ";";


    $result1 = $conn->query($sql1);
    $arr = array();
// 输出每行数据
    while($row = $result1->fetch_assoc()) {
        $count=count($row);//不能在循环语句中，由于每次删除row数组长度都减小
        for($i=0;$i<$count;$i++){
            unset($row[$i]);//删除冗余数据
        }
        array_push($arr,$row);
    }
    $result2 = $conn->query($sql2);
// 输出每行数据
    while($row = $result2->fetch_assoc()) {
        $count=count($row);//不能在循环语句中，由于每次删除row数组长度都减小
        for($i=0;$i<$count;$i++){
            unset($row[$i]);//删除冗余数据
        }
        array_push($arr,$row);
    }
    echo json_encode($arr,JSON_UNESCAPED_UNICODE);//json编码
    $conn->close();
}