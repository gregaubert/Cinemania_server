<?php

/**
 * This is the model class for table "device2game".
 *
 * The followings are the available columns in table 'device2game':
 * @property string $deviceid
 * @property string $gameid
 * @property string $playerid
 */
class Device2game extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Device2game the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'device2game';
	}
  
  public function getGameForeignKey()
  {
    return 'gameid';
  }
  
  public function getDeviceForeignKey()
  {
    return 'deviceid';
  }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('deviceid, gameid', 'required'),
			array('deviceid', 'length', 'max'=>16),
			array('gameid', 'length', 'max'=>10),
			array('playerid', 'length', 'max'=>45),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('deviceid, gameid, playerid', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'deviceid' => 'Devices',
			'gameid' => 'Games',
			'playerid' => 'Playerid',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('deviceid',$this->deviceid,true);
		$criteria->compare('gameid',$this->gameid,true);
		$criteria->compare('playerid',$this->playerid,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}