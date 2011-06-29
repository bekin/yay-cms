<?php
	if(Cms::module()->rtepath != false)
		Yii::app()->clientScript-> registerScriptFile(Yii::app()->getModule('cms')->rtepath, CClientScript::POS_HEAD); 
	if(Cms::module()->rteadapter != false)
		Yii::app()->clientScript-> registerScriptFile(Yii::app()->getModule('cms')->rteadapter, CClientScript::POS_HEAD); 
	if(Cms::module()->rtescript != false)
		Yii::app()->clientScript->registerScript('rte_init', Cms::module()->rtescript);
?>
<div class="form">
	<?php $form=$this->beginWidget('CActiveForm', array(
				'id'=>'sitecontent-form',
				'enableAjaxValidation'=>true,
				));
	?>

	<?php echo $form->errorSummary($model); ?>


	<fieldset class="box-fieldset-right">
    	<legend ><?php echo Cms::t('Metatags'); ?></legend>
		<?php
			$metatags = $model->metatags;
			
			if(!$metatags)
				$metatags = array();

			foreach(Cms::module()->allowedMetaTags as $metatag) {
				echo '<div class="row">';
				echo $form->labelEx($model, $metatag);	
				echo CHtml::textField("Sitecontent[metatags][$metatag]", 
						isset($metatags[$metatag]) ? (string) $metatags[$metatag] : '');	
				echo $form->error($model, $metatag);	
			}
		?>
	</fieldset>

	<fieldset class="box-fieldset-left">
    	<legend ><?php echo Cms::t('Site options'); ?></legend>
        
        <div class="row">
            <?php echo $form->labelEx($model,'parent'); ?>
            <?php echo CHtml::activeDropDownList($model,
                    'parent',
                    CHtml::listData(Sitecontent::model()->findAll(),
                        'id',
                        'title'),
                    array(
                        'empty' => array(
                            '0' => ' - ')));
            ?>
            <?php echo $form->error($model,'header'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'visible'); ?>
            <?php echo $form->dropDownList($model, 'visible', $model->itemAlias('visible')); ?>
            <?php echo $form->error($model,'visible'); ?>
        </div>

        <div class="row password" style="display: none;">
            <?php echo $form->labelEx($model,'password'); ?>
            <?php echo $form->passwordField($model, 'password'); ?>
            <?php echo $form->labelEx($model,'password_repeat'); ?>
            <?php echo $form->passwordField($model, 'password_repeat'); ?>
            
            <div class="hint">
                <?php echo Cms::t('Enter a password to allow access by password'); ?>
            </div>
            
            <div class="hint">
                <?php echo Cms::t('Leave it empty to only allow access by non-guest users'); ?>
            </div>
            
            <?php echo $form->error($model,'password'); ?>
        </div>

        <?php Yii::app()->clientScript->registerScript('dropdown_visible', "
            if($('#Sitecontent_visible').val() == 2)
                $('.password').show();
        $('#Sitecontent_visible').change(function() {
            if($(this).val() == 2)
                $('.password').show(500);
            else
                $('.password').hide(500);
        });
        ");
        ?>

        <div class="row">
            <?php echo $form->labelEx($model,'position'); ?>
            <?php for($i = 0; $i < 10; $i++) $position[] = $i; ?>
            <?php echo CHtml::dropDownList('Sitecontent[position]',
                    $model->position,
                    $position); ?>
            <?php echo $form->error($model,'position'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'id'); ?>
            <?php echo $form->textField($model,'id',array('size'=>5,'maxlength'=>11)); ?>
            <?php echo $form->error($model,'id'); ?>
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
            <?php echo $form->labelEx($model,'language'); ?>
            <?php echo $form->dropDownList($model,'language',Cms::module()->languages); ?>
            <?php echo $form->error($model,'language'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'content'); ?>
            <?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>
            <?php echo $form->error($model,'content'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton($model->isNewRecord 
                    ? Yii::t('CmsModule.cms', 'Create sitecontent') 
                    : Yii::t('CmsModule.cms', 'Save sitecontent')); ?>
        </div>
	</fieldset>
    
    <div class="clear"></div>
    <?php $this->endWidget(); ?>
</div><!-- form -->