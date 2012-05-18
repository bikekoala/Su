<?PHP
/**
 * 数据输出阶段,默认处理类
 */
class Su_Ctrl_Phase_Output implements Su_Ctrl_Phase_Interface
{
	/**
	 * 全局变量
	 */
	protected $cycle;

	/**
	 * 配置信息
	 */
	protected $conf;

	/**
	 * 构造函数 设置cycle
	 */
	public function __construct(Su_Ctrl_Cycle $cycle)
	{
		$this->cycle = $cycle;
		$this->conf = $this->cycle->retrieveObject(Su_Const::OBJ_CONF);
	}

	/**
	 * phase接口方法,执行输出阶段
	 */
	public static function phase(Su_Ctrl_Cycle $cycle) 
	{
		$phase = new self($cycle);
		// 判断是否异常
		$fault = $phase->cycle->getMeta(Su_Const::META_FAULT);
		if (isset($fault) && $fault['code'] != 200) {
			$phase->fault($fault);
		}
		$phase->doOutput();
	}

	/**
	 * 输出处理
	 */
	public function doOutput()
	{
		$output = $this->conf['ctrl_front']['phase']['output'];
		$agent = $this->cycle->getAgent();

		// 当Action中使用过header或redirect函数，http头信息会放入cycle中，在这步输出http头
		$this->outputHeaders();

		// 支持的输出格式列表 
		$ftList = array(Su_Const::FT_HTML,
				Su_Const::FT_JSON,
				Su_Const::FT_XML,
				Su_Const::FT_TEXT,
				Su_Const::FT_SERIAL,
				Su_Const::FT_BINARY);

		// 输出格式判定优先级
		// 1. 客户端action参数指定
		// 2. http请求Accept头信息 
		// 3. 对应请求类型指定默认
		// 4. 默认输出格式
		if ($output['allow_format'] && (isset($_GET['format']) && in_array($_GET['format'], $ftList))) {
			$format = $_GET['format'];
		} elseif ($this->cycle->getMeta(Su_Const::META_FORMAT)) {
			$format = $this->cycle->getMeta(Su_Const::META_FORMAT);
		} elseif (isset($output['format'][$agent])) {
			$format = $output['format'][$agent];
		} else {
			$format = $output['format']['default'];
		}

		// 根据不同格式 做输出处理 
		switch ($format) {
			case Su_Const::FT_HTML :
				return $this->outputHtml();
			case Su_Const::FT_JSON :
				return $this->outputJson();
			case Su_Const::FT_XML :
				return $this->outputXml();
			case Su_Const::FT_TEXT :
				return $this->outputText();
			case Su_Const::FT_SERIAL :
				return $this->outputSerial();
			case Su_Const::FT_BINARY :
				return $this->outputBinary();
			default :
				throw new Su_Ctrl_Phase_Exc('output format error' . $format, 500);
		}
	}

	/**
	 * 输出http头
	 */
	protected function outputHeaders()
	{
		$headers = $this->cycle->getOutgoingHeaders();
		if (is_array($headers)) {
			foreach ($headers as $val) {
				header($val);
			}
		}
	}

	/**
	 * outputHtml html格式的输出 
	 */
	protected function outputHtml()
	{
		header("Content-Type: text/html; charset=UTF-8");
		$this->outputCommon(Su_Const::FT_HTML);
	}

	/**
	 * outputJson 
	 */
	protected function outputJson()
	{
		header("Content-Type: application/json; charset=UTF-8");
		echo json_encode($this->getInterfaceData());
	}

	/**
	 * outputXml 
	 */
	protected function outputXml()
	{
		header("Content-Type: text/xml; charset=UTF-8");
		echo self::toXml($this->getInterfaceData());
	}

	/**
	 * outputText 
	 */
	protected function outputText()
	{
		header("Content-Type: text/plain; charset=UTF-8");
		echo "<pre>\n";
		var_export($this->getInterfaceData());
		echo "</pre>\n";
	}

	/**
	 * outputSerial 
	 */
	protected function outputSerial()
	{
		header("Content-Type: text/plain; charset=UTF-8");
		echo serialize($this->getInterfaceData());
	}

	/**
	 * outputBinary 输出二进制内容
	 */
	protected function outputBinary()
	{
		echo $this->cycle->getResponse();
	}

	/**
	 * outputCommon 使用smarty几种类型输出的公共处理
	 */
	protected function outputCommon($format)
	{
		$tpl = $this->cycle->getMeta(Su_Const::META_TEMPLATE);
		if ($tpl) {
			$this->display($format);
		} else {
			$response = $this->cycle->getResponse();
			if (is_string($response)) {
				echo $response;
			} else {
				echo "<pre>\n";
				var_export($response);
				echo "</pre>\n";
			}
		}
	}

	/**
	 * getInterfaceData 
	 */
	protected function getInterfaceData()
	{
		if ($fault = $this->cycle->getMeta(Su_Const::META_FAULT)) {
			return $fault;
		} else {
			return array('code' => 200, 'data' => $this->cycle->getResponse());
		}
	}

	/**
	 * fault 异常输出
	 */
	public function fault($fault)
	{
		$this->cycle->addOutgoingHeader(array());
		if (isset($this->conf['fault']['class'])) {
			$className = $this->conf['fault']['class'];
			$action = new $className($this->cycle, $fault);
			$action->execute();
		} else {
			$this->cycle->setResponse($fault);
			if (isset($this->conf['fault']['tpl'])) {
				$this->cycle->setMeta(Su_Const::META_TEMPLATE, $this->conf['fault']['tpl']);
			}
		}
	}

	/**
	 * display smarty输出显示
	 */
	protected function display($format)
	{
		// 使用smarty, 设置模板、设置参数 
		$tpl = new Su_Tpl($this->conf['tpl'], $format);
		$response = $this->cycle->getResponse();
		is_array($response) || $response = array('result' => $response);
		foreach ($response as $key => $val) {
			$tpl->assign($key, $val);
		}

		$file = $this->cycle->getMeta(Su_Const::META_TEMPLATE) . '.' . $format;
		restore_error_handler();
		$tpl->display($file);
		$errorType = ENVIRONMENT == 'development' ?  E_ALL | E_STRICT : (E_ALL & ~E_NOTICE) | E_STRICT;
		set_error_handler('Su_Facade::errorHandler', $errorType);
	}

	public static function toXml($data, $rootNodeName = 'root', $xml = null)
	{
		$xml == null && $xml = simplexml_load_string("<$rootNodeName />");
		foreach ($data as $key => $val) {
			if (is_numeric($key)) {
				$key = "node";
			}
			if (is_array($val)) {
				$node = $xml->addChild($key);
				self::toXml($val, $rootNodeName, $node);
			} else {
				$val = htmlentities($val);
				$xml->addChild($key, $val);
			}
		}
		return $xml->asXML();
	}
}
