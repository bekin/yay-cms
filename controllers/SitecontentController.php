<?php

Yii::import('application.modules.cms.models.Sitecontent');
class SitecontentController extends Controller
{
	public $defaultAction='admin';
	private $_model;

	public function beforeAction($action)
	{
		$this->layout = Yii::app()->controller->module->layout;
		return true;
	}

	public function actionSearch() 
	{
		$results = Sitecontent::model()->findAll(
				'content like "%'.trim($_POST['search']).'%"');

		$this->render('results', array(
					'results' => $results,
					'search' => $_POST['search']));
	}
	
	public static function getContent($id) {
		if($model = Sitecontent::model()->findByPk($id)) {
			return $model->content;
		}
	}

	public function filters()
	{
		return array('accessControl');
	}

	public function actionView()
	{
		$model = $this->loadContent();

		$this->breadcrumbs = array($model->title);

		$this->render('view', array(
					'sitecontent' => $model,
					));
	}

	public function actionCreate()
	{
		$model=new Sitecontent;

		$this->performAjaxValidation($model);

		if(isset($_POST['Sitecontent']))
		{
			$model->attributes=$_POST['Sitecontent'];
			$model->createtime = time();
			$model->updatetime = time();

			if(isset(Yii::app()->user->id))
					$model->authorid = Yii::app()->user->id;

			if($model->save())
				$this->redirect(array('admin'));
		}

		if(isset($_GET['position']))
			$model->position = $_GET['position'];

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionUpdate()
	{
		$model=$this->loadContent();

		$this->performAjaxValidation($model);

		if(isset($_POST['Sitecontent']))
		{
			$model->attributes=$_POST['Sitecontent'];
			$model->updatetime = time();
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			$this->loadContent()->delete();

			if(!isset($_POST['ajax']))
				$this->redirect(array('index'));
		}
		else
			throw new CHttpException(400,Yii::t('App','Invalid request. Please do not repeat this request again.'));
	}

	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Sitecontent');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	public function actionAdmin()
	{
		$model=new Sitecontent('search');
		if(isset($_GET['Sitecontent']))
			$model->attributes=$_GET['Sitecontent'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function loadContent()
	{
		if($this->_model===null)
		{
			if(is_array(@$_GET['id']))
				$this->_model = Sitecontent::model()->find('id = :id and language = :language',  array(
							':id' => $_GET['id']['id'],
							':language' => $_GET['id']['language'],
							));
			if(!is_array(@$_GET['id'])) 
				$this->_model = Sitecontent::model()->find('id = :id',  array(
							':id' => $_GET['id'],
							));

			if($this->_model === null)
				$this->_model = Sitecontent::model()->find("title_url = :page", array(
							':page' => $_GET['page']));
			if($this->_model===null)
				throw new CHttpException(404,Yii::t('App','We are sorry. The requested page does not exist.'));
		}
		return $this->_model;
	}

	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='sitecontent-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
