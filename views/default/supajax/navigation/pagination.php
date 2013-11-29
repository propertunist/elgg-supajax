<?php
$hidden_paginator = elgg_extract('hidden_paginator', $vars);
$time = time();

?>

<div class="atPaginatorWrapper">
	<div class="atLoadMoreBtn">
		<a href="javascript:void(0)" id="at-load-more" data-pagination="<?php echo $time ?>">
			<?php echo elgg_echo('supajax:load_more') ?>
		</a>
		<span class="atAjaxLoader hidden">
		  <?php echo elgg_view("graphics/ajax_loader", array("hidden" => false)); ?>
		</span>
	</div>

	<div class="atHiddenPaginator" data-pager="<?php echo $time ?>">
		<?php echo $hidden_paginator; ?>
	</div>

</div>

<div id="output"></div>