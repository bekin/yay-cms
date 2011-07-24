<?php

class Sitecontent extends CActiveRecord
{
	public $password;
	public $password_repeat;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function isVisible() {
		if(Yii::app()->user->id == 1)
			return true;
		if($this->visible == 3 || $this->visible == 4)
			return true;
		else if($this->visible == 2) {
			if(Yii::app()->user->hasState('yay_cms_password')) {
				$pwd = Yii::app()->user->getState('yay_cms_password');
				if ($pwd == $this->password)
					return true;
			}
			if($this->password === null && !Yii::app()->user->isGuest)
				return true;

		}	
		return false;
	}

	public function primaryKey() {
		return array('id', 'language');	
	}

	public function behaviors() {
		return array(
				'CSerializeBehavior' => array(
					'class' => 'application.modules.cms.components.CSerializeBehavior',
					'serialAttributes' => array(
						'metatags')));
	}

	public static function itemAlias($alias, $value = -10) {
		// - 10 is needed to avoid that a sitecontent has a value of NULL and
		// a array gets returned accidentally
		$visible = array(
				'0' => Cms::t('System Page'),
				'1' => Cms::t('Hidden'),
				'2' => Cms::t('Restricted'),
				'3' => Cms::t('Public'),
				'4' => Cms::t('Redirect'),
				);

		if($alias == 'visible' && $value === -10)
			return $visible;

		if($alias == 'visible' && $value !== null)
			return $visible[$value];
	}

	public function beforeValidate() {
		if(Cms::module()->enableHtmlPurifier) {
			$purifier = new CHtmlPurifier();
			$this->content = $purifier->purify($this->content);
		}	

		if($this->visible == 2)
			$this->scenario = 'restricted';

		if($this->redirect && $this->redirect == $this->id)
			$this->addError('redirect', Cms::t('Redirect to self is not allowed'));
		return parent::beforeValidate();	
	}

	public function redirectUrl() {
		if(is_numeric($this->redirect)) {
			$sc = Sitecontent::model()->find('id = :id', array(
						':id' => $this->redirect));
			return Yii::app()->controller->createAbsoluteUrl(
					Cms::module()->sitecontentViewRoute, array(
						'page' => $sc->title_url));; 
		}
		else
			return $this->redirect;
	}

	public function registerMetaTags() {
		if(isset(Yii::app()->controller->pageTitle))
			Yii::app()->controller->pageTitle = $this->title_browser;

		if($this->metatags)
			foreach($this->metatags as $tag => $content)  {
				if($content == '' && isset(Cms::module()->defaultMetaTags[$tag]))
					$content = Cms::module()->defaultMetaTags[$tag];					
				Yii::app()->clientScript->registerMetaTag($content, $tag);
			}
		return true;

	}

	public function tableName()
	{
		return 'sitecontent';
	}

	public function getBreadcrumbs($route = '//cms/sitecontent/view') {
		$breadcrumbs = array();
	$breadcrumbs[$this->title] = $this->getAbsoluteUrl($route);

	return $breadcrumbs;
}

public function getAbsoluteUrl($route = '//cms/sitecontent/view') {
return Yii::app()->controller->createAbsoluteUrl($route, array(
			'page' => $this->title_url));
}

public function getParentTitles() {
	$titles = array($this->title_url);
	if($this->parent)
		$titles = array_merge($titles, $this->Parent->getParentTitles());

	unset ($titles[0]);
	return $titles;
}

	public function getUrl($route = '//cms/sitecontent/view') {
		if($this->visible == 4)
			return $this->redirectUrl();
	return $this->getAbsoluteUrl($route);
}


public function getChildTitles() {
	$titles = array($this->title_url);
	if($this->childs)
		foreach($this->childs as $child)
			$titles = array_merge($titles, $child->getChildTitles());

	return $titles;
}

public function rules()
{
	return array(
			array('id, position, title, language', 'required'),
			array('parent, position, createtime, updatetime, visible', 'numerical', 'integerOnly'=>true),
			array('password, password_repeat', 'length', 'max' => 255, 'on' => 'restricted'),
			array('password, password_repeat', 'safe'),
			array('title, redirect', 'length', 'max'=>255),
			array('metatags, redirect', 'safe'),
			array('content, title_url, title_browser', 'safe'),
			array('id, position, title, metatags, content, authorid, createtime, updatetime, language', 'safe', 'on'=>'search'),
			);
}

public function relations()
{
	return array(
			'Parent' => array(self::BELONGS_TO, 'Sitecontent', 'parent'),
			'childs' => array(self::HAS_MANY, 'Sitecontent', 'parent'),
			);
}

public function attributeLabels()
{
	return array(
			'id' => '#',
			'parent' => Yii::t('CmsModule.cms', 'Parent'), 
			'position' => Yii::t('CmsModule.cms', 'Position'),
			'title' => Yii::t('CmsModule.cms', 'Title'),
			'title_url' => Yii::t('CmsModule.cms', 'URL title'),
			'title_browser' => Yii::t('CmsModule.cms', 'Browser title'),
			'content' => Yii::t('CmsModule.cms', 'Content'),
			'authorid' => Yii::t('CmsModule.cms', 'Authorid'),
			'createtime' => Yii::t('CmsModule.cms', 'Createtime'),
			'updatetime' => Yii::t('CmsModule.cms', 'Updatetime'),
			'language' => Yii::t('CmsModule.cms', 'Language'),
			'visible' => Cms::t('Visible'),
			'redirect' => Cms::t('Redirect'),
			'password' => Cms::t('Password'),
			'password_repeat' => Cms::t('Repeat Password'),
			);
}

public function search()
{
	$criteria=new CDbCriteria;

	$criteria->compare('id',$this->id);
	$criteria->compare('parent',$this->parent);
	$criteria->compare('position',$this->position);
	$criteria->compare('language',$this->language);
	$criteria->compare('title',$this->title,true);
	$criteria->compare('metatags',$this->metatags,true);
	$criteria->compare('content',$this->content,true);
	$criteria->compare('authorid',$this->authorid);
	$criteria->compare('createtime',$this->createtime);
	$criteria->compare('updatetime',$this->updatetime);
	$criteria->compare('visible',$this->visible);

	return new CActiveDataProvider('Sitecontent', array(
				'criteria'=>$criteria,
				'pagination' => array(
					'pageSize' => 25 
					)
				));
}
}
