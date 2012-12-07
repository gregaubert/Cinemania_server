<?php
class SiteController extends Controller
{		
	/**
	 * Welcome page
	 */
	public function actionIndex()
	{
		$this->actionLogin();
	}		
	
	public function actionLogin()
	{
		 $form=new LoginForm;
    // collect user input data
    if(isset($_POST['LoginForm']))
    {
        $form->attributes=$_POST['LoginForm'];
        // validate user input and redirect to previous page if valid
        if($form->validate()  && $form->login()) $this->redirect(Yii::app()->user->returnUrl);
    }
    // display the login form
    $this->render('login',array('model'=>$form));
	}
	
}