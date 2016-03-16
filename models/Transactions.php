<?php

namespace app\models;

use Yii;

class Transactions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->params['scheme_name'].'.TRANSACTIONS';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['TN', 'TRACT_DATETIME', 'USER_IP'], 'required'],
            [['ID'], 'safe']
        ];
    }

    
}
