<?php
//人脸信息类，包含13项感性评价和眼镜贴图需要的左右眼坐标

class faces
{
    var  $face_size;
    var  $face_width;
    var  $face_shape;
    var  $eye_size;
    var  $eye_shape;
    var  $eye_length;
    var  $nose_length;
    var  $nose_width;
    var  $mouth_thick;
    var  $mouth_width;
    var  $eye_distance;
    var  $forehead;
    var  $facial_feature;
    //智熄忘了坐标由x,y两个数据组成
    var  $left_eye_x;
    var  $right_eye_x;
    var  $left_eye_y;
    var  $right_eye_y;

    var  $f_set;

    var $figName;
}