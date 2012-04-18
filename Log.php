<?PHP
/**
 * 灵活的日志记录的实现 
 *
 * Su_Log::getLogger($conf) 根据配置信息获取一个日志对象
 * Su_Log::debug($message, ...) 进行日志记录
 * 配置文件数组 示例:
 * array (
 *	'name'  => 'error', //log标记
 *	'write' => '/tmp/myapp.log', //写入处理对象
 *	'level' => 3, //错误级别
 *	'handle'=> default //错误级别
 * )
 */
class Su_Log
{
	/**
	 * log级别  
	 */
	const EMERG   = 0;  
	const ALERT   = 1; 
	const CRIT    = 2;
	const ERROR   = 3;
	const WARN    = 4;
	const NOTICE  = 5;
	const INFO    = 6;
	const DEBUG   = 7;

	/**
	 * level 用以支持常量的转化
	 */
	static $level = array('EMERG', 'ALERT', 'CRIT', 'ERROR', 'WARN', 'NOTICE', 'INFO', 'DEBUG');

	/**
	 * conf 存放配置信息
	 */
	private $conf;

	/**
	 * 构造函数,不允许直接实例化
	 */
	protected function __construct(array $conf)
	{
		$this->conf = $conf;
	}

	/**
	 * 获取log实例
	 */
	public static function getLogger(array $conf)
	{
		// $instances存放所有logger的静态变量
		static $instances;
		// 查看$instances中是否已经存放了我们需要的logger的静态变量
		$name = $conf['name'];
		if ( ! isset($instances[$name])) {
			$instances[$name] = new self($conf);
		}
		return $instances[$name];
	}

	/**
	 * debug级别的日志
	 */
	public function debug(string $message)
	{
		// 使用反射方法
		$method = new ReflectionMethod($this, 'log');
		$args = func_get_args();
		array_unshift($args, self::DEBUG);
		$method->invokeArgs($this, $args);
	}

	/**
	 * 记录notice级别的日志
	 */
	public function notice(string $message)
	{
		$method = new ReflectionMethod($this, 'log');
		$args = func_get_args();
		array_unshift($args, self::NOTICE);
		$method->invokeArgs($this, $args);
	}

	/**
	 * 记录warn级别的日志
	 */
	public function warn(string $message)
	{
		$method = new ReflectionMethod($this, 'log');
		$args = func_get_args();
		array_unshift($args, self::WARN);
		$method->invokeArgs($this, $args);
	}

	/**
	 * 记录error级别的日志
	 */
	public function error(string $message)
	{
		$method = new ReflectionMethod($this, 'log');
		$args = func_get_args();
		array_unshift($args, self::ERROR);
		$method->invokeArgs($this, $args);
	}


	/**
	 * 写入函数处理
	 * 可变参数支持vsprintf方式修饰message
	 */
	public function log(int $level, string $message)
	{
		if ($level > $this->conf['level']) {
			return false; 
		}

		// 如果参数大于2个，则截取前2个以外的参数
		if (func_num_args() > 2) {
			$allArgs = func_get_args();
			$args = array_splice($allArgs, 2);
		} else {
			$args = array();
		}

		// 格式化日志信息   
		$str = '';
		if ( ! isset($this->conf['handle']) || $this->conf['handle'] == 'default') {
			$str .= '"' . date('Y-m-d H:i:s') . '" ' . $this->conf['name'] . ' ' . self::$level[$level] . ' ';
			$str .= '"' . addslashes(isset($args) ? vsprintf($message, $args) : $message) . '"';
			$str .= "\n";
		} else {
			$str .= call_user_func_array($this->conf['handle'] . '::format', array($this->conf, $level, $message, $args));
		}

		// 写入的处理, 如果是php支持的流方式 php:// 和文件系统直接写入 如果是设置处理类直接调用 
		if ( ! isset($this->conf['write'])) {
			var_dump($str);
		} elseif (preg_match("/^php:\/\/|\//", $this->conf['write'])) {
			file_put_contents($this->conf['write'], $str, FILE_APPEND);
		} else {
			call_user_func_array($this->conf['write'] . '::write', array($this->conf, $str));
		}
	}
}
