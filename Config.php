<?PHP
/**
 * Model配置类的抽象父类
 */
abstract class Su_Config implements ArrayAccess
{
	/**
	 * data 存放原始数据的数组
	 */
	protected $data;

	/**
	 * 构造函数 禁止直接实例化
	 */
	protected function __construct() 
	{
	}

	/**
	 * 定义子类必须都是单例模式
	 */
	public static function single()
	{
	} 

	/**
	 * isset($arr['test']) 接口实现
	 */
	public function offsetExists($key)
	{
		return isset($this->data[$key]); 
	}

	/**
	 * 获取数据接口实现
	 */
	public function offsetGet($key)
	{
		return $this->data[$key];
	}

	/**
	 * 禁止设置数据变量
	 */
	public function offsetSet($key, $val)
	{
		$key = $val = null;
		throw new Su_Config_Exc('no support offsetSet', 400);
	}

	/**
	 * 禁止删除
	 */
	public function offsetUnset($key)
	{
		$key = null;
		throw new Su_Config_Exc('no support offsetUnset', 400);
	}

	/**
	 * 配置信息重写方法
	 */
	public function rewrite($params)
	{
		foreach ($params as $key => $val) {
			$this->data[$key] = $val;
		}
	}

	/**
	 * 魔术方法的实现
	 * 实例支持 $obj->name 方式调用
	 */
	public function __get($key)  
	{
		return (object) $this->data[$key];
	}
}
