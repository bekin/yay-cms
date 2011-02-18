<?php
$this->breadcrumbs=array(
	Yii::t('CmsModule.cms', 'Sitecontent')=>array('admin'),
	Yii::t('CmsModule.cms', 'Create'),
);

$this->menu=array(
		array(
			'label'=>Yii::t('CmsModule.cms', 'Manage Menustructure'), 
			'url'=>array('menustructure/admin')
			),
		array(
			'label'=>Yii::t('CmsModule.cms', 'Manage Sitecontent'), 
			'url'=>array('sitecontent/admin')
			),
);
?>

<h2><?php echo Yii::t('CmsModule.cms', 'Create new Sitecontent'); ?></h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
