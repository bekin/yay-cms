<?php
$this->breadcrumbs = array(Yii::t('CmsModule.cms', 'Search results'));

if($results != array()) {
	printf('<h2>%s %s</h2>',
			count($results),
			Yii::t('CmsModule.cms', 'Results:'));

	echo '<ul>';	
	foreach($results as $result) {
		printf('<li>%s</li>',
				CHtml::link($result->title, array(
						'//cms/sitecontent/view', 'id' => $result->id, 'highlight' => $search)));
	}
	echo '</ul>';	
} else {
	echo Yii::t('CmsModule.cms', 'No results found');
}
?>
