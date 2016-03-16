<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Модель формы выдачи задания не связана с таблицей заданий
 */
class IssueForm extends Model
{
    //проперти полей
    public $stagenum;
    public $income_number;
    public $reason_type;
    public $designation;
    public $task_number;
    public $podr_list;
    public $persons_list;
    public $ordernum;
    public $peoordernum;
    public $message;
    public $date;
    public $documentid;

    /**
     * @return возвращает массив валидируемых данных
     */
    public function rules()
    {
        return [
            [['stagenum', 'income_number', 'reason_type', 'designation', 'task_number', 'podr_list', 'ordernum', 'peoordernum', 'message', 'date'], 'required'],
            [['task_number'], 'integer'],
            [['task_number'], 'validateUniqueTaskNumber'],
            [['persons_list', 'documentid'], 'safe']
        ];
    }

    /*
        функция валидации уникального номера задания
    */
    public function validateUniqueTaskNumber()
    {
        $tasks = Tasks::find()->where(['TASK_NUMBER' => $this->task_number])->one();
        if($tasks) {
            $this->addError('task_number', 'Исходящий номер уже зарегистрирован в системе. Укажите другой.');
        }
    }

    /*
        Описание полей для формирования label
    */
    public function attributeLabels()
    {
        return [
            'stagenum' => 'Этап',
            'income_number' => 'Входящий номер',
            'reason_type' => 'Вид основания',
            'designation' => 'Основание',
            'task_number' => 'Исходящий номер',
            'podr_list' => 'Подразделения',
            'persons_list' => 'Исполнители',
            'ordernum' => 'Изделие',
            'peoordernum' => 'Заказ ПЭО',
            'message' => 'Содержание задания',
            'date' => 'Срок выполнения',
        ];
    }

}