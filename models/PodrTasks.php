<?php
/*
    Модель для таблицы  podr_tasks
*/
namespace app\models;

use Yii;

class PodrTasks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Yii::$app->params['scheme_name'].'.PODR_TASKS';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['TASK_ID', 'KODZIFR', 'TRACT_ID'], 'required'],
            [['DEL_TRACT_ID'], 'safe'],
        ];
    }

    
}
