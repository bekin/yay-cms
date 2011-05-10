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

	public function tableName()
	{
		return 'sitecontent';
	}

	public function rules()
	{
		return array(
			array('id, position, title, language', 'required'),
			array('parent, position, createtime, updatetime', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('content, title_url, title_browser', 'safe'),
			array('id, position, title, content, authorid, createtime, updatetime, language', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'parent' => array(self::BELONGS_TO, 'Sitecontent', 'parent'),
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
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('position',$this->position);
		$criteria->compare('language',$this->language);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('authorid',$this->authorid);
		$criteria->compare('createtime',$this->createtime);
		$criteria->compare('updatetime',$this->updatetime);

		return new CActiveDataProvider('Sitecontent', array(
			'criteria'=>$criteria,
		));
	}
}
