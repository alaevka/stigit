<?php

namespace app\models;

use Yii;

class ReasonTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->params['scheme_name'].'.REASON_TYPES';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['REASON_TYPE'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'REASON_TYPE' => 'Вид основания',
        ];
    }
    
}
