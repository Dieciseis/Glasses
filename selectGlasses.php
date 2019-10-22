<?php

header("content-Type: text/html; charset=utf-8");//字符编码设置

require_once'DBC.php';
$db = new DBC;

$fid = $_GET['fid'];
$style = $_GET['style'];

if($fid != null && $style == null){
    $conn = new mysqli($db->servername, $db->username, $db->password, $db->dbname);
// 检测连接
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //查面部数据
    $sql1 = "SELECT `left_ear_x`,`left_ear_y`,`right_ear_x`,`right_ear_y`,`left_eye_x`,`left_eye_y`,`right_eye_x`,`right_eye_y`,`figName` FROM faces WHERE fid=" . $fid . ";";
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
    echo json_encode($arr,JSON_UNESCAPED_UNICODE);//json编码
    $conn->close();
}