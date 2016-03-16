<?php

namespace app\models;

use Yii;

class TaskDocsRecvrs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->params['scheme_name'].'.TASK_DOCS_RECVRS';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['TASK_ID', 'KODZIFR', 'TRACT_ID'], 'required'],
            [['DEL_TRACT_ID'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'TASK_ID' => 'Идентификатор текущей задачи',
            'KODZIFR' => 'Идентификатор согласующего подразделения',
            'TRACT_ID' => 'Идентификатор текущей транзакции пользователя',
            'DEL_TRACT_ID' => 'Идентификатор текущей транзакции пользователя',
        ];
    }
    
}
