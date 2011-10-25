<?php

Yii::import('application.modules.cms.models.Sitecontent');
class SitecontentController extends Controller
{
	public $defaultAction='admin';
	public $pageTitle = '';
	private $_model;

	public function beforeAction($action)
	{
		$this->layout = Cms::module()->layout;
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

	public function actionAuth() {
		if(isset($_POST['password'])) {
			$password = md5($_POST['password']);
			Yii::app()->user->setState('yay_cms_password', $password);

			$valid = false;
			foreach(Sitecontent::model()->findAll('visible = 2') as $content) 
				if($content->password !== null) 
					if($password == $content->password)
						$valid = true;

			if($valid)
				Cms::setFlash('The password is correct');
			else
				Cms::setFlash('The password is incorrect');

			$this->redirect($_POST['returnUrl']);
		}
	}

	public function accessRules() {
		return array(
				array('allow',
					'actions'=>array('view', 'auth'),
					'users'=>array('*'),
					),
				array('allow',
					'actions'=>array('update', 'create', 'admin', 'delete', 'deleteImage'),
					'users'=>array('admin'),
					),
				array('deny',  // deny all other users
					'users'=>array('*'),
					),
				);

	}

	public function actionView($ajax = false)
	{
		$model = $this->loadContent();

		if($model->visible == 4 && $model->redirect !== null) 
			$this->redirect($model->redirectUrl());

		if($model->title_browser)
			$this->pageTitle = $model->title_browser;

		$model->registerMetaTags();


		if(!isset($this->breadcrumbs))
			$this->breadcrumbs = array($model->title);

		if($ajax)
			$this->renderPartial(Cms::module()->sitecontentViewFile, array(
						'sitecontent' => $model,
						));
		else
			$this->render(Cms::module()->sitecontentViewFile, array(
						'sitecontent' => $model,
						));
	}

	public function checkPassword (&$model, $password, $password_repeat) {
		if(($model->visible == 2 || $model->isNewRecord)
				&& $password == $password_repeat) {
			if($model->password != $password)
				$model->password = md5($password);
			if($password == '' && $password_repeat == '')
				$model->password = null;
		}
		unset($_POST['Sitecontent']['password']);
		unset($_POST['Sitecontent']['password_repeat']);
	}

	public function actionCreate()
	{
		$this->layout = Cms::module()->adminLayout;
		$model = new Sitecontent;

		if($model->visible === null)
			$model->visible = 3;

		$this->performAjaxValidation($model);

		if(isset($_POST['Sitecontent']))
		{
			$model->attributes = $_POST['Sitecontent'];

			$this->checkPassword($model,
					$_POST['Sitecontent']['password'],
					$_POST['Sitecontent']['password_repeat']);

			$model->processImages($_FILES);

			$model->createtime = time();
			$model->updatetime = time();

			if(isset(Yii::app()->user->id))
				$model->authorid = Yii::app()->user->id;

			if($model->validate(null, false) && $model->save()) {
				Cms::setFlash('The page has been created');
				$this->redirect(array('admin'));
			}
		}

		if(isset($_GET['position']))
			$model->position = $_GET['position'];

		if(!isset($model->id) || $model->id === null)
			$model->id = Sitecontent::nextAvailableId();

		$this->render('create',array(
					'model'=>$model,
					));
	}

	public function actionUpdate()
	{
		$this->layout = Cms::module()->adminLayout;
		$model=$this->loadContent();

		$this->performAjaxValidation($model);

		if(isset($_POST['Sitecontent']))
		{
			$model->attributes=$_POST['Sitecontent'];
			$this->checkPassword($model,
					$_POST['Sitecontent']['password'],
					$_POST['Sitecontent']['password_repeat']);

			$model->processImages($_FILES);

			$model->updatetime = time();

			if($model->validate(null, false) && $model->save()) {
				Cms::setFlash('The page has been updated');
				$this->redirect(array('admin'));
			}
		}

		$this->render('update',array(
					'model'=>$model,
					));
	}

	public function actionDeleteImage($model_id, $language, $image) {
		$model = Sitecontent::model()->find(
				'id = :model_id and language = :language', array(
			':model_id' => $model_id,
			':language' => $language));

		if($model && $image) {
			$images = $model->images;
			foreach($images as $i => $img)
				if($image == $img)
					unset($images[$i]);
			$model->images = $images;

			if($model->save(false, array('images')))
				Cms::setFlash('Image has been removed');
			else
				Cms::setFlash('Error while removing image');
		
			$this->redirect(array(
						'//cms/sitecontent/update',
						'id' => $model_id,
						'language' => $language));

}	

	}

	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
			$this->loadContent()->delete();
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
		$this->layout = Cms::module()->adminLayout;
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
			if(isset($_GET['id']) && is_array(@$_GET['id']))
				$this->_model = Sitecontent::model()->find('id = :id and language = :language',  array(
							':id' => $_GET['id']['id'],
							':language' => $_GET['id']['language'],
							));
			if(isset($_GET['id']) && !is_array($_GET['id'])) 
				$this->_model = Sitecontent::model()->find('id = :id',  array(
							':id' => $_GET['id'],
							));

			if($this->_model === null && isset($_GET['page']))
				$this->_model = Sitecontent::model()->find('title_url = :page', array(
							':page' => $_GET['page']));

			if($this->_model===null)
				throw new CHttpException(404,Cms::t(
							'The requested page does not exist'));
		} 

		if($this->_model) {
			if(Yii::app()->user->isGuest && !$this->_model->isVisible()) 
				throw new CHttpException(403, Cms::t(
							'This page is not available to the public'));

			else if(!Yii::app()->user->isGuest 
					&& !$this->_model->isVisible())
				throw new CHttpException(403, Cms::t(
							'Only authenticated members can view this resource'));
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
