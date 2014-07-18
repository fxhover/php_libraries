<?php
/**
 * 基于RMM中文分词（逆向匹配法）
 * @author tangpan<tang0pan@qq.com>
 * @date 2013-10-12
 * @version 1.0.0
 **/
class SplitWord {
    //public $Tag_dic = array();  //存储词典分词
    public $Rec_dic = array();  //存储重组的分词
    public $Split_char = ' ';    //分隔符
    public $Source_str = '';    //存储源字符串
    public $Result_str = '';    //存储分词结果字符串
    public $limit_lenght = 2;
    public $Dic_maxLen = 28;     //词典中词的最大长度
    public $Dic_minLen = 2;     //词典中词的最小长度
    
    public function __construct() {
        $dic_path = dirname(__FILE__).'/words.csv'; //预先载入词典以提高分词速度
        $fp = fopen( $dic_path, 'r' );  //读取词库中的词
        while( $line = fgets( $fp, 256 ) ) {
            $ws = trim($line);  //对词库中的词进行分割
            @$ws = trim(iconv('UTF-8','GBK',$ws)); //编码转换
            //$this->Tag_dic[$ws[0]] = true;    //以词为索引，序号为值
            $this->Rec_dic[strlen($ws)][$ws] = true;    //以词长度和词分别为二维数组的索引，以n为值，来重组词库
        }
        fclose($fp);    //关闭词库
    }
    
    /**
     * 设置源字符串
     * @param 要分词的字符串
     */
    public function SetSourceStr( $str ) {
        $str = iconv( 'utf-8', 'GBK', $str );   //  将utf-8编码字符转换为GBK编码
        $this->Source_str = $this->DealStr( $str );  //初步处理字符串
    }
    
    /**
     * 检查字符串
     * @param $str  源字符串
     * @return bool
     */
    public function checkStr( $str ) {
        if ( trim($str) == '' )     return; //若字符串为空，直接返回
        if ( ord( $str[0] ) > 0x80 )  return true;    //是中文字符则返回true
        else    return false;   //不是中文字符则返回false
    }
    
    
    /**
     * RMM分词算法
     * @param $str  待处理字符串
     */
    public function SplitRMM( $str = '' ) {
        if ( trim( $str ) == '' )     return;     //若字符串为空，则直接返回
        else    $this->SetSourceStr( $str );    //字符串不为空时，设置源字符串
        if ( $this->Source_str == ' ' )     return; //当源字符串为空时，直接返回
        $split_words = explode( ' ', $this->Source_str ); //以空格来切分字符串
        $lenght = count( $split_words );    //计算数组长度
        for ( $i = $lenght - 1; $i >= 0; $i-- ) {
            if ( trim( $split_words[$i] ) == ' ' )  continue;   //如果字符为空时，跳过后面的代码，直接进入下一次循环
            if ( $this->checkStr( $split_words[$i] ) ) {  //检查字符串,如果是中文字符
                if ( strlen( $split_words[$i] ) >= $this->limit_lenght ) { //字符串长度大于限制大小时
                    //对字符串进行逆向匹配
                    $this->Result_str = $this->pregRmmSplit( $split_words[$i] ).$this->Split_char.$this->Result_str;
                }
            } else {
                $this->Result_str = $split_words[$i].$this->Split_char.$this->Result_str;
            }
        }
        $this->clear( $split_words );   //释放内存
        return iconv('GBK', 'utf-8', $this->Result_str);
    }
    
    /**
     * 对中文字符串进行逆向匹配方式分解
     * @param $str  字符串
     * @return $retStr  分词完成的字符串
     */
    public function pregRmmSplit( $str ) {
        if ( $str == ' ' )  return;
        $splen = strlen( $str );
        $Split_Result = array();
        for ( $j = $splen - 1; $j >= 0; $j--) {     //逆向匹配字符
            if ( $splen <= $this->Dic_minLen ) {     //当字符长度大于词典中最小字符长度时
                if ( $j == 1 ) {    //当长度为 1 时
                    $Split_Result[] = substr( $str, 0, 2 );
                } else {
                    $w = trim( substr( $str, 0, $this->Dic_minLen + 1 ) );  //截取前四个字符
                    if ( $this->IsWord( $w ) ) {    //判断词典中是否存在该字符
                        $Split_Result[] = $w;   //存在，则写入数组存储
                    } else {
                        $Split_Result[] = substr( $str, 2, 2 ); //逆向存储
                        $Split_Result[] = substr( $str, 0, 2 );
                    }
                }
                $j = -1;    //关闭循环；
                break;
            } 
            if ( $j >= $this->Dic_maxLen )  $max_len = $this->Dic_maxLen;   //当字符长度大于词典最大词的长度时，赋值最大限制长度
            else    $max_len = $j;
            for ( $k = $max_len; $k >= 0; $k = $k - 2 ) { //一次跳动为一个中文字符
                $w = trim( substr( $str, $j - $k, $k + 1 ) );
                if ( $this->IsWord( $w ) ) {
                    $Split_Result[] = $w;   //保存该词
                    $j = $j - $k - 1;   //位置移动到已匹配的字符的位置
                    break;  //分词成功即跳出当前循环，进入下一循环
                }
            }
        }
        $retStr = $this->resetWord( $Split_Result );    //重组字符串,并返回处理好的字符串
        $this->clear( $Split_Result );  //释放内存
        return $retStr;
    }
    
