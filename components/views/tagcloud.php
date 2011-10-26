<div class="tag-cloud">
<?php if($tags) { ?>
<ul>
<?php foreach($tags as $tag => $weight) { ?>
	<?php printf('<li>%s</li>',
			CHtml::link($tag, array(
					'//cms/sitecontent/search', 'search' => $tag))); ?>
				<?php } ?>
</ul>
<?php } ?>
</div> 
<div style="clear: both;"></div>
