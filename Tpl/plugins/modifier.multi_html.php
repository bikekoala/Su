<?php
/**
 * smarty_modifier_multi_html 
 * 分页插件
 * 
 * @param array $meta 分页数据源
 * @param string $head  指定url前缀,同时会忽略get参数和最后两个参数
 * @param int $size  分页显示大小
 * @param array $exclude 指定要排除的get参数
 * @param array $include 指定要包含的get参数, 忽略其他参数
 * @access public
 * @return void
 */
function smarty_modifier_multi_html($meta, $head = null, $size = 10, $exclude= array(), $include = null)
{
	//检查是否需要分页
	if ($meta['pages'] <= 1) return; 
	$pre = $meta['page'] - 1;
	$next = $meta['page'] + 1;
	if ($head === null) {
		$head = '?';
		foreach ($_GET as $key => $val){
			if (is_array($include && in_array($key, $include))) {
				$head .=  $key."=".urlencode($val)."&";
			}if (!in_array($key, $exclude)) {
				$key != 'page' && $head .=  $key."=".urlencode($val)."&";
			}
		}
	}

	if ($meta['page'] != 1) {
		//$str .= "<a class='first_available' href='{$head}page=1'>首页</a>";
		$str .= "<a class='prev_available' href='{$head}page={$pre}'>上一页</a>";
	}
	//以当前页为中心 取10个导航页链接
	if ($size > 0){
		$tmp = floor($size/2);
		$start = max(min($meta['page'] - $tmp, $meta['pages'] - $size + 1), 1);
		$end = min($start + $size - 1, $meta['pages']);
		for ($i = $start; $i <= $end; $i++) {
			if($i == $meta['page']) {
                $str .= "<span class='mxpage_cur'>". $i ."</span>";
			}else {
                $str .= "<a href='{$head}page=$i'>". $i ."</a>";
			}
		}
	}
	if ($meta['page'] < $meta['pages']) {
		$str .= "<a class='next_available' href='{$head}page={$next}'>下一页</a> ";
	//	$str .= "<a class='last_available' href='{$head}page={$meta['pages']}'>尾页</a>";
	}
	return '<div class="mxpage">' . $str . '</div>';
} 
