<?php
/******************************
目录类
2015年1月7日 12:39:55
******************************/
class Dir{
	
	//判断目录是否存在
	static public function is_dir($dir){
		if (is_dir($dir)) {
			return true;
		}else{
			return false;
		}
	}

	//递归创建目录
	static public function mkdir_digui($dir, $mode = 0777){
		if (is_dir($dir) || @mkdir($dir,$mode)) {
			return true;
		}
		if (!self::mkdir_digui(dirname($dir),$mode)) {
			return false;
		}
		return @mkdir($dir,$mode);
	}

	//自动创建目录
	static public function create_dir_auto ($dir) {
		//判断有没有主文件夹，如果没有则创建
		if (!self::is_dir($dir)) {
			//递归创建目录
			self::mkdir_digui($dir);
		}
	}

}