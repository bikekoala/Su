<?PHP
/**
 * pdo数据库抽象类的封装
 */
final class Su_Db extends PDO
{
	/**
	 * 构造函数
	 */
	public function __construct($dsn) 
	{
		$temp = parse_url($dsn);
		if ($temp['scheme'] == 'mysql') {
			parse_str($temp['query'], $query);
			$user = isset($temp['user']) ? $temp['user'] : 'root';
			$pass = isset($temp['pass']) ? $temp['pass'] : '';
			$port = isset($temp['port']) ? $temp['port'] : '3306';
			$charset = isset($query['charset']) ? $query['charset'] : 'UTF-8';
			$str = 'mysql:dbname=' . $query['dbname'] . ';host=' . $temp['host'] . ';port=' . $port . ';charset=' . $charset;
			$options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$options[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
			parent::__construct($str, $user, $pass, $options);
		} else {
			parent::__construct($dsn);
		}
	}

	/**
	 * 获取对象实例静态方法
	 */
	public static function getInstance($dsn)
	{
		static $instances = array();
		if ( ! isset($instances[$dsn])) {
			$instances[$dsn] = new self($dsn);
		}
		return $instances[$dsn];
	}

	/**
	 * 根据变量数组组合sql复制语句
	 */
	public static function genSqlValueStr($keys, &$vals)
	{
		$columns = array();
		foreach ($keys as $key) {
			if (isset($vals[$key])) {
				$columns[] = '`' . $key . '`=:' .$key;
			}
		}
		return implode(',', $columns);
	}

	/**
	 * 给变量绑定数据
	 */
	public static function genSqlBindValue(PDOStatement $sth, $keys, &$vals)
	{
		foreach ($keys as $key) {
			if (isset($vals[$key])) {
				$sth->bindValue(':' . $key, $vals[$key]);
			}
		}
	}
}
