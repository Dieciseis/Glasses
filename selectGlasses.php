<?php
header("content-Type: text/html; charset=utf-8");//字符编码设置
require_once'DBC.php';
require_once'PNN.php';

$style = $_GET['style'];
$fid = $_GET['fid'];

$db = new DBC;
if($fid != null && $style == null){
    $conn = new mysqli($db->servername, $db->username, $db->password, $db->dbname);
// 检测连接
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    //查面部数据
    $sql1 = "SELECT  `figName`,`left_eye_x`,`left_eye_y`,`right_eye_x`,`right_eye_y`,`figName` FROM  `faces` WHERE fid=" . $fid . ";";
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
    $sql2 = "SELECT * FROM `face_with_glasses` WHERE fid=".$fid;
    $res2 = $conn->query($sql2);
    if(mysqli_num_rows($res2)>0){
        $conn->close();
    }else{
        echo "worked";
        $sql3 = "SELECT gid FROM glasses";
        $res3 = $conn->query($sql3);
        while($row = $res3->fetch_assoc()){

            $gid = $row["gid"];
            $faces = $conn->query("SELECT `face_size`,`face_width` ,`face_shape` ,`eye_size` ,`eye_shape` ,`eye_length` ,`nose_length` ,`nose_width` ,`mouth_thick` ,`mouth_width` ,`eye_distance` ,`forehead` ,`facial_feature`   FROM `faces` WHERE fid=".$fid.";");
            $glasses = $conn->query("SELECT `frame_shape` ,`frame_thickness` ,`frame_type` ,`frame_width`,`materials` FROM `glasses` WHERE gid=".$gid.";");

            $rowf = $faces->fetch_assoc();
            $rowg = $glasses->fetch_assoc();

            $test = array(array($rowf["face_size"],$rowf["face_width"],$rowf["face_shape"],$rowf["eye_size"],$rowf["eye_shape"],$rowf["eye_length"],$rowf["nose_length"],$rowf["nose_width"],$rowf["mouth_thick"],$rowf["mouth_width"],$rowf["eye_distance"],$rowf["forehead"],$rowf["facial_feature"],$rowg["frame_shape"],$rowg["frame_thickness"],$rowg["frame_type"],$rowg["frame_width"],$rowg["materials"]));
            $res = get_comm_Result($test);

            echo json_encode($res,JSON_UNESCAPED_UNICODE);
        }
    }
}else{
    if($fid != null && $style != null){
        echo "test select";
    }
}
