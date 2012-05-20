<?PHP
/**
 * 全局常量定义
 */
final class Su_Const
{
	/**
	 * Su_Ctrl_Cycle::registerObject 命名空间  应用程序配置信息
	 */
	const OBJ_CONF 				= 'conf';
	/**
	 * Su_Ctrl_Cycle::registerObject 命名空间 日志记录对象
	 */
	const OBJ_LOGGER 			= 'logger';


	/**
	 * 元数据类型:模板文件名
	 */
	const META_TEMPLATE			= 'meta_template';
	/**
	 * 元数据类型:输出的格式
	 */
	const META_FORMAT			= 'meta_format';
	/**
	 * 元数据类型:异常信息
	 */
	const META_FAULT 			= 'meta_fault';
	/**
	 * 元数据类型:验证信息
	 */
	const META_AUTH				= 'meta_auth';

	/**
	 * 浏览器请求类型(默认)  
	 */
	const AGENT_HTML			= 'html';
	/**
	 * wap请求类型 
	 */
	const AGENT_WAP				= 'wap';
	/**
	 * wap2.0的请求类型 
	 */
	const AGENT_XHTML			= 'xhtml';
	/**
	 * ajax请求类型  
	 */
	const AGENT_AJAX			= 'ajax';
	/**
	 * 命令行的请求类型
	 */
	const AGENT_CLI				= 'cli';
	/**
	 * 接口请求类型 参考Su_Curl
	 */
	const AGENT_INTERFACE		= 'interface';

	/**
	 * 输出内容格式 html smarty输出
	 */
	const FT_HTML 				= 'html';
	/**
	 * ajax的json数据格式输出  
	 */
	const FT_JSON 				= 'json';
	/**
	 * xml数据格式输出  
	 */
	const FT_XML				= 'xml';
	/**
	 * 文本数据格式输出  
	 */
	const FT_TEXT				= 'text';
	/**
	 * php serial格式输出 
	 */
	const FT_SERIAL				= 'serial';
	/**
	 * 二进制格式输出   
	 */
	const FT_BINARY				= 'binary';
}
