<?php
header("content-Type: text/html; charset=utf-8");//字符编码设置
require_once ('DBC.php');
//通过数据库中已有的52项关联性指标和对应的13项感性评价指标，预测测试集的52项关联性指标对应的13项感性评价指标

$feature_data = array();
$label = array();

//平方函数
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

//将有13项感性评价的52项人脸特征关联性指标作为训练集载入，13项感性评价作为标签
function load_comm_data(){
    $conn = createDBC();
    global $feature_data;
    $feature_data = array();
    global $label;
    $label = array();
    $tran_set= $conn->query("SELECT * FROM `face_point` where fid in (SELECT fid from `faces`);");//查询有13项感性指标的52项人脸特征关联性数据，每行一组52项指标数据
    while($row = mysqli_fetch_assoc($tran_set)) {
        global $feature_data;
        global $label;
        $faces = $conn->query("SELECT `face_size`,`face_width` ,`face_shape` ,`eye_size` ,`eye_shape` ,`eye_length` ,`nose_length` ,`nose_width` ,`mouth_thick` ,`mouth_width` ,`eye_distance` ,`forehead` ,`facial_feature`   FROM `faces` WHERE fid=".$row["fid"].";");
       
        $rowf = mysqli_fetch_assoc($faces);

        $data = array($row["R0"],$row["R1"],$row["R2"],$row["R3"],$row["R4"],$row["R5"],$row["R6"],$row["R7"],$row["R8"],$row["R9"],$row["R10"],$row["R11"],$row["R12"],$row["R13"],$row["R14"]
        ,$row["R15"],$row["R16"],$row["R17"],$row["R18"],$row["R19"],$row["R20"],$row["R21"],$row["R22"],$row["R23"],$row["R24"],$row["R25"],$row["R26"],$row["R27"],$row["R28"]
        ,$row["R29"],$row["R30"],$row["R31"],$row["R32"],$row["R33"],$row["R34"],$row["R35"],$row["R36"],$row["R37"],$row["R38"],$row["R39"],$row["R40"],$row["R41"],$row["R42"]
        ,$row["R43"],$row["R44"],$row["R45"],$row["R46"],$row["R47"],$row["R48"],$row["R49"],$row["R50"],$row["R51"]);
        $label_temp = array($rowf["face_size"],$rowf["face_width"],$rowf["face_shape"],$rowf["eye_size"],$rowf["eye_shape"],$rowf["eye_length"],$rowf["nose_length"],$rowf["nose_width"],$rowf["mouth_thick"],$rowf["mouth_width"],$rowf["eye_distance"],$rowf["forehead"],$rowf["facial_feature"]);

        array_push($feature_data,$data);
        array_push($label,$label_temp);

    }
    $conn->close();
    
}

function Normalization($data){//归一化
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

function sum_layer($Gauss,$label){//求和层
    $r = count($label);
    $c = count($label[0]);
    $r_Gauss = count($Gauss);
    $sumArray = array_fill(0,$c + 1,0);
    if($r_Gauss != $r){
        return -1;
    }else{
        $sumArray[0] = array_sum($Gauss);
        for($i = 0; $i < $c;$i++){
            for($j = 0;$j < $r ;$j++)
            $sumArray[$i+1] += $Gauss[$j] * floatval($label[$j][$i]);
        }
        return $sumArray;
    }
}

function output_layer($sum){//输出层
    $c = count($sum);
    $res = array_fill(0,$c-1,0);
    for($i = 1;$i < $c;$i++){
        $res[$i-1] = $sum[$i]/$sum[0];
    }

    return $res;
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
    $res = GRNN($feature_data,$test,$label);
    return $res;
}

//↓测试用代码
// $temp = array_fill(0,52,1);
// $test_temp = array($temp);
// $res = get_face_comm($test_temp);
// var_dump($res);