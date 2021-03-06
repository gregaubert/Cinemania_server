<?php

/**
 * This is the model class for table "game".
 *
 * The followings are the available columns in table 'game':
 * @property string $id
 * @property string $data
 * @property integer $turn
 * @property integer $currentPlayer
 * @property string $version
 *
 * The followings are the available model relations:
 * @property Device[] $devices
 */
class Game extends CActiveRecord
{
  
  private static $primaryKey = 'id';
  
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Game the static model class
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
		return 'game';
	}
  
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('turn, currentPlayer', 'numerical', 'integerOnly'=>true),
			array('version', 'length', 'max'=>10),
			array('data', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, data, turn, currentPlayer, version', 'safe', 'on'=>'search'),
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
			'devices' => array(self::MANY_MANY, 'Device', 'device2game(gameid, deviceid)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'data' => 'Data',
			'turn' => 'Turn',
			'currentPlayer' => 'Current Player',
			'version' => 'Version',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('data',$this->data,true);
		$criteria->compare('turn',$this->turn);
		$criteria->compare('currentPlayer',$this->currentPlayer);
		$criteria->compare('version',$this->version,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

  /**
   * Returns only the games that have available slots
   * with the information of how many devices are already registered
   */
  public function findAvailable(){
    $dev2game = Device2game::model()->tableName();
    $game = self::tableName();
    $gamepk = self::$primaryKey; 
    $dev2gamepk = Device2game::getGameForeignKey();
    $dev2gamepk2 = Device2game::getDeviceForeignKey();
    
    $command = Yii::app()->db->createCommand(
      sprintf(
        'SELECT * FROM (SELECT g.*, (SELECT COUNT(d.%5$s) FROM %1$s d WHERE %4$s = g.%3$s) numDevices FROM %2$s g LEFT JOIN %1$s d ON d.%4$s = g.%3$s ) test WHERE numDevices < 4  AND turn = 1 GROUP BY %3$s ORDER BY numDevices DESC',
        $dev2game,$game,$gamepk,$dev2gamepk,$dev2gamepk2
      )
    );   
    
    return $command->queryAll();
  }
  
}