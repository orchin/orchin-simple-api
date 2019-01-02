<?php
/*
* api方法类
* api业务方法类，类名随意，对应修改index.php里Routers::map传入的类
* 注意方法名，public的方法将会把方法名批量映射到路由，protected和private方法不会被映射
*/
//require_once('./class/response.class.php');
//require_once('./class/database.class.php');
//require_once('./class/cache.class.php');
//require_once('./util.class.php');

class apis{
	/*
	* 测试用，列出所有路由
	*/
	public static function test(){
		return Response::show('200','test',Routers::$route_map);
	}
	/*
	* 【私有方法】
	* 获取列表数据，参数 $table表 ， $col列，$where条件数组
	* 直接从json参数获取 page / limit
	*/
	private static function getList($table,$col="*",$where=[]){
		$page = (int)Util::jsonParam('page',1);
		$limit = (int)Util::jsonParam('limit',1);
		$pos = ($page-1)*$limit;
		$database = Util::DB();
		$queryWhere = $where + ["LIMIT" => [$pos , $limit]];
		$list = $database->select($table,"*", $queryWhere);
		if(!is_array($list)) return Util::failure();
		$total =  $database->count($table,"id", '');
		$data = ['list'=>$list,'page'=>$page,'limit'=>$limit,'total'=>$total];
		return Util::success($data);
	}
	//用户登录
	public static function login(){
		$database = Util::DB();
		$username = (string)Util::jsonParam('username','');
		$password = (string)Util::jsonParam('password','');
		if($username==''){
			return Util::failure('缺少用户名');
		};
		if($password==''){
			return Util::failure('缺少密码');
		};
		$res=$database->get('os_user',
							['id','username','nickname','level','avatar','phone','email','score'],
							['AND'=>['username'=>$username,'password'=>md5($password)]]
							);
		if($res){
			$database->update('os_user',
							['last_ip'=>$_SERVER['REMOTE_ADDR'],'last_time'=>strtotime('now')],
							['id'=>$res["id"]]
							);
			$res['token']=Util::get_token($res['id']);
			return Util::success($res,'登录成功');
		};
		return Util::failure('用户名或者密码错误');
	}
	
	//用户登出
	public static function logout(){
		$uid = (int)Util::jsonParam('id',0);
		if($uid==0){
			return Util::failure('缺少用户ID');
		};
		Util::delRealToken($uid);
		return Util::success("","登出成功");
	}
	
	//用户列表
	public static function userlist(){ 
		Util::check_token();//登录检查
		return self::getList('os_user');
	}
	
	//用户添加
	public static function useradd(){ 
		Util::check_token();//登录检查
		$database = Util::DB();
		$level = (int)Util::jsonParam('level',1);
		$username = (string)Util::jsonParam('username','');
		$nickname = (string)Util::jsonParam('nickname','');
		$password = (string)Util::jsonParam('password','');
		$avatar = (string)Util::jsonParam('avatar','');
		$phone = (string)Util::jsonParam('phone','');
		$email = (string)Util::jsonParam('email','');
		if($username==''){
			return Util::failure('缺少用户名');
		};
		if($database->has('os_user',['username'=>$username])){
			return Util::failure('用户名已存在');
		};
		if($password==''){
			return Util::failure('缺少密码');
		};
		if($nickname==''){
			return Util::failure('缺少昵称');
		};
		$data = [
			'level'=>$level,
			'username'=>$username,
			'nickname'=>$nickname,
			'password'=>md5($password),
			'avatar'=>$avatar,
			'phone'=>$phone,
			'email'=>$email,
			'reg_ip'=>$_SERVER['REMOTE_ADDR'],
			'create_time'=>strtotime('now')
		];
		$database->insert('os_user',[$data]);
		return Util::success($data);
	}
	
	//用户更新
	public static function userupdate(){ 
		Util::check_token();//登录检查
		$database = Util::DB();
		$uid = (int)Util::jsonParam('id',0);
		$nickname = (string)Util::jsonParam('nickname','');
		$level = Util::jsonParam('level',null);
		$password = Util::jsonParam('password',null);
		$phone = Util::jsonParam('phone',null);
		$email = Util::jsonParam('email',null);
		$avatar = Util::jsonParam('avatar',null);
		if($uid==0){
			return Util::failure('缺少用户ID');
		};
		if(!$database->has('os_user',['id'=>$uid])){
			return Util::failure('用户不存在');
		};
		if($nickname==''){
			return Util::failure('缺少昵称');
		};
		$data = [
			'nickname'=>$nickname
		];
		if($level!==null){
			if($uid!=1) $data['level']=$level;
		};
		if($password!==null){
			$data['password']=md5($password);
		};
		if($phone!==null){
			$data['phone']=$phone;
		};
		if($email!==null){
			$data['email']=$email;
		};
		if($avatar!==null){
			$data['avatar']=$avatar;
		};
		$res=$database->update('os_user',$data,['id'=>$uid]);
		if($database->error()[0]=="00000"){
			return Util::success($data);
		};
		return Util::failure('修改失败',$database->error());
	}
	
	//用户删除
	public static function userdelete(){
		Util::check_token();//登录检查
		$database = Util::DB();
		$uid = (int)Util::jsonParam('id',0);
		if($uid==0){
			return Util::failure('缺少用户ID');
		};
		if($uid==1){
			return Util::failure('admin用户禁止删除');
		};
		$res=$database->delete('os_user',['id'=>$uid]);
		if($res){
			return Util::success();
		};
		return Util::failure('删除失败');
	}
	
	//文档列表
	public static function postlist(){ 
		Util::check_token();//登录检查
		return self::getList('os_post');
	}
	
	//文档删除
	public static function postdelete(){
		Util::check_token();//登录检查
		$database = Util::DB();
		$id = (int)Util::jsonParam('id',0);
		if($id==0){
			return Util::failure('缺少ID');
		};
		$res=$database->delete('os_post',['id'=>$id]);
		if($res){
			return Util::success();
		};
		return Util::failure('删除失败');
	}
}
?>