    /**
     * 重新识别并组合分词
     * @param   $Split_Result   重组目标字符串
     * @return $ret_Str     重组字符串
     */
    public function resetWord( $Split_Result ) {
        if ( trim( $Split_Result[0] ) == '' ) return;
        $Len = count( $Split_Result ) - 1;
        $ret_Str = '';
        $spc = $this->Split_char;
        for ( $i =  $Len; $i >= 0; $i-- ) {
            if ( trim( $Split_Result[$i] ) != '' ) {
                $Split_Result[$i] = iconv( 'GBK', 'utf-8', $Split_Result[$i] );
                $ret_Str .= $spc.$Split_Result[$i].' ';
            }
        }
        //$ret_Str = preg_replace('/^'.$spc.'/','、',$ret_Str);
        $ret_Str = iconv('utf-8','GBK',$ret_Str);
        return $ret_Str;
    }
    
    /**
     * 检查词典中是否存在某个词
     * @param $okWord 检查的词
     * @return bool;
     */
    public function IsWord( $okWord ) {
        $len = strlen( $okWord );
        if ( $len > $this->Dic_maxLen + 1 )     return false;
        else { //根据二维数组索引匹配，是否存在该词
            return isset($this->Rec_dic[$len][$okWord]);
        }
            
    }
    
    /**
     * 初步处理字符串（以空格来替换特殊字符）
     * @param $str   要处理的源字符串
     * @return $okStr   返回预处理好的字符串
     */
    public function DealStr( $str ) {
        $spc = $this->Split_char;   //拷贝分隔符
        $slen = strlen( $str ); //计算字符的长度
        if ( $slen == 0 )   return;     //如果字符长度为0，直接返回
        $okstr = '';    //初始化变量
        $prechar = 0;   //字符判断变量(0-空白，1-英文，2-中文，3-符号)
        for ( $i = 0; $i < $slen; $i++ ) {
            $str_ord = ord( $str[$i] );
            if ( $str_ord < 0x81 ) {   //如果是英文字符
                if ( $str_ord < 33 ) {     //英文的空白符号
                    if ( $str[$i] != '\r' && $str[$i] != '\n' )
                        $okstr .= $spc;
                    $prechar = 0;
                    continue;
                } else if ( preg_match('[@\.%#:\^\&_-]',$str[$i]) ) { //如果关键字的字符是数字或英文或特殊字符
                    if ( $prechar == 0 ) {  //当字符为空白符时
                        $okstr .= $str[$i];
                        $prechar = 3;
                    } else {
                        $okstr .= $spc.$str[$i];    //字符不为空白符时,在字符前串上空白符
                        $prechar = 3;
                    }
                } else if ( preg_match('[0-9a-zA-Z]', $str[$i]) ) { //分割英文数字组合
                    if ( (preg_match('[0-9]',$str[$i-1]) && preg_match('[a-zA-Z]',$str[$i]))
                        || (preg_match('[a-zA-Z]',$str[$i-1]) && preg_match('[0-9]',$str[$i])) ) {
                        $okstr .= $spc.$str[$i];
                    } else {
                        $okstr .= $str[$i];
                    } 
                }
            } else { //如果关键字的第二个字符是汉字
                if ( $prechar != 0 && $prechar != 2 )  //如果上一个字符为非中文和非空格，则加一个空格
                    $okstr .= $spc;
                if ( isset( $str[$i+1] ) ) {    //如果是中文字符
                    $c = $str[$i].$str[$i+1];   //将两个字符串在一起，构成一个中文字
                    $n = hexdec( bin2hex( $c ) );   //将ascii码转换成16进制，再转化为10进制
                    if ( $n > 0xA13F && $n < 0xAA40 ) {   //如果为中文标点符号
                        if ( $prechar != 0 ) $okstr .= $spc; //将中文标点替换为空
                        //else $okstr .= $spc;  //若前一个字符为空，则直接串上
                        $prechar = 3;
                    } else {    //若不是中文标点
                        $okstr .= $c;
                        $prechar = 2;
                    }
                    $i++;   // $i 再加 1 ，即使一次移动为一个中文字符
                }
            }
        }
        return $okstr;
    }
    
    /**
     * 释放内存
     * @param $data    暂存数据
     */
    public function clear( $data ) {
        unset( $data ); //删除暂存数据
    }
}
?>