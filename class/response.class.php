<?php
/*
* Response接口响应类，格式化输出，支持json、xml、jsonp三种格式
*/
class Response{
	
	const TYPE_JSON = 'json'; //默认输出格式
	const TYPE_XML = 'xml';
	const TYPE_JSONP = 'jsonp';
	/**
	* 按综合通信方式输出数据
	* $code 状态码
	* $message 提示信息
	* $data 数据
	*/		
	public static function show($code,$message='',$data='',$type=self::TYPE_JSON){
		if(!is_numeric($code)){
			return '';
		};
		header("Content-Type:application/json");
		$type = isset($_GET['format']) ? $_GET['format'] : $type;
		$result = array(
			'code'=>$code,
			'msg'=>$message,
			'data'=>$data
		);
		if($type == self::TYPE_JSON){
			echo self::jsonEncode($code,$message,$data);
			exit;
		}elseif($type == self::TYPE_XML){
			echo self::xmlEncode($code,$message,$data);
			exit;
		}elseif($type == self::TYPE_JSONP){
			$callbackName = isset($_GET['callback']) ? $_GET['callback'] : 'callback';
			echo $callbackName."(".self::jsonEncode($code,$message,$data).")";
			exit;
		}else{
			echo "抱歉，暂时未提供此种数据格式";
		}
	}
	/**
	* 按json格式封装数据
	* $code 状态码
	* $message 提示信息
	* $data 数据
	*/
	public static function jsonEncode($code,$message='',$data){
		if(!is_numeric($code)){
			return '';
		}
		$result = array(
			'code'=>$code,
			'msg'=>urlencode($message),
			'data'=>$data
		);
		return urldecode(json_encode($result));
		exit;
	}
	
	/**
	* 按xml格式封装数据
	* $code 状态码
	* $message 提示信息
	* $data 数据
	*/	
	public static function xmlEncode($code,$message,$data){
		if(!is_numeric($code)){
			return '';
		}
		$result = array(
			'code'=>$code,
			'msg'=>$message,
			'data'=>$data
		);
		header("Content-Type:text/xml");
		$xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
		$xml .= "<root>\n";
		$xml .= self::arrayToXml($result);
		$xml .= "</root>";
		return $xml;
	}
	//解析xmlEncode()方法里的$result数组，拼装成xml格式	
	public static function arrayToXml($data){
		$xml = $attr = "";
		foreach($data as $key => $value){
			if(is_numeric($key)){//xml节点不能为数字,如$key是数字，重定义节点并数字作节点id
				$attr = " id='{$key}'";
				$key = "item";
			}
			$xml .= "<{$key}{$attr}>\n";
			//递归方法处理$value数组
			$xml .= is_array($value) ? self::arrayToXml($value) : $value;
			$xml .= "</{$key}>";
		}
		return $xml;
	}
}
?>