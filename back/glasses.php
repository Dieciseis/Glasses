<?php
//眼镜设计要素类

class glasses
{
    var $frame_shape;
    var $frame_thickness;
    var $frame_width;
    var $frame_type;
    var $materials;

    //框住眼镜主体的矩形左上角坐标，改贴图算法前准备用左右耳位置，改完用矩形角，但定的变量名懒得改了
    var  $left_ear_x;
    var  $right_ear_x;
    //框住眼镜主体的矩形右下角坐标，变量命名理由同上，用到的地方太多，懒得改了
    var  $left_ear_y;
    var  $right_ear_y;

    var  $left_eye_x;
    var  $right_eye_x;
    var  $left_eye_y;
    var  $right_eye_y;

    var $figName;
}