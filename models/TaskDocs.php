<?php

namespace app\models;

use Yii;

class TaskDocs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->params['scheme_name'].'.TASK_DOCS';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['TASK_ID', 'DOC_CODE', 'TRACT_ID', 'FORMAT_QUANTITY'], 'required'],
            [['DOC_CODE'], 'unique'],
            [['DEL_TRACT_ID', 'PERS_TASKS_ID'], 'safe'],
            [['FORMAT_QUANTITY'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'TASK_ID' => 'Идентификатор текущей задачи',
            'DOC_CODE' => 'Имя помещенного в архив файла',
            'TRACT_ID' => 'Идентификатор текущей транзакции пользователя',
            'DEL_TRACT_ID' => 'Идентификатор текущей транзакции пользователя',
            'FORMAT_QUANTITY' => 'Количество форматов А4',
        ];
    }
    
}
