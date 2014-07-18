<?php
header('Content-Type:text/html;charset=utf-8');
require_once 'splitword.php';
ini_set('memory_limit','30M');
function timer() {
    list($msec, $sec) = explode(' ', microtime());	
    return ((float)$msec + (float)$sec);
}  
$str = 'PHP，是英文超文本预处理语言Hypertext Preprocessor的缩写。PHP 是一种 HTML 内嵌式的语言，是一种在服务器端执行的嵌入HTML文档的脚本语言，语言的风格有类似于C语言，被广泛地运用。';
echo '<b>分词文本:</b> '.$str.'<br/><br/>';
$split = new SplitWord();
$time1 = timer();
echo '<b>分词结果: </b>';
echo $split->SplitRMM($str);
//$res = $split->pregRmmSplit($str);
$time2 = timer();
echo '<br/>';
echo '<br/>';
echo '<br/>';
echo '<b>分词时间: </b>'. ($time2 - $time1);
//echo strlen('a');

?>