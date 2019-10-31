<?php
header("content-Type: text/html; charset=utf-8");//字符编码设置
require_once'faces.php';
require_once 'DBC.php';


function insertFace(){
    $face = new faces();

    if ($_FILES["file"]["error"] > 0)
    {
        echo "错误: " . $_FILES["file"]["error"] . "<br />";
    }
    else {
        //眼镜图像匹配位置
        $face->left_ear_x = 0;//$_POST['left_ear_x'];修改了眼镜佩戴效果贴图算法，弃用该点
        $face->right_ear_x = 0;//$_POST['right_ear_x'];
        $face->left_eye_x = $_POST['left_eye_x'];
        $face->right_eye_x = $_POST['right_eye_x'];
        $face->left_ear_y = 0;//$_POST['left_ear_y'];
        $face->right_ear_y = 0;//$_POST['right_ear_y'];
        $face->left_eye_y = $_POST['left_eye_y'];
        $face->right_eye_y = $_POST['right_eye_y'];
        $face->f_set = $_POST['f_set'];
        //脸部参数
        $face->face_size = $_POST['face_size'];
        $face->face_width = $_POST['face_width'];
        $face->face_shape = $_POST['face_shape'];
        //眼睛参数
        $face->eye_size = $_POST['eye_size'];
        $face->eye_shape = $_POST['eye_shape'];
        $face->eye_length = $_POST['eye_length'];
        //鼻子参数
        $face->nose_length = $_POST['nose_length'];
        $face->nose_width = $_POST['nose_width'];
        //嘴唇参数
        $face->mouth_thick = $_POST['mouth_thick'];
        $face->mouth_width = $_POST['mouth_width'];
        //零散参数
        $face->forehead = $_POST['forehead'];
        $face->facial_feature = $_POST['facial_feature'];
        $face->eye_distance = $_POST['eye_distance'];

        $fname_temp = $_FILES['file']['name'];
        $face->figName ="\"$fname_temp\"";
        move_uploaded_file($_FILES["file"]["tmp_name"],
            "fig/face/" . $_FILES["file"]["name"]);
    }
    return $face;
}

$db = new DBC();

// 创建连接
$conn =new mysqli($db->servername, $db->username, $db->password, $db->dbname);
// 检测连接
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//可以用预处理和绑定，但不是很必要
$f = insertFace();
$sql="insert `faces`(`figName`,`face_size`,`face_width`,`face_shape`,`eye_size`,`eye_shape`,`eye_length`,`nose_length`,`nose_width`,`mouth_thick`,`mouth_width`,`eye_distance`,`forehead`,`facial_feature`,`left_eye_x`,`right_eye_x`,`belong`,`left_eye_y`,`right_eye_y`) values("
    .$f->figName.",".$f->face_size.",".$f->face_width.",".$f->face_shape.",". $f->eye_size.",".$f->eye_shape.",".$f->eye_length.",".$f->nose_length.",".$f->nose_width.",".$f->mouth_thick.",".$f->mouth_width.",".$f->eye_distance.",".$f->forehead.",".$f->facial_feature.",".$f->left_eye_x.",".$f->right_eye_x.",".$f->f_set.",".$f->left_eye_y.",".$f->right_eye_y.");";
//echo $sql;
$conn->query($sql);

//
if($conn->affected_rows){
    $res = 1;//操作成功
}else{
    $res = 0;//操作失败
}

$url = "http://www.deepbluecape.ink/glasses/uploadPhoto.html?res=".$res ;
$conn->close();

echo "<script type=\"text/javascript\">window.location.href='$url'</script>";
