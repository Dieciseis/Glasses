<?php
header("content-Type: text/html; charset=utf-8");//字符编码设置
require_once'faces.php';
require_once 'DBC.php';

echo "test1";
function insertFace(){
    $face = new faces();

    if ($_FILES["file"]["error"] > 0)
    {
        echo "错误: " . $_FILES["file"]["error"] . "<br />";
    }
    else {

        //眼镜图像匹配位置
        $face->left_ear = $_POST['left_ear'];
        $face->right_ear = $_POST['right_ear'];
        $face->left_eye = $_POST['left_eye'];
        $face->right_eye = $_POST['right_eye'];
        $face->f_style = $_POST['f_style'];
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

        $face->figName = $_FILES['file']['name'];

        move_uploaded_file($_FILES["file"]["tmp_name"],
            "http://www.deepbluecape.ink/glasses/fig/" . $_FILES["file"]["name"]);
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

echo "test2";
//预处理和绑定
$f = new faces();
$pre = $conn->prepare("insert `faces`(`face_size`,`face_width`,`face_shape`,`eye_size`,`eye_shape`,`eye_length`,`nose_length`,`nose_width`,`mouth_thick`,`mouth_width`,`eye_distance`,`forehead`,`facial_feature`,`left_ear`,`right_ear`,`left_eye`,`right_eye`,`belong`,`figName`)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);");
$pre->bind_param("dddddddddddddddddis",$f->face_size,$f->face_width, $f->face_shape,$f->eye_size,$f->eye_shape,$f->eye_length,$f->nose_length,$f->nose_width,$f->mouth_thick,$f->mouth_width,$f->eye_distance,$f->forehead,$f->facial_feature,$f->left_ear,$f->right_ear,$f->left_eye,$f->right_eye,$f->f_set,$figName);

$f_temp = insertFace();

$result = $pre->execute();


echo $result;//操作结果
$pre->close();
$conn->close();
