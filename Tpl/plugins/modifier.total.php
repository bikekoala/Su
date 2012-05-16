	<?php
function smarty_modifier_total($meta)
{
	if($meta['pages'] > 1){
		return $meta['pages'];
	}else{
		return 0;
	}
}
?>
