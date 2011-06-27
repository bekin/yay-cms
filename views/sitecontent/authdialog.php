<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'cms-auth-dialog',
    'options'=>array(
        'title'=>Cms::t('Please enter the password'),
        'autoOpen'=>false,
    ),
));

		echo Cms::t('Please enter a Password to open this resource');
		echo '<br /><hr /><br />';
		echo CHtml::beginForm(array('//cms/sitecontent/auth'));
		echo CHtml::hiddenField('returnUrl', $this->createAbsoluteUrl(''));
    echo CHtml::label(Cms::t('Password'), 'password') . '<br />';
    echo CHtml::passwordField('password');
		echo CHtml::submitButton('Ok');
		echo CHtml::endForm();

$this->endWidget('zii.widgets.jui.CJuiDialog');

echo CHtml::link($label, '#', array(
   'onclick'=>'$("#cms-auth-dialog").dialog("open"); return false;',
));
?>
