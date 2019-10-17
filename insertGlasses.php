<?php
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
        $glasses->g_set = $_POST['g_set'];
        //参数
        $glasses->g_frame = $_POST['g_frame'];
        $glasses->g_arm = $_POST['g_arm'];
        $glasses->g_bridge = $_POST['g_bridge'];
        $glasses->g_footwear = $_POST['g_footwear'];
        $glasses->g_style = $_POST['g_style'];

        $fname_temp = $_FILES['file']['name'];
        $glasses->figName ="\"$fname_temp\"";
        move_uploaded_file($_FILES["file"]["tmp_name"],
            "fig/glasses/" . $_FILES["file"]["name"]);
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
$sql = "insert `glasses`(`style`,`figName`,`belong`,`left_eye_x`,`right_eye_x`,`left_ear_x`,`right_ear_x`,`left_eye_y`,`right_eye_y`,`left_ear_y`,`right_ear_y`,`frame`,`arm`,`bridge`,`footwear`) values("
    .$g->g_style.",".$g->figName.",".$g->g_set.",".$g->left_eye_x.",". $g->right_eye_x.",".$g->left_ear_x.",".$g->right_ear_x.",".$g->left_eye_y.",".$g->right_eye_y.",".$g->left_ear_y.",".$g->right_ear_y.",".$g->g_frame.",".$g->g_arm.",".$g->g_bridge.",".$g->g_footwear.");";

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
