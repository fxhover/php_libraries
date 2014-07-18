<?php
//使用方法
require 'Export.php';
$th=array('学号','姓名','分数');
$data=array(
	array('no'=>1,'name'=>'张超','score'=>80),
	array('no'=>2,'name'=>'张和','score'=>60),
	array('no'=>3,'name'=>'袁博','score'=>70),
	array('no'=>4,'name'=>'袁绍','score'=>75),
	array('no'=>5,'name'=>'曹操','score'=>79),
	array('no'=>6,'name'=>'刘备','score'=>77),
	array('no'=>7,'name'=>'张飞','score'=>81),
	array('no'=>8,'name'=>'诸葛亮','score'=>90),
	array('no'=>9,'name'=>'关羽','score'=>88),
	array('no'=>10,'name'=>'孙权','score'=>87)
);
$export=new Export();
$export->exportXml('学生成绩',$data,'users','user');