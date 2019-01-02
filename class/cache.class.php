<?php
/*
* Cache缓存类
*/
class Cache {
    private $cache_path; //缓存文件路径
    private $cache_expire; //缓存超时时间(秒)
    //cache构造,传入$exp_time有效时间及$path保存路径
    public function Cache($exp_time = 3600, $path = "cache/") {
        $this->cache_expire = $exp_time;
        $this->cache_path = $path;
    }
    //返回缓存文件路径,文件名md5加密
    private function fileName($key) {
        return $this->cache_path . md5($key);
    }
    //创建缓存文件,传入$key，以$key的md5文件名保存$data
    public function set($key, $data) {
        $values = serialize($data);
        $filename = $this->fileName($key);
		$dirname=dirname($filename);
		if(!file_exists($dirname)) mkdir($dirname);
        $file = fopen($filename, 'w');
        if ($file) {
            fwrite($file, $values);
            fclose($file);
        } else return false;
    }
	//删除缓存文件,传入$key
	public function del($key) {
		$filename = $this->fileName($key);
		if(is_file($filename)){
			if(unlink($filename)){
				return true;
			}else{
				return false;
			} 
		};
	}
    //获取缓存数据,传入$key
    public function get($key) {
        $filename = $this->fileName($key);
        if (!file_exists($filename) || !is_readable($filename)) { //检查缓存文件是否存在
            return false;
        }
        if (time() < (filemtime($filename) + $this->cache_expire)) { //检查是否超时
            $file = fopen($filename, "r");
            if ($file) {
                $data = fread($file, filesize($filename));
                fclose($file);
                return unserialize($data); //返回数据
            } else return false;
        } else return false;
    }
}
?>