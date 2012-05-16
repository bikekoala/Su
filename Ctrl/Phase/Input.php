<?PHP
/**
 * 输入阶段处理
 */
class Su_Ctrl_Phase_Input implements Su_Ctrl_Phase_Interface
{
	public static function phase(Su_Ctrl_Cycle $cycle) 
	{
		$cycle->setRequest(new Su_Ctrl_Cycle_Request());
	}
}
