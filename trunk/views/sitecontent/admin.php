<?php
$this->breadcrumbs=array(
	Cms::t('Sitecontent')=>array('index'),
	Cms::t('Manage'),
);
?>

<h2><?php echo Yii::t('CmsModule.cms', 'Manage Sitecontent'); ?></h2>

<?php 
$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'sitecontent-grid',
			'dataProvider'=>$model->search(),
			'filter'=>$model,
			'columns'=>array(
				'id',
				array(
					'name' => 'parent',
					'value' => '$data->Parent ? $data->Parent->title_url : "-"',
					'filter' => CHtml::listData(Sitecontent::model()->findAll(), 'id', 'title_url'),
					),
				array(
					'name' => 'language',
					'filter' => Cms::module()->languages,
					),
				'title',
				array(
					'name'=>'createtime',
					'value'=>'date(Cms::module()->dateformat, $data->createtime)',
					'filter' => false,
				),
				array(
					'name'=>'updatetime',
					'value'=>'date(Cms::module()->dateformat, $data->updatetime)',
					'filter' => false,
				),
				array(
					'name' => 'visible',
					'value' => '$data->itemAlias("visible", $data->visible)',
					'filter' => Sitecontent::itemAlias('visible'),
					),

				array(
					'class'=>'CButtonColumn',
					'viewButtonUrl' => 'Yii::app()->controller->createUrl(
						"//cms/sitecontent/view", array( "page" => $data->title_url))',
					),
				),
			)); ?>

<?php echo CHtml::link('New sitecontent', array('//cms/sitecontent/create')); ?>
