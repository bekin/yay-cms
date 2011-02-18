<?php
Yii::setPathOfAlias('CmsAssets' , dirname(__FILE__) . '/assets/');   

class CmsModule extends CWebModule
{
	public $version = '0.3-svn';
	public $layout = 'cms';
	public $dateformat = 'd.m.Y G:i:s';
	public $rtepath = false; // Don't use an Rich text Editor
	public $rteadapter = false; // Don't use an Adapter
	public $ckfinderPath = false; // do not use CKFinder

	public function init()
	{
		$this->setImport(array(
			'cms.models.*',
			'cms.components.*',
			'cms.controllers.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			return true;
		}
		else
			return false;
	}
}
