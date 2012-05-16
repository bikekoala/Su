<?php
/**
 * 返回随机内容
 */
function smarty_modifier_random($url, $params)
{
	if (is_array($params)) {
		return str_replace(',', ':', $params[array_rand($params)]);
	}
	return $params;
} 
