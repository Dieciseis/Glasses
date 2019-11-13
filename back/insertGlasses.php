<?php
//接收前端录入的眼镜设计要素、贴图坐标等信息和上传的图片，入库
header("content-Type: text/html; charset=utf-8");//字符编码设置
require_once'glasses.php';
require_once 'DBC.php';


function insertGlasses(){
    $glasses = new glasses();

    if ($_FILES["file"]["error"] > 0)
    {
        echo "错误: " . $_FILES["file"]["error"] . "<br />";
    }
    else {
        //眼镜图像匹配位置
        $glasses->left_ear_x = $_POST['left_ear_x'];
        $glasses->right_ear_x = $_POST['right_ear_x'];
        $glasses->left_eye_x = $_POST['left_eye_x'];
        $glasses->right_eye_x = $_POST['right_eye_x'];
        $glasses->left_ear_y = $_POST['left_ear_y'];
        $glasses->right_ear_y = $_POST['right_ear_y'];
        $glasses->left_eye_y = $_POST['left_eye_y'];
        $glasses->right_eye_y = $_POST['right_eye_y'];
        //设计要素参数
        $glasses->frame_shape = $_POST['frame_shape'];
        $glasses->frame_thickness = $_POST['frame_thickness'];
        $glasses->frame_type = $_POST['frame_type'];
        $glasses->frame_width = $_POST['frame_width'];
        $glasses->materials = $_POST['materials'];

        $fname_temp = $_FILES['file']['name'];
        $glasses->figName ="\"$fname_temp\"";
        move_uploaded_file($_FILES["file"]["tmp_name"],
            "fig/glasses/" . $_FILES["file"]["name"]);//图片存入服务器对应目录
    }
    return $glasses;
}

$db = new DBC();

// 创建连接
$conn =new mysqli($db->servername, $db->username, $db->password, $db->dbname);
// 检测连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//可以用预处理和绑定，但不是很必要
$g = insertGlasses();
$sql = "insert `glasses`(`figName`,`left_eye_x`,`right_eye_x`,`left_ear_x`,`right_ear_x`,`left_eye_y`,`right_eye_y`,`left_ear_y`,`right_ear_y`,`frame_shape`,`frame_thickness`,`frame_type`,`frame_width`,`materials`) values("
    .$g->figName.",".$g->left_eye_x.",". $g->right_eye_x.",".$g->left_ear_x.",".$g->right_ear_x.",".$g->left_eye_y.",".$g->right_eye_y.",".$g->left_ear_y.",".$g->right_ear_y.",".$g->frame_shape.",".$g->frame_thickness.",".$g->frame_type.",".$g->frame_width.",".$g->materials.");";

echo $sql;
$conn->query($sql);

//
if($conn->affected_rows){
    $res = 1;//操作成功
}else{
    $res = 0;//操作失败
}

$url = "http://www.deepbluecape.ink/glasses/uploadGlasses.html?res=".$res ;
$conn->close();

echo "<script type=\"text/javascript\">window.location.href='$url'</script>";
