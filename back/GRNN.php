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
//待修改，改为从人脸识别数据集到评价集
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

function distance_mat($Nor_tran,$Nor_test){
    $r = count($Nor_tran);
    $c = count($Nor_tran[0]);
    if($c != count($Nor_test[0])){
        return -1;//异常
    }else{
        $Euclidean_D = array();
        for($i = 0;$i<$r;$i++){
            $Euclidean_D[$i] = distance($Nor_tran[$i],$Nor_test[0]);
        }
        return $Euclidean_D;
    }
}

function Gauss($Euclidean_D,$sigma){
    $r = count($Euclidean_D);
    $Guass = array();
    for($i = 0;$i < $r;$i++){
        $Guass[$i] = exp(-$Euclidean_D[$i]/(2*square($sigma)));
    }
    return $Guass;
}

function sum_layer($Gauss,$label){
    $r = count($label);
    $c = count($Gauss);
    $sumArray = array_fill(0,$c + 1,0);
    if($c != count($label[0])){
        return -1;
    }else{
        $sumArray[0] = array_sum($Gauss);
        for($i = 1; $i < $c ;$i++){
            for($j = 0;$j < $r ;$j++)
            $sumArray[$i] += $Gauss[$j] * $label[$j][$i];
        }
        return $sumArray;
    }
}

function output_layer($sum){
    $c = count($sum);
    $res = array_fill(0,$c -1,0);
    for($i = 0;$i < $c;$i++){
        $res[$i] = $sum[$i]/$sum[0];
    }
    return $sum;
}

function GRNN($tran,$test,$label){
    $Nor_tran = Normalization($tran);

    $Nor_test = Normalization($test);

    $Euclidean_D = distance_mat($Nor_tran,$Nor_test);

    $Gauss_mat = Gauss($Euclidean_D,0.1);

    $sum = sum_layer($Gauss_mat,$label);

    $comm = output_layer($sum);

    return $comm;
}

function get_face_comm($test){
    load_comm_data();//载入训练集
    global $feature_data;
    global $label;
    $res = PNN($feature_data,$test,$label);
    return $res;
}