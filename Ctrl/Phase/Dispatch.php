<?PHP
/**
 * 分发阶段,对Action进行调用执行,且默认处理异常
 */
class Su_Ctrl_Phase_Dispatch implements Su_Ctrl_Phase_Interface
{
	public static function phase(Su_Ctrl_Cycle $cycle) 
	{
		// 取出cycle中的Action类名,实例化该类,执行标准接口
		$conf = $cycle->retrieveObject(Su_Const::OBJ_CONF);
		$config = $conf['ctrl_front']['phase']['dispatch'];
		$className = $cycle->getOperation();

		try {
			$action = new $className($cycle);
			$action->preExecute();
			$action->execute();
			$action->postExecute();
		} catch (exception $e) {
			if ($config['catch_error']) {
				$msg = isset($config['error_msg']) ? $config['error_msg'] : $e->getMessage();
				$cycle->setMeta(Su_Const::META_FAULT, array('code' => 500, 'message' => $msg));
				if (isset($conf['error_log'])) {
					Su_Log::getLogger($conf['error_log'])->error("phpexception:code:%d\nmessage:%s\nfile:%s:%d\ntrace:%s", $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
				}
			} else {
				throw $e;
			}
		}
	}
}
