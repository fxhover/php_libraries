<?php
//导出excel文件 
include'lib/XmlExcel.php';
$xls=new XmlExcel;
$xls->setDefaultWidth(80);
$xls->setDefaultAlign("center");
$xls->setDefaultHeight(18);
for($ind=0;$ind<50;$ind++):
  $xls->addPageRow(array("title1","title2","title3","title4","title5","title6"),array($ind,$ind,$ind,$ind,$ind,$ind),10,$xls->uniqueName("demo"));
endfor;
$xls->export("demo2");
