<?php

class GamesController extends Controller
{

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','join','list','new'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

  public function actionJoin()
  {
    // avoid the system printing any HTML at all
    $this->layout = '';
    
    // are there any games with available slots yet?
    $games = Game::model()->findAllWithNumDevices();
    
    // if so, join the device ID to the game ID
    if (count($games)){
      $device = isset($_POST['device'])?$_POST['device']:'';
      
      if (!$device)
      {
        echo $this->jsonError("wrong device");        
        Yii::app()->end();
      }
      
      // wrong device
      if (!count(Device::model()->findByPk($device)))
      {
        echo $this->jsonError("already registered");
        Yii::app()->end();
      }
      
      if (count(Device2game::model()->findByPk($device)))
      {
        echo $this->jsonError("already registered");
        Yii::app()->end();
      }
      
      $membership = new Device2game;
      $membership->games_id = $games[0]['id'];
      $membership->devices_id = $device;
      
      $membership->save();
      
      echo CJSON::encode(array('success'=>1,'message'=>"OK"));
      Yii::app()->end();
    }
    
    Debug::message($games);
    
    // do they have enough room?
    
    // if so, join the device ID to the game ID
    
    // and return the game ID and data
    
    // if no games yet
    
    // get the data from the request 
    
    // validate it
    
    // create a new game
    
    // return its id to the user
    
    // by default print an error
    
    echo $this->jsonError("unknown");
  }
  
  public function actionNew()
  {
    // avoid the system printing any HTML at all
    $this->layout = '';
    
    $device = isset($_POST['device']) ? $_POST['device'] : '';
    $data = isset($_POST['data']) ? $_POST['data'] : '';
    
    if (!$device)
    {
      echo $this->jsonError("missing device");        
      Yii::app()->end();
    }
    
    if (!$data)
    {
      echo $this->jsonError("missing data");        
      Yii::app()->end();
    }
    
    $game = new Game;
    $game->data = $data;
    $game->save();
    
    echo CJSON::encode(array(
      'success'=>1,
      'game'=>$game->id
    ));
    
  }
  
  public function actionList()
  {
    // avoid the system printing any HTML at all  
    $this->layout = '';
    
    // get a list of games and print it in JSON format
    $result = Game::model()->findAll();
    
    $games = array();
    foreach($result as $game){
      $games[] = array(
        'id'=>$game->id,
        'turn'=>$game->turn,
        'currentPlayer'=>$game->currentPlayer
      );
    }
    
    echo CJSON::encode($games);
  }

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Game;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Game']))
		{
			$model->attributes=$_POST['Game'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Game']))
		{
			$model->attributes=$_POST['Game'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Game');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Game('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Game']))
			$model->attributes=$_GET['Game'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Game::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='game-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
