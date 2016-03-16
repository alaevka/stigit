<?php
/*
    Класс формирования xls отчета
*/
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class ReportsController extends Controller
{

    /*
        Метод формирования доступа к страницам
    */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /*
        Метод формирования отчета по выбранным заданиям
    */
    public function actionExcel() {
        /*
            Проверка на доступ пользователя к странице
        */
        $permissions_report_task_search = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action) or
                                        (SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :dolg_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL != :perm_level and ACTION_ID = :action)', ['subject_type_dolg' => 1, 'dolg_id' => \Yii::$app->session->get('user.user_iddolg'), 'action' => 82, 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 0])->one();
        if($permissions_report_task_search) {
            /*
                Проверяем получены ли идентификаторы заданий для формирования отчета
            */
            if(Yii::$app->request->get('ids')) {

                $issues_ids = Yii::$app->request->get('ids');
                $issues_ids = explode(',', $issues_ids);
                /*
                    Делаем выборку необходимых заданий
                */
                $model = \app\models\Tasks::find()->where(['ID' => $issues_ids])->all();
                if($model) {

                    // Создаем объект класса PHPExcel
                    $xls = new \PHPExcel();
                    // Устанавливаем индекс активного листа
                    $xls->setActiveSheetIndex(0);
                    // Получаем активный лист
                    $sheet = $xls->getActiveSheet();
                    // Подписываем лист
                    $sheet->setTitle('Отчет по отобранным заданиям');
                    $sheet->getStyle('A1')->getFont()->setBold(true);
                    // Вставляем текст в ячейку A1
                    $sheet->setCellValue("A1", 'Отчет по отобранным заданиям');
                    $sheet->getStyle('A1')->getFill()->setFillType(
                        \PHPExcel_Style_Fill::FILL_SOLID);
                    $sheet->getStyle('A1')->getFill()->getStartColor()->setRGB('EEEEEE');

                    // Объединяем ячейки
                    $sheet->mergeCells('A1:I1');

                    // Выравнивание текста
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal(
                        \PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    // Формируем шапку
                    $sheet->setCellValue("A2", 'Заказ ПЭО');
                    $sheet->setCellValue("B2", 'Номер заказа');
                    $sheet->setCellValue("C2", 'Проект/Тема');
                    $sheet->setCellValue("D2", 'Обозначение');
                    $sheet->setCellValue("E2", 'Наименование');
                    $sheet->setCellValue("F2", 'Срок выполнения');

                    $sheet->setCellValue("G2", 'Статус');
                    $sheet->setCellValue("H2", 'Ф.И.О. и Дата');
                    $sheet->setCellValue("I2", 'Форматов А4');

                    /* устанавливаем ширину колонок и стили*/
                    $sheet->getStyle('A2:I2')->getFont()->setBold(true);
                    $sheet->getColumnDimension('A')->setAutoSize(true);
                    $sheet->getColumnDimension('B')->setAutoSize(true);
                    $sheet->getColumnDimension('C')->setAutoSize(true);
                    $sheet->getColumnDimension('D')->setAutoSize(true);
                    $sheet->getColumnDimension('E')->setAutoSize(true);
                    $sheet->getColumnDimension('F')->setAutoSize(true);
                    $sheet->getColumnDimension('G')->setWidth(20);
                    $sheet->getColumnDimension('H')->setAutoSize(true);
                    $sheet->getColumnDimension('I')->setAutoSize(true);

                    
                    

                    $row_number = 3;
                    foreach ($model as $task) {
                        
                        $sheet->setCellValue("A".$row_number, $task->PEOORDERNUM);
                        $sheet->setCellValue("B".$row_number, $task->ORDERNUM);
                        $sheet->setCellValue("C".$row_number, '');
                        $sheet->setCellValue("D".$row_number, $task->TASK_NUMBER);
                        $sheet->setCellValue("E".$row_number, 'Задание');
                        $sheet->setCellValue("F".$row_number, \Yii::$app->formatter->asDate($task->DEADLINE, 'php:d-m-Y'));
                        
                        //вставляем информацию по статусам
                        $task_states = \app\models\TaskStates::find()->where(['TASK_ID' => $task->ID])->orderBy('STATE_ID ASC')->all();
                        if($task_states) {
                            
                            foreach ($task_states as $state) {

                                $state_date = $state->getStateDate();    

                                $logo = new \PHPExcel_Worksheet_Drawing();
                                $logo->setPath(Yii::getAlias('@webroot').'/images/items_status/'.$state->getStateColour().'.png');
                                $logo->setCoordinates("G".$row_number);                
                                $logo->setOffsetX(5);
                                $logo->setOffsetY(2);   
                                $logo->setResizeProportional(true);
                                $logo->setWidth(16);
                                $logo->setWorksheet($sheet);

                                $sheet->setCellValue("G".$row_number, '        '.$state->getStateName());

                                $pers_tasks = \app\models\PersTasks::findOne($state->PERS_TASKS_ID);
                                $query = new \yii\db\Query;
                                $query->select('*')
                                    ->from('STIGIT.V_F_PERS')
                                    ->where('TN = \'' . $pers_tasks->TN .'\'');
                                $command = $query->createCommand();
                                $data = $command->queryOne();

                                $sheet->setCellValue("H".$row_number, $data['FIO'].' '.$state_date);

                                $task_docs = \app\models\TaskDocs::find()->where(['PERS_TASKS_ID' => $state->PERS_TASKS_ID])->one();
                                if($task_docs) {
                                    $quantity = $task_docs->FORMAT_QUANTITY;
                                } else {
                                    $quantity = 0;
                                }
                                $sheet->setCellValue("I".$row_number, $quantity);
                                
                                $row_number++;
                            }
                        }

                        $row_number++;
                    }

                    //стили для рамки таблицы
                    $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN
                            )
                        )
                    );
                    $total_rows = $row_number-1;
                    $sheet->getStyle('A1:I'.$total_rows)->applyFromArray($styleArray);

                    //параметры страницы для печати - альбомная
                    $xls->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                    $xls->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
                    $xls->getActiveSheet()->getPageSetup()->setFitToPage(true);
                    $xls->getActiveSheet()->getPageSetup()->setFitToWidth(1);
                    $xls->getActiveSheet()->getPageSetup()->setFitToHeight(0);

                    // Выводим HTTP-заголовки
                    header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT" );
                    header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
                    header ( "Cache-Control: no-cache, must-revalidate" );
                    header ( "Pragma: no-cache" );
                    header ( "Content-type: application/vnd.ms-excel" );
                    header ( "Content-Disposition: attachment; filename=report.xls" );

                    //Выводим содержимое файла
                    $objWriter = new \PHPExcel_Writer_Excel5($xls);
                    $objWriter->save('php://output');
                } else {
                    /*
                        Вызываем эксепшн в случае, если были переданы не верные параметры заданий
                    */
                    throw new \yii\web\NotFoundHttpException('Что-то пошло не так. Пожалуйста, обратитесь к администратору системы.');
                }
            }

        } else {
            /*
                Вызываем эксепшн в случае, если доступ к формированию отчета запрещен
            */
            throw new \yii\web\ForbiddenHttpException('У Вас нет прав на редактирование "Формирование отчета"'); 
        }
    }

}