<?php                                                                           
if(isset($sc))
 $this->pageTitle = $sc->title . ' - ' . Yii::app()->name;
if(isset($menu))
	 $this->pageTitle = $menu->title. ' - ' . Yii::app()->name;

if(Yii::app()->user->id == 1) // is admin 
{
	if(is_object($sitecontent)) 
	{
		$this->renderPartial('draw', array('sitecontent' => $sitecontent));
		echo "<br />";
		echo CHtml::link(Yii::t('CmsModule.cms', 'Edit this sitecontent'),
				array('/cms/sitecontent/update', 'id' => $sitecontent->id));
	}
	else if ($sitecontent == array()) 
	{
		echo CHtml::link(Yii::t('CmsModule.cms', 'Create new sitecontent here'),
				array('/cms/sitecontent/create', 'position' => $menu->id));

	} else if (is_array($sitecontent))  
	{
		foreach($sitecontent as $sc) 
		{
			$this->renderPartial('draw', array('sitecontent' => $sc));
			echo "<br />";
			echo CHtml::link(Yii::t('CmsModule.cms', 'Edit this sitecontent'),
					array('/cms/sitecontent/update', 'id' => $sc->id));
		}
	}
}
else
{
	if(!is_null($sitecontent))
		if(is_object($sitecontent))
			$this->renderPartial('draw', array('sitecontent' => $sitecontent));
		else
			foreach($sitecontent as $sc)
			{
				$this->renderPartial('draw', array('sitecontent' => $sc));
			}

}

if(isset($menu))
	$this->breadcrumbs = array($menu->title);

