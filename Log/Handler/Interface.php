<?PHP
/**
 * Log内容格式化处理的接口定义
 */
interface Su_Log_Handler_Interface
{
	/**
	 * 格式化log内容的静态方法
	 * 该方法传入log配置信息,定义的原始信息,和可变参数.返回一个组合的字符串
	 */
	public static function format($conf, $level, $message, $args = array());
}
