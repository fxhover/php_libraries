<?php
//使用方法
require 'excel.php';
require_once('excel.php');
$doc = array (
    0 => array ('中国', '中国人', '中国人民', '123456');
);
$xls = new Excel;
$xls->addArray ($doc);
$xls->generateXML ("mytest");
