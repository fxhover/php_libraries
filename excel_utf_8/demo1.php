<?php
//导出excel文件 
include'lib/XmlExcel.php';
$xls=new XmlExcel;
$xls->setDefaultWidth(80);
$xls->setDefaultAlign("center");
$xls->setDefaultHeight(18);
$xls->addHead(array("title1","title2","title3","title4","title5","title6"),"demo1");
for($ind=0;$ind<10;$ind++):
  $xls->addRow(array($ind,$ind,$ind,$ind,$ind,$ind),"demo1");
endfor;
$xls->export("demo1");
