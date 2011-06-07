<?php

Yii::import('application.modules.cms.models.*');
Yii::import('application.modules.cms.controllers.*');

class Cms {
	public static function module() {
		return Yii::app()->getModule('cms');

	}
	public static function t($string, $params = array())
	{
		Yii::import('application.modules.cms.CmsModule');
		return Yii::t('CmsModule.cms', $string, $params);
	}

	public static function render($id = null, $lang = null) {
		echo Cms::get($id, $lang, true);
	}

	public static function get($id = null, $lang = null, $render = false) {
		if($lang === null)
			$lang = Yii::app()->language;

		$column = 'id';
		if(!is_numeric($id))
			$column = 'title_url';

		if($id) {
			$sitecontent = Sitecontent::model()->find(
					$column . ' = :id and language = :lang', array(
						':id' => $id,
						':lang' => $lang));

			// If the sitecontent is not available in the requested language,
			// try to fallback to the first natural found sitecontent in the db
			if(!$sitecontent)
				$sitecontent = Sitecontent::model()->find(
						$column .' = :id', array(
							':id' => $id));

			if(!$sitecontent && Cms::module()->strict404raising)
				throw new CHttpException(404);

			if ($render && $sitecontent->visible != 1)
				throw new CHttpException(404);

			if($sitecontent)
				return $sitecontent->content;	
		}
	}

	// for usage in CMenu Widget
	public static function getMenuPoints($id, $lang = null) {
		if(!$lang)
			$lang = Yii::app()->language;

		$column = 'title_url';
		if(is_numeric($id))
			$column = 'id';
			
			$sitecontent = Sitecontent::model()->find(
					$column.' = :id and language = :lang', array(
						':lang' => $lang,
						':id' => $id,
						));
			$items = array();
			if($sitecontent) {
				$childs = $sitecontent->childs;
				if($childs)  {
					foreach($sitecontent->childs as $child) {
						$items[] = array(
								'visible' => $child->visible,
								'label' => $child->title,
								'url' => array('//cms/sitecontent/view', 'page' => $child->title_url)
								);
					}
				}
			}
			return $items;
	}

	public static function renderMenuPoints($id, $lang = null) {
		if(!$lang)
			$lang = Yii::app()->language;

		if(is_numeric($id))
			$sitecontent = Sitecontent::model()->find(
					'id = :id and language = :lang', array(
						':lang' => $lang,
						':id' => $id,
						));
		$childs = $sitecontent->childs;
		if($childs)  {
			echo '<ul>';
			foreach($sitecontent->childs as $child) {
				printf('<li>%s</li>',
						CHtml::link($child->title, array(
								'/cms/sitecontent/view', 'page' => $child->title_url) ));
			}
			echo '</ul>';
		}
	}
}
