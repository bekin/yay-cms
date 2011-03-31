<?php
$this->breadcrumbs=array(
	Yii::t('CmsModule.cms', 'Sitecontent')=>array('admin'),
	$model->id=>array('view','id'=>$model->id),
	Yii::t('CmsModule.cms', 'Update'),
);

$this->menu=array(
		array(
			'label'=>Yii::t('CmsModule.cms', 'Manage Sitecontent'), 
			'url'=>array('sitecontent/admin')
			),
);
?>

<h2><?php echo Yii::t('CmsModule.cms', 'Update');?> <?php echo $model->title; ?></h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
