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
				'actions'=>array('index','view','join','list','new','passturn','data'),
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
    
    $device = $this->validatePostDevice();
    $game = $this->validatePostGame();
    
    // check that there are enough spaces
    if (count($game->devices) == 4)
    {
      echo $this->jsonError('game is already full');
      Yii::app()->end();
    }
   
    // since we already have the object, there is no need for a query 
    $memberships = Device2game::model()->findAllByAttributes(array('games_id'=>$game->id));  
  
    foreach ($memberships as $_membership)
    {
      if ($_membership->devices_id == $device->id)
      {
        echo $this->jsonError("already registered");
        Yii::app()->end();
      }
    }
 
    // now we are ok to register the device with the game
    $membership = new Device2game;
    $membership->games_id = $game->id;
    $membership->devices_id = $device->id;

    // get the player number by reducing options according to current players
    $availablePlayers = array(1,2,3,4);
    
    foreach($memberships as $_membership)
    {
      $pos = array_search($_membership->playerid,$availablePlayers);
      
      if ($pos !== false)
      {
        unset($availablePlayers[$pos]);  
      }
    }
    $membership->playerid = array_shift($availablePlayers);
      
    $membership->save();
      
    echo CJSON::encode(array('success'=>1,'playerid'=>$membership->playerid));
    Yii::app()->end();
    
  }
  
  public function actionNew()
  {
    // avoid the system printing any HTML at all
    $this->layout = '';
    
    $device = $this->checkPostDevice();
    $data = $this->checkPostData();
    
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
  
  public function actionData()
  {
    // avoid the system printing any HTML at all  
    $this->layout = '';
    
    $game = $this->validatePostGame();
    $device = $this->validatePostDevice();
    
    if (!count(Device2game::model()->findAllByAttributes(array('games_id'=>$game->id,'devices_id'=>$device->id))))
    {
      echo $this->jsonError("not authorised");
      Yii::app()->end();
    }
    
    echo CJSON::encode($game->attributes);
  }
  
  public function actionPassturn()
  {
    // avoid the system printing any HTML at all
    $this->layout = '';
    
    $device = $this->validatePostDevice();
    $game = $this->validatePostGame();
    $data = $this->checkPostData(); 
    
    // check that the device is registered to the game
    $memberships = Device2game::model()->findAllByAttributes(array('games_id'=>$game->id,'devices_id'=>$device->id));  
  
    if (!count($memberships))
    {
      echo $this->jsonError("not authorised");
      Yii::app()->end();
    }

    if ($game->currentPlayer != $memberships[0]->playerid)
    {
      echo $this->jsonError("it is not your turn");
      Yii::app()->end();
    }
    
    // now, update game data
    $game->data = $data;
    // TODO: calculate current player according to really available players
    // for now on, assuming that games are full
    $game->currentPlayer = $this->getNextPlayer($game);
    $game->turn ++;

    if (!$game->save())
    {
      echo $this->jsonError("unknown");
      Yii::app()->end();
    }
    
    // send GCM notifications
    $sendTo = array();
    foreach($game->devices as $device)
    {
      $sendTo[] = $device->regkey;
    }
    
    //$result = GCM::message($sendTo,array('action' => "PASS_TURN" ));    

    echo CJSON::encode(array('success'=>1,'currentPlayer'=>$game->currentPlayer,'turn'=>$game->turn));
    
  }

  private function getNextPlayer($game)
  {
    $playerid = $game->currentPlayer;
      
    do 
    {
      $playerid ++;
      if ($playerid > 4)
      {
        $playerid = 1;
      }

      $memberships = Device2game::model()->findAllByAttributes(array('games_id'=>$game->id,'playerid'=>$playerid));

    }
    while (!count($memberships));      
    
    return $memberships[0]->playerid;
  }
  
  private function checkPostDevice()
  {
    return $this->checkPostField('device');
  }
  
  private function checkPostData()
  {
    return $this->checkPostField('data');
  }
  
  private function checkPostGame()
  {
    return $this->checkPostField('game');
  }
  
  private function validatePostDevice()
  {
    $deviceID = $this->checkPostDevice();
       
    // validate the device and the game
    $device = Device::model()->findByPk($deviceID);    
    if (!count($device))
    {
      echo $this->jsonError('wrong device');
      Yii::app()->end();
    }
    
    return $device;
  }
  
  private function validatePostGame()
  {
    $gameID = $this->checkPostGame();
    
    // game
    $game = Game::model()->findByPk($gameID);   
    if (!count($game))
    {
      echo $this->jsonError('wrong game');
      Yii::app()->end();
    }
    
    return $game;
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
