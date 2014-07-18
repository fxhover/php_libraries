<?php
/**
 *简单的将数据导出为excel、xml、csv文件
 * author: fxhover fxhover@163.com
 */
class Export{
	private $type=array('excel'=>"\t",'csv'=>","); //不同类型文件行采用的分隔符
	/**
	 *导出为Excel文件
	 *@param $fileName string 导出的文件名，不用写后缀名
	 *@param $th array 导出excel文件的列名
	 *@param $data array 导出的数据
	 */
	public function exportExcel($fileName,$th=array(),$data=array()){
		$this->checkFileData($fileName,$data);
		$dataStr=$this->getDataStr($th,$data,'excel');
		header('Content-Type:application/vnd.ms-execl;charset=utf-8');
		header('Content-Disposition:attachment;filename='.iconv('utf-8','gb2312',$fileName).'.xls');
		header('Expires:0');
		echo $dataStr;
	}
	/**
	 *检查文件名和数据项
	 */
	private function checkFileData($fileName,$data){
		if(empty($fileName)){
			exit('文件名不能为空！');
		}
		if(!is_array($data) || count($data)<1){
			exit('数据为空或不是一个数组！');
		}
	}
	/**
	 *返回导出数据字符串
	 *@param $th array 列名
	 *@param $data array 数据
	 *@param $type string 导出文件的类型
	 */
	private function getDataStr($th=array(),$data=array(),$type){
		$dataStr='';
		if(!empty($th)){
			for($i=0,$num=count($th);$i<$num;$i++){
				if($i==$num-1){
					$dataStr.=iconv('utf-8','gb2312',$th[$i])."\n";
				}else{
					$dataStr.=iconv('utf-8','gb2312',$th[$i]).$this->type[$type];
				}
			}
		}
		$keys=array_keys($data[0]);
		$flset=array_pop($keys);
		foreach($data as $val){
			if(is_array($val)){
				foreach($val as $k=>$v){
					if($k==$flset){
						$dataStr.=iconv('utf-8','gb2312',$v)."\n";
					}else{
						$dataStr.=iconv('utf-8','gb2312',$v).$this->type[$type];
					}
				}
			}else{
				exit('数据格式必须是二维数组！');
			}
		}
		return $dataStr;
	}
	/**
	 *导出为CSV文件
	 *@param $fileName string 导出的文件名，不用写后缀名
	 *@param $th array 导出CSV文件的列名
	 *@param $data array 导出的数据
	 */
	public function exportCsv($fileName,$th=array(),$data=array()){
		$this->checkFileData($fileName,$data);
		$dataStr=$this->getDataStr($th,$data,'csv');
		header('Content-Disposition:attachment;filename='.iconv('utf-8','gb2312',$fileName).'.csv');
		header('Expires:0');
		echo $dataStr;
	}
	/**
	 *导出为XML文件
	 *@param $fileName string 导出的文件名，不用写后缀名
	 *@param $data array 导出的数据
	 *@param $root string 根标记名称
	 *@param $fields string 二级标记名称
	 */
	public function exportXml($fileName,$data,$root='data',$fields='row'){
		$this->checkFileData($fileName,$data);
		$dom=new DOMDocument('1.0','utf-8');
		$dataNode=$dom->createElement($root);
		foreach($data as $val){
			if(is_array($val)){
				$field=$dom->createElement($fields);
				foreach($val as $k=>$v){
					$kNode=$dom->createElement($k);
					$value=$dom->createTextNode($v);
					$kNode->appendChild($value);
					$field->appendChild($kNode);
				}
				$dataNode->appendChild($field);
				$dom->appendChild($dataNode);
			}else{
				exit('数据格式必须是二维数组！');
			}
		}
		header('Content-Description:File Transfer');
		header('Content-Type:application/octet-stream;charset=utf-8');
		header('Content-Disposition:attachment;filename='.iconv('utf-8','gb2312',$fileName).'.xml');
		header('Expires:0');
		echo $dom->saveXML();
	}
}
