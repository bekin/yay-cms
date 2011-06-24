<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <?php Cms::register('cms.css'); ?>
</head>

<body>
	<?php $this->widget('zii.widgets.CBreadcrumbs', array(
		'links'=>$this->breadcrumbs,
	)); ?><!-- breadcrumbs -->

	<?php echo $content; ?>

	<div style="float:right;">
		<?php 
			$this->widget('zii.widgets.CMenu',array(
				'items'=>$this->menu
				)
			);
		?>
	</div>

	<div class="clear"> </div>

	<div id="footer">
		<p> CMS Module by thyseus@gmail.com </p>
		<?php echo Yii::powered(); ?>
	</div><!-- footer -->


</body>
</html>

<?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/form.css'); ?>
