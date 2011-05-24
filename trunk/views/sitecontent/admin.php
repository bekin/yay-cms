<?php
$this->breadcrumbs=array(
	Cms::t('Sitecontent')=>array('index'),
	Cms::t('Manage'),
);

$this->menu=array(
		array(
			'label'=>Cms::t('Manage Sitecontent'), 
			'url'=>array('sitecontent/admin')
			),
		array(
			'label'=>Cms::t('Create new Sitecontent'),
			'url'=>array('create')),
		);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('sitecontent-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h2><?php echo Yii::t('CmsModule.cms', 'Manage Sitecontent'); ?></h2>

<?php echo CHtml::link(Yii::t('App','Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php 
$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'sitecontent-grid',
			'dataProvider'=>$model->search(),
			'filter'=>$model,
			'columns'=>array(
				'id',
				array(
					'name' => 'language',
					'filter' => Cms::module()->languages,
					),
				'title',
				array(
					'name'=>'createtime',
					'value'=>'date(Yii::app()->controller->module->dateformat, $data->createtime)',
					'filter' => false,
				),
				array(
					'name'=>'updatetime',
					'value'=>'date(Yii::app()->controller->module->dateformat, $data->updatetime)',
					'filter' => false,
				),
				array(
					'name' => 'visible',
					'value' => '$data->itemAlias("visible", $data->visible)',
					'filter' => Sitecontent::itemAlias('visible'),
					),

				array(
					'class'=>'CButtonColumn',
					),
				),
			)); ?>

<?php echo CHtml::link('New sitecontent', array('//cms/sitecontent/create')); ?>
