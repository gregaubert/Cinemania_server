<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="en" />
 
    <!-- blueprint CSS framework -->
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
 
       <!-- some more headers... -->
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
 
<body>
   
    <?php $this->widget('zii.widgets.CBreadcrumbs', array(
        'links'=>$this->breadcrumbs,
    )); ?><!-- breadcrumbs -->
 
    <?php echo $content; ?>
 
</body>
</html>