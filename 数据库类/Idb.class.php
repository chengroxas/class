<?php
	
	interface Idb{
		public function add($table,$_data);
		public function update($table,$_data,$_where);
		public function get($table,$_field,$_where);
		public function getList($table,$_field,$_where='',$_limit='');
		public function delete($table,$_where);
		public function count($table,$_where='');
	}
?>
