<?php
/**
 *  
 */
function smarty_modifier_multi_xhtml($meta, $head = null, $exclude = array(), $include = null)
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
			} if (!in_array($key, $exclude)) {
				$key != 'page' && $head .=  $key."=".urlencode($val)."&";
			}
		}
	}
	$str = '';
	if ($meta['page'] != 1) {
		$str .= "<a class='prev_available' href='{$head}page={$pre}'>上一页</a>";
	} else {
		$str .= "<a class='prev_unavailable' href='{$head}page=1'>上一页</a>";
	}
	if ($meta['page'] < $meta['pages']) {
		$str .= " <a class='next_available' href='{$head}page={$next}'>下一页</a> ";
	} else {
		$str .= " <a class='next_unavailable' href='{$head}page={$next}'>下一页</a> ";
	}
	return '<div class="mxpage">' . $str . '</div>';
} 
