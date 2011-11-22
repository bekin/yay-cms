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

	public function actionSearch($search = null) 
	{
		if($search === null && isset($_POST['search']))
			$search = $_POST['search'];

		if($search) {
			$results = Sitecontent::model()->findAll(
					'visible > 0 and (
						title like :search 
						or title_browser like :search 
						or title_url like :search 
						or content like :search
						or tags like :search)', array(
							':search' => "%$search%"));

			$this->render(Cms::module()->searchResultsViewFile, array(
						'results' => $results,
						'search' => $search));
		} else throw new CHttpException(404);
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
					'actions'=>array('view', 'auth', 'search'),
					'users'=>array('*'),
					),
				array('allow',
					'actions'=>array('update', 'create', 'admin', 'adminImages',
						'delete', 'moveImage', 'deleteImage', 'unlinkImage'),
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

		// update view counter
		$model->views++;
		$model->save(false, array('views'));

		if($ajax)
			$this->renderPartial(Cms::module()->sitecontentViewFile, array(
						'sitecontent' => $model,
						));
		else
			$this->render(Cms::module()->sitecontentViewFile, array(
						'sitecontent' => $model,
						));
	}

	public function actionMoveImage($id, $language, $image, $direction) {
		$sitecontent = $this->loadContent();
		if($sitecontent) {
			$images = $sitecontent->images;

			foreach($images as $key => $value) {
				if($image == $value) {
					// Image already at top ?
					if($direction == 'up' && $key == 0)
						break;
					// Image already at bottom ?
					if($direction == 'down' && $key == count($images) - 1)
						break;

					// swap	
					if($direction == 'up') {
						$tmp = $images[$key - 1];
						$images[$key - 1] = $image;
						$images[$key] = $tmp;
					}else if($direction == 'down') {
						$tmp = $images[$key + 1];
						$images[$key + 1] = $image;
						$images[$key] = $tmp;
					}
				}
			}
			$sitecontent->images = $images;

			$sitecontent->save(false, array('images'));

			$this->redirect( array(
						Cms::module()->sitecontentUpdateRoute,
						'page' => $sitecontent->title_url));
		}
		throw new CHttpException(404);
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

			if(isset($_POST['Sitecontent']['password'])
					&& isset($_POST['Sitecontent']['password_repeat']))
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
			if(isset($_POST['Sitecontent']['password'])
					&& isset($_POST['Sitecontent']['password_repeat']))
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

	public function actionUnlinkImage($filename) {
		unlink(Yii::app()->basePath.'/../'.Cms::module()->imagePath.$filename);
		$this->redirect(array(Cms::module()->imageAdminRoute));
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
						Cms::module()->sitecontentUpdateRoute,
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

	public function actionAdminImages()
	{
		$this->layout = Cms::module()->adminLayout;

		if(isset($_FILES['image_0'])) {
			foreach($_FILES as $key => $image) {
				if($image['name']) {
					move_uploaded_file($image['tmp_name'],
							Cms::module()->imagePath . $image['name']);
				}
			}

			$this->redirect(array(Cms::module()->sitecontentAdminRoute));
		}

		$handle = opendir(Yii::app()->basePath . '/../' . Cms::module()->imagePath);

		while($image = readdir($handle))
			if($image != '.' && $image != '..') 
				$images[] = $image;

		$this->render('admin_images',array(
					'images'=>$images,
					));
	}

	public function loadContent()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']) && is_array(@$_GET['id']))
				$this->_model = Sitecontent::model()->find(
						'id = :id and language = :language',  array(
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
