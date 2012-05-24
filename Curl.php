<?PHP
/**
 * curl库的封装
 */
class Su_Curl
{
	protected $ch;
	protected $url;
	protected $lastInfo;

	/**
	 * 构造函数
	 */
	public function __construct($url = null)
	{
		$this->url = $url;
		$this->ch = curl_init($url);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, true);
	}

	/**
	 * 设置curl选项
	 */
	public function setopt($type, $val)
	{
		curl_setopt($this->ch, $type, $val);
	}

	/**
	 * 发送post请求的快捷方法
	 */
	public function post($fields)
	{
		curl_setopt($this->ch, CURLOPT_POST, count($fields));
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($this->ch);
		$this->lastInfo = curl_getinfo($this->ch);
		return $result;
	}

	/**
	 * 发送get请求的快捷方法
	 */
	public function get($fields = null)
	{
		if (is_array($fields)) {
			$info = curl_getinfo($this->ch);	
			$url = $info['url'] . '?' . http_build_query($fields);
			curl_setopt($this->ch, CURLOPT_URL, $url);
		}
		$result = curl_exec($this->ch);
		$this->lastInfo = curl_getinfo($this->ch);
		return $result;
	}

	/**
	 * 接口请求处理,支持serialize&json
	 */
	public function rest($fields = null, $method = 'post', $format = null)
	{
		$data = $method == 'post' ? $this->post($fields) : $this->get($fields);
		switch ($format) {
			case Su_Const::FT_SERIAL :
				if (false === ($result = unserialize($data))) {
					throw new Su_Exc('unserialize error' . $data, $this->lastInfo['http_code']);
				}
				break;
			case Su_Const::FT_JSON :
			default :
				if (false === ($result = json_decode($data, true))) {
					throw new Su_Exc('json_decode error' . $data, $this->lastInfo['http_code']);
				}
		}
		return $result;
	}

	/**
	 * 最后一次请求的信息记录
	 */
	public function lastInfo()
	{
		return $this->lastInfo;
	}
}
