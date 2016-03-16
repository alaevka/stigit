<?php

namespace app\models;

use Yii;

class TaskDates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->params['scheme_name'].'.TASK_DATES';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['TASK_ID', 'DATE_TYPE_ID', 'TASK_TYPE_DATE', 'TRACT_ID'], 'required'],
            [['DEL_TRACT_ID'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'TASK_ID' => 'Идентификатор текущей задачи',
            'DATE_TYPE_ID' => '3',
            'TASK_TYPE_DATE' => 'Дата поступления в сектор.',
            'TRACT_ID' => 'Идентификатор текущей транзакции пользователя',
            'DEL_TRACT_ID' => 'if deleted',
        ];
    }
    
}
