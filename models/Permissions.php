<?php
/*
    Модель прав доступа
*/
namespace app\models;
use Yii;

class Permissions extends \yii\db\ActiveRecord
{
    /**
     * метод, возвращающий имя таблицы ACTIONS
     */
    public static function tableName()
    {
        return Yii::$app->params['scheme_name'].'.PERMISSIONS';
    }

    /**
     * валидация данных
     */
    public function rules()
    {
        return [
            [['SUBJECT_ID', 'SUBJECT_TYPE', 'ACTION_ID', 'TRACT_ID', 'PERM_TYPE'], 'required'],
            [['PERM_LEVEL', 'DEL_TRACT_ID'], 'safe'],
        ];
    }

    /*
        реляция с моделью Actions
    */
    public function getPermactiontype()
    {
        return $this->hasOne(\app\models\Actions::className(), ['ID' => 'ACTION_ID']);
    }

    /*
        реляция с моделью States
    */
    public function getPermstatestype()
    {
        return $this->hasOne(\app\models\States::className(), ['ID' => 'ACTION_ID']);
    }

    
}
