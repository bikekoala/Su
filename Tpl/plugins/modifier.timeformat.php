<?php
/**
 * smarty_modifier_timeformat  时间戳格式化
 * 
 * @param intval $timestam 
 * @access public
 * @return string
 */
function smarty_modifier_timeformat($time)
{
	$now = time();
	$timediff = max($now - $time, 0);       
	$pre_time = 86400; //1天        
	$cut_time = $now - $pre_time;   
	$dateformat = 'Y-n-j'; 
	if($timediff < 3600){ //一小时内    
		return ceil($timediff / 60) . '分钟前';
	}else if ($timediff < 86400) { //一天内
		return ceil($timediff / 3600) . '小时前';
	}else {
		return date('Y-n-d', $time);
	}
}
