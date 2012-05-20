<?PHP
/**
 * Su_Func 共用的全局静态方法
 */
final class Su_Func
{
	/**
	 * 获取请求客户端真实ip
	 */
	public static function ip() 
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}

	/**
	 * 设置cookie的函数封装
	 */
	public static function cookie($key, $val, $life = 0, $domain = '.sukai.me') 
	{
		return setcookie($key, $val, $life ? $_SERVER['REQUEST_TIME'] + $life : 0, '/',
				$domain, isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
	}

	/**
	 * DES加密函数 包括加密和解码
	 */
	public static function encrypt($string, $key, $operation = 'CODE')
	{
		$keyLength = strlen($key);
		$string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
		$stringLength = strlen($string); $rndkey = $box = array(); $result = '';
		for ($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($key[$i % $keyLength]); $box[$i] = $i;
		}
		for ($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i]; $box[$i] = $box[$j]; $box[$j] = $tmp;
		}
		for ($a = $j = $i = 0; $i < $stringLength; $i++) {
			$a = ($a + 1) % 256; $j = ($j + $box[$a]) % 256; $tmp = $box[$a];
			$box[$a] = $box[$j]; $box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if ($operation == 'DECODE') {
			if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
				return substr($result, 8);
			}
		} else {
			return str_replace('=', '', base64_encode($result));
		}
	}
}
