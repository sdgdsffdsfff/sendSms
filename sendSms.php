<?php
/*
 * 手机短信接口文件 （来自106.ihuyi.cn短信接口, 加以整理为类）
 * authors  andychen 2014-11-14
*/
class sendSms{
	function __construct(){
		$this->target   = 'http://106.ihuyi.cn/webservice/sms.php?method=Submit';
		$this->account  = 'yourAccount';     //在ihuyi购买发短信的服务后，就可以把账号和密码填写在此
		$this->password = 'yourPassword';
	}

	public function Post($curlPost,$url){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_NOBODY, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
			$return_str = curl_exec($curl);
			curl_close($curl);
			return $return_str;
	}
	public function xml_to_array($xml){
		$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
		if(preg_match_all($reg, $xml, $matches)){
			$count = count($matches[0]);
			for($i = 0; $i < $count; $i++){
			$subxml= $matches[2][$i];
			$key = $matches[1][$i];
				if(preg_match( $reg, $subxml )){
					$arr[$key] = $this->xml_to_array( $subxml );
				}else{
					$arr[$key] = $subxml;
				}
			}
		}
		return $arr;
	}
	public function random($length = 6 , $numeric = 0) {
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		if($numeric) {
			$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
		} else {
			$hash = '';
			$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++) {
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		return $hash;
	}

	/* *
     * 短信发送手机验证码（用户注册时）
     * @param str    $mobile     目标手机号
     * @param int    $send_code  对照码，防用户恶意请求
	*/
	public function sendCode($mobile, $send_code){
		if(empty($mobile)){
			exit('手机号码不能为空');
		}
		$mobile_code = $this->random(4,1);

		if(empty($_SESSION['send_code']) or $send_code!=$_SESSION['send_code']){
			//防用户恶意请求
			exit('请求超时，请刷新页面后重试');
		}
		$msg = rawurlencode("您的验证码是：".$mobile_code."。请不要把验证码泄露给其他人。");
		$post_data = "account=".$this->account."&password=".$this->password."&mobile=".$mobile."&content=".$msg;

		//密码可以使用明文密码或使用32位MD5加密
		$gets =  $this->xml_to_array($this->Post($post_data, $this->target));
		if($gets['SubmitResult']['code']==2){
			$_SESSION['mobile'] = $mobile;
			$_SESSION['mobile_code'] = $mobile_code;
		}

		echo $gets['SubmitResult']['msg'];
	}


	/* *
	 * 短信发送信息
	 * @param   int  $mobile    手机号码
	 * @param   str  $msg       要发送的内容
	*/
	public function sendMsg($mobile, $msg){
		$post_data = "account=".$this->account."&password=".$this->password."&mobile=".$mobile."&content=".$msg;
		//密码可以使用明文密码或使用32位MD5加密
		$gets =  $this->xml_to_array($this->Post($post_data, $this->target));

		echo $gets['SubmitResult']['msg'];
	}

}

?>
