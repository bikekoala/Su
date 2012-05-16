<?PHP
/**
 * Action的适配器阶段，此阶段完成根据$_GET['do']参数或者默认 计算出需要执行的action的类名
 */
class Su_Ctrl_Phase_Adapter implements Su_Ctrl_Phase_Interface
{
	public static function phase(Su_Ctrl_Cycle $cycle) 
	{
		$conf = $cycle->retrieveObject(Su_Const::OBJ_CONF);

		// 判定请求类型,默认是html 
		$agent = $conf['ctrl_front']['phase']['adapter']['agent']['default'];
		$cycle->setAgent($agent);

		// 确定调用的Action类
		$operation = isset($_GET['do']) ? $_GET['do'] : $conf['ctrl_front']['phase']['adapter']['default'];

		// 使用.切割Action
		$operation = implode('_', array_map('ucfirst', explode('.', $operation)));

		// 从配置文件中取出应用的请求空间放在cycle中
		$cycle->setOperation($conf['ctrl_front']['phase']['adapter']['prefix'] . $operation);
	}
}
