<?PHP
class Su_Facade
{
	public static $conf;

	public static function startup($conf, $front = null)
	{
		self::$conf = $conf;	
		self::init();
		//xx
	}

	/**
	 * init 初始化 配置autoload, 默认异常处理, 错误处理
	 */
	public static function init()
	{
		define('ENVIRONMENT', self::$conf['environment'];);	
		// 注册自动加载函数
		sql_autoload_register('Su_Facade::loadClass');
		// 参考 http://cn2.php.net/manual/zh/errorfunc.constants.php
		$errorType = ENVIRONMENT === 'development' ? E_ALL | E_STRICT : (E_ALL & ~E_NOTICE) | E_STRICT;
		// 注册错误抓捕函数
		set_error_handler('Su_Facade::errorHandler', $errorType);
		// 注册异常抓捕函数
		set_exception_handler('Su_Facade::exceptionHandler');
	}

	/**
	 * 自动加载类文件
	 * eg: Demo_Action_Main
	 */
	public static function loadClass($className, $dir = null)
	{
		$class = strtolower($className);	
		// 引入smarty,同样自动加载机制
		if (substr($class, 0, 16) === 'smarty_internal_' || $class === 'smarty_security') {
			$file = 'Su/Tpl/sysplugins/' . $class . '.php';	
		} else {
			$file = str_replace('_', '/', $class) . '.php';	
		}
		if ($dir) {
			include $dir . '/' . $file;
		} else {
			include $file;	
		}
		if ( ! class_exists($className, false) && ! interface_exists($className, false)) {
			throw new Su_Exc('文件:' . $file . '不存在对应的类:' . $className, 500);	
		}
		return true;
	}

	/**
	 * PHP错误信息处理的回调方法
	 */
	public static function errorHandler($exc)
	{
	
	}

	/**
	 * 默认的异常处理方法 进行日志记录
	 */
	public static function exceptionHandler($exc)
	{
	
	}
}
