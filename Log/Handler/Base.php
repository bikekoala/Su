<?PHP
/**
 * 最基础的log格式处理 默认自动增加 格式如下:
 * 时间\t记录名称\t记录等级\记录内容
 */
class Su_Log_Handler_Base implements Su_Log_Handler_Interface
{
	public static function format($conf, $level, $message, $args)
	{
		$str = '"' . date('Y-m-d H:i:s') . '" ' . $this->conf['name'] . ' ' . self::$level[$level] . ' ';
		$str .= '"' . addslashes(isset($args) ? vsprintf($message, $args) : $message) . '"';
		$str .= "\n";
		return $str;
	}
}
