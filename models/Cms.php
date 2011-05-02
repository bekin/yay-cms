<?php

Yii::import('application.modules.cms.models.*');
Yii::import('application.modules.cms.controllers.*');

	class Cms {
		public static function t($string, $params = array())
		{
			Yii::import('application.modules.cms.CmsModule');
			return Yii::t('CmsModule.cms', $string, $params);
		}

		public static function render($id = null, $return = false, $lang = null) {
			if($lang === null)
				$lang = Yii::app()->language;

			if($id) {
				$sitecontent = Sitecontent::model()->find(
						'id = :id and language = :lang', array(
							':id' => $id,
							':lang' => $lang));

				// If the sitecontent is not available in the requested language,
				// try to fallback to the first natural found sitecontent in the db
				if(!$sitecontent)
					$sitecontent = Sitecontent::model()->find(
							'id = :id', array(
								':id' => $id));

				if(!$sitecontent)
					throw new CHttpException(404);
				else if($return)
					return $sitecontent->content;
				else
					echo $sitecontent->content;	
			}
		}

		public static function renderMenuPoints($id) {
			if(is_numeric($id))
				$sitecontent = Sitecontent::model()->findByPk($id);
			$childs = $sitecontent->childs;
			if($childs)  {
				foreach($sitecontent->childs as $child) {
					printf('<li>%s</li>',
							CHtml::link($child->title, array(
									'/cms/sitecontent/view', 'page' => $child->title_url) ));
				}
			}
		}
	}
