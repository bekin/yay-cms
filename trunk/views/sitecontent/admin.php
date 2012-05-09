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


?>

<h2><?php echo Cms::t('Manage Sitecontent'); ?></h2>

<?php 
$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'sitecontent-grid',
			'dataProvider'=>$model->search(),
			'template' => '{summary} {pager} <br /> {items} {pager}',
			'filter'=>$model,
			'columns'=>array(
				array(
					'name' => 'id',
					'headerHtmlOptions' => array(
						'style' => 'width:25px;',
						),
					),
				array(
					'name' => 'parent',
					'value' => '$data->Parent ? $data->Parent->title_url : "-"',
					'filter' => Sitecontent::listData(),
					'headerHtmlOptions' => array(
						'style' => 'width:100px;',
						),
					),
				array(
					'name' => 'language',
					'filter' => Cms::module()->languages,
					'headerHtmlOptions' => array(
						'style' => 'width:25px;',
						),
					),
				'title',
				'title_url',
				array(
					'name'=>'createtime',
					'value'=>'date(Cms::module()->dateformat, $data->createtime)',
					'filter' => false,
					'headerHtmlOptions' => array(
						'style' => 'width:85px;',
						),
				),
				array(
					'name'=>'updatetime',
					'value'=>'date(Cms::module()->dateformat, $data->updatetime)',
					'filter' => false,
					'headerHtmlOptions' => array(
						'style' => 'width:85px;',
						),
				),
				array(
					'name' => 'visible',
					'value' => '$data->itemAlias("visible", $data->visible)',
					'filter' => Sitecontent::itemAlias('visible'),
					'headerHtmlOptions' => array(
						'style' => 'width:50px;',
						),

					),
			array(
					'name' => 'tags',
					'headerHtmlOptions' => array(
						'style' => 'width:100px;',
						),
					),
				array(
					'class'=>'CButtonColumn',
					'viewButtonUrl' => 'Yii::app()->controller->createUrl(
						"//cms/sitecontent/view", array( "page" => $data->title_url))',
					),
				),
			)); ?>

			<?php echo CHtml::link(
					Cms::t('Create new Sitecontent'), array(
						'//cms/sitecontent/create'), array('tabindex' => 1)); ?>
