<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
  /**
   * @var string the default layout for the controller view. Defaults to '//layouts/column1',
   * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
   */
  public $layout = '//layout/main';
  
  /**
   * @var array context menu items. This property will be assigned to {@link CMenu::items}.
   */
  public $menu = array();
  
  /**
   * @var array the breadcrumbs of the current page. The value of this property will
   * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
   * for more details on how to specify this property.
   */
  public $breadcrumbs = array();  
  
  protected function jsonError($message='')
  {
    echo CJSON::encode(array('success'=>0,'error'=>$message));
    Yii::app()->end();
  }
  
  protected function jsonSuccess($message='')
  {
    if (is_array($message))
    {
      $data = array_merge($message,array('success'=>1));
    }
    else 
    {
      $data = array('success'=>1,'message'=>$message);
    }
    
    echo CJSON::encode($data);
    Yii::app()->end();
  }
  
  protected function checkPostField($fieldName)
  {
    $field = isset($_POST[$fieldName]) ? $_POST[$fieldName] : '';
    
    if (!$field)
    {
      $this->jsonError("missing " . $fieldName);        
    }
    
    return $field;
  }
  
  protected function checkPostDevice()
  {
    return $this->checkPostField('device');
  }
  
  protected function checkPostData()
  {
    return $this->checkPostField('data');
  }
  
  protected function checkPostGame()
  {
    return $this->checkPostField('game');
  }
  
  protected function validatePostDevice()
  {
    $deviceID = $this->checkPostDevice();
       
    // validate the device and the game
    $device = Device::model()->findByPk($deviceID);    
    if (!count($device))
    {
      $this->jsonError('wrong device');
    }
    
    return $device;
  }
  
  protected function validatePostGame()
  {
    $gameID = $this->checkPostGame();
    
    // game
    $game = Game::model()->findByPk($gameID);   
    if (!count($game))
    {
      $this->jsonError('wrong game');
    }
    
    return $game;
  }
}