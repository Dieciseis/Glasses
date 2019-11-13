<?php
//连接数据库的类，同call_API,建立数据库连接时不用打这一堆了，也方便改动数据库地址。
//执行database文件夹下的4个.sql文件，即可在新的数据库中建立对应的数据表。

class DBC
{
    var $servername = "bdm255611668.my3w.com";//数据库地址，本地连接的话是localhost:XXXX（端口号）
    var $username = "bdm255611668";//登录数据库的用户名
    var $password = "deepblueLib416";//用户名对应的密码
    var $dbname = "bdm255611668_db";//数据库名
}