<?PHP
/**
 * http请求来源参数的封装 将$_GET $_POST $_FILE 请求参数的获取封装成对象 并且提供过滤方法
 */
class Su_Ctrl_Cycle_Request implements ArrayAccess
{
	/**
	 * 原始输入数据 默认为$_REQUEST
	 */
	private $rawData;

	/**
	 * 构造函数 初始化数据源
	 */
	public function __construct($rawData = null)
	{
		$this->rawData = $rawData === null ? $_REQUEST : $rawData;
	}

	/**
	 * 魔术方法__get实现 支持动态获取数据源
	 */
	public function __get($key) 
	{
		if ($key === 'request_method') {
			return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'cli';
		} else {
			return isset($this->rawData[$key]) ? $this->rawData[$key] : null;
		}
	}

	/**
	 * 魔术方法__set实现 支持重载内部属性
	 */
	public function __set($key, $value)
	{
		$this->rawData[$key] = $value;
	}

	/**
	 * 魔术方法__isset实现 支持isset()判断
	 */
	public function __isset($key)
	{
		return isset($this->rawData[$key]);
	}

	/**
	 * 魔术方法__unset 实现
	 */
	public function __unset($key) 
	{
		unset($this->rawData[$key]);
	}

	/**
	 * ArrayAccess 数组访问isset($result['test'])的实现
	 */
	public function offsetExists($offset)
	{
		return $this->__isset($offset);
	}

	/**
	 * echo $request['test']  实现
	 */
	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}

	/**
	 * $request['test'] = 'test' 的实现
	 */
	public function offsetSet($offset, $value)
	{
		$this->__set($offset, $value);
	}

	/**
	 * unset($request['test']) 方式实现
	 */
	public function offsetUnset($offset)
	{
		$this->__unset($offset);
	}

	/**
	 * sanitizing 净化数据源
	 */
	public function sanitizing($key, $filter = FILTER_DEFAULT, $options = null)
	{
		$this->rawData[$key] = filter_var($this->rawData[$key], $filter, $options);
		return $this->rawData[$key];
	}

	/**
	 * 过滤器 php filter扩展方法的封装 此方法不会读数据源生效
	 */
	public function filter($key, $filter = FILTER_DEFAULT, $options = null, $type = null) 
	{
		/**
		 * 简化正则的调用 
		 */
		if ($filter == FILTER_VALIDATE_REGEXP && is_string($options)) {
			$options = array('options' => array('regexp' => $options));
		}
		if (isset($this->rawData[$key]) && $type === null) {
			return filter_var($this->rawData[$key], $filter, $options);
		} else {
			return filter_input($type, $key, $filter, $options);
		}
	}

	/**
	 * php扩展filter_array封装
	 */
	public function filterArray($definition, $type = null)
	{
		if ($type === null) {
			return filter_var_array($this->rawData, $definition);
		} else {
			return filter_input_array($type, $definition);
		}
	}
}
