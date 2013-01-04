<?php

class GamesController extends Controller
{

  public function actionJoin()
  {
    $device = $this->validatePostDevice();
    $game = $this->validatePostGame();
    
    // check that there are enough spaces
    if (count($game->devices) == 4)
    {
      $this->jsonError('game is already full');
    }
   
    // since we already have the object, there is no need for a query 
    $memberships = Device2game::model()->findAllByAttributes(array('gameid'=>$game->id));  
  
    foreach ($memberships as $_membership)
    {
      if ($_membership->deviceid == $device->id)
      {
        $this->jsonError("already registered");
      }
    }
 
    // now we are ok to register the device with the game
    $membership = new Device2game;
    $membership->gameid = $game->id;
    $membership->deviceid = $device->id;

    // get the player number by reducing options according to current players
    $availablePlayers = array(0, 1, 2, 3);
    
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

    // update game data since we know player identifier
    $json = CJSON::decode($game->data);
    $json["players"][$membership->playerid]["id"] = $device->id;
    $json["game"]["player"] = $device->id;
    $game->data = CJSON::encode($json);
    $game->save();

    // notify by GCM the game could be start
    $sendTo = array();
    foreach($game->devices as $_device)
    {
      $sendTo[] = $device->regkey;       
    }
    if (count($sendTo))
    {
      $result = GCM::message($sendTo, array('action' => "START_GAME"));  
    }     
    
    $this->jsonSuccess(array('playerid'=>$membership->playerid));   
  }
  
  public function actionNew()
  {
    $device = $this->validatePostDevice();
    $data = $this->checkPostData();

    // generate a new identifier
    $game = new Game;
    $game->save();
    
    // actually the client generate data therefore we need to fix game and players identifiers
    $json = CJSON::decode($data);
    $json["game"]["id"] = $game->id;
    $game->data = CJSON::encode($json);
    $game->save();
    
    $this->jsonSuccess(array('game'=>$game->id));    
  }
  
  public function actionList()
  {
    
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
    
    $this->jsonSuccess(array('games'=>$games));
  }

  public function actionAvailable()
  {  
    // get a list of games and print it in JSON format
    $result = Game::model()->findAllWithNumDevices();
    
    $games = array();
    foreach($result as $game){
      $games[] = array(
        'id'=>$game['id'],
        'devices'=>$game['numDevices']
      );
    }
    
    $this->jsonSuccess(array('games'=>$games));
  }
  
  public function actionData()
  {
    $game = $this->validatePostGame();
    $device = $this->validatePostDevice();
    
    if (!count(Device2game::model()->findAllByAttributes(array('gameid'=>$game->id,'deviceid'=>$device->id))))
    {
      $this->jsonError("not authorised");
    }
    
    $this->jsonSuccess(array('game'=>$game->attributes));
  }
  
  public function actionPassturn()
  {
    $device = $this->validatePostDevice();
    $game = $this->validatePostGame();
    $data = $this->checkPostData(); 
    
    // check that the device is registered to the game
    $memberships = Device2game::model()->findAllByAttributes(array('gameid'=>$game->id,'deviceid'=>$device->id));  
    
    if (!count($memberships))
    {
      $this->jsonError("not authorised");
    }

    if ($game->currentPlayer != $memberships[0]->playerid)
    {
      $this->jsonError("it is not your turn");
    }
    
    // now, update game data
    $game->data = $data;
    // TODO: calculate current player according to really available players
    // for now on, assuming that games are full
    $game->currentPlayer = $this->getNextPlayer($game);
    $game->turn ++;

    if (!$game->save())
    {
      $this->jsonError("unknown");
    }
    
    // send GCM notifications
    $sendTo = array();
    foreach($game->devices as $_device)
    {
      $sendTo[] = $device->regkey;       
    }
 
    if (count($sendTo))
    {
      $result = GCM::message($sendTo,array('action' => "PASS_TURN" ));  
    }        

    $this->jsonSuccess(array('currentPlayer'=>$game->currentPlayer,'turn'=>$game->turn));
    
  }

  private function getNextPlayer($game)
  {
    $playerid = $game->currentPlayer;
      
    do 
    {
      $playerid ++;
      if ($playerid > 3)
      {
        $playerid = 0;
      }

      $memberships = Device2game::model()->findAllByAttributes(array('gameid'=>$game->id,'playerid'=>$playerid));

    }
    while (!count($memberships));      
    
    return $memberships[0]->playerid;
  }

  /**
   * Lists all models.
   */
  public function actionIndex()
  {
		$this->actionAvailable();
  }

  }
