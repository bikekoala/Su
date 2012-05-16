<?PHP
/**
 * Su_Tpl smary模板类封装,符合Su的类方式
 */
include 'Su/Tpl/Smarty.class.php';

final class Su_Tpl extends Smarty
{
	public function __construct($conf, $format = null)
	{
		parent::__construct();
		$this->left_delimiter = $conf['left_delimiter'];
		$this->right_delimiter = $conf['right_delimiter'];
		$this->template_dir = isset($conf['template_dir_' . $format]) ? $conf['template_dir_' . $format] : $conf['template_dir'];
		$this->compile_dir = isset($conf['compile_dir_' . $format]) ? $conf['compile_dir_' . $format] : $conf['compile_dir'];
		$static = isset($conf['static_' . $format]) ? $conf['static_' . $format] : $conf['static'];
		$this->assign('_static', $static);
		isset($conf['gstatic']) && $this->assign('_gstatic', $conf['gstatic']);// 全局静态文件地址
		isset($conf['build']) && $this->assign('_build', $conf['build']);// 构建版本号

		if (isset($conf['output_filter'])) {
			$this->register->outputFilter($conf['output_filter']);
		}

		// 注册资源句柄 
		if (isset($conf['resource'])) {
			foreach ($conf['resource'] as $key => $val) {
				$this->register->resource($key, $val);
			}
		}
	}
}
