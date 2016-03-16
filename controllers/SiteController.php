<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Transactions;

class SiteController extends Controller
{

    private $_podr_data_array = []; // в результате формирования содержит сформированный nested-массив подразделений
    private $_multidemensional_podr; // в резудьтате формирования содержит сформированный html-код дерева подразделений для поля выбора подразделений при создании или изменении задания
    private $_multidemensional_podr_filter; // в резудьтате формирования содержит сформированный html-код дерева подразделений для поля выбора подразделений "фильтра подразделений"
    private $_multidemensional_agreed_filter; // в резудьтате формирования содержит сформированный html-код дерева подразделений для поля выбора подразделений "фильтра согласованно"
    private $_multidemensional_podr_agreed; // в резудьтате формирования содержит сформированный html-код дерева подразделений для поля выбора подразделений поля "согласованно" при изменении задания
    private $_multidemensional_podr_transmitted; // в резудьтате формирования содержит сформированный html-код дерева подразделений для поля выбора подразделений поля "передано в" при изменении задания
    private $dateFormat = 'YYYY-MM-DD hh24:mi:ss'; // формат даты для записи в БД
    const _UNDEFINED = '----------- не задано -----------'; // константа для вывода в таблицу информации о задании, если данные отсутствуют
    
    /*
        Метод формирования доступа к страницам
    */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'index'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['logout', 'index'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            // 'verbs' => [
            //     'class' => VerbFilter::className(),
            //     'actions' => [
            //         'logout' => ['post'],
            //     ],
            // ],
        ];
    }

    /*
        global actions
    */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction', // метод обработки исключений
            ],
            // 'captcha' => [
            //     'class' => 'yii\captcha\CaptchaAction', // метод формирования капчи (не используется)
            //     'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            // ],
        ];
    }

    /*
        Index - метод формирования главной страницы
        данные рендерятся в шаблон /views/site/index.php возвращая следующие данные
        @return podr_data - сформированный html-код дерева подразделений для создания и редактирования задания
        @return podr_data_filter - сформированный html-код дерева подразделений для фильтра "подразделения"
        @return agreed_data_filter - сформированный html-код дерева подразделений для фильтра "согласованно"
        @return model - модель создаваемого задания
        @return dataProvider - объект, содержащий список заданий, выводимых на главной странице
        @return searchModel - модель (наследованная от Tasks) для фильтрации заданий
    */
    public function actionIndex()
    {
        //создаем новый объект для формы добавления задания
        $model = new \app\models\IssueForm;
        //получаем дерево подразделей        
        $this->_podr_data_array = $this->_getPodrData();
        $this->_createPodrTree(1, 0, $link = 'checkbox-podr-link');
        $this->_createPodrTree(1, 0, 'checkbox-podr-link-filter');
        $this->_createPodrTree(1, 0, 'checkbox-agreed-link-filter');
        
        // ajax - валидация при создании задания
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
        }
        // если данные переданы POST запросом, то загружаем эти данные в объект модели
        if ($model->load(Yii::$app->request->post())) {
            // дополнительно валидируем полученные данные
            if($model->validate()) {
                // получаем объект текущей транзакции
                $transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();
                /*
                    Сохраняем модель TASKS-----------------------------------------------------------------------------------------
                */
                // создаем новый объект модели Tasks
                $task = new \app\models\Tasks;
                // в случае, если DESIGNATION не был выбран из имеющихся данных и создан новый
                if(empty($model->documentid)) {
                    //$task->DESIGNATION = $model->designation;
                    $task->REASON = $model->designation;
                    
                } else { // в случае, если DESIGNATION был выбран из имеющихся данных получаем его значение из представления
                    $query = new \yii\db\Query;
                    $query->select('REASON AS name')
                            ->from('STIGIT.V_F_REASONS')
                            ->where('REASONID = \'' . $model->documentid .'\'');
                    $command = $query->createCommand();
                    $data = $command->queryOne();
                    $task->REASON = $data['name'];
                    $task->REASONID = $model->documentid;
                }
                
                //сохраняем прямые поля в модель
                $task->TASK_NUMBER = $model->task_number;
                $task->ORDERNUM = $model->ordernum;
                $task->PEOORDERNUM = $model->peoordernum;
                $task->STAGENUM = $model->stagenum;
                $task->TASK_TEXT = $model->message;

                $deadline = explode('-', $model->date);
                $deadline_formatted = $deadline[2].'-'.$deadline[1].'-'.$deadline[0];

                $task->DEADLINE = new \yii\db\Expression("to_date('" . $deadline_formatted . "','{$this->dateFormat}')");
                $task->TRACT_ID = $transactions->ID;
                // сохраняем задание
                if($task->save()) { //если объект модели сохранен в БД
                    /*
                        Сохраняем модель PODR_TASKS
                    */
                    //обходим все указанные подразделения и для каждого создаем модель и сохраняем в БД
                    foreach(explode(',', $model->podr_list) as $podr) { 
                        $podr_task = new \app\models\PodrTasks;
                        $podr_task->TASK_ID = $task->ID;
                        $podr_task->KODZIFR = trim($podr);
                        $podr_task->TRACT_ID = $transactions->ID;
                        $podr_task->save();
                    }
                    /*
                        Сохраняем модель PERS_TASKS, если были выбраны конкретные исполнители
                    */
                    //обходим всех указанных исполнителей и для каждого создаем объект модели и сохраняем в БД
                    if(!empty($model->persons_list)) { //поверяем есть ли вообще исполнители в задании
                        foreach(explode(',', $model->persons_list) as $person) { // обходим полученный массив исполнителей и каждый сохраняем в объект, а затем в БД
                            $person_task = new \app\models\PersTasks;
                            $person_task->TASK_ID = $task->ID;
                            $person_task->TN = $person;
                            $person_task->TRACT_ID = $transactions->ID;
                            $person_task->save();
                        } 
                    }
                    //генерируем флэш сообщение и редиректим на главную страницу заданий
                    \Yii::$app->getSession()->setFlash('flash_message_success', 'Задание выдано');
                    return $this->redirect(['index']);
                } else { // в случае неудачного сохранения задания генерируем флэш об ошибке и редиректим на главную - все ошибки в логах для администратора

                    print_r($task->errors); die();

                    \Yii::$app->getSession()->setFlash('flash_message_error', 'Что-то пошло не так. Обратитесь к администратору.');
                    return $this->redirect(['index']);
                }
            } else { //в случае, если введенные данные не прошли валидацию. Такого бытьне может, так как сейчас двухуровневая валидация и все данные валидируются на строне клиента. Но на всякий случай условие написано.
                \Yii::$app->getSession()->setFlash('flash_message_error', 'Что-то пошло не так. Обратитесь к администратору.');
                return $this->redirect(['index']);
            }
        }        

        // формируем модель фильтрации заданий и объект, содержащий список заданий, а так же указываем количество страниц в пейджере
        $searchModel = new \app\models\SearchTasks;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $dataProvider->pagination->pageSize=15;

        // обработчики для правильного заполнения полей поиска фильтрации, в случае, если в этих полях отсутсвуют данные
        if(!is_array($searchModel->ORDERNUM))
            $searchModel->ORDERNUM = [];
        if(!is_array($searchModel->PEOORDERNUM))
            $searchModel->PEOORDERNUM = [];
        if(!is_array($searchModel->documentation))
            $searchModel->documentation = [];

        // рендерим шаблон и передаем в него переменные
        return $this->render('index', [
            'podr_data' => $this->_multidemensional_podr,
            'podr_data_filter' => $this->_multidemensional_podr_filter,
            'agreed_data_filter' => $this->_multidemensional_agreed_filter,
            'model' => $model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /*
        Метод, проверяющий есть ли вложенные подразделения у текущего, в зависимости от результата возвращает true или false
        @return true or false
    */
    public function _checkNextPodrTree($parent_id) {
        if (isset($this->_podr_data_array[$parent_id])) { 
            return true;
        }
    }


    /*
        Метод формирования html-кода для дерева подразделений
        @param $parent_id - указатель на родителя подразделения
        @param $level - текущий уровень вложенности в дереве
        @param $checkbox_link - переменная, содержащая css класс, на основе которого строится дерево для того либо иного модального окна подразделений (описано в коде функции)
        @return html next format: 
        <ul>
            <li>...</li>
            ...
        </ul> с данными для вставки в поле в зависимости от переменной $checkbox_link, в которой содержится значение, для какого типа поля формировать код
    */  
    public function _createPodrTree($parent_id, $level, $checkbox_link) {
        if (isset($this->_podr_data_array[$parent_id])) { //проверяем, существует ли элемент массива в массиве подразделений $this->_podr_data_array

            //формируем начало html кода добавлением открывающего списка тега ul
            switch ($checkbox_link) {
                case 'checkbox-podr-link': // в случае, если генерируем список для выборки подразделений в форму выдачи задания
                    $this->_multidemensional_podr .= "<ul>";
                    break;
                case 'checkbox-podr-link-agreed': // в случае, если генерируем список для выборки подразделений в форму редактирования задания "согласовано с"
                    $this->_multidemensional_podr_agreed .= "<ul>";
                    break;
                case 'checkbox-podr-link-transmitted': // в случае, если генерируем список для выборки подразделений в форму выдачи задания "передано в"
                    $this->_multidemensional_podr_transmitted .= "<ul>";
                    break;
                case 'checkbox-podr-link-filter': //в случае, если генерируем список для выборки подразделений в фильтр заданий
                    $this->_multidemensional_podr_filter .= "<ul>";
                    break;
                case 'checkbox-agreed-link-filter': //в случае, если генерируем список для выборки подразделений в фильтр заданий "согласованно с"
                    $this->_multidemensional_agreed_filter .= "<ul>";
                    break;
            }
            
            //обходим массив подразделений
            foreach ($this->_podr_data_array[$parent_id] as $value) {
                if($this->_checkNextPodrTree($value['id'])) { // проверяем на вложенность подразделений
                    $class = "class=\"collapsed\""; // если есть вложенные элементы, указываем класс для сворачивания ветки дерева
                } else { // иначе класс не указываем, так как дерево разворачиваться не будет
                    $class = '';
                }

                //получаем количество заданий в работе для каждого подразделения
                $kodzifr = $value['code'];
                $podr_tasks = \app\models\PodrTasks::find()->where(['KODZIFR' => $kodzifr, 'DEL_TRACT_ID' => 0])->all();
                if($podr_tasks) {
                    
                    $tasks_array = [];
                    foreach($podr_tasks as $task) {
                        $tasks_array[] = $task->TASK_ID;
                    }

                    $tasks = \app\models\Tasks::find()->where(['in', 'ID', $tasks_array])->all();
                    $list = [];
                    foreach($tasks as $task) {
                        $id = $task->ID;
                        $persons = \app\models\PersTasks::find()->where(['TASK_ID' => $id, 'DEL_TRACT_ID' => 0])->all();
                        if($persons) {
                            $states_array = [];
                           
                            foreach($persons as $person) {
                                
                                $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' =>$id, 'TN' => $person->TN, 'DEL_TRACT_ID' => 0])->one();
                               
                                $task_state = \app\models\TaskStates::find()->where(['IS_CURRENT' => 1, 'PERS_TASKS_ID' => $pers_tasks->ID, 'TASK_ID' => $id])->one();
                                if($task_state) {
                                    $states_array[] = $task_state->STATE_ID;
                                } else {
                                    $list[] = $id;
                                }
                            }
                            if(!empty($states_array)) {
                                $min_state = min($states_array);
                                $state = \app\models\States::findOne($min_state);
                            
                            }
                        }
                        if(isset($state)) {

                            if($state->ID != 7 || $state->ID != 9) {
                                $list[] = $id;
                            }
                        }

                    }

                    $list = array_unique($list);
                    $counter = count($list);
                    $counter = ' <span class="label label-info"><a target="_blank" href="'.\yii\helpers\Url::to(['/site/index', 'for_podr' => $kodzifr]).'">Заданий в работе: '.$counter.'</a></span>';
                } else {
                    $counter = '';
                }

                
                //формируем строку списка в зависимости от нужного нам дерева подразделений
                switch ($checkbox_link) {
                    case 'checkbox-podr-link': // в случае, если генерируем список для выборки подразделений в форму выдачи задания
                        $this->_multidemensional_podr .= "<li ".$class."><input id=\"checkbox_".$value['code']."\" style=\"display: none;\" type='checkbox' name=\"podr_check[]\" data-title=\"".$value['name']."\" value=\"".$value['code']."\" /><span style=\"font-weight: normal; font-size: 11px;\" for=\"checkbox_".$value['code']."\"><a href=\"#\" data-id=\"".$value['code']."\" class=\"".$checkbox_link."\">".$value['vid']." ".$value['code'].". ".$value['name']."</a>".$counter."</span>";
                        break;
                    case 'checkbox-podr-link-agreed': // в случае, если генерируем список для выборки подразделений в форму редактирования задания "согласовано с"
                        $this->_multidemensional_podr_agreed .= "<li ".$class."><input id=\"checkbox_".$value['code']."\" style=\"display: none;\" type='checkbox' name=\"podr_check[]\" data-title=\"".$value['name']."\" value=\"".$value['code']."\" /><span style=\"font-weight: normal; font-size: 11px;\" for=\"checkbox_".$value['code']."\"><a href=\"#\" data-id=\"".$value['code']."\" class=\"".$checkbox_link."\">".$value['vid']." ".$value['code'].". ".$value['name']."</a>".$counter."</span>";
                        break;
                    case 'checkbox-podr-link-transmitted': // в случае, если генерируем список для выборки подразделений в форму выдачи задания "передано в"
                        $this->_multidemensional_podr_transmitted .= "<li ".$class."><input id=\"checkbox_".$value['code']."\" style=\"display: none;\" type='checkbox' name=\"podr_check[]\" data-title=\"".$value['name']."\" value=\"".$value['code']."\" /><span style=\"font-weight: normal; font-size: 11px;\" for=\"checkbox_".$value['code']."\"><a href=\"#\" data-id=\"".$value['code']."\" class=\"".$checkbox_link."\">".$value['vid']." ".$value['code'].". ".$value['name']."</a>".$counter."</span>";
                        break;
                    case 'checkbox-podr-link-filter': //в случае, если генерируем список для выборки подразделений в фильтр заданий
                        $this->_multidemensional_podr_filter .= "<li ".$class."><input id=\"checkbox_filter_".$value['code']."\" style=\"display: none;\" type='checkbox' name=\"podr_check[]\" data-title=\"".$value['name']."\" value=\"".$value['code']."\" /><span style=\"font-weight: normal; font-size: 11px;\" for=\"checkbox_".$value['code']."\"><a href=\"#\" data-id=\"".$value['code']."\" class=\"".$checkbox_link."\">".$value['vid']." ".$value['code'].". ".$value['name']."</a>".$counter."</span>";
                        break;
                    case 'checkbox-agreed-link-filter': //в случае, если генерируем список для выборки подразделений в фильтр заданий "согласованно с"
                        $this->_multidemensional_agreed_filter .= "<li ".$class."><input id=\"checkbox_filter_agreed_".$value['code']."\" style=\"display: none;\" type='checkbox' name=\"podr_check[]\" data-title=\"".$value['name']."\" value=\"".$value['code']."\" /><span style=\"font-weight: normal; font-size: 11px;\" for=\"checkbox_".$value['code']."\"><a href=\"#\" data-id=\"".$value['code']."\" class=\"".$checkbox_link."\">".$value['vid']." ".$value['code'].". ".$value['name']."</a>".$counter."</span>";
                        break;
                }

                $level++;
                //формируем вложенные списки
                $this->_createPodrTree($value['id'], $level, $checkbox_link);
                $level--; 
            }

            //формируем конец html кода добавлением открывающего списка тега ul 
            switch ($checkbox_link) { 
                case 'checkbox-podr-link': // в случае, если генерируем список для выборки подразделений в форму выдачи задания
                    $this->_multidemensional_podr .= "</ul>";
                    break;
                case 'checkbox-podr-link-agreed': // в случае, если генерируем список для выборки подразделений в форму редактирования задания "согласовано с"
                    $this->_multidemensional_podr_agreed .= "</ul>";
                    break;
                case 'checkbox-podr-link-transmitted': // в случае, если генерируем список для выборки подразделений в форму выдачи задания "передано в"
                    $this->_multidemensional_podr_transmitted .= "</ul>";
                    break;
                case 'checkbox-podr-link-filter': //в случае, если генерируем список для выборки подразделений в фильтр заданий
                    $this->_multidemensional_podr_filter .= "</ul>";
                    break;
                case 'checkbox-agreed-link-filter': //в случае, если генерируем список для выборки подразделений в фильтр заданий "согласованно с"
                    $this->_multidemensional_agreed_filter .= "</ul>";
                    break;    
            }
        }
    }

    /*
        Формирования списков подразделений для формы редактировани задания поля "Согласовано"
        @param $parent_id - идентификатор родителя подразделения
        @param $level - уровень вложенности дерева
        @return записывает в проперти класса _multidemensional_podr_agreed html код дерева
    */
    public function _createPodrTreeAgreed($parent_id, $level) {
        if (isset($this->_podr_data_array[$parent_id])) { // проверяем, существует ли элемент массива в массиве подразделений $this->_podr_data_array
            $this->_multidemensional_podr_agreed .= "<ul>";
            foreach ($this->_podr_data_array[$parent_id] as $value) {
                if($this->_checkNextPodrTree($value['id'])) { // проверяем на вложенность подразделений
                    $class = "class=\"collapsed\""; // если есть вложенные элементы, указываем класс для сворачивания ветки дерева
                } else { // иначе класс не указываем, так как дерево разворачиваться не будет
                    $class = '';
                }

                $this->_multidemensional_podr_agreed .= "<li ".$class."><input id=\"checkbox_".$value['code']."\" style=\"display: none;\" type='checkbox' name=\"podr_check[]\" data-title=\"".$value['name']."\" value=\"".$value['code']."\" /><span style=\"font-weight: normal; font-size: 11px;\" for=\"checkbox_".$value['code']."\"><a href=\"#\" data-id=\"".$value['code']."\" class=\"checkbox-podr-link-agreed\">".$value['name']."</a></span>";
                $level++;
                $this->_createPodrTreeAgreed($value['id'], $level);
                $level--; 
            }
            $this->_multidemensional_podr_agreed .= "</ul>";
        }
    }

    /*
        Формирования списков подразделений для формы редактировани задания поля "Передано в"
        @param $parent_id - идентификатор родителя подразделения
        @param $level - уровень вложенности дерева
        @return записывает в проперти класса _multidemensional_podr_transmitted html код дерева
    */
    public function _createPodrTreeTransmitted($parent_id, $level) {
        if (isset($this->_podr_data_array[$parent_id])) { 
            $this->_multidemensional_podr_transmitted .= "<ul>";
            foreach ($this->_podr_data_array[$parent_id] as $value) {
                if($this->_checkNextPodrTree($value['id'])) { // проверяем на вложенность подразделений
                    $class = "class=\"collapsed\""; // если есть вложенные элементы, указываем класс для сворачивания ветки дерева
                } else { // иначе класс не указываем, так как дерево разворачиваться не будет
                    $class = '';
                }

                $this->_multidemensional_podr_transmitted .= "<li ".$class."><input id=\"checkbox_".$value['code']."\" style=\"display: none;\" type='checkbox' name=\"podr_check[]\" data-title=\"".$value['name']."\" value=\"".$value['code']."\" /><span style=\"font-weight: normal; font-size: 11px;\" for=\"checkbox_".$value['code']."\"><a href=\"#\" data-id=\"".$value['code']."\" class=\"checkbox-podr-link-transmitted\">".$value['name']."</a></span>";
                $level++;
                $this->_createPodrTreeAgreed($value['id'], $level);
                $level--; 
            }
            $this->_multidemensional_podr_transmitted .= "</ul>";
        }
    }

    /*
        НЕ ИСПОЛЬЗУЕТСЯ. Метод формирования иерархии дерева подразделений. В настоящее время не используется, но оставлю на всякий случай.
    */
    public function _getMulti(&$rs, $parent) {
        $out = array();
        if (!isset($rs[$parent]))
        {
            return $out;
        }
        foreach ($rs[$parent] as $row)
        {
            $chidls = $this->_getMulti($rs, $row['id']);
            if ($chidls)
            {
                //$row['expanded'] = false;
                $row['children'] = $chidls;            
            }
            $out[] = $row;
        }
        return $out;
    }

    /*
        Возвращает массив подразделей
        @param $parent - указатель на родительсткое подразделение
        @param $i - уровень вложенности
        @return возвращает массив подразделений с индексом родителей
    */
    public function _getPodrData($parent = 1, $i = 0) {
        $query = new \yii\db\Query;
        $query->select('NAIMPODR AS name, VIDPODR as vid, KODPODR AS id, KODRODIT as parent, KODZIFR as code')
                ->from('STIGIT.V_F_PODR')
                ->orderBy('NAIMPODR asc');
        $command = $query->createCommand();
        $data = $command->queryAll();
        $return = array();

        foreach ($data as $value) { //обходим полученный массив и пишем данные в новый массв для последующей обработки
            $return[$value['parent']][] = $value;
        }
        return $return;
    }

    /*
        Метод live-search поиска по основанию
        @param $q - искомое значение
        @return возвращает массив найденных значений по свопадению с $q
    */
    public function actionDesignationsearch($q = null, $id = null) {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) { // проверяем, существует ли параметр поиска в запросе
            $query = new \yii\db\Query;
            $query->select('REASONID AS id, REASON AS text, ORDERNUM AS ordernum, PEOORDERNUM AS peoordernum, STAGENUM as stagenum, INCOME as income')
                ->from('STIGIT.V_F_REASONS')
                ->where('LOWER(REASON) LIKE \'%' . mb_strtolower($q, 'UTF-8') .'%\'')
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();

            $out['results'] = array_values($data);
        }
        return $out;
    }

    /*
        Метод live-search поиска по номеру заказа
        @param $q - искомое значение
        @return возвращает массив найденных значений по свопадению с $q
    */
    public function actionFilterordernumsearch($q = null, $id = null) {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) { // проверяем, существует ли параметр поиска в запросе
            $query = new \yii\db\Query;
            $query->select('ORDERNUM AS id, ORDERNUM AS text')
                ->from('STIGIT.V_PRP_DESIGNATION')
                ->where('LOWER(ORDERNUM) LIKE \'%' . mb_strtolower($q, 'UTF-8') .'%\'')
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    /*
        Метод live-search поиска по заказ ПЭО
        @param $q - искомое значение
        @return возвращает массив найденных значений по свопадению с $q
    */
    public function actionFilterpeoordernumsearch($q = null, $id = null) {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new \yii\db\Query;
            $query->select('PEOORDERNUM AS id, PEOORDERNUM AS text')
                ->from('STIGIT.V_PRP_DESIGNATION')
                ->where('LOWER(PEOORDERNUM) LIKE \'%' . mb_strtolower($q, 'UTF-8') .'%\'')
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    /*
        Метод live-search поиска по документации
        @param $q - искомое значение
        @return возвращает массив найденных значений по свопадению с $q
    */
    public function actionFilterdocumentationsearch($q = null, $id = null) {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) { // проверяем, существует ли параметр поиска в запросе
            $query = new \yii\db\Query;
            $query->select('DOC_CODE AS id, DOC_CODE AS text')
                ->from(Yii::$app->params['scheme_name'].'.TASK_DOCS')
                ->where('LOWER(DOC_CODE) LIKE \'%' . mb_strtolower($q, 'UTF-8') .'%\'')
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    /*
        Метод live-search поиска по заказу для формы редактирования
        @param $q - искомое значение
        @return возвращает массив найденных значений по свопадению с $q
    */
    public function actionOrdernumsearch($q = null, $id = null) {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) { // проверяем, существует ли параметр поиска в запросе 
            $query = new \yii\db\Query;
            $query->select('DOCUMENTID AS id, DESIGNATION AS designation, ORDERNUM AS text, PEOORDERNUM AS peoordernum')
                ->from('STIGIT.V_PRP_DESIGNATION')
                ->where('LOWER(ORDERNUM) LIKE \'%' . mb_strtolower($q, 'UTF-8') .'%\'')
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    /*
        Метод live-search поиска по заказу ПЭО для формы редактирования
        @param $q - искомое значение
        @return возвращает массив найденных значений по свопадению с $q
    */
    public function actionPeoordernumsearch($q = null, $id = null) {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) { // проверяем, существует ли параметр поиска в запросе
            $query = new \yii\db\Query;
            $query->select('DOCUMENTID AS id, DESIGNATION AS designation, ORDERNUM AS ordernum, PEOORDERNUM AS text')
                ->from('STIGIT.V_PRP_DESIGNATION')
                ->where('LOWER(PEOORDERNUM) LIKE \'%' . mb_strtolower($q, 'UTF-8') .'%\'')
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    /*
        Метод формирования списка сотрудников для конкретного подразделения
        @return возвращает HTML код списка сотрудников, входящий в выбранные подразделения
    */
    public function actionGetpersons() {
        
        if (Yii::$app->request->isAjax) { //проверка на асинхронный запрос
            $post_data = $_POST['selected_podr'];

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; // устанавливаем ответ сервера в виде JSON объекта
            $persons_list = '<ul>';
            foreach(json_decode($post_data) as $kodzifr => $value) { // обходим список полученных подразделений
                $query = new \yii\db\Query;
                $query->select('NAIMPODR AS name, VIDPODR as vid, KODPODR AS id, KODRODIT as parent, KODZIFR as code')
                        ->from('STIGIT.V_F_PODR')
                        ->where('KODZIFR = \''.$kodzifr.'\'');
                $command = $query->createCommand();
                $data = $command->queryOne();
                
                //формирование списка 
                $persons_list .= "<li class=\"expanded\"><span style=\"font-weight: normal; font-size: 12px;\">".$data['vid']." ".$data['code'].". ".$data['name']."</span>";
                $query = new \yii\db\Query;
                $query->select('*')
                    ->from('STIGIT.V_F_PERS')
                    ->where('KODZIFR = \'' . $kodzifr .'\'');
                $command = $query->createCommand();
                $data = $command->queryAll();
                $persons_list .= '<ul>';

                foreach ($data as $key => $value) { // обходим список сотрудников каждого подразделения

                    //получаем кол-во заданий для каждого исполнителя
                    $pers_tasks = \app\models\PersTasks::find()->where(['TN' => $value['TN'], 'DEL_TRACT_ID' => 0])->all();
                    if($pers_tasks) {
                        
                        $tasks_array = [];
                        foreach($pers_tasks as $task) {
                            $tasks_array[] = $task->TASK_ID;
                        }

                        $tasks = \app\models\Tasks::find()->where(['in', 'ID', $tasks_array])->all();
                        $list = [];
                        foreach($tasks as $task) {
                            $id = $task->ID;
                            $persons = \app\models\PersTasks::find()->where(['TASK_ID' => $id, 'DEL_TRACT_ID' => 0])->all();
                            if($persons) {
                                $states_array = [];
                               
                                foreach($persons as $person) {
                                    
                                    $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' =>$id, 'TN' => $person->TN, 'DEL_TRACT_ID' => 0])->one();
                                   
                                    $task_state = \app\models\TaskStates::find()->where(['IS_CURRENT' => 1, 'PERS_TASKS_ID' => $pers_tasks->ID, 'TASK_ID' => $id])->one();
                                    if($task_state) {
                                        $states_array[] = $task_state->STATE_ID;
                                    } else {
                                        $list[] = $id;
                                    }
                                }
                                if(!empty($states_array)) {
                                    $min_state = min($states_array);
                                    $state = \app\models\States::findOne($min_state);
                                
                                }
                            }
                            if(isset($state)) {

                                if($state->ID != 7 || $state->ID != 9) {
                                    $list[] = $id;
                                }
                            }

                        }

                        $list = array_unique($list);
                        $counter = count($list);
                        $counter = ' <span class="label label-info"><a target="_blank" href="'.\yii\helpers\Url::to(['/site/index', 'for_person' => $value['TN']]).'">Заданий в работе: '.$counter.'</a></span>';
                    } else {
                        $counter = '';
                    }

                    $persons_list .= "<li><input id=\"checkbox_".$value['TN']."\" type='checkbox' name=\"persons_check[]\" data-title=\"".$value['FIO']."\" value=\"".$value['TN']."\" /> <span style=\"font-size: 11px;\">".$value['FAM']." ".$value['IMJ']." ".$value['OTCH']." ".$counter."</span></li>";
                }
                $persons_list .= '</ul>';
            }
            $persons_list .= '</ul>';
            return $persons_list;
        }
    }

    /*
        Метод вывода информации по заданию в модальном окне - только для асинхронных запросов.
        @return возвращает JSON- объект с HTML кодом таблицы со значениями по полученному заданию, а так же значения для разрешения отображения ссылок на редактирование, либо сообщения о закрытом досутпе
    */
    public function actionGetissuedata() {
        //проверка на ajax запрос
        if (Yii::$app->request->isAjax) {
            $issue_id = $_POST['id'];
            //устанавливаем формат ответа JSON
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $issue = \app\models\Tasks::findOne($issue_id);

            //проверка на просмотр задания
            if($permission_open_issue_modal = \app\controllers\PermissionsController::_checkPermissions('open_issue_modal', $issue_id)) {
                $permission_open_issue_modal_in_current_status = \app\controllers\PermissionsController::_checkPermissions('open_issue_modal_in_current_status', $issue_id);
                //проверка на просмотр задания в текущем статусе
                if($permission_open_issue_modal_in_current_status == 'true' || $permission_open_issue_modal_in_current_status == 'true_for_boss') {

                    //получаем список подразделений задания
                    $podr_tasks = \app\models\PodrTasks::find()->where(['TASK_ID' => $issue->ID, 'DEL_TRACT_ID' => 0])->all();
                    if($podr_tasks) { // если существуют подразделения, то формируем их список
                        $podr_list = '';
                        $podr_list_kodzifr_array = [];
                        foreach($podr_tasks as $task) { //обходим список подразделений
                            $query = new \yii\db\Query;
                            $query->select('*')
                                ->from('STIGIT.V_F_PODR')
                                ->where('KODZIFR = \'' . trim($task->KODZIFR) .'\'');
                            $command = $query->createCommand();
                            $data = $command->queryOne();
                            if(isset($data['NAIMPODR'])) { // проверяем на существование названия подразделения (на момент разработки не для всех были названия)
                                $podr_list .= $data['NAIMPODR']."<br>";
                                $podr_list_kodzifr_array[] = $data['KODZIFR'];
                            }
                        }
                    }
                    //получаем список подразделений для поля "Согласовано"
                    $tasks_confirms = \app\models\TaskConfirms::find()->where(['TASK_ID' => $issue->ID, 'DEL_TRACT_ID' => 0])->all();
                    if($tasks_confirms) { // если существуют подразделения, то формируем их список
                        $task_confirms_list = '';
                        foreach($tasks_confirms as $task) {
                            $query = new \yii\db\Query;
                            $query->select('*')
                                ->from('STIGIT.V_F_PODR')
                                ->where('KODZIFR = \'' . trim($task->KODZIFR) .'\'');
                            $command = $query->createCommand();
                            $data = $command->queryOne();
                            if(isset($data['NAIMPODR'])) // проверяем на существование названия подразделения (на момент разработки не для всех были названия)
                                $task_confirms_list .= $data['NAIMPODR']."<br>";
                        }
                    } else { // иначе устанавливаем значение из константы
                        $task_confirms_list = self::_UNDEFINED;
                    }
                    //получаем список подразделений для поля "Передано в"
                    $tasks_docs_recvrs = \app\models\TaskDocsRecvrs::find()->where(['TASK_ID' => $issue->ID, 'DEL_TRACT_ID' => 0])->all();
                    if($tasks_docs_recvrs) { // если существуют подразделения, то формируем их список
                        $task_docs_recvrs_list = '';
                        foreach($tasks_docs_recvrs as $task) {
                            $query = new \yii\db\Query;
                            $query->select('*')
                                ->from('STIGIT.V_F_PODR')
                                ->where('KODZIFR = \'' . trim($task->KODZIFR) .'\'');
                            $command = $query->createCommand();
                            $data = $command->queryOne();
                            if(isset($data['NAIMPODR']))
                                $task_docs_recvrs_list .= $data['NAIMPODR']."<br>";
                        }
                    } else { // иначе устанавливаем значение из константы
                        $task_docs_recvrs_list = self::_UNDEFINED;
                    }
                    //получаем список пользователей, кому назначено задание
                    $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' => $issue->ID, 'DEL_TRACT_ID' => 0])->all();
                    $pers_list = '';
                    if($pers_tasks) {  // если существуют исполнители, то формируем их список
                        $persons_array = [];
                        foreach($pers_tasks as $task) {
                            $query = new \yii\db\Query;
                            $query->select('*')
                                ->from('STIGIT.V_F_PERS')
                                ->where('TN = \'' . $task->TN .'\'');
                            $command = $query->createCommand();
                            $data = $command->queryOne();
                            $pers_list .= $data['FIO']."<br>";
                            $persons_array[] = $data['TN'];
                        }
                    } else { // иначе устанавливаем значение из константы 
                        $pers_list = self::_UNDEFINED;
                        $persons_array = [];
                    }


                    $transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();
                    if(in_array(\Yii::$app->user->id, $persons_array)) { //save date for person if his owner this issue - формируем дату поступления в группу, если задание открыто первый раз

                        //check if window opened first time for date
                        $old_task_date_1 = \app\models\TaskDates::find()->where(['TASK_ID' => $issue_id, 'DEL_TRACT_ID' => 0, 'DATE_TYPE_ID' => 1])->one();
                        if(!$old_task_date_1) { // если задание открывается первый раз пишем дату
                            
                            $task_date_1 = new \app\models\TaskDates;
                            $task_date_1->TASK_TYPE_DATE = new \yii\db\Expression("to_date('" . date("Y-m-d H:i:s") . "','{$this->dateFormat}')");
                            $task_date_1->TASK_ID = $issue_id;
                            $task_date_1->DATE_TYPE_ID = 1;
                            $task_date_1->TRACT_ID = $transactions->ID;
                            $task_date_1->save();

                        }
                    }

                    //получаем дату поступления в группу
                    $old_task_date_2 = \app\models\TaskDates::find()->where(['TASK_ID' => $issue_id, 'DEL_TRACT_ID' => 0, 'DATE_TYPE_ID' => 2])->one();
                    if(!$old_task_date_2) { // если нет даты - устанавливаем ее
                        $transactions_for_date = \app\models\Transactions::findOne($issue->TRACT_ID);
                        $group_date = \Yii::$app->formatter->asDate($transactions_for_date->TRACT_DATETIME, 'php:Y-m-d');
                        $group_date_for_table = \Yii::$app->formatter->asDate($transactions_for_date->TRACT_DATETIME, 'php:d-m-Y');

                        $task_date_2 = new \app\models\TaskDates;
                        $task_date_2->TASK_TYPE_DATE = new \yii\db\Expression("to_date('" . $group_date . "','{$this->dateFormat}')");
                        $task_date_2->TASK_ID = $issue_id;
                        $task_date_2->DATE_TYPE_ID = 2;
                        $task_date_2->TRACT_ID = $transactions->ID;
                        $task_date_2->save();
                    } else { // иначе полученную дату форматируем под требуемый для вывода формат
                        $group_date_for_table = \Yii::$app->formatter->asDate($old_task_date_2->TASK_TYPE_DATE, 'php:d-m-Y');
                    }

                    //формируем поля по заданию
                    if(!empty($issue->SOURCENUM)) { // если переменная SOURCENUM содержит значение
                        $sourcenum = $issue->SOURCENUM;
                    } else { // иначе устанавливаем значение константы
                        $sourcenum = self::_UNDEFINED;
                    }
                    if(!empty($issue->ADDITIONAL_TEXT)) { // если переменная ADDITIONAL_TEXT содержит значение
                        $additional_text = $issue->ADDITIONAL_TEXT;
                    } else { // иначе устанавливаем значение константы
                        $additional_text = self::_UNDEFINED;
                    }
                    if(!empty($issue->REPORT_TEXT)) { // если переменная REPORT_TEXT содержит значение
                        $report_text = $issue->REPORT_TEXT;
                    } else { // иначе устанавливаем значение константы
                        $report_text = self::_UNDEFINED;
                    }

                    $task_date_first_time = \app\models\TaskDates::find()->where(['DATE_TYPE_ID' => '1', 'TASK_ID' => $issue_id, 'DEL_TRACT_ID' => 0])->one();
                    if($task_date_first_time) { // проверяем сущетсвует ли дата открытия задания
                        $first_date = \Yii::$app->formatter->asDate($task_date_first_time->TASK_TYPE_DATE, 'php:d-m-Y');
                    } else { // иначе устанавливаем значение константы
                        $first_date = self::_UNDEFINED;
                    }

                    $task_date_closed = \app\models\TaskDates::find()->where(['DATE_TYPE_ID' => '4', 'TASK_ID' => $issue_id, 'DEL_TRACT_ID' => 0])->one();
                    if($task_date_closed) { // проверяем существует ли дата завершения
                        $closed_date = \Yii::$app->formatter->asDate($task_date_closed->TASK_TYPE_DATE, 'php:d-m-Y');
                    } else { // иначе устанавливаем значение константы
                        $closed_date = self::_UNDEFINED;
                    }

                    $task_sector_date = \app\models\TaskDates::find()->where(['DATE_TYPE_ID' => '3', 'TASK_ID' => $issue_id, 'DEL_TRACT_ID' => 0])->one();
                    if($task_sector_date) { // проверяем дату поступления в сектор
                        $sektor_date = \Yii::$app->formatter->asDate($task_sector_date->TASK_TYPE_DATE, 'php:d-m-Y');
                    } else { // иначе устанавливаем значение константы
                        $sektor_date = self::_UNDEFINED;
                    }

                    $transactions = \app\models\Transactions::findOne($issue->TRACT_ID);

                    $task_docs = \app\models\TaskDocs::find()->where(['TASK_ID' => $issue_id, 'DEL_TRACT_ID' => 0])->all();
                    if($task_docs) { // в случае если сущетсвуют привязанные документы к заданию
                        $task_docs_list = '';
                        foreach($task_docs as $doc) { // формируем список документации
                            $task_docs_list .= '<a target="_blank" href="/storage/'.$doc->DOC_CODE.'">'.$doc->DOC_CODE.'</a><br>';
                        }
                    } else { // иначе устанавливаем значение константы
                        $task_docs_list = self::_UNDEFINED;
                    }

                    if($permission_open_issue_modal_in_current_status == 'true_for_boss') {
                        $task_states_list = '';
                    } else {
                        $task_state = $issue->_getLastTaskStatusWithText($issue->ID);
                        if($task_state != '') { // если сущетсвует текущий статус задания
                            $task_states_list = $task_state;
                        } else { // иначе устанавливаем значение константы
                            $task_states_list = self::_UNDEFINED;
                        }
                    }
                    


                    //формируем html - таблицу для вывода информации
                    $result_table = '<table class="table table-bordered">';
                    $result_table .= '
                                    <tr>
                                        <td class="issue-table-label">Подразделения</td>
                                        <td>'.$podr_list.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Исполнитель</td>
                                        <td>'.$pers_list.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Исходящий номер</td>
                                        <td>'.$issue->TASK_NUMBER.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Входящий номер</td>
                                        <td>'.$sourcenum.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Заказ (изделие)</td>
                                        <td>'.$issue->ORDERNUM.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Заказ ПЭО</td>
                                        <td>'.$issue->PEOORDERNUM.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Срок выполнения</td>
                                        <td>'.\Yii::$app->formatter->asDate($issue->DEADLINE, 'php:d-m-Y').'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Содержание</td>
                                        <td>'.$issue->TASK_TEXT.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Дополнительные указания</td>
                                        <td>'.$additional_text.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Дата поступления в сектор</td>
                                        <td>'.$sektor_date.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Дата поступления в группу</td>
                                        <td>'.$group_date_for_table.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Дата поступления исполнителю</td>
                                        <td>'.$first_date.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Дата завершения</td>
                                        <td>'.$closed_date.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Выпущенная документация</td>
                                        <td>'.$task_docs_list.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Отчет о работе</td>
                                        <td>'.$report_text.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Согласовано с</td>
                                        <td>'.$task_confirms_list.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Передано в</td>
                                        <td>'.$task_docs_recvrs_list.'</td>  
                                    </tr>
                                    <tr>
                                        <td class="issue-table-label">Состояние</td>
                                        <td>'.$task_states_list.'</td>  
                                    </tr>
                    ';
                    $result_table .= '</table>';

                    //проверяем есть ли доступ на редактирование для отображения ссылки (кнопки) редактирования
                    $permission_for_update = \app\controllers\PermissionsController::_checkPermissions('update_issue', $issue_id);
                    if($permission_for_update == 'update_issue_for_boss' || $permission_for_update == 'update_issue_for_person') {
                        $permissions_for_write = 1;
                    } else {
                        $permissions_for_write = 0;
                    }

                    // возвращаем json массив
                    $items = ['permissons_for_read' => 1, 'permissions_for_write' => $permissions_for_write, 'issue_id' => $issue_id, 'issue_designation' => $issue->TASK_NUMBER, 'result_table' => $result_table];
                    
                } else {
                   
                    $error_message = $permission_open_issue_modal_in_current_status;
                    $items = ['permissons_for_read' => 0, 'error_message' => $error_message, 'issue_id' => $issue_id, 'issue_designation' => $issue->TASK_NUMBER];
                }
            } else {
                $error_message = 'У Вас нет прав на просмотр "Форма свойств задания"';
                $items = ['permissons_for_read' => 0, 'error_message' => $error_message, 'issue_id' => $issue_id, 'issue_designation' => $issue->TASK_NUMBER];
            } 
            return $items;  
        }
    }

    /*
        Метод загрузки документации
        @return возвращает JSON - объект об успешной либо не успешной загрузке файлов документации
    */
    public function actionDocumentsupload() {
        
        if(Yii::$app->request->post()) { //проверка что запрос асинхронный
            $formats = Yii::$app->request->post();
            $transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();
            $task_id = $_GET['task_id'];
            if($_FILES) { // проверяем переданы ли вообще файлы для загрузки
                $no_error = 0;
                $errors = '';
                //обходим все загружаемые файлы
                foreach($_FILES['documentation']['name'] as $key => $filename) {
                    
                    //пишем в модель и сохраняем
                    $model = new \app\models\TaskDocs;
                    $model->DOC_CODE = $filename;
                    $model->TASK_ID = $task_id;
                    $model->TRACT_ID = $transactions->ID;
                    $model->FORMAT_QUANTITY = $formats[$key];
                    $pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' =>$task_id, 'TN' => \Yii::$app->user->id, 'DEL_TRACT_ID' => 0])->one();
                    $model->PERS_TASKS_ID = $pers_tasks->ID;

                    if($model->validate()) { // проверяем на валидность загруженных данных
                        //загрузка файлов
                        move_uploaded_file($_FILES['documentation']['tmp_name'][$key], Yii::$app->params['documents_dir'] . $filename);
                        if($model->save()) { // если файл сохранен, то возвращаем код ошибки 0
                            $no_error = 0;
                        }
                    } else { //сообщение об ошибке если валидация не прошла, код ошибки 1 и сам текст ошибки
                        foreach ($model->errors as $key => $value) {
                            $errors .= '<b>Документ</b> '.$filename.': <b>'. implode("|",$value).'</b><br>';
                        }
                        $no_error = 1;
                    }
                }
                //в зависимости от ошибки формируем json-ответ
                if($no_error == 0) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return [];
                } else {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ['error' => $errors];
                }
            } else { // файлы не были посланы в запросе - ничего не делаем
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return [];
            }

        }
    }

    /*
        Метод удаления загруженных документов
        @return возвращает пустой массив
    */
    public function actionDocumentdelete() {
        if (Yii::$app->request->isAjax) { // проверка на ajax запрос
            $transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();
            $doc_id = $_POST['key'];
            $document = \app\models\TaskDocs::findOne($doc_id);
            $document->DEL_TRACT_ID = $transactions->ID;
            $document->save();
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [];
        }
    }

    /*
        Метод изменения задания
        @param $id - идентификатор задания для редактирования
        @return если есть доступ и сущетсвует задание переданное в параметре, то рендерим страницу и передаем в нее объект модели задания, иначе формируем сообщение об ошибке
    */
    public function actionUpdateissue($id) {

        /*
            проверка на доступ к заданию пользователя
        */
        //проверка на просмотр задания
        if($permission_open_issue_modal = \app\controllers\PermissionsController::_checkPermissions('open_issue_modal', $id)) {
            $permission_open_issue_modal_in_current_status = \app\controllers\PermissionsController::_checkPermissions('open_issue_modal_in_current_status', $id);
            //проверка на просмотр задания в текущем статусе
            if($permission_open_issue_modal_in_current_status == 'true' || $permission_open_issue_modal_in_current_status == 'true_for_boss') {
                $permission_for_update = \app\controllers\PermissionsController::_checkPermissions('update_issue', $id);
                //проверка на редактирование задания
                if($permission_for_update == 'update_issue_for_boss' || $permission_for_update == 'update_issue_for_person') {
                    //формируем модель данных для формы редактирования
                    $model = $this->findModel($id);
                    
                    $model->DEADLINE = \Yii::$app->formatter->asDate($model->DEADLINE, 'php:d-m-Y');
                    $podr_tasks = \app\models\PodrTasks::findAll(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0]);
                    $task_confirms = \app\models\TaskConfirms::findAll(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0]);
                    $task_docs_recvrs = \app\models\TaskDocsRecvrs::findAll(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0]);
                    $pers_tasks = \app\models\PersTasks::findAll(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0]);

                    $task_date_3 = \app\models\TaskDates::find()->where(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0, 'DATE_TYPE_ID' => 3])->one();
                    if($task_date_3) { // если существует дата DATE_TYPE_ID => 3
                        $model->task_type_date_3 = \Yii::$app->formatter->asDate($task_date_3->TASK_TYPE_DATE, 'php:d-m-Y');  
                        $last_model_task_type_date_3 = $model->task_type_date_3;
                    } else { // иначе устанавливаем значние даты null
                        $last_model_task_type_date_3 = null;
                    }

                    $task_date_1 = \app\models\TaskDates::find()->where(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0, 'DATE_TYPE_ID' => 1])->one();
                    if($task_date_1) { // если существует дата DATE_TYPE_ID => 1
                        $model->task_type_date_1 = \Yii::$app->formatter->asDate($task_date_1->TASK_TYPE_DATE, 'php:d-m-Y');  
                        $last_model_task_type_date_1 = $model->task_type_date_1;
                    } else { // иначе устанавливаем значние даты null
                        $last_model_task_type_date_1 = null;
                    }

                    $task_date_4 = \app\models\TaskDates::find()->where(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0, 'DATE_TYPE_ID' => 4])->one();
                    if($task_date_4) { // если существует дата DATE_TYPE_ID => 4
                        $model->task_type_date_4 = \Yii::$app->formatter->asDate($task_date_4->TASK_TYPE_DATE, 'php:d-m-Y');  
                        $last_model_task_type_date_4 = $model->task_type_date_4;
                    } else { // иначе устанавливаем значние даты null
                        $last_model_task_type_date_4 = null;
                    }

                    $transactions_for_date = \app\models\Transactions::findOne($model->TRACT_ID);
                    $model->transactions_tract_datetime = \Yii::$app->formatter->asDate($transactions_for_date->TRACT_DATETIME, 'php:d-m-Y');  

                    if($permission_for_update == 'update_issue_for_person') {
                        $pers_tasks_this = \app\models\PersTasks::find()->where(['TASK_ID' =>$model->ID, 'TN' => \Yii::$app->user->id, 'DEL_TRACT_ID' => 0])->one();
                        $task_state = \app\models\TaskStates::find()->where(['PERS_TASKS_ID' => $pers_tasks_this->ID, 'IS_CURRENT' => 1])->one();
                        if($task_state) { // проверяем есть ли статус
                            $model->state = $task_state->STATE_ID;
                            $last_state = $task_state->STATE_ID;
                        } else { // иначе устанавливаем значние состояния
                            $last_state = null;
                        }
                        $show_states = 1;
                        $model->scenario = \app\models\Tasks::SCENARIO_UPDATE_PERSON;
                    } else {
                        //не отображаем состояния, если редактирует руководитель
                        $show_states = 0;
                        $model->scenario = \app\models\Tasks::SCENARIO_UPDATE_BOSS;
                    }

                    //ajax - валидация формы редактирования
                    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) { // если данные были переданы в ajax - запросе
                        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return \yii\widgets\ActiveForm::validate($model);
                    }

                    if ($model->load(Yii::$app->request->post())) { //если был сабмит формы - пишем нове данные в модель и сохраняем ее       
                                     
                        $transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();

                        if(!empty($model->hidden_ordernum))
                            $model->ORDERNUM = $model->hidden_ordernum;
                        if(!empty($model->hidden_peoordernum))
                            $model->PEOORDERNUM = $model->hidden_peoordernum;

                        $deadline = explode('-', $model->DEADLINE);
                        $deadline_formatted = $deadline[2].'-'.$deadline[1].'-'.$deadline[0];
                        
                        $model->DEADLINE = new \yii\db\Expression("to_date('" . $deadline_formatted . "','{$this->dateFormat}')");

                        if($model->save()) { // если объект модели Tasks сохранен в БД
                            
                            /*
                            Удаляем (помечаем) старые данные и сохраняем модель PODR_TASKS----------------------------------------------
                            */
                            //get isset tasks array
                            $isset_podr_tasks = \app\models\PodrTasks::find()->where(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0])->all();
                            $isset_podr_tasks_array = [];
                            if($isset_podr_tasks) { // проверяем существуют ли подразделения в текущем задании
                                foreach($isset_podr_tasks as $isset_podr_task) { // обходим список подразделений и формируем массив для изменения
                                    $isset_podr_tasks_array[$isset_podr_task->ID] = $isset_podr_task->KODZIFR;
                                }
                            }    

                            $new_podr_tasks_array = explode(',', $model->podr_list);
                            foreach($isset_podr_tasks_array as $key_id => $val_kodzifr) { // обходим массив текущих подразделений
                                if(!in_array($val_kodzifr, $new_podr_tasks_array)) { // если в текущем массиве есть новоем подразделение
                                    
                                    //помечаем как удаленный
                                    $podr_task = \app\models\PodrTasks::findOne($key_id);
                                    $podr_task->DEL_TRACT_ID = $transactions->ID;
                                    $podr_task->save();
                                }
                            }
                            foreach($new_podr_tasks_array as $kodzifr) { // аналогично, только добавление нового подразделения
                                if(!in_array($kodzifr, $isset_podr_tasks_array)) { // проверка на не вхождение в массив
                                    //добавляем новое значение
                                    
                                    $podr_task = new \app\models\PodrTasks;
                                    $podr_task->TASK_ID = $model->ID;
                                    $podr_task->KODZIFR = trim($kodzifr);
                                    $podr_task->TRACT_ID = $transactions->ID;
                                    $podr_task->save();
                                }
                            }
                            /*
                                ------------------------------------------------------------------------------------------------------------
                            */
                            
                            /*
                                Удаляем (помечаем) старые данные и сохраняем модель PERS_TASKS----------------------------------------------
                            */ 
                            $isset_pers_tasks = \app\models\PersTasks::find()->where(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0])->all();
                            $isset_pers_tasks_array = [];
                            if($isset_pers_tasks) { // проверяем, существуют ли исполнители в текущем задании
                                foreach($isset_pers_tasks as $isset_pers_task) { // формируем массив текущих исполнителей
                                    $isset_pers_tasks_array[$isset_pers_task->ID] = $isset_pers_task->TN;
                                }
                            }
                            $new_pers_tasks_array = explode(',', $model->persons_list);
                            foreach($isset_pers_tasks_array as $key_id => $val_tn) { // обходим текущий массив
                                if(!in_array($val_tn, $new_pers_tasks_array)) {
                                    //помечаем как удаленный
                                    $pers_task = \app\models\PersTasks::findOne($key_id);
                                    $pers_task->DEL_TRACT_ID = $transactions->ID;
                                    $pers_task->save();

                                    //удаляем (IS_CURRENT = 0) состояние задания для пользователя
                                    $pers_tasks_this = \app\models\PersTasks::find()->where(['TASK_ID' =>$model->ID, 'TN' => \Yii::$app->user->id, 'DEL_TRACT_ID' => 0])->one();
                                    $task_states = \app\models\TaskStates::find()->where(['IS_CURRENT' => 1, 'PERS_TASKS_ID' => $pers_tasks_this->ID])->one();
                                    //проверяем, есть ли статусы у пользователя
                                    if($task_states) {
                                        $task_states->IS_CURRENT = 0;
                                        $task_states->save();
                                    }
                                }
                            }
                            foreach($new_pers_tasks_array as $tn) {
                                if(!in_array($tn, $isset_pers_tasks_array)) {
                                    //добавляем новое значение
                                    $person_task = new \app\models\PersTasks;
                                    $person_task->TASK_ID = $model->ID;
                                    $person_task->TN = $tn;
                                    $person_task->TRACT_ID = $transactions->ID;
                                    $person_task->save();
                                }
                            }
                            /*
                                ------------------------------------------------------------------------------------------------------------
                            */ 

                            /*
                                Проверяем на изменение и сохраняем все даты в таблицу TASK_DATES
                            */ 
                                if($last_model_task_type_date_3 != $model->task_type_date_3) {
                                    $old_task_date_3 = \app\models\TaskDates::find()->where(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0, 'DATE_TYPE_ID' => 3])->one();
                                    if($old_task_date_3) {
                                        $old_task_date_3->DEL_TRACT_ID = $transactions->ID;
                                        $old_task_date_3->save();
                                    }

                                    $task_date_3 = new \app\models\TaskDates;
                                    $task_type_date_3 = explode('-', $model->task_type_date_3);
                                    $task_type_date_3_formatted = $task_type_date_3[2].'-'.$task_type_date_3[1].'-'.$task_type_date_3[0];
                                    $task_date_3->TASK_TYPE_DATE = new \yii\db\Expression("to_date('" . $task_type_date_3_formatted . "','{$this->dateFormat}')");
                                    $task_date_3->TASK_ID = $model->ID;
                                    $task_date_3->DATE_TYPE_ID = 3;
                                    $task_date_3->TRACT_ID = $transactions->ID;
                                    $task_date_3->save();
                                }

                                if($last_model_task_type_date_1 != $model->task_type_date_1) {
                                    $old_task_date_1 = \app\models\TaskDates::find()->where(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0, 'DATE_TYPE_ID' => 1])->one();
                                    if($old_task_date_1) {
                                        $old_task_date_1->DEL_TRACT_ID = $transactions->ID;
                                        $old_task_date_1->save();
                                    }

                                    $task_date_1 = new \app\models\TaskDates;
                                    $task_type_date_1 = explode('-', $model->task_type_date_1);
                                    $task_type_date_1_formatted = $task_type_date_1[2].'-'.$task_type_date_1[1].'-'.$task_type_date_1[0];
                                    $task_date_1->TASK_TYPE_DATE = new \yii\db\Expression("to_date('" . $task_type_date_1_formatted . "','{$this->dateFormat}')");
                                    $task_date_1->TASK_ID = $model->ID;
                                    $task_date_1->DATE_TYPE_ID = 1;
                                    $task_date_1->TRACT_ID = $transactions->ID;
                                    $task_date_1->save();
                                }

                                if($last_model_task_type_date_4 != $model->task_type_date_4) {
                                    $old_task_date_4 = \app\models\TaskDates::find()->where(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0, 'DATE_TYPE_ID' => 4])->one();
                                    if($old_task_date_4) {
                                        $old_task_date_4->DEL_TRACT_ID = $transactions->ID;
                                        $old_task_date_4->save();
                                    }

                                    $task_date_4 = new \app\models\TaskDates;
                                    $task_type_date_4 = explode('-', $model->task_type_date_4);
                                    $task_type_date_4_formatted = $task_type_date_4[2].'-'.$task_type_date_4[1].'-'.$task_type_date_4[0];
                                    $task_date_4->TASK_TYPE_DATE = new \yii\db\Expression("to_date('" . $task_type_date_4_formatted . "','{$this->dateFormat}')");
                                    $task_date_4->TASK_ID = $model->ID;
                                    $task_date_4->DATE_TYPE_ID = 4;
                                    $task_date_4->TRACT_ID = $transactions->ID;
                                    $task_date_4->save();
                                }
                                


                            /*
                                ------------------------------------------------------------------------------------------------------------
                            */

                            /*
                                Обработка полей "Согласовано с"
                            */
                                //get isset tasks array
                                $isset_podr_tasks_confirms = \app\models\TaskConfirms::find()->where(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0])->all();
                                $isset_podr_tasks_confirms_array = [];
                                if($isset_podr_tasks_confirms) {
                                    foreach($isset_podr_tasks_confirms as $isset_podr_task_confirm) {
                                        $isset_podr_tasks_confirms_array[$isset_podr_task_confirm->ID] = $isset_podr_task_confirm->KODZIFR;
                                    }
                                }    

                                $new_podr_tasks_confirms_array = explode(',', $model->agreed_podr_list);

                                foreach($isset_podr_tasks_confirms_array as $key_id => $val_kodzifr) {
                                    if(!in_array($val_kodzifr, $new_podr_tasks_confirms_array)) {
                                        
                                        //помечаем как удаленный
                                        $task_confirm = \app\models\TaskConfirms::findOne($key_id);
                                        $task_confirm->DEL_TRACT_ID = $transactions->ID;
                                        $task_confirm->save();
                                    }
                                }
                                foreach($new_podr_tasks_confirms_array as $kodzifr) {
                                    if(!in_array($kodzifr, $isset_podr_tasks_confirms_array)) {
                                        //добавляем новое значение
                                        
                                        $task_confirms = new \app\models\TaskConfirms;
                                        $task_confirms->TASK_ID = $model->ID;
                                        $task_confirms->KODZIFR = $kodzifr;
                                        $task_confirms->TRACT_ID = $transactions->ID;
                                        $task_confirms->save();
                                    }
                                }

                            /*
                                ------------------------------------------------------------------------------------------------------------
                            */

                            /*
                                Обработка полей "Передано в"
                            */
                                //get isset tasks array
                                $isset_podr_task_docs_recvrs = \app\models\TaskDocsRecvrs::find()->where(['TASK_ID' => $model->ID, 'DEL_TRACT_ID' => 0])->all();
                                $isset_podr_task_docs_recvrs_array = [];
                                if($isset_podr_task_docs_recvrs) {
                                    foreach($isset_podr_task_docs_recvrs as $isset_podr_task_docs_recvrs) {
                                        $isset_podr_task_docs_recvrs_array[$isset_podr_task_docs_recvrs->ID] = $isset_podr_task_docs_recvrs->KODZIFR;
                                    }
                                }    

                                $new_podr_task_docs_recvrs_array = explode(',', $model->transmitted_podr_list);



                                foreach($isset_podr_task_docs_recvrs_array as $key_id => $val_kodzifr) {
                                    if(!in_array($val_kodzifr, $new_podr_task_docs_recvrs_array)) {
                                        
                                        //помечаем как удаленный
                                        $task_doc = \app\models\TaskDocsRecvrs::findOne($key_id);
                                        $task_doc->DEL_TRACT_ID = $transactions->ID;
                                        $task_doc->save();
                                    }
                                }
                                foreach($new_podr_task_docs_recvrs_array as $kodzifr) {
                                    if(!in_array($kodzifr, $isset_podr_task_docs_recvrs_array)) {
                                        //добавляем новое значение
                                        
                                        $task_doc = new \app\models\TaskDocsRecvrs;
                                        $task_doc->TASK_ID = $model->ID;
                                        $task_doc->KODZIFR = $kodzifr;
                                        $task_doc->TRACT_ID = $transactions->ID;
                                        $task_doc->save();
                                    }
                                }

                            /*
                                ------------------------------------------------------------------------------------------------------------
                            */

                            /*
                                Обработка состояния задания
                            */
                                if($show_states == 1) {
                                    if($last_state != $model->state) {

                                        $new_state = new \app\models\TaskStates;
                                        $new_state->TASK_ID = $model->ID;
                                        $new_state->STATE_ID = $model->state;
                                        $new_state->TRACT_ID = $transactions->ID;
                                        $new_state->IS_CURRENT = 1;
                                        $pers_tasks_this = \app\models\PersTasks::find()->where(['TASK_ID' =>$model->ID, 'TN' => \Yii::$app->user->id, 'DEL_TRACT_ID' => 0])->one();
                                        $new_state->PERS_TASKS_ID = $pers_tasks_this->ID;
                                        $new_state->save();

                                        //обновление поля IS_CURRENT для предыдущего состояния
                                        if($last_state != null) {
                                            $task_state->IS_CURRENT = 0;
                                            $task_state->save();
                                        }
                                    }
                                }

                            /*
                                ------------------------------------------------------------------------------------------------------------
                            */

                            \Yii::$app->getSession()->setFlash('flash_message_success', 'Изменения сохранены');
                            return $this->refresh();

                        } else {
                            print_r($model->errors); die();
                        }

                    } elseif (Yii::$app->request->isAjax) {
                        $this->_podr_data_array = $this->_getPodrData();
                        $this->_createPodrTree(1, 0, 'checkbox-podr-link');
                        $this->_createPodrTree(1, 0, 'checkbox-podr-link-agreed');
                        $this->_createPodrTree(1, 0, 'checkbox-podr-link-transmitted');
                        
                        return $this->renderAjax('_formupdateissue', [
                            'model' => $model,
                            'not_ajax' => false,
                            'podr_data' => $this->_multidemensional_podr,
                            'podr_tasks' => $podr_tasks,
                            'pers_tasks' => $pers_tasks,
                            'agreed_podr_data' => $this->_multidemensional_podr_agreed,
                            'transmitted_podr_data' => $this->_multidemensional_podr_transmitted,
                        ]);
                    } else {
                        $this->layout = 'updateissue';
                        $this->_podr_data_array = $this->_getPodrData();
                        $this->_createPodrTree(1, 0, 'checkbox-podr-link');
                        $this->_createPodrTree(1, 0, 'checkbox-podr-link-agreed');
                        $this->_createPodrTree(1, 0, 'checkbox-podr-link-transmitted');
                        

                        return $this->render('_formupdateissue', [
                            'model' => $model,
                            'not_ajax' => true,
                            'podr_data' => $this->_multidemensional_podr,
                            'podr_tasks' => $podr_tasks,
                            'pers_tasks' => $pers_tasks,
                            'task_confirms' => $task_confirms,
                            'task_docs_recvrs' => $task_docs_recvrs,
                            'agreed_podr_data' => $this->_multidemensional_podr_agreed,
                            'transmitted_podr_data' => $this->_multidemensional_podr_transmitted,
                            'show_states' => $show_states
                        ]);
                    }
                } else {
                    throw new \yii\web\ForbiddenHttpException('У Вас нет прав на "Форму свойств задания"'); 
                }
            } else {
                throw new \yii\web\ForbiddenHttpException($permission_open_issue_modal_in_current_status); 
            }
        } else {
            throw new \yii\web\ForbiddenHttpException('У Вас нет прав на "Форму свойств задания"'); 
        }
    }


    /*
        Формирование модели задания
    */
    protected function findModel($id)
    {
        if (($model = \app\models\Tasks::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
        Метод авторизации
    */
    public function actionLogin()
    {
        $this->layout = 'empty';

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            //записываем в таблицу TRANSACTIONS данные
            $transactions = new Transactions;
            $transactions->TN = new \yii\db\Expression('TO_NUMBER('.\Yii::$app->user->id.')');
            $transactions->TRACT_DATETIME = new \yii\db\Expression('SYSDATE');
            $transactions->USER_IP = $_SERVER['REMOTE_ADDR'];
            $transactions->insert();

            //заносим в сессию данные пользователя
            $query = new \yii\db\Query;
            $query->select('*')
                    ->from('STIGIT.V_DOLG_PODR')
                    ->innerJoin('STIGIT.V_F_SHRAS', 'STIGIT.V_DOLG_PODR.IDDOLG = STIGIT.V_F_SHRAS.IDDOLG ')
                    ->where('TN = \'' . \Yii::$app->user->id .'\'');
            $command = $query->createCommand();
            $user_dolg_podr_data = $command->queryAll();
            $user_dolg_podr_data_block = 'табельный номер: <b>'.\Yii::$app->user->id.'</b>';
            if($user_dolg_podr_data) {
                $iddolg_array = [];
                $idpodr_array = [];
                foreach ($user_dolg_podr_data as $data_dolg_podr) {
                    if(!empty($data_dolg_podr['NAIMDOLG'])) {
                        if(!in_array($data_dolg_podr['IDDOLG'], $iddolg_array)) {
                            $user_dolg_podr_data_block .= ', должность <b>'.$data_dolg_podr['NAIMDOLG'].'</b>';
                            $iddolg_array[] = $data_dolg_podr['IDDOLG'];
                            \Yii::$app->session->set('user.user_iddolg', $data_dolg_podr['IDDOLG']);
                        }
                    }
                    
                    if(!empty($data_dolg_podr['KODPODR_M'])) {
                        if(!in_array($data_dolg_podr['KODPODR_M'], $idpodr_array)) {
                            //get podr
                            $query_kodzifr = new \yii\db\Query;
                            $query_kodzifr->select('NAIMPODR AS naimpodr, KODZIFR as kodzifr')
                                ->from('STIGIT.V_F_PODR')
                                ->where('KODPODR = \'' . $data_dolg_podr['KODPODR_M'] .'\'');
                            $command_kodzifr = $query_kodzifr->createCommand();
                            $naimpodr_data = $command_kodzifr->queryOne(); 
                            if($naimpodr_data) {
                                $user_dolg_podr_data_block .= '<br>руководимое подразделение: <b>'.$naimpodr_data['naimpodr'].'</b>';
                                \Yii::$app->session->set('user.user_boss_of', $naimpodr_data['kodzifr']);
                            }
                            $idpodr_array[] = $data_dolg_podr['KODPODR_M'];
                        }
                    }

                }
            }

            \Yii::$app->session->set('user.user_dolg_podr_data_block', $user_dolg_podr_data_block);

            //пишем в сессию массив прав доступа
            $actions = \app\models\Actions::find()->all();
            $actions_array = [];
            foreach ($actions as $key => $value) {
                $actions_array[$value->ACTION] = $value->ID;
            }
            \Yii::$app->session->set('user.permissions_array', $actions_array);


            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /*
        Метод logout
    */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /*
        Формирования списка последющих состояний
    */
    public function actionSetstatenext() {

        if (Yii::$app->request->isAjax) {
            $this_value = $_POST['this_value'];
            $parent_value = $_POST['parent_value'];
            $status = $_POST['status'];

            $transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();

            if($status == 'checked') {
                $next_state = new \app\models\StatesNext;
                $next_state->STATE_ID = $parent_value;
                $next_state->NEXT_STATE_ID = $this_value;
                $next_state->TRACT_ID = $transactions->ID;
                if($next_state->save()) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ['error' => 0];
                } else {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ['error' => 1];
                }

            } else {
                //update DEL_TRACT_ID for old value
                $next_state_old = \app\models\StatesNext::find()->where(['STATE_ID' => $parent_value, 'NEXT_STATE_ID' => $this_value])->one();
                $next_state_old->DEL_TRACT_ID = $transactions->ID;
                if($next_state_old->save()) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ['error' => 0];
                } else {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ['error' => 1];
                }

            }
        }

    }

    /*
        Метод назначения прав на чтение и запись
    */
    public function actionSetpermissions() {
        if (Yii::$app->request->isAjax) {

            $permissions_for_states_change = \app\models\Permissions::find()->where('(SUBJECT_TYPE = :subject_type and SUBJECT_ID = :user_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL = :perm_level and ACTION_ID = :action) or 
                (SUBJECT_TYPE = :subject_type_dolg and SUBJECT_ID = :dolg_id and DEL_TRACT_ID = :del_tract and PERM_LEVEL = :perm_level and ACTION_ID = :action)', ['subject_type_dolg' => 1, 'dolg_id' => \Yii::$app->session->get('user.user_iddolg'), 'action' => \app\controllers\PermissionsController::_getPermissionsArray('permission_management'), 'subject_type' => 2, 'user_id' => \Yii::$app->user->id, 'del_tract' => 0, 'perm_level' => 2])->one();
            if($permissions_for_states_change) {

                $parent_id = $_POST['parent_id'];
                $parent_type = $_POST['parent_type'];
                $original_id = $_POST['original_id'];
                $original_type = $_POST['original_type'];
                $transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();

                switch($original_type) {
                    case "actions": $perm_type = 1; break;
                    case "states": $perm_type = 2; break;
                }

                switch($parent_type) {
                    case "v_f_shra": $subject_type = 1; break;
                    case "v_f_pers": $subject_type = 2; break;
                }

                $permissions = new \app\models\Permissions;
                $permissions->SUBJECT_ID = $parent_id;
                $permissions->SUBJECT_TYPE = $subject_type;
                $permissions->ACTION_ID = $original_id;
                $permissions->TRACT_ID = $transactions->ID;
                $permissions->PERM_TYPE = $perm_type;

                if($permissions->save()) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ['error' => 0, 'inserted_id' => $permissions->ID];
                } else {
                    //print_r($permissions->errors); die();
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ['error' => 1];
                }
            }
            
        }

    }

    /*
        Удаление действий и состояний из дерева прав
    */
    public function actionDeletepermissions() {

        if (Yii::$app->request->isAjax) {
            $permission_id = $_POST['permission_id'];
            
            $transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();

            $permissions = \app\models\Permissions::findOne($permission_id);
            $permissions->DEL_TRACT_ID = $transactions->ID;

            if($permissions->save()) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['error' => 0];
            } else {
                //print_r($permissions->errors); die();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['error' => 1];
            }
            
        }

    }

    /*
        Присваивание состоянию или действию уровня доступа
    */    
    public function actionSetpermlevel() {

        if (Yii::$app->request->isAjax) {

            $permission_id = $_POST['permission_id'];
            $level = $_POST['level'];
            
            //$transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();

            $permissions = \app\models\Permissions::findOne($permission_id);
            $permissions->PERM_LEVEL = $level; 

            if($permissions->save()) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['error' => 0];
            } else {
                //print_r($permissions->errors); die();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['error' => 1];
            }
        }

    }

    /*
        Метод ручной установки статуса в выбранные задания
    */
    public function actionChangestatus() {
        if (Yii::$app->request->isAjax) {

            $status_id = $_POST['status'];
            $selected_issues = $_POST['selected_issues'];


            $user_cant_permissions_on = [];
            $user_have_permission_and_status_changed_on = [];
            foreach(json_decode($selected_issues) as $issue) {


                $pers_tasks_this = \app\models\PersTasks::find()->where(['TASK_ID' =>$issue, 'TN' => \Yii::$app->user->id, 'DEL_TRACT_ID' => 0])->one();
                
                

                //проверяем имеет ли доступ пользователь к заданию и входит ли вообще в него
                if($pers_tasks_this) {
                    $task_state = \app\models\TaskStates::find()->where(['PERS_TASKS_ID' => $pers_tasks_this->ID, 'IS_CURRENT' => 1])->one();

                    if($task_state) { // проверяем есть ли статус
                        $last_state = $task_state->STATE_ID;
                    } else { // иначе устанавливаем значние даты null
                        $last_state = null;
                    }


                    if($last_state != $status_id) {

                        $transactions = \app\models\Transactions::find()->where(['TN' => \Yii::$app->user->id ])->orderBy('ID DESC')->one();

                        $new_state = new \app\models\TaskStates;
                        $new_state->TASK_ID = $issue;
                        $new_state->STATE_ID = $status_id;
                        $new_state->TRACT_ID = $transactions->ID;
                        $new_state->IS_CURRENT = 1;
                        $pers_tasks_this = \app\models\PersTasks::find()->where(['TASK_ID' =>$issue, 'TN' => \Yii::$app->user->id, 'DEL_TRACT_ID' => 0])->one();
                        $new_state->PERS_TASKS_ID = $pers_tasks_this->ID;
                        $new_state->save();

                        $user_have_permission_and_status_changed_on[] = $issue;

                        //обновление поля IS_CURRENT для предыдущего состояния
                        if($last_state != null) {
                            $task_state->IS_CURRENT = 0;
                            $task_state->save();
                        }
                    }

                } else {
                    $user_cant_permissions_on[] = $issue;
                }

            }

            $string_status_changed = '';
            if(!empty($user_have_permission_and_status_changed_on)) {
                foreach ($user_have_permission_and_status_changed_on as $issue) {
                    $task = \app\models\Tasks::findOne($issue);
                    $string_status_changed .= 'Задание №'.$task->TASK_NUMBER.',';
                }
            }

            $string_status_not_changed = '';
            if(!empty($user_cant_permissions_on)) {
                
                foreach ($user_cant_permissions_on as $issue) {
                    $task = \app\models\Tasks::findOne($issue);
                    $string_status_not_changed .= 'Задание №'.$task->TASK_NUMBER.',';
                }
            }



            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['string_status_changed' => $string_status_changed, 'string_status_not_changed' => $string_status_not_changed];

        }
    }

}
