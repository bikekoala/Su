<?PHP
/**
 * 默认的前段控制器实现
 */
class Su_Ctrl_Front
{
	/**
	 * 默认执行flow各个阶段处理类
	 */
	protected $defautClass = array(
			'INPUT' => 'Su_Ctrl_Phase_Input',
			'ADAPTER' => 'Su_Ctrl_Phase_Adapter',
			'AUTH' => 'Su_Phase_Auth',
			'DISPATCH' => 'Su_Ctrl_Phase_Dispatch',
			'OUTPUT' => 'Su_Ctrl_Phase_Output',
			'LOGGER' => 'Su_Ctrl_Phase_Logger'
			);

	/**
	 * cycle 请求过程全局对象
	 */
	protected $cycle;

	/**
	 * conf 配置信息, php关联数组
	 */
	protected $conf;


	/**
	 * 运行前端控制器
	 */
	public function run($conf) 
	{
		$this->conf = $conf;
		$this->init();
		$this->runPhase();
	}

	/**
	 * init 初始化创建cycle对象,注册配置信息
	 */
	protected function init() 
	{
		// cycle从当前位置开始存在
		// 其作用是放置对象、数据
		// cycle会在runPhase函数中交给每个流程
		$this->cycle = new Su_Ctrl_Cycle();
		// 将配置文件放入cycle
		$this->cycle->registerObject(Su_Const::OBJ_CONF, $this->conf);
	}

	/**
	 * runPhase 根据配置文件指定的,执行请求的各个步骤
	 */
	protected function runPhase()
	{
		// 从配置文件中取出前端控制器的程序流程
		// $conf['ctrl_front']['phase']['flow'] = array('INPUT', 'ADAPTER', 'DISPATCH', 'OUTPUT');
		// 输入、适配、分发、输出
		foreach ($this->conf['ctrl_front']['phase']['flow'] as $val) 
		{	
			// 从配置文件中取出流程的类名
			$className = isset($this->conf['ctrl_front']['phase'][$val]) ? $this->conf['ctrl_front']['phase'][$val] : $this->defautClass[$val];
			// 按指定流程顺序开跑～
			call_user_func($className . '::phase', $this->cycle);
		}
	}
}
