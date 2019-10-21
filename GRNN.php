<?php
header("content-Type: text/html; charset=utf-8");//字符编码设置
require_once ('DBC.php');

$feature_data = array();
$label = array();

function square($s){
    return $s * $s;
}

function createDBC(){
    $db = new DBC();
    $conn = new mysqli($db->servername, $db->username, $db->password, $db->dbname);
// 检测连接
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }else{
        return $conn;
    }
}

function load_comm_data(){
    $conn = createDBC();
    global $feature_data;
    $feature_data = array();
    global $label;
    $label = array();
    $tran_set= $conn->query("SELECT `fid`,`gid`,`style` FROM `face_with_glasses` where belong = 0;");
    while($row = mysqli_fetch_assoc($tran_set)) {
//        echo"this row is ".$row["fid"];
//        echo"<br>";
        global $feature_data;
        global $label;
        $faces = $conn->query("SELECT `face_size`,`face_width` ,`face_shape` ,`eye_size` ,`eye_shape` ,`eye_length` ,`nose_length` ,`nose_width` ,`mouth_thick` ,`mouth_width` ,`eye_distance` ,`forehead` ,`facial_feature`   FROM `faces` WHERE fid=".$row["fid"].";");
        $glasses = $conn->query("SELECT `frame` ,`arm` ,`bridge` ,`footwear`   FROM `glasses` WHERE gid=".$row["gid"].";");
//        echo "alive until sql";
//        echo"<br>";
        $rowf = mysqli_fetch_assoc($faces);
        $rowg = mysqli_fetch_assoc($glasses);
//        echo "face size is ".$rowf["face_size"];
//        echo"<br>";
        $data = array($rowf["face_size"],$rowf["face_width"],$rowf["face_shape"],$rowf["eye_size"],$rowf["eye_shape"],$rowf["eye_length"],$rowf["nose_length"],$rowf["nose_width"],$rowf["mouth_thick"],$rowf["mouth_width"],$rowf["eye_distance"],$rowf["forehead"],$rowf["facial_feature"],$rowg["frame"],$rowg["arm"],$rowg["bridge"],$rowg["footwear"]);
        $label_temp = array($row["style"]);
//        echo "alive before push array<br>";
//        echo "data[0] is".$data[0]."<br>";
        array_push($feature_data,$data);
        array_push($label,$label_temp);
//        echo "alive after push array<br>";
    }
    $conn->close();
//    $one_set[] = $feature_data;
//    $one_set[] = $label;
//    return $one_set;
}

function Normalization($data){
    $r = count($data);//行
    $c = count($data[0]);//列
    $Nor_feature = array();
    $row = array();
    $sum = 0;
    for($i = 0;$i<$r;$i++){
        for($j = 0;$j < $c;$j++){
            $row[$j] = square($data[$i][$j]);
            $sum += $row[$j];
        }
        for($j = 0;$j < $c;$j++){
            $row[$j] /= $sum  ;
        }
        array_push($Nor_feature,$row);
        $sum = 0;
    }
    return $Nor_feature;
}

function distance($X,$Y){
    $dis = 0;
    $rx = count($X);//行
    $cx = count($X[0]);//列
    $ry = count($Y);//行
    $cy = count($Y[0]);//列
    if($cx != $cy || $rx != $ry){
        return -1;//异常
    }else{
        for($i = 0;$i <$cx;$i++){
            $dis += square($X[$i] - $Y[$i]);
        }
        return sqrt($dis);
    }
}