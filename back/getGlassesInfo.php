<?php
//根据请求的gid查询眼镜设计要素数据
//返回数据格式：json 
header("content-Type: text/html; charset=utf-8");//字符编码设置
require_once'DBC.php';

$gid = $_GET['gid'];
$db = new DBC;
// 创建连接
$conn = new mysqli($db->servername, $db->username, $db->password, $db->dbname);
// 检测连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$glasses = $conn->query("SELECT `figName`,`frame_shape` ,`frame_thickness` ,`frame_type` ,`frame_width`,`materials` FROM `glasses` WHERE gid=".$gid.";");
$arr = array();
while($row = $glasses->fetch_assoc()){
    $count = count($row);
    for($i = 0;$i<$count;$i++){
        unset($row[$i]);
    }
    array_push($arr,$row);
}

echo json_encode($arr,JSON_UNESCAPED_UNICODE);//json编码
$conn->close();