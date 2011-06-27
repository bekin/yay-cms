<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <?php Cms::register('cms.css'); ?>
</head>

<body id="cms">
	<div id="container">
    	<div id="header">
        	<h1><?php echo Chtml::link('Yiicms',array(
			Cms::module()->sitecontentAdminRoute))  ?></h1>
        </div>
        
        <div id="navigation">
            <?php 
                $this->widget('zii.widgets.CMenu',array(
                    'items'=>$this->menu
                    )
                );
            ?>
        </div><!-- navigation -->
    
        <div class="clear"> </div>
        
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
            'links'=>$this->breadcrumbs,
        )); ?><!-- breadcrumbs -->
        
        
    	<div id="content">
        	<?php echo $content; ?>
    	</div><!-- content -->
         
    	<div class="clear"> </div>
       
    
        <div id="footer">
            <p> CMS Module by thyseus@gmail.com </p>
            <?php echo Yii::powered(); ?>
        </div><!-- footer -->
    </div><!-- container -->


</body>
</html>

<?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/form.css'); ?>
