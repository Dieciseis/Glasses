<?php
header("content-Type: text/html; charset=utf-8");//字符编码设置
require_once 'DBC.php';
require_once 'call_API.php';
require_once 'GRNN.php';

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

$contour_top = array_fill(0,2,0);
$sql1 = "SELECT `fid` ,`contour_top_x` ,`contour_top_y`  from `faces` WHERE  `figName`= ".$figName_tmp.";";
$result1 = $conn->query($sql1);
$row = $result1->fetch_assoc();

if($row == null){
    echo "error!";
}else{
    $fid = intval($row["fid"]);
    $contour_top[0] = intval(floatval($row["contour_top_x"])* 400);
    $contour_top[1] = intval(floatval($row["contour_top_y"])* 400);
}

$sql2 = "SELECT  *  from `face_point` WHERE  `fid`= ".$fid.";";
$result2 =  $conn->query($sql2);
$row = $result2->fetch_assoc();

if($row == null){//查不到，需要计算R系列
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
    $image_url = "http://www.deepbluecape.ink/glasses/back/fig/face/".$figName;
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

    $area = array_fill(0,7,0);
    //脸部面积
    $area[0] = ($landmark["contour_left5"]["y"]-$contour_top[1] + 0.5 * ($landmark["contour_chin"]["y"] - $landmark["contour_left5"]["y"]))*($landmark["contour_right5"]["x"]-$landmark["contour_left5"]["x"]);
    //额头面积
    $area[1] = ($landmark["left_eyebrow_upper_middle"]["y"] - $contour_top[1])*($landmark["right_eyebrow_right_corner"]["x"] - $landmark["left_eyebrow_left_corner"]["x"]);
    //下巴面积
    $area[2] = 0.5 * ($landmark["contour_chin"]["y"] - $landmark["contour_left5"]["y"])*($landmark["contour_right5"]["x"]-$landmark["contour_left5"]["x"]);
    //眼睛面积
    $area[3] = 0.5*($landmark["left_eye_right_corner"]["x"]-$landmark["left_eye_left_corner"]["x"])*($landmark["left_eye_bottom"]["y"]-$landmark["left_eye_top"]["y"])+ 0.5*($landmark["right_eye_right_corner"]["x"]-$landmark["right_eye_left_corner"]["x"])*($landmark["right_eye_bottom"]["y"]-$landmark["right_eye_top"]["y"]);
    //鼻子面积
    $area[4] = 0.5*($landmark["nose_right"]["x"]-$landmark["nose_left"]["x"]+ $landmark["nose_contour_right1"]["x"]-$landmark["nose_contour_left1"]["x"])*($landmark["nose_contour_lower_middle"]["y"]-$landmark["nose_contour_left1"]["y"]);
    //嘴面积,梯形*2
    $area[5] = 0.5*($landmark["mouth_upper_lip_right_contour1"]["x"]-$landmark["mouth_upper_lip_left_contour1"]["x"] + $landmark["mouth_right_corner"]["x"]-$landmark["mouth_left_corner"]["x"])*($landmark["mouth_left_corner"]["y"]-$landmark["mouth_upper_lip_left_contour1"]["y"])
    + 0.5* ($landmark["mouth_lower_lip_right_contour1"]["x"]-$landmark["mouth_lower_lip_left_contour1"]["x"] + $landmark["mouth_right_corner"]["x"]-$landmark["mouth_left_corner"]["x"])*($landmark["mouth_lower_lip_right_contour1"]["y"]-$landmark["mouth_left_corner"]["y"]);
    //五官面积
    $area[6] = 0.5*($landmark["right_eye_right_corner"]["x"]-$landmark["left_eye_left_corner"]["x"]+ $landmark["mouth_right_corner"]["x"] - $landmark["mouth_left_corner"]["x"])*($landmark["mouth_left_corner"]["y"]-$landmark["left_eye_left_corner"]["y"]);

    $length = array_fill(0,10,0);
    //脸长
    $length[0] = $landmark["contour_chin"]["y"]-$contour_top[1];
    //脸宽
    $length[1] = $landmark["contour_right2"]["x"]-$landmark["contour_left2"]["x"];
    //眼长
    $length[2] = 0.5 * ($landmark["left_eye_right_corner"]["x"]-$landmark["left_eye_left_corner"]["x"] + $landmark["right_eye_right_corner"]["x"]-$landmark["right_eye_left_corner"]["x"]);
    //眼宽
    $length[3] = 0.5 * ($landmark["left_eye_bottom"]["y"]-$landmark["left_eye_top"]["y"] + $landmark["right_eye_bottom"]["y"] - $landmark["right_eye_top"]["y"]);
    //鼻长
    $length[4] = $landmark["nose_contour_lower_middle"]["y"] - $landmark["nose_contour_left1"]["y"];
    //鼻宽
    $length[5] = $landmark["nose_right"]["x"] - $landmark["nose_left"]["x"];
    //嘴长
    $length[6] = $landmark["mouth_upper_lip_right_contour1"]["x"] - $landmark["mouth_upper_lip_left_contour1"] ["x"];
    //嘴宽
    $length[7] = $landmark["mouth_lower_lip_bottom"]["y"] - $landmark["mouth_upper_lip_top"]["y"];
    //下颚长
    $length[8] = $landmark["contour_chin"]["y"] - $landmark["mouth_upper_lip_top"]["y"];
    //下颚宽
    $length[9] = $landmark["contour_right5"]["x"] - $landmark["contour_left5"]["x"];
    //上嘴宽
    $length[10] = $landmark["mouth_upper_lip_bottom"]["y"] - $landmark["mouth_upper_lip_top"]["y"];
    //下嘴宽
    $length[11] = $landmark["mouth_lower_lip_bottom"]["y"] - $landmark["mouth_lower_lip_top"]["y"];
    //眼内间距
    $length[12] = $landmark["right_eye_left_corner"]["x"] - $landmark["left_eye_right_corner"]["x"];
    //眼外间距
    $length[13] = $landmark["right_eye_right_corner"]["x"] - $landmark["left_eye_left_corner"]["x"];
    //单眼外距
    $length[14] = 0.5 * ($landmark["left_eye_left_corner"]["x"] - $landmark["contour_left1"]["x"] + $landmark["contour_right1"]["x"] - $landmark["right_eye_right_corner"]["x"]);
    //眉上距
    $length[15] = $landmark["left_eyebrow_upper_middle"]["y"] - $contour_top[1];
    //眉眼间距
    $length[16] = 0.5 * ($landmark["left_eye_top"]["y"] - $landmark["left_eyebrow_upper_middle"]["y"] + $landmark["right_eye_top"]["y"]-$landmark["right_eyebrow_upper_middle"]["y"]);
    //眼鼻间距
    $length[17] = $landmark["nose_contour_lower_middle"]["y"] - $landmark["left_eye_bottom"]["y"];
    //鼻唇间距
    $length[18] = $landmark["mouth_upper_lip_top"]["y"] - $landmark["nose_contour_lower_middle"]["y"];
    //唇下距
    $length[19] = $landmark["contour_chin"]["y"] - $landmark["mouth_lower_lip_bottom"]["y"];
    //眉鼻间距
    $length[20] = $landmark["nose_contour_lower_middle"]["y"] - $landmark["left_eyebrow_lower_middle"]["y"];
    //鼻下距
    $length[21] = $landmark["contour_chin"]["y"] - $landmark["nose_contour_lower_middle"]["y"];
    //鼻孔宽
    $length[22] = $landmark["nose_contour_right3"]["x"] - $landmark["nose_contour_left3"]["x"];


    $R = array_fill(0,52,0);
    //R1	脸/额头	计算额头部分占脸部大小
    $R[0] = $area[0]/$area[1];
    //R2	脸/下巴	计算下巴部分占脸部大小
    $R[1] = $area[0]/$area[2];
    //R3	脸/眼睛	计算单眼部分占脸部大小（双眼面积求平均）
    $R[2] = $area[0]/$area[3] * 2;
    //R4	脸/鼻子	计算鼻子部分占脸部大小
    $R[3] = $area[0]/$area[4];
    //R5	脸/嘴	计算嘴部分占脸部大小
    $R[4] = $area[0]/$area[5];
    //R6	额头/眼睛	计算眼睛部分与额头部分的比例
    $R[5] = $area[1]/$area[3]* 2;
    //R7	额头/鼻子	计算鼻子部分与额头部分的比例
    $R[6] = $area[1]/$area[4];
    //R8	额头/嘴	计算嘴部分与额头部分的比例
    $R[7] = $area[1]/$area[5];
    //R9	下巴/眼睛	计算下巴部分与下巴部分的比例
    $R[8] = $area[2]/$area[3]* 2;
    //R10	下巴/鼻子	计算鼻子部分与下巴部分的比例
    $R[9] = $area[2]/$area[4];
    //R11	下巴/嘴	计算嘴部分与下巴部分的比例
    $R[10] = $area[2]/$area[5];
    //R12	脸/五官	计算脸部与五官所占脸部区域面积的比例
    $R[11] = $area[0]/$area[6];
    //R13	五官/五官实际	计算五官所占脸部区域面积与五官面积的比例
    $R[12] = $area[6]/($area[3]+$area[4]+$area[5]);

    //R14	脸长/脸宽	计算脸长度与脸宽度的比例（脸长为纵向，脸宽为横向）
    $R[13]= $length[0]/$length[1];
    //R15	脸长/眼宽	计算脸长度与眼睛宽度的比例（眼宽为纵向，眼长为横向）
    $R[14]= $length[0]/$length[3];
    //R16	脸长/鼻长	计算脸长度与鼻子长度的比例（鼻长为纵向，鼻宽为横向）
    $R[15]= $length[0]/$length[4];
    //R17	脸长/嘴宽	计算脸长度与嘴唇宽度的比例（嘴宽为纵向，嘴长为横向）
    $R[16]= $length[0]/$length[7];
    //R18	脸宽/眼长	计算脸宽度与眼睛长度的比例
    $R[17]= $length[1]/$length[2];
    //R19	脸宽/鼻宽	计算脸宽度与鼻子宽度的比例
    $R[18]= $length[1]/$length[5];
    //R20	脸宽/嘴长	计算脸宽度与嘴唇长度的比例
    $R[19]= $length[1]/$length[6];
    //R21	脸宽/下颚宽	计算下颚宽度与脸宽度的比例（下颚长为纵向，下颚宽为横向）
    $R[20]= $length[1]/$length[9];
    //R22	下颚宽/眼长	计算下颚宽度与眼睛长度的比例
    $R[21]= $length[9]/$length[2];
    //R23	下颚宽/鼻宽	计算下颚宽度与鼻子宽度的比例
    $R[22]= $length[9]/$length[5];
    //R24	下颚宽/嘴长	计算下颚宽度与嘴唇长度的比例
    $R[23]= $length[9]/$length[6];
    //R25	下颚长/眼宽	计算下颚长度与眼睛宽度的比例
    $R[34]= $length[8]/$length[3];
    //R26	下颚长/鼻长	计算下颚长度与鼻子宽度的比例
    $R[25]= $length[8]/$length[4];
    //R27	下颚长/嘴宽	计算下颚长度与嘴唇宽度的比例
    $R[26]= $length[8]/$length[7];
    //R28	眼长/眼宽	计算眼睛长度与眼睛宽度的比例（计算特征自身长宽比例）
    $R[27]= $length[2]/$length[3];
    //R29	鼻长/鼻宽	计算鼻子长度与鼻子宽度的比例
    $R[28]= $length[4]/$length[5];
    //R30	嘴长/嘴宽	计算嘴唇长度与嘴唇宽度的比例
    $R[29]= $length[6]/$length[7];
    //R31	鼻宽/鼻孔宽	计算鼻子宽度与鼻孔宽度的比例（计算鼻孔外轮廓的比例）
    $R[30]= $length[5]/$length[22];
    //R32	嘴宽/上嘴宽	计算嘴唇宽度与上嘴唇宽度的比例（计算上嘴唇的大小比例）
    $R[31]= $length[7]/$length[10];
    //R33	嘴宽/下嘴宽	计算嘴唇宽度与下嘴唇宽度的比例（计算下嘴唇的大小比例）
    $R[32]= $length[7]/$length[11];
    //R34	脸宽/眼内间距	计算内眼角之间的距离比例
    $R[33]= $length[1]/$length[12];
    //R35	脸宽/眼外间距	计算外眼角之间的距离比例
    $R[34]= $length[1]/$length[13];
    //R36	脸宽/单眼外距	计算眼睛外侧到脸轮廓的距离比例  
    $R[35]= $length[1]/$length[14];
    //R37	脸长/眉上距	计算眉毛上方到脸轮廓的距离比例
    $R[36]= $length[0]/$length[15];
    //R38	脸长/眉眼间距	计算眉毛到眼睛上点之间的距离比例
    $R[37]= $length[0]/$length[16];
    //R39	脸长/眼鼻间距	计算眼睛下点到鼻子下点之间的距离比例
    $R[38]= $length[0]/$length[17];
    //R40	脸长/鼻唇间距	计算鼻子下点到嘴唇上点之间的距离比例
    $R[39]= $length[0]/$length[18];
    //R41	脸长/鼻下距	计算鼻子下点到脸轮廓下点之间的距离比例
    $R[40]= $length[0]/$length[21];
    //R42	脸长/唇下距	计算嘴唇下点到脸轮廓下点之间的距离比例
    $R[41]= $length[0]/$length[19];
    //R43	眉上距/眉鼻间距	计算眉毛上方到脸轮廓下点之间的距离与眉毛下方到鼻子下点之间的距离比例
    $R[42]= $length[15]/$length[20];
    //R44	眉鼻间距/鼻下距	计算眉毛下方到鼻子下点之间的距离与鼻子下点到脸轮廓下点之间的距离比例
    $R[43]= $length[20]/$length[21];
    //R45	眉上距/鼻下距	计算眉毛上方到脸轮廓下点之间的距离与鼻子下点到脸轮廓下点之间的距离比例
    $R[44]= $length[15]/$length[21];
    //R46	脸部斜率1	计算颧骨点到下颚上点的斜率（左右求平均）
    $R[45]= 0.5 * (abs(($landmark["nose_contour_lower_middle"]["y"] - $landmark["mouth_upper_lip_left_contour1"]["y"])/($landmark["nose_contour_lower_middle"]["x"]-$landmark["mouth_upper_lip_left_contour1"]["x"]))+abs(($landmark["nose_contour_lower_middle"]["y"] - $landmark["mouth_upper_lip_right_contour1"]["y"])/($landmark["nose_contour_lower_middle"]["x"]-$landmark["mouth_upper_lip_right_contour1"]["x"])));
    //R47	脸部斜率2	计算下颚上点到下颚下点的斜率
    $R[46]= abs(($landmark["contour_chin"]["y"] - $landmark["mouth_lower_lip_bottom"]["y"])/($landmark["contour_chin"]["x"] - $landmark["mouth_lower_lip_bottom"]["x"]));
    //R48	眼睛斜率1	计算上眼皮内斜率
    $R[47]= abs(($landmark["left_eye_top"]["y"] - $landmark["left_eye_right_corner"]["y"])/($landmark["left_eye_top"]["x"] - $landmark["left_eye_right_corner"]["x"]));
    //R49	眼睛斜率2	计算上眼皮外斜率
    $R[48]= abs(($landmark["left_eye_top"]["y"] - $landmark["left_eye_left_corner"]["y"])/($landmark["left_eye_top"]["x"] - $landmark["left_eye_left_corner"]["x"]));
    //R50	眼睛斜率3	计算下眼皮斜率
    $R[49]= abs(($landmark["left_eye_bottom"]["y"] - $landmark["left_eye_right_corner"]["y"])/($landmark["left_eye_bottom"]["x"] - $landmark["left_eye_right_corner"]["x"]));
    //R51	眼睛斜率4	计算内眼角到外眼角的斜率
    $R[50]= abs(($landmark["left_eye_left_corner"]["y"] - $landmark["left_eye_right_corner"]["y"])/($landmark["left_eye_left_corner"]["x"] - $landmark["left_eye_right_corner"]["x"]));
    //R52	嘴唇斜率	计算上嘴唇斜率
    $R[51]= abs(($landmark["mouth_upper_lip_left_contour1"]["y"]-$landmark["mouth_left_corner"]["y"])/($landmark["mouth_upper_lip_left_contour1"]["x"]-$landmark["mouth_left_corner"]["x"]));
    
    $sql3 = "insert `face_point`(`fid`,`R0`,`R1`,`R2`,`R3`,`R4`,`R5`,`R6`,`R7`,`R8`,`R9`,`R10`,`R11`,`R12`,`R13`,`R14`,`R15`,`R16`,`R17`,`R18`,`R19`,`R20`,`R21`,`R22`,`R23`,`R24`,`R25`,`R26`,`R27`,`R28`,`R29`,`R30`,`R31`,`R32`,`R33`,`R34`,`R35`,`R36`,`R37`,`R38`,`R39`,`R40`,`R41`,`R42`,`R43`,`R44`,`R45`,`R46`,`R47`,`R48`,`R49`,`R50`,`R51`) values("
    .$fid.",".$R[0].",".$R[1].",".$R[2].",".$R[3].",".$R[4].",".$R[5].",".$R[6].",".$R[7].",".$R[8].",".$R[9].",".$R[10].",".$R[11].",".$R[12].",".$R[13].",".$R[14].",".$R[15].",".$R[16].",".$R[17].",".$R[18].",".$R[19].",".$R[20].",".$R[21].",".$R[22].",".$R[23].",".$R[24].","
    .$R[25].",".$R[26].",".$R[27].",".$R[28].",".$R[29].",".$R[30].",".$R[31].",".$R[32].",".$R[33].",".$R[34].",".$R[35].",".$R[36].",".$R[37].",".$R[38].",".$R[39].",".$R[40].",".$R[41].",".$R[42].",".$R[43].",".$R[44].",".$R[45].",".$R[45].",".$R[47].",".$R[48].",".$R[49].",".$R[50].",".$R[51].");";

    $conn->query($sql3);

    if($conn->affected_rows){
        $test = array($R);//将R作为测试集
        $res = get_face_comm($test);

        $rply = array_fill(0,13,0);
        $rply["fid"] = sprintf("%.0f",$fid);
        $rply["face_size"] = sprintf("%.3f",$res[0]);
        $rply["face_width"] = sprintf("%.3f",$res[1]);
        $rply["face_shape"] = sprintf("%.3f",$res[2]);
        $rply["eye_size"] = sprintf("%.3f",$res[3]);
        $rply["eye_shape"] = sprintf("%.3f",$res[4]);
        $rply["eye_length"] = sprintf("%.3f",$res[5]);
        $rply["nose_length"] = sprintf("%.3f",$res[6]);
        $rply["nose_width"] = sprintf("%.3f",$res[7]);
        $rply["mouth_thick"] = sprintf("%.3f",$res[8]);
        $rply["mouth_width"] = sprintf("%.3f",$res[9]);
        $rply["eye_distance"] = sprintf("%.3f",$res[10]);
        $rply["forehead"] = sprintf("%.3f",$res[11]);
        $rply["facial_feature"] = sprintf("%.3f",$res[12]);
        
        $arr = array();
        $conn->close();
        $count = count($rply);
        for($i=0;$i<$count;$i++){
            unset($rply[$i]);//删除冗余数据
        }
        array_push($arr,$rply);
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);//json编码
    }else{
        echo"error in insert R[]s !";
    }
}else{
        $sql = "SELECT `fid` ,`face_size`,`face_width` ,`face_shape` ,`eye_size`,`eye_shape` ,`eye_length` ,`nose_length` ,`nose_width` ,`mouth_thick` ,`mouth_width` ,`eye_distance` ,`forehead` ,`facial_feature`    from `faces` WHERE  `figName`= ".$figName_tmp.";";
        $result1 = $conn->query($sql);
        $arr = array();

        //输出每行数据
        while($row = $result1->fetch_assoc()) {
            $count=count($row);//不能在循环语句中，由于每次删除row数组长度都减小
            for($i=0;$i<$count;$i++){
                unset($row[$i]);//删除冗余数据
            }
            array_push($arr,$row);
        }
    $conn->close();
    echo json_encode($arr,JSON_UNESCAPED_UNICODE);//json编码
}





//  /*


// */