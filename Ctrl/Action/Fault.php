<?PHP
/**
 * 默认的异常错误显示页面
 */
class Su_Ctrl_Action_Fault extends Su_Ctrl_Action
{
	public function execute()
	{
		$this->format(Su_Const::FT_BINARY);
		$this->response('抱歉! 服务器内部错误');
	}
}
