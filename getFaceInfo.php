<?php
header("content-Type: text/html; charset=utf-8");//字符编码设置
require_once 'DBC.php';
require_once 'call_API.php';

function curlPost($url,$data){
    $ch = curl_init();
//
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在

//设置请求选项
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER,0);

//这是请求类型
    curl_setopt($ch, CURLOPT_POST, TRUE);
//添加post数据到请求中
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);

//错误处理
    if($response == FALSE){
        echo "错误信息:".curl_error($ch)."<br/>";
    }
    curl_close($ch);
//输出返回信息
    return $response;
}

$db = new DBC();

// 创建连接
$conn =new mysqli($db->servername, $db->username, $db->password, $db->dbname);
// 检测连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$figName = $_FILES['file']['name'];
$figName_tmp = "\"".$figName."\"";

$api = new call_API();
$img_url = "http://www.deepbluecape.ink/glasses/back/fig/faces/".$figName;
$data=[
    'api_key'=> "-v4e-wr31tG1a-EYZQl0zYyFipmnzbK0" ,
    'api_secret'=>"CYIVrLWH2Kcvu70ZXUIjmuT_7bm7Vijp",
    'image_file' => new CURLFile(realpath($img_url)),
    'return_landmark'=>1//1：83特征点。2:106特征点。0：不检测
];
$detect_api_url  ="https://api-cn.faceplusplus.com/facepp/v3/detect";

//image_url
$image_url = "http://www.deepbluecape.ink/glasses/back/fig/faces/".$figName;

$return_landmark = "1";

$url = "{$detect_api_url}";

$data = [
    "api_key" => "{$api->API_Key}",
    "api_secret" => "{$api->API_Secret}",
    "image_url" => "$image_url",
    "return_landmark" => "{$return_landmark}",
];


$response = curlPost($url,$data);
$data = json_decode($response,1);

$landmark = $data['faces'][0]["landmark"];
var_dump($landmark);



//$sql = "SELECT `fid` ,`face_size`,`face_width` ,`face_shape` ,`eye_size`,`eye_shape` ,`eye_length` ,`nose_length` ,`nose_width` ,`mouth_thick` ,`mouth_width` ,`eye_distance` ,`forehead` ,`facial_feature`    from `faces` WHERE  `figName`= ".$figName_tmp.";";
//
//$result1 = $conn->query($sql);
//
//$arr = array();
//
////输出每行数据
//while($row = $result1->fetch_assoc()) {
//   $count=count($row);//不能在循环语句中，由于每次删除row数组长度都减小
//   for($i=0;$i<$count;$i++){
//        unset($row[$i]);//删除冗余数据
//    }
//    array_push($arr,$row);
//}
//$conn->close();
//echo json_encode($arr,JSON_UNESCAPED_UNICODE);//json编码
//
