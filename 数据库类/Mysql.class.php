<?php
/**
 * 数据库类
 **/
Loader::import('core/Mysql/Idb');
class Mysql implements Idb{
	private $conn=NULL;
	private $debug=FALSE;
	private $config=array();//数据库连接的参数
	
	public function __construct($_config){
		$this->config=$_config;
	}
	
	public function connect(){//连接数据库
		$this->conn=mysqli_connect($this->config['host'],$this->config['user'],
									$this->config['password'],$this->config['dbname'],
									$this->config['port']);
		mysqli_query($this->conn,"set names ".$this->config['charset']);
		if(!$this->conn){
			die("连接错误: " . mysqli_connect_error());
		}
	}
	/**
	 * 添加一条记录
	 * @param $table 表名 字符串
	 * @param $_data 数据 字符串
	 * @return id | false
	 **/
	public function add($table,$_data){
		//添加前先确认是否已存在数据
		$row=$this->row_exist($table,$_data);
		if( $row == NULL ){//数据不存在则添加
			$field="";
			$value="";
			foreach($_data as $key=>$val){
				if($field==""){
					$field.="`{$key}`";
				}else{
					$field.=",`{$key}`";
				}
				if($value==""){
					$value.="'{$val}'";
				}else{
					$value.=",'{$val}'";
				}
			}
			$sql="INSERT INTO {$this->config['prefix']}{$table}(";
			$sql.="{$field}) VALUES ({$value});";
			$ret=$this->query($sql);
			if( $ret == FALSE ){
				return false;
			}
			return mysqli_insert_id($this->conn);
		}else{
			return false;
		}
		
	}
	/**
	 * 更新数据
	 * @param $table  数据表名 字符串
	 * @param $_data  更新的数据 字符串 | 名值数组 
	 * @param $_where 条件 字符串 | 名值数组
	 * @return bool true | false
	 **/
	public function update($table,$_data,$_where){
		//"UPDATE table SET `field`=value `field`=value WHERE 条件"
		//根据修改的条件判断修改的数据是否存在
		$row=$this->row_exist($table,$_where);
		if( $row != NULL ){
			$sql="";
			$data='';
			if(is_string($_data)){
				$data=$_data;
			}else if(is_array($_data)){
				foreach($_data as $key=>$val){
					if($data==''){
						$data.="`{$key}`='{$val}'";
					}else{
						$data.=",`{$key}`='{$val}'";
					}
				}
			}
			$sql.="UPDATE {$table} SET {$data}";
			$where='';
			
			$where=self::where($_where);
			
			if( $where != '' ){
				$sql.=" WHERE ";
			}
			$sql.="{$where}";
			$ret=$this->query($sql);
			return $ret;
		}else{
			return false;
		}
	}
	/**
	 * 获取一条数据 
	 * @param $table 数据表 字符串
	 * @param $_field 字段 字符串 | 名值数组 | ''
	 * @param $_where 条件 字符串 | 名值数组
	 * @return array 查询的结果
	 **/
	public function get($table,$_field,$_where){
		$field='';
		if( is_string($_field) ){
			$field=$_field;
		}else if( is_array($_field)){//数组转换为字符串
			$field=implode(',',$_field);
		}
		$sql='';
		if( $field == '' ){//如果查找字段为空则为*
			$sql="SELECT * FROM {$this->config['prefix']}{$table}";
		}else{
			$sql="SELECT {$field} FROM {$this->config['prefix']}{$table}";
		}
		$where='';
		
		$where=self::where($_where);
		
		if( $where !='' ){
			$sql.=" WHERE ";
		}
		$sql.="{$where}";
		$ret=$this->query($sql);
		
		return mysqli_fetch_assoc($ret);
	}
	/**
	 * 获取全部 
	 * @param $table 数据表 字符串
	 * @param $_field 字段 字符串 | 名值数组 | ''
	 * @param $_where 条件 字符串 | 名值数组
	 * @param $_limit 查询记录数 字符串 
	 * @return array 查询的结果
	 **/
	public function getList($table,$_field,$_where='',$_limit=''){
		$field='';
		if( is_string($_field) ){
			$field=$_field;
		}else if( is_array($_field) ){//数组转换为字符串
			$field=implode(',',$_field);
		}
		$sql='';
		if( $field == '' ){//如果查找字段为空则为*
			$sql="SELECT * FROM {$this->config['prefix']}{$table} order by id asc";
		}else{
			$sql="SELECT {$field} FROM {$this->config['prefix']}{$table} order by id asc";
		}
		$where='';
		$where=self::where($_where);
		if($where==''){
			$sql.=" ";
		}else{
			$sql.=" WHERE {$_where}";
		}
		$limit=$_limit;
		if($limit==''){
			$sql.=" ";
		}else{
			$sql.=" limit {$limit} ";
		}
		$ret=$this->query($sql);
		$rows=array();
		while($row=mysqli_fetch_assoc($ret)){
			$rows[]=$row;
		}
		return $rows;
	}
	/**
	 * 统计记录数
	 * @param $table 数据表名
	 * @param $_where 条件 字符串 | 名值数组
	 * @return 记录数 字符串
	 **/
	public function count($table,$_where=''){
		$sql="SELECT COUNT(*) AS count FROM {$this->config['prefix']}{$table} ";
		$where='';
		$where=self::where($_where);
		if($where!=''){
			$sql.=" WHERE {$where}";
		}
		$ret=$this->query($sql);
		if($ret==NULL){
			echo "{$sql}";
			die('记录数为零');
		}
		$row=mysqli_fetch_assoc($ret);
		return $row['count'];
	}
	/**
	 * 删除数据
	 * @param $table 数据表 字符串
	 * @param $_where 条件 名值数组
	 * @return 影响记录行数 | false
	 **/
	public function delete($table,$_where){
		$where='';
		$where=self::where($_where);
		if($where==''){
			die("一定要输入条件");
		}
		$sql="DELETE FROM {$this->config['prefix']}{$table} WHERE {$where};";
		$ret=$this->query($sql);
		if( $ret == FALSE ){
			return false;
		}
		return  mysqli_affected_rows($this->conn);
	}
	/**
	 * 判断处理的数据是否存在 
	 * @param $table 数据表 字符串
	 * @param $_where 条件 
	 * @return 结果集 | NULL
	 **/
	public function row_exist($table,$_where){
		$where='';
		
		$where=self::where($_where);

		$unique="SELECT * FROM {$this->config['prefix']}{$table} WHERE {$where}";
		$res=$this->query($unique);
		return @$row=mysqli_fetch_assoc($res);
	}
	/**
	 * 拼接where 
	 * @param $_where 条件 字符串 | 名值数组
	 * @return where 字符串
	 **/
	public static function where($_where){
		$where='';
		if(is_string($_where)){
			$where=$_where;
		}else if(is_array($_where)){
			foreach($_where as $key=>$val){
					if( strpos($key,'|')!==FALSE ){//存在|,用or代替
						$key=str_replace('|',' or ',$key);
					}else{
						if($where!=''){//不存在 or,加and
							$key=" and {$key}";
						}
					}
					$where.="{$key}='{$val}'";//拼接成id='' and name=''的形式
				}
			}
		return $where;
	}
	/**
	 * 执行sql语句
	 * @param $sql 字符串
	 * @return 结果集
	 **/
	public  function query($sql){//执行sql语句
		if( $this->debug !== FALSE ){//默认debug为false默认不输出  调试时设置为true输出sql语句
			echo "Query sql:".$sql;
		}
		if( $this->conn== NULL ){//如果数据库为NULL,则连接数据库
			$this->connect();//执行连接数据库的方法
		}
		return mysqli_query($this->conn,$sql);
	}
	/**
	 * 调试 
	 * @param $debug ture | false
	 * @return 无
	 **/
	public function debug($debug){//修改debug输出sql语句
		$this->debug=$debug;
	}
	
	public function __destruct(){
		if($this->conn!=NULL){
			mysqli_close($this->conn);
		}
		$this->conn=NULL;
	}
}

