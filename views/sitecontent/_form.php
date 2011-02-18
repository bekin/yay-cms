<?php
if(Yii::app()->getModule('cms')->rtepath != false)
	Yii::app()->clientScript-> registerScriptFile(Yii::app()->getModule('cms')->rtepath, CClientScript::POS_HEAD); 
if(Yii::app()->getModule('cms')->rteadapter != false)
	Yii::app()->clientScript-> registerScriptFile(Yii::app()->getModule('cms')->rteadapter, CClientScript::POS_HEAD); 

?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'sitecontent-form',
	'enableAjaxValidation'=>true,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
	<?php echo $form->labelEx($model,'parent'); ?>
	<?php echo CHtml::activeDropDownList($model,
			'parent',
				CHtml::listData(Sitecontent::model()->findAll(),
					'id',
					'title'),
				array(
					'empty' => array(
						'0' => ' - '))); ?>
	<?php echo $form->error($model,'header'); ?>
	</div>

	<div class="row" style="float:right;">
		<?php echo $form->labelEx($model,'position'); ?>
		<?php for($i = 0; $i < 10; $i++) $position[] = $i; ?>
		<?php echo CHtml::dropDownList('Sitecontent[position]',
				$model->position,
				$position); ?>
		<?php echo $form->error($model,'position'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title_browser'); ?>
		<?php echo $form->textField($model,'title_browser',array('size'=>60,'maxlength'=>80)); ?>
		<?php echo $form->error($model,'title_browser'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'title_url'); ?>
		<?php echo $form->textField($model,'title_url',array('size'=>60,'maxlength'=>80)); ?>
		<?php echo $form->error($model,'title_url'); ?>
	</div>


	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
		<?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
<?php Yii::app()->clientScript->registerScript("ckeditor", "$('#Sitecontent_content').ckeditor();"); ?>
		<?php echo $form->error($model,'content'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord 
		? Yii::t('CmsModule.cms', 'Create sitecontent') 
		: Yii::t('CmsModule.cms', 'Save sitecontent')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
