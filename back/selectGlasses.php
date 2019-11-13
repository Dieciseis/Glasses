<?php
//请求参数为fid和getFig:查询并返回该fid对应的图片figName,在前端显示照片预览
//请求参数为fid:计算并返回眼镜推荐序列
//请求参数为fid和gid:查询并返回眼镜佩戴效果贴图坐标
header("content-Type: text/html; charset=utf-8");//字符编码设置
require_once'DBC.php';
require_once'PNN.php';

if(array_key_exists('fid', $_GET)){
    $fid = $_GET['fid'];
    if(array_key_exists('gid', $_GET)){
        $gid = $_GET['gid'];
    }else{
        $gid = 0;
        if(array_key_exists('fig', $_GET)){//fig换figName的请求
            $getFig = 1;
        }else{
            $getFig = 0;

        }
    }
}else{
    $fid = 0;
}

$db = new DBC;
// 创建连接
$conn = new mysqli($db->servername, $db->username, $db->password, $db->dbname);
// 检测连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if($fid != 0 && $gid == 0 && $getFig ==0){//非请求贴图数据和fid换figName
    $sql1 = "SELECT * FROM `face_with_glasses` WHERE fid=".$fid;
    $res1 = $conn->query($sql1);//查询有无人脸佩戴眼镜的风格评价记录
    if(mysqli_num_rows($res1)>0){
        //有匹配记录，就当无事发生过
    }else{
        //无匹配记录，生成匹配信息，入库
        $sql2 = "SELECT gid FROM glasses";
        $res2 = $conn->query($sql2);
        while($row = $res2->fetch_assoc()){
            $gid = $row["gid"];
            $faces = $conn->query("SELECT `face_size`,`face_width` ,`face_shape` ,`eye_size` ,`eye_shape` ,`eye_length` ,`nose_length` ,`nose_width` ,`mouth_thick` ,`mouth_width` ,`eye_distance` ,`forehead` ,`facial_feature`   FROM `faces` WHERE fid=".$fid.";");
            $glasses = $conn->query("SELECT `frame_shape` ,`frame_thickness` ,`frame_type` ,`frame_width`,`materials` FROM `glasses` WHERE gid=".$gid.";");

            $rowf = $faces->fetch_assoc();
            $rowg = $glasses->fetch_assoc();

            $test = array(array($rowf["face_size"],$rowf["face_width"],$rowf["face_shape"],$rowf["eye_size"],$rowf["eye_shape"],$rowf["eye_length"],$rowf["nose_length"],$rowf["nose_width"],$rowf["mouth_thick"],$rowf["mouth_width"],$rowf["eye_distance"],$rowf["forehead"],$rowf["facial_feature"],$rowg["frame_shape"],$rowg["frame_thickness"],$rowg["frame_type"],$rowg["frame_width"],$rowg["materials"]));
            $res = get_comm_Result($test);
            $belong = 1;//属于测试集，不会被作为训练样本载入
            $sql3 = "insert `face_with_glasses`(`fid`,`gid`,`style1`,`belong`,`prob1`,`style2`,`prob2`,`style3`,`prob3`,`style4`,`prob4`) values(".
                $fid.",".$gid.",".$res["style1"].",".$belong .",".$res["prob1"].",".$res["style2"].",".$res["prob2"].",".$res["style3"].",".$res["prob3"].",".$res["style4"].",".$res["prob4"].");";
            $conn->query($sql3);
            if(!$conn->affected_rows){
                echo "error in insert style! <br>";
            }
        }
    }

    //保证库里有匹配信息后，返回按prob1降序排列的眼镜组
    $sql4 = "SELECT `gid`,`style1`,`prob1` ,`style2` ,`prob2` ,`style3` ,`prob3` ,`style4` ,`prob4` FROM `face_with_glasses` where `fid`=".$fid." ORDER BY `prob1` DESC;";
    $res4 = $conn->query($sql4);

    $arr4 = array();
    // 输出每行数据
    while($row = $res4->fetch_assoc()) {
        $count=count($row);//不能在循环语句中，由于每次删除row数组长度都减小
        for($i=0;$i<$count;$i++){
            unset($row[$i]);//删除冗余数据
        }
        array_push($arr4,$row);
    }

    echo json_encode($arr4,JSON_UNESCAPED_UNICODE);//json编码
    $conn->close();
}else{
    //查贴图数据
    if($fid != 0 && $gid != 0 && $getFig ==0 ){
        //查面部数据
        $sql5 = "SELECT `left_eye_x`,`left_eye_y`,`right_eye_x`,`right_eye_y`,`figName` FROM faces WHERE fid=" . $fid . ";";
        //查眼镜数据
        $sql6 = "SELECT `left_ear_x`,`left_ear_y`,`right_ear_x`,`right_ear_y`,`left_eye_x`,`left_eye_y`,`right_eye_x`,`right_eye_y`,`figName` FROM glasses WHERE gid=" . $gid . ";";
        $res5 = $conn->query($sql5);
        $arr5 = array();
        // 输出每行数据
        while($row = $res5->fetch_assoc()) {
            $count=count($row);//不能在循环语句中，由于每次删除row数组长度都减小
            for($i=0;$i<$count;$i++){
                unset($row[$i]);//删除冗余数据
            }
            array_push($arr5,$row);
        }
        $res6 = $conn->query($sql6);
        // 输出每行数据
        while($row = $res6->fetch_assoc()) {
            $count=count($row);//不能在循环语句中，由于每次删除row数组长度都减小
            for($i=0;$i<$count;$i++){
                unset($row[$i]);//删除冗余数据
            }
            array_push($arr5,$row);
        }
        echo json_encode($arr5,JSON_UNESCAPED_UNICODE);//json编码
        $conn->close();
    }else{//fid换figName
        if($fid != 0 && $getFig ==1){
            $sql7 = "SELECT figName FROM faces where fid=".$fid.";";
            $res7 = $conn->query($sql7);
            $row = $res7->fetch_assoc();
            echo $row["figName"];
        }
    }
}

