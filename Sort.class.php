<?php
class Sort{
	public function bubbleSort($arr){
		$len=count($arr);
		for($i=1;$i<$len-1;$i++){
			for($j=0;$j<$len-$i;$j++){
				if($arr[$j]>$arr[$j+1]){
					$temp=$arr[$j];
					$arr[$j]=$arr[$j+1];
					$arr[$j+1]=$temp;
				}
			}
		}
		return $arr;
	}//bubbleSort()
	public function selectSort($arr){
		$len=count($arr);
		for($i=0;$i<$len;$i++){
			$key=$i;//假定第一个为最小
			for($j=$i+1;$j<$len;$j++){
				if($arr[$key]>$arr[$j]){
					$key=$j;//真正最小的索引
				}
				if($key!=$i){
					$temp=$arr[$key];
					$arr[$key]=$arr[$i];
					$arr[$i]=$temp;
				}
			}
		}
		return $arr;
	}//selectSort()
	public function quickSort($arr){
		$len=count($arr);
		if($len<1){return $arr;}
		$key=$arr[0];
		$small=array();	
		$big=array();
		for($i=1;$i<$len;$i++){
			if($arr[$i]<$key){
				$small[]=$arr[$i];
			}else{
				$big[]=$arr[$i];
			}
		}
		$small=$this->quickSort($small);
		$big=$this->quickSort($big);
		return array_merge($small,[$key],$big);
	}//quickSort()
}//Sort类结束
$sort=new Sort();
$arr=array(1,5,6,4,8,7,3,15,1);
$row=$sort->bubbleSort($arr);
print_r($row);
