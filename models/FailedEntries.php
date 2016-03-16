<?php
/*
    Модель ошибочных авторизаций 
*/
namespace app\models;

use Yii;

class FailedEntries extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'FAILED_ENTRIES';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['LOGIN', 'PASSWORD', 'TRACT_DATETIME', 'USER_IP'], 'required'],
        ];
    }

    
}
