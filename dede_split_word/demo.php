<?
require("lib_splitword_full.php");

//$str = "浅析我国旅行社运作模式前景";
$str = "PHP，是英文超文本预处理语言Hypertext Preprocessor的缩写。PHP 是一种 HTML 内嵌式的语言，是一种在服务器端执行的嵌入HTML文档的脚本语言，语言的风格有类似于C语言，被广泛地运用。";

$t1 = ExecTime();

$sp = new SplitWord();

$t2 = ExecTime();

//$t0 = $t2-$t1;

//echo "载入时间： $t0 <br><br>";


//echo $sp->FindNewWord($sp->SplitRMM($str))."<hr>";
echo $sp->SplitRMM($str)."<hr>";

$sp->Clear();

echo $str."<br>";

$t3 = ExecTime();
$t0 = $t3-$t2;
echo "<br>处理时间： $t0 <br><br>";


function ExecTime(){ 
	$time = explode(" ", microtime());
	$usec = (double)$time[0]; 
	$sec = (double)$time[1]; 
	return $sec + $usec; 
}
?>