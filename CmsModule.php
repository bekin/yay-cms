<?php
Yii::setPathOfAlias('CmsAssets' , dirname(__FILE__) . '/assets/');   

class CmsModule extends CWebModule
{
	public $version = '0.4';
	public $layout = 'cms';
	public $dateformat = 'd.m.Y G:i:s';
	public $enableHtmlPurifier = true;
	public $rtepath = false; // Don't use an Rich text Editor
	public $rteadapter = false; // Don't use an Adapter
	public $ckfinderPath = false; // do not use CKFinder

	// Which languages do your cms serve?
	public $languages = array('en' => 'English');

	// If a page is requested by CMS::render and not found, should
	// a 404 be raised or the content simply not be delivered?
	public $strict404raising = false;

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
