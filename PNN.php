<?php
header("content-Type: text/html; charset=utf-8");//字符编码设置
require_once ('DBC.php');

$feature_data = array();
$label = array();
//方便测试的打印多维数组函数,打印一维数组会有问题.可以用php自带var_dump()代替
function printArray($a){
    $r = count($a);//行
    $c = count($a[0]);//列
    for($i = 0; $i < $r; $i++){
        foreach($a[$i] as $key => $value){
           echo $value;
           echo " ";
        }
        echo "<br>";
    }
    echo "<br>";
}

//打印一维数组
function printSingleArray($a){
    $r = count($a);//行
    for($i = 0; $i < $r; $i++){
        echo $a[$i];
        echo " ";
    }
    echo "<br>";
}

//PHP有sqrt却没有square,惊了
function square($s){
    return $s * $s;
}

//数据库连接
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

//正向评价载入训练集
function load_comm_data(){
    $conn = createDBC();
    global $feature_data;
    $feature_data = array();
    global $label;
    $label = array();
    $tran_set= $conn->query("SELECT `fid`,`gid`,`style1`,`style2`,`style3`,`style4` FROM `face_with_glasses` where belong = 0;");
    while($row = mysqli_fetch_assoc($tran_set)) {

        global $feature_data;
        global $label;
        $faces = $conn->query("SELECT `face_size`,`face_width` ,`face_shape` ,`eye_size` ,`eye_shape` ,`eye_length` ,`nose_length` ,`nose_width` ,`mouth_thick` ,`mouth_width` ,`eye_distance` ,`forehead` ,`facial_feature`   FROM `faces` WHERE fid=".$row["fid"].";");
        $glasses = $conn->query("SELECT `frame_shape` ,`frame_thickness` ,`frame_type` ,`frame_width`,`materials` FROM `glasses` WHERE gid=".$row["gid"].";");

        $rowf = mysqli_fetch_assoc($faces);
        $rowg = mysqli_fetch_assoc($glasses);

        $data = array($rowf["face_size"],$rowf["face_width"],$rowf["face_shape"],$rowf["eye_size"],$rowf["eye_shape"],$rowf["eye_length"],$rowf["nose_length"],$rowf["nose_width"],$rowf["mouth_thick"],$rowf["mouth_width"],$rowf["eye_distance"],$rowf["forehead"],$rowf["facial_feature"],$rowg["frame_shape"],$rowg["frame_thickness"],$rowg["frame_type"],$rowg["frame_width"],$rowg["materials"]);
        $label_temp = array($row["style1"],$row["style2"],$row["style3"],$row["style4"]);

        array_push($feature_data,$data);
        array_push($label,$label_temp);
    }
    $conn->close();
}

//反向评价载入训练集
function load_inverse_comm_data(){
    $conn = createDBC();
    global $feature_data;
    $feature_data = array();
    global $label;
    $label = array();
    $tran_set= $conn->query("SELECT `fid`,`gid`,`style` FROM `face_with_glasses` where belong = 0;");
    while($row = mysqli_fetch_assoc($tran_set)) {
        global $feature_data;
        global $label;
        $faces = $conn->query("SELECT `face_size`,`face_width` ,`face_shape` ,`eye_size` ,`eye_shape` ,`eye_length` ,`nose_length` ,`nose_width` ,`mouth_thick` ,`mouth_width` ,`eye_distance` ,`forehead` ,`facial_feature`   FROM `faces` WHERE fid=".$row["fid"].";");
        $glasses = $conn->query("SELECT `frame_shape` ,`frame_width` ,`frame_thickness` ,`frame_width`,`materials`   FROM `glasses` WHERE gid=".$row["gid"].";");

        $rowf = mysqli_fetch_assoc($faces);
        $rowg = mysqli_fetch_assoc($glasses);

        $data = array($rowf["face_size"],$rowf["face_width"],$rowf["face_shape"],$rowf["eye_size"],$rowf["eye_shape"],$rowf["eye_length"],$rowf["nose_length"],$rowf["nose_width"],$rowf["mouth_thick"],$rowf["mouth_width"],$rowf["eye_distance"],$rowf["forehead"],$rowf["facial_feature"],$row["style"]);
        $l_temp = array($rowg["frame_shape"],$rowg["frame_width"],$rowg["frame_thickness"],$rowg["frame_width"],$rowg["materials"]);
        array_push($feature_data,$data);
        array_push($label,$l_temp);
    }
    $conn->close();
}

//归一化
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

//两样本之间距离
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

//单个待测样本与所有训练样本的欧氏距离
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

//高斯函数
function Gauss($Euclidean_D,$sigma){
    $r = count($Euclidean_D);
    $Guass = array();
    for($i = 0;$i < $r;$i++){
        $Guass[$i] = exp(-$Euclidean_D[$i]/(2*square($sigma)));
    }
    return $Guass;
}

//测试样本属于各类的概率
function Prob_mat($Gauss_mat,$Label){
    $label_type = count($Label[0]);
    $tran_total = count($Label);
    $label_type_g = count($Gauss_mat);
    if($tran_total != $label_type_g){
        echo "num of tran is ".$tran_total."<br>";
        echo "num of label is ".$label_type_g."<br>";
        return -1;//error
    }else{//标签分类
        $Prob = array();
        for($i = 0;$i < $tran_total;$i++){
            for($j = 0;$j < $label_type;$j++){
                if(!array_key_exists($Label[$i][$j],$Prob[$j])){
                    $Prob[$j][$Label[$i][$j]] = 0;
                }
            }
        }
        for($i = 0;$i < $tran_total ;$i++){
            for($j = 0; $j < $label_type;$j++){
                if(array_key_exists($Label[$i][$j],$Prob[$j])){
                    $Prob[$j][$Label[$i][$j]] += $Gauss_mat[$i];
                }
            }
        }
        return $Prob;
    }
}

//分类
function class_result($Prob){
    $result = array();
    $r = count($Prob);
    for($i = 0; $i < $r; $i++){
        $cmp = 0;
        foreach ($Prob[$i] as $key => $value){

            if($value > $cmp){
                $result["style".($i+1)] = $key;
                $result["prob".($i+1)] = $value;
                $cmp = $value;
            }
        }
    }
    return $result;
}

function PNN($tran,$test,$label){
    $Nor_tran = Normalization($tran);

    $Nor_test = Normalization($test);

    $Euclidean_D = distance_mat($Nor_tran,$Nor_test);

    $Gauss_mat = Gauss($Euclidean_D,0.1);

    $prob = Prob_mat($Gauss_mat,$label);

    $predict = class_result($prob);

    return $predict;
}

//正向评价预测测试样本标签
function get_comm_Result($test){
    global $feature_data;
    global $label;
    load_comm_data();//载入训练集
    $res = PNN($feature_data,$test,$label);
    return $res;
}

//反向评价预测测试样本标签
function get_inverse_comm_Result($test){
    load_inverse_comm_data();//载入训练集
    global $feature_data;
    global $label;
    $res = PNN($feature_data,$test,$label);
    return $res;
}



//$test_tran = array(array(1,2,3,4,5,6),array(2,3,4,5,6,7),array(1,2,2,4,2,3));
//printArray($test_tran);
//$test_label = array(array(2),array(4),array(2));
//printArray($test_label);
//$test = array(array(1,2,2,4,1,4));
//printArray($test);
//$res  = PNN($test_tran,$test,$test_label);
//echo "<br>the result is ";
//printSingleArray($res);


