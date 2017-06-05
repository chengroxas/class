<?php
class Msession{
	protected static $m;
	protected static $ltime;
	public static function start($memcache){
		self::$m=$memcache;
		self::$ltime=ini_get("session.gc_maxlifetime");
		ini_set("session_save_handler","user");
		session_set_save_handler(
			array(__CLASS__,'open'),
			array(__CLASS__,'close'),
			array(__CLASS__,'read'),
			array(__CLASS__,'write'),
			array(__CLASS__,'destory'),
			array(__CLASS__,'gc')
		);
		session_start();
	}
	
	public static function open($path,$name){
		return true;
	}
	public static function close(){
		return true;
	}
	public static function read($session_id){
		$val=self::$m->get('mem_'.$session_id);
		if(empty($val)){return "";}
		return $val;
	}
	public static function write($session_id,$data){
		return self::$m->set('mem_'.$session_id,$data,MEMCACHE_COMPRESSED,self::$ltime);
	}
	public static function destory($session_id){
		return self::$m->delete('mem_'.$session_id);
	}
	public static function gc($lifetime){
		return true;
	}
}//mession类
$mem=new Memcache();
$mem->connect('192.168.1.53',11211);
Msession::start($mem);
$_SESSION['name']='admin';
echo $_SESSION['name'];

//echo $mem->get('mem_name');
