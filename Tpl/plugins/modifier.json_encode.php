<?php
/**
 * smarty_modifier_json_encode 
 * 
 * @param mixed $string 
 * @access public
 * @return void
 */
function smarty_modifier_json_encode($string)
{
	return json_encode($string);
} 
