<?PHP
/**
 * 贯穿App生命周期变量管理 在Front进行创建,传递给各执行Phase做为执行流的变量管理
 * 例: 
 * 在Su_Ctrl_Phase_Adapter阶段计算出执行的Action名通过Su_Ctrl_Cycle::setOperation
 * Su_Ctrl_Phase_Dispatch阶段通过Su_Ctrl_Cycle::getOperation进行调用
 */
class Su_Ctrl_Cycle
{
	/**
	 * 保存请求类型的内部变量
	 */
	protected $agent;
	/**
	 * 保存原数据的内部变量: 模板文件名|错误信息...
	 */
	protected $metas;
	/**
	 * 保存执行的Action的ClassName 
	 */
	protected $operation;
	/**
	 * 请求的输入数据对象
	 */
	protected $request;
	/**
	 * 输出的请求头集合 headers
	 */
	protected $outgoingHeaders;
	/**
	 * 输出的内容变量数组
	 */
	protected $response;
	/**
	 * 只传递给smarty的变量集合 不进行接口输出
	 */
	protected $autoResponse;
	/**
	 * 对象容器 registerObject|retrieveObject 方法使用的内部存储空间
	 */
	protected $objectContainer;

	/**
	 * 设置请求客户端标志
	 */
	public function setAgent($agent)
	{
		$this->agent = $agent;
	}

	/**
	 * 获取请求客户端标志
	 */
	public function getAgent()
	{
		return $this->agent;
	}

	/**
	 * setMeta 设置元数据
	 * $key  参考Su_Const::
	 */
	public function setMeta($key, $val) 
	{
		$this->metas[$key] = $val;
	}

	/**
	 * getMeta 获取元数据
	 */
	public function getMeta($key) 
	{
		return isset($this->metas[$key]) ? $this->metas[$key] : null;
	}

	/**
	 * setOperation 设置执行的action类
	 */
	public function setOperation($val) 
	{
		$this->operation = $val;
	}

	/**
	 * getOperation 获取操作action类名
	 */
	public function getOperation() 
	{
		return $this->operation;
	}

	/**
	 * 设置请求数据
	 */
	public function setRequest($val)
	{
		$this->request = $val;
	}

	/**
	 * getRequest 获取请求数据
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * 增加输出的http头
	 * $val如果为空key可为数组传入
	 */
	public function addOutgoingHeader($key, $val = null)
	{
		if (func_num_args() == 1) {
			$this->outgoingHeaders = $key;
		} else {
			$this->outgoingHeaders[$key] = $val;
		}
	}

	/**
	 * 获取输入http请求头集合
	 */
	public function getOutgoingHeaders()
	{
		return $this->outgoingHeaders;
	}

	/**
	 * 设置输出内容 如果只传递一个参数可以覆盖整个数组
	 */
	public function setResponse($key, $val = null, $auto = false)
	{
		if (func_num_args() == 1) {
			$this->response = $key;
		} else {
			$auto ? $this->autoResponse[$key] = $val : $this->response[$key] = $val;
		}
	}

	/**
	 * getResponse 获取返回对象
	 */
	public function getResponse($auto = false)
	{
		return $auto ? $this->autoResponse : $this->response;
	}

	/**
	 * 注册数据对象
	 * $name 参考Su_Const::OBJ_*
	 */
	public function registerObject($name, $object)
	{
		$this->objectContainer[$name] = $object;
	}

	/**
	 * 取出数据对象
	 * $name 参考Su_Const::OBJ_*
	 */
	public function retrieveObject($name)
	{
		return isset($this->objectContainer[$name]) ? $this->objectContainer[$name] : null;
	}
}
