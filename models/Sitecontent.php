<?php

class Sitecontent extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
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
		return parent::beforeValidate();	
	}

	public function tableName()
	{
		return 'sitecontent';
	}

	public function rules()
	{
		return array(
				array('id, position, title, language', 'required'),
				array('parent, position, createtime, updatetime, visible', 'numerical', 'integerOnly'=>true),
				array('title', 'length', 'max'=>255),
				array('metatags', 'safe'),
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
				'visible' => Yii::t('CmsModule.cms', 'Visible'),
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
					));
	}
}
