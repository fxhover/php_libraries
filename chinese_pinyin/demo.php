<?php
//使用方法
require 'ChineseSpell.php';
$cs = new ChineseSpell() ;
echo $cs->getFirstLetter(iconv('utf-8', 'gbk', '爱我中华')).'<br>' ;
echo $cs->getFullSpell(iconv('utf-8', 'gbk', '爱我中华')).'<br>' ;