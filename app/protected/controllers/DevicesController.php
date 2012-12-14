<?php

class DevicesController extends Controller
{
  public function actionRegister()
  {
    $devid = $this->checkPostDevice();
    $regkey = $this->checkPostField('key');

    $model = Device::model()->findByPk($devid);
    
    $existed = false;
    if ($model)
    {
      $existed = true;
    }
    else
    {
      $model = new Device;  
      $model->id = $devid;
     }
    
    $model->regkey = $regkey;
    
    if($model->save())
    {
      if ($existed)
      {
        $this->jsonSuccess('updated');
      }
      else 
      {
        $this->jsonSuccess('registered');  
      }      
    }
    
    $this->jsonError('unknown');
   
  }
  
  public function actionUnregister()
  {
    $device = $this->validatePostDevice();
    
    if($device->delete())
    {      
      $this->jsonSuccess('unregistered');
    }
    
    $this->jsonError('unknown');

  }
  
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->actionRegister();
	}
  
}
