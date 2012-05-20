<?PHP
/**
 * Action抽象类 定义实现接口和封装的常用的方法
 */
abstract class Su_Ctrl_Action
{
	/**
	 * 生命周期变量管理器
	 */
	protected $cycle;
	/**
	 * 配置信息
	 */
	protected $conf;
	/**
	 * 请求对象封装
	 */
	protected $request;
	/**
	 * 上下文内容 用于callAction调用传递信息
	 */
	protected $ctx;
	/**
	 * 错误日志记录对象
	 */
	protected $errorLog;
	/**
	 * 验证信息
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $auth;

	/**
	 * 构造函数 
	 */
	public function __construct(Su_Ctrl_Cycle $cycle, $ctx = null) 
	{
		$this->cycle = $cycle;
		$this->ctx = $ctx;
		$this->conf = $cycle->retrieveObject(Su_Const::OBJ_CONF);
		$this->request = $cycle->getRequest();
		$this->auth = $cycle->getMeta(Su_Const::META_AUTH);
		isset($this->conf['error_log']) && $this->errorLog = Su_Log::getLogger($this->conf['error_log']);
	}

	/**
	 * 前置方法
	 */
	public function preExecute()
	{
	}

	/**
	 *  在具体应用中实现的Action都继承于Su_Ctrl_Action
	 *  工作内容主要是实现execute()
	 */
	abstract public function execute();

	/**
	 * 后置方法 
	 */
	public function postExecute()
	{
	}

	/**
	 * 输出变量的简便调用
	 */
	protected function response($key, $val = null, $auto = false)
	{
		if (func_num_args() == 1) {
			$this->cycle->setResponse($key);
		} else {
			$this->cycle->setResponse($key, $val, $auto);
		}
	}

	/**
	 * 设置输出头
	 */
	protected function header($str)
	{
		$pos = strpos($str, ':');
		$key = $pos ? substr($str, 0, $pos) : $str;
		$this->cycle->addOutgoingHeader($key, $str);
	}

	/**
	 * fault 设置异常输出
	 */
	protected function fault($code = 500, $msg = '', $forward = null, $note = null)
	{
		($forward && $note == null) && $note = $forward;
		$this->cycle->setMeta(Su_Const::META_FAULT,
				array('code' => $code, 'message' => $msg, 'forward' => $forward, 'note' => $note));
	}

	/**
	 * callAction 执行其他Action
	 */
	protected function callAction($actionName) 
	{
		$action = new $actionName($this->cycle, $this->cycle->getResponse());
		$action->preExecute();
		$action->execute();
		$action->postExecute();
		$this->ctx = $this->cycle->getResponse(); 
	}

	/**
	 * 设置模板文件的名
	 */
	protected function tpl($tpl) 
	{
		$this->cycle->setMeta(Su_Const::META_TEMPLATE, $tpl);
	}

	/**
	 * agent 变更设置请求类型
	 */
	protected function agent($agent)
	{
		$this->cycle->setAgent($agent);
	}

	/**
	 * format 指定输出格式
	 */
	protected function format($format)
	{
		$this->cycle->setMeta(Su_Const::META_FORMAT, $format);
	}

	/**
	 * redirect url跳转方法
	 */
	protected function redirect($url, $code = 302)
	{
		if ($code == 301) {
			$this->header('HTTP/1.1 301 Moved Permanently');
		} else {
			$this->header('Location: ' . $url);
		}
	}

	/**
	 * getTpl 获取Su_Tpl对象,action内部处理用
	 */
	protected function getTpl($format = Su_Const::FT_HTML)
	{
		return new Su_Tpl($this->conf['tpl'], $format);
	}
}
