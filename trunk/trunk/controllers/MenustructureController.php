<?php

class MenustructureController extends Controller
{
	public $defaultAction='admin';
	private $_model;

	public function beforeAction($action)
	{
		$this->layout = Yii::app()->controller->module->layout;
		return true;	
	}

	public function filters()
	{
		return array(
			'accessControl',
		);
	}

	public function actionView()
	{
		$this->render('view',array(
					'model'=>$this->loadModel(),
					));
	}

	public function actionAjaxFillTree()
	{
		if (!Yii::app()->request->isAjaxRequest) {
			exit();
		}

		$parentId = 0;
		if (isset($_GET['root'])) {
			$parentId = (int) $_GET['root'];
		}

		$req = Yii::app()->db->createCommand(
				"SELECT m1.id, m1.title AS text, m2.id IS NOT NULL AS hasChildren "
				. "FROM menustructure AS m1 LEFT JOIN menustructure AS m2 ON m1.id=m2.parent "
				. "WHERE m1.parent <=> $parentId "
				. "GROUP BY m1.id ORDER BY m1.sort ASC"
				);

		$children = $req->queryAll();

		echo str_replace(
				'"hasChildren":"0"',
				'"hasChildren":false',
				CTreeView::saveDataAsJson($children)
				);
		exit();
	}

	public function actionCreate()
	{
	$model=new Menustructure;

		if(isset($_POST['Menustructure']))
		{
			$model->attributes=$_POST['Menustructure'];
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionUpdate()
	{
		$model=$this->loadModel();

		$this->performAjaxValidation($model);

		if(isset($_POST['Menustructure']))
		{
			$model->attributes=$_POST['Menustructure'];
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public static function renderMenuPoints($point)
	{
		if(!isset($_GET['menu']))
			$_GET['menu'] = $point;

		$menu = Menustructure::model()->findAll( array(
					'condition' => 'parent = :point',
					'params' => array(':point' => $point),
					'order' => 'sort',
					)
				);

		if(isset($menu))
			foreach($menu as $point)
			{
				echo CHtml::openTag('li', array(
							'class' => $point->id == $_GET['menu'] ? 'current' : ''
							)
						);

				echo CHtml::link($point->title, array(
							'/cms/sitecontent/view',
							'menu' => $point->id), array(
								'class' => $point->id == $_GET['menu'] ? 'current' : '')
						);                                                                  
				echo CHtml::closeTag('li');
			}


	}

	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel()->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_POST['ajax']))
				$this->redirect(array('index'));
		}
		else
			throw new CHttpException(400,Yii::t('App','Invalid request. Please do not repeat this request again.'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Menustructure');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Menustructure('search');
		if(isset($_GET['Menustructure']))
			$model->attributes=$_GET['Menustructure'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
				$this->_model=Menustructure::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,Yii::t('App','The requested page does not exist.'));
		}
		return $this->_model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='Menustructure-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
