<?php

Yii::import('application.modules.cms.models.*');
Yii::import('application.modules.cms.controllers.*');

	class Cms {
		public static function t($string, $params = array())
		{
			Yii::import('application.modules.cms.CmsModule');
			return Yii::t('CmsModule.cms', $string, $params);
		}

		public static function render($id = null, $return = false) {
			if($id) {
				$sitecontent = Sitecontent::model()->findByPk($id);
				if($return)
					return $sitecontent->content;
				else
					echo $sitecontent->content;	
			} else
				throw new CHttpException(404);
		}

		public static function renderMenuPoints($id) {
			if(is_numeric($id))
				$sitecontent = Sitecontent::model()->findByPk($id);
			$childs = $sitecontent->childs;
			if($childs)  {
				foreach($sitecontent->childs as $child) {
					printf('<li>%s</li>',
							CHtml::link($child->title, array(
									'/cms/sitecontent/view', 'id' => $child->id) ));
				}
			}
		}
	}
