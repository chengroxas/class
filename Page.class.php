<?php
/**
 * 分页类
 **/
class Page{
	private $_total=0;//总记录数
	private $_pagesize=0;//显示的记录数
	private $_pageno=0;//当前页数
	public function __construct($total,$pagesize){
		$this->_total=$total;
		$this->_pagesize=$pagesize;
	}
	/**
	 * 显示分页条
	 * @param $_left  字符串 当前页前显示的页数
	 * @param $_right 字符串	当前页后显示的页数
	 * @return 分页条  html
	 **/
	public function show($_left=1,$_right=1){
		//总页数
		$maxPage=ceil($this->_total/$this->_pagesize);
		$tmp_url=$_SERVER['REQUEST_URI'];
		$tmp_arr=explode('?',$tmp_url);
		//切割保留参数
		if(!isset($tmp_arr[1])){
			$link='';
		}else{
			$arr=explode('&',$tmp_arr[1]);
			$data='';
			foreach($arr as $val){
				$arr=explode('=',$val);
				if($arr[0]!='pageno'){
					$data.=$val.'&';
				}
			}
			$link=$data;
		}
		$link.="pageno=";
		
		if(empty($_GET['pageno'])){
			$this->_pageno=1;
		}else{
			$this->_pageno=$_GET['pageno'];
		}
		
		if($this->_pageno<1){
			$this->_pageno=1;
		}
		
		if($this->_pageno>$maxPage){
			$this->_pageno=$maxPage;
		}
		
		$html='<div class="page-box">';	
		$left=$_left;
		$right=$_right;
		$start=$this->_pageno-$left;
		
		if($start<1){
			$start=1;
		}

		if($this->_pageno>1){
			$html.='<a href="?'.$link.'1" class="able">首页</a>';
			$html.='<a href="?'.$link.($this->_pageno-1).'" class="able">上一页</a>';
		}else{
			$html.='<a  class="unble">首页</a>';
			$html.='<a  class="unble">上一页</a>';
		}
		
		for($i=$start;$i<$this->_pageno;$i++){
			$html.='<a href="?'.$link.$i.'" class="able">'.$i.'</a>';
		}
		$html.='<a class="current">'.$this->_pageno.'</a>';
		$end=$this->_pageno+$right;
		
		if($end>$maxPage){
			$end=$maxPage;
		}
		
		for($i=$this->_pageno+1;$i<=$end;$i++){
			$html.='<a  href="?'.$link.$i.'" class="able">'.$i.'</a>';
		}
		
		if($this->_pageno<$maxPage){
			$html.='<a href="?'.$link.$maxPage.'" class="able">尾页</a>';
			$html.='<a href="?'.$link.($this->_pageno+1).'" class="able">下一页</a>';
		}else{
			$html.='<a  class="unble">尾页</a>';
			$html.='<a  class="unble">下一页</a>';
		}
		
		$html.='</div>';
		return $html;
	}
	/**
	 * 获取当前页记录起始索引
	 * @return 字符串
	 **/
	public function getOffset(){
		$start=($this->_pageno-1)*$this->_pagesize;
		return $start;
	}
}
$total=20;
$pagesize=4;
$page=new Page($total,$pagesize);
$html=$page->show(2,1);
$off=$page->getOffset();
var_dump($off);
?>

<html>
<head>
	<title></title>
	<meta charset="utf-8">
<style>
.page-box a{display:inline-block;height:20px;border-right:1px solid #ccc;padding:10px;text-decoration:none;color:black;}
.page-box .current{background:red;color:white;}
.page-box .unble{color:#ccc;}
.page-box .able:hover{background:red;color:white;cursor:pointer;}
.page-box{over-flow:hidden;float:left;border:1px solid #ccc;border-right:none;}
</style>
</head>
<body>
		<?=$html?>
</body>
</html>	

