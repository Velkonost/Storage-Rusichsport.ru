<?php

namespace app\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Leads;
use app\models\Contacts;
use app\models\Meta;


class MoneyController extends Controller {

    private $startPeriod = 0;
    private $finishPeriod = 0;
    public $layout = 'money';
    
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    // вот жта штука отлючает проверку CSRF для POST запроса, который делает AMOCRM
    public function beforeAction($action)
    {
        if ($action->id == 'webhook') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
    
    public function actionGetpost(){
        return $this->render('getPosts',
        []);
    }
    
    public function actionFind(){
        return $this->render('find',
        []);
    }
    
    public function actionWebhook() {
        try {
            $listenerContact = new \AmoCRM\Webhooks\Listener();
            $listenerContact->on('add_contact', function ($domain, $id, $data) {
                $user=array(
                  'USER_LOGIN'=>'kranfear@mail.ru', #Ваш логин (электронная почта)
                  'USER_HASH'=>'38f2e3461e664db15032bcab8b7c26e8' #Хэш для доступа к API (смотрите в профиле пользователя)
                );
         
                $subdomain='new584549b112ca4'; #Наш аккаунт - поддомен
         
                #Формируем ссылку для запроса
                $link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';

                $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
                #Устанавливаем необходимые опции для сеанса cURL
                curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
                curl_setopt($curl,CURLOPT_URL,$link);
                curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
                curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($user));
                curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
                curl_setopt($curl,CURLOPT_HEADER,false);
                curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
                curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
                 
                $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
                $code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
                curl_close($curl); #Завершаем сеанс cURL

                sleep(2);

                $link2 = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list?id[]='.$id;

                $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
                #Устанавливаем необходимые опции для сеанса cURL
                curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
                curl_setopt($curl,CURLOPT_URL,$link2);
                curl_setopt($curl,CURLOPT_HEADER,false);
                curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
                curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

                $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
                $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
                curl_close($curl);
                // $out = unparseLeadsIds($out);
                $data = json_decode($out);

                $amountContacts = count($data->{'response'}->{'contacts'});

                $clientsNames = [];
                $clientsPhones = [];
                $clientsCities = [];
                $clientsIds = [];

                for ($i = 0; $i < $amountContacts; $i ++) {
                    
                    array_push($clientsNames, $data->{'response'}->{'contacts'}[$i]->{'name'});
                    array_push($clientsPhones, unparseContactPhone($data->{'response'}->{'contacts'}[$i]->{'custom_fields'}));       
                    
                    array_push($clientsIds, $data->{'response'}->{'contacts'}[$i]->{'id'});       
                }

                for ($i = 0; $i < count($clientsIds); $i ++) {
                    
                    $post = new Contacts;
                    $post->contact_id = $clientsIds[$i];
                    $post->name = $clientsNames[$i];
                    $post->phone = $clientsPhones[$i];
                    $post->city = "Город";

                    $post->save();

                }

                sleep(2);
            });
            $listenerContact -> listen();
        } catch (\AmoCRM\Exception $e) {
            printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
        }

        try {       
            $listener = new \AmoCRM\Webhooks\Listener();
            // Добавление обработчика на уведомление contacts->add
            $listener->on('add_lead', function ($domain, $id, $data) {

                #Массив с параметрами, которые нужно передать методом POST к API системы
                $user=array(
                  'USER_LOGIN'=>'kranfear@mail.ru', #Ваш логин (электронная почта)
                  'USER_HASH'=>'38f2e3461e664db15032bcab8b7c26e8' #Хэш для доступа к API (смотрите в профиле пользователя)
                );
         
                $subdomain='new584549b112ca4'; #Наш аккаунт - поддомен
         
                #Формируем ссылку для запроса
                $link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';
        
                $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
                #Устанавливаем необходимые опции для сеанса cURL
                curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
                curl_setopt($curl,CURLOPT_URL,$link);
                curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
                curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($user));
                curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
                curl_setopt($curl,CURLOPT_HEADER,false);
                curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
                curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
                 
                $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
                $code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
                curl_close($curl); #Завершаем сеанс cURL
        
                sleep(2);

                $link2 = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/list?id='.$id;

                // $link2 = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/list?limit_rows=500&limit_offset='.$offset;

                $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
                #Устанавливаем необходимые опции для сеанса cURL
                curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
                curl_setopt($curl,CURLOPT_URL,$link2);
                curl_setopt($curl,CURLOPT_HEADER,false);
                curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
                curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

                $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
                $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
                curl_close($curl);

                $data = json_decode($out);

                $leadsIds = [];
                $leadsDateCreate = [];
                $leadsDateClose = [];
                $leadsStatusId = [];
                $leadsSdekSumma = [];
                $leadsPrice = [];

                $clientsIds = [];
                $clientsCity = [];
                $amountLeads = count($data->{'response'}->{'leads'});
                $responsibles = [];

                for ($i = 0; $i < $amountLeads; $i ++) {
                    array_push($leadsIds, $data->{'response'}->{'leads'}[$i]->{'id'});
                    array_push($clientsIds, $data->{'response'}->{'leads'}[$i]->{'main_contact_id'});
                    
                    array_push($leadsDateCreate, $data->{'response'}->{'leads'}[$i]->{'date_create'});
                    array_push($leadsDateClose, $data->{'response'}->{'leads'}[$i]->{'date_close'});
                    array_push($leadsStatusId, $data->{'response'}->{'leads'}[$i]->{'status_id'});
                    array_push($leadsSdekSumma, unparseSdekSumma($data->{'response'}->{'leads'}[$i]->{'custom_fields'}));
                    array_push($clientsCity, unparseSdekCity($data->{'response'}->{'leads'}[$i]->{'custom_fields'}));
                    array_push($leadsPrice, unparsePrice($data->{'response'}->{'leads'}[$i]->{'custom_fields'}));


                    if ($data->{'response'}->{'leads'}[$i]->{'responsible_user_id'} == 1178568) {
                        array_push($responsibles, "Алексей Камышлов");
                    }

                }

                // sleep(2);

                // $link3 = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/accounts/current?free_users=Y';

                // $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
                // #Устанавливаем необходимые опции для сеанса cURL
                // curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                // curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
                // curl_setopt($curl,CURLOPT_URL,$link3);
                // curl_setopt($curl,CURLOPT_HEADER,false);
                // curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                // curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                // curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
                // curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

                // $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
                // $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
                // curl_close($curl);

                // $data = json_decode($out);
                
 
 //1178568
                for ($i = 0; $i < count($clientsIds); $i ++) {
                    
                    $post = new Leads;
                    $post->lead_id = $leadsIds[$i];
                    $post->critical_acc = $responsibles[$i];
                    $post->contact_id = $clientsIds[$i];
                    $post->lead_status = $leadsStatusId[$i];

                    $post->lead_summa = $leadsPrice[$i];
                    $post->sdek_summa = $leadsSdekSumma[$i];

                    $post->city = $clientsCity[$i];

                    $post->lead_date_close = $leadsDateClose[$i];
                    $post->lead_date_create = $leadsDateCreate[$i];

                    $post->save();

                }

                // sleep(2);
            });
            $listener -> listen();
        } catch (\AmoCRM\Exception $e) {
            printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
        }

        try {
            $listenerStatus = new \AmoCRM\Webhooks\Listener();
            $listenerStatus->on('status_lead', function ($domain, $id, $data) {
                $user=array(
                  'USER_LOGIN'=>'kranfear@mail.ru', #Ваш логин (электронная почта)
                  'USER_HASH'=>'38f2e3461e664db15032bcab8b7c26e8' #Хэш для доступа к API (смотрите в профиле пользователя)
                );
         
                $subdomain='new584549b112ca4'; #Наш аккаунт - поддомен
         
                #Формируем ссылку для запроса
                $link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';

                $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
                #Устанавливаем необходимые опции для сеанса cURL
                curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
                curl_setopt($curl,CURLOPT_URL,$link);
                curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
                curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($user));
                curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
                curl_setopt($curl,CURLOPT_HEADER,false);
                curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
                curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
                 
                $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
                $code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
                curl_close($curl); #Завершаем сеанс cURL

                sleep(2);

                $link2 = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/list?id[]='.$id;

                $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
                #Устанавливаем необходимые опции для сеанса cURL
                curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
                curl_setopt($curl,CURLOPT_URL,$link2);
                curl_setopt($curl,CURLOPT_HEADER,false);
                curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
                curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

                $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
                $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
                curl_close($curl);
                $status = unparseStatus($out);

                //12988851 - ДОСТАВЛЕН
                //142 - УСПЕШНО ДОСТАВЛЕН
                //143 - ВОЗВРАТ ТОВАРА
                //12998565 - ВОЗВРАТ
                //12988845 - ОТПРАВЛЕН


                $data = json_decode($out);
          
                $update = Leads::find()->where("lead_id='$id'")->one();
                $update->lead_status = $status;
                $date = $data->{'response'}->{'leads'}[0]->{'last_modified'};
                

                if ($status == 12988851) {
                    $update->lead_date_delivered = $date;
                } else if ($status == 142) {
                    $update->lead_date_success_delivered = $date;
                    $update->lead_date_close = $data->{'response'}->{'leads'}[$i]->{'date_close'};
                } else if ($status == 12998565) {
                    $update->lead_date_reset = $date;
                } else if ($status == 12988845) {
                    $update->lead_date_send = $date;
                } else if ($status == 143) {
                    $update->lead_date_reset_thing = $date;
                    $update->lead_date_close = $data->{'response'}->{'leads'}[$i]->{'date_close'};
                }

                $update->save();
    
            });
            $listenerStatus -> listen();
        } catch (\AmoCRM\Exception $e) {
            printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
        }

        try {
            $listenerUpdate = new \AmoCRM\Webhooks\Listener();
            $listenerUpdate->on('update_lead', function ($domain, $id, $data) {
                $user=array(
                  'USER_LOGIN'=>'kranfear@mail.ru', #Ваш логин (электронная почта)
                  'USER_HASH'=>'38f2e3461e664db15032bcab8b7c26e8' #Хэш для доступа к API (смотрите в профиле пользователя)
                );
         
                $subdomain='new584549b112ca4'; #Наш аккаунт - поддомен
         
                #Формируем ссылку для запроса
                $link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';

                $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
                #Устанавливаем необходимые опции для сеанса cURL
                curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
                curl_setopt($curl,CURLOPT_URL,$link);
                curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
                curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($user));
                curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
                curl_setopt($curl,CURLOPT_HEADER,false);
                curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
                curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
                 
                $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
                $code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
                curl_close($curl); #Завершаем сеанс cURL

                sleep(2);

                $link2 = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/list?id='.$id;

                // $link2 = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/list?limit_rows=500&limit_offset='.$offset;

                $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
                #Устанавливаем необходимые опции для сеанса cURL
                curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
                curl_setopt($curl,CURLOPT_URL,$link2);
                curl_setopt($curl,CURLOPT_HEADER,false);
                curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
                curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

                $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
                $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
                curl_close($curl);

                $data = json_decode($out);

                $leadsIds = [];
                $leadsDateCreate = [];
                $leadsDateClose = [];
                $leadsStatusId = [];
                $leadsSdekSumma = [];
                $leadsPrice = [];

                $clientsIds = [];
                $clientsCity = [];
                $amountLeads = count($data->{'response'}->{'leads'});

                for ($i = 0; $i < $amountLeads; $i ++) {
                    array_push($leadsIds, $data->{'response'}->{'leads'}[$i]->{'id'});
                    array_push($clientsIds, $data->{'response'}->{'leads'}[$i]->{'main_contact_id'});
                    
                    array_push($leadsDateCreate, $data->{'response'}->{'leads'}[$i]->{'date_create'});
                    array_push($leadsDateClose, $data->{'response'}->{'leads'}[$i]->{'date_close'});
                    array_push($leadsStatusId, $data->{'response'}->{'leads'}[$i]->{'status_id'});
                    array_push($leadsSdekSumma, unparseSdekSumma($data->{'response'}->{'leads'}[$i]->{'custom_fields'}));
                    array_push($clientsCity, unparseSdekCity($data->{'response'}->{'leads'}[$i]->{'custom_fields'}));
                    array_push($leadsPrice, unparsePrice($data->{'response'}->{'leads'}[$i]->{'custom_fields'}));

                }
 
                for ($i = 0; $i < count($clientsIds); $i ++) {
                    
                    $post = Leads::find()->where("lead_id='$id'")->one();
                    // $post->lead_id = $leadsIds[$i];
                    // $post->critical_acc = "Ответственный";
                    $post->contact_id = $clientsIds[$i];
                    $post->lead_status = $leadsStatusId[$i];

                    $post->lead_summa = $leadsPrice[$i];
                    $post->sdek_summa = $leadsSdekSumma[$i];

                    $post->city = $clientsCity[$i];

                    $post->lead_date_close = $leadsDateClose[$i];

                    $post->save();

                }

                // sleep(2);
            });
            $listenerUpdate -> listen();
        } catch (\AmoCRM\Exception $e) {
            printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
        }
        return $this->render('webhook');

    }


    public function actionIndex()
    {



        // First day of this month
        $d = strtotime(date('1-m-Y',strtotime('this month')));

        $check = Meta::find()->where("meta_key='is_empty'")->one();
        $check_value = $check->meta_value;
        if ($check_value == '0') {
            // $user = Yii::$app->user->identity;
            #Массив с параметрами, которые нужно передать методом POST к API системы
            $user=array(
              'USER_LOGIN'=>'kranfear@mail.ru', #Ваш логин (электронная почта)
              'USER_HASH'=>'38f2e3461e664db15032bcab8b7c26e8' #Хэш для доступа к API (смотрите в профиле пользователя)
            );
     
            $subdomain='new584549b112ca4'; #Наш аккаунт - поддомен
     
            #Формируем ссылку для запроса
            $link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';

            $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
            #Устанавливаем необходимые опции для сеанса cURL
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
            curl_setopt($curl,CURLOPT_URL,$link);
            curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
            curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($user));
            curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
            curl_setopt($curl,CURLOPT_HEADER,false);
            curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
             
            $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
            $code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
            curl_close($curl); #Завершаем сеанс cURL

            sleep(2);

            $offset = 0;
            
            //1171
            for (;$offset < 1000; $offset += 500) {
                $link2 = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list?limit_rows=500&limit_offset='.$offset;

                $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
                #Устанавливаем необходимые опции для сеанса cURL
                curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
                curl_setopt($curl,CURLOPT_URL,$link2);
                curl_setopt($curl,CURLOPT_HEADER,false);
                curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
                curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

                $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
                $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
                curl_close($curl);
                // $out = unparseLeadsIds($out);
                $data = json_decode($out);

                $amountContacts = count($data->{'response'}->{'contacts'});

                $clientsNames = [];
                $clientsPhones = [];
                $clientsCities = [];
                $clientsIds = [];

                for ($i = 0; $i < $amountContacts; $i ++) {
                    
                    array_push($clientsNames, $data->{'response'}->{'contacts'}[$i]->{'name'});
                    array_push($clientsPhones, unparseContactPhone($data->{'response'}->{'contacts'}[$i]->{'custom_fields'}));       
                
                    array_push($clientsIds, $data->{'response'}->{'contacts'}[$i]->{'id'});       
                }

                for ($i = 0; $i < count($clientsIds); $i ++) {
                    
                    $post = new Contacts;
                    $post->contact_id = $clientsIds[$i];
                    $post->name = $clientsNames[$i];
                    $post->phone = $clientsPhones[$i];
                    $post->city = "City";

                    $post->save();

                }

                sleep(2);
            }

            $link2 = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/list?limit_rows=500&limit_offset='.$offset;

            $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
            #Устанавливаем необходимые опции для сеанса cURL
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
            curl_setopt($curl,CURLOPT_URL,$link2);
            curl_setopt($curl,CURLOPT_HEADER,false);
            curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

            $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
            $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
            curl_close($curl);
            // $out = unparseLeadsIds($out);
            $data = json_decode($out);

            $amountContacts = count($data->{'response'}->{'contacts'});

            $clientsNames = [];
            $clientsPhones = [];
            $clientsCities = [];
            $clientsIds = [];

            for ($i = 0; $i < $amountContacts; $i ++) {
                
                array_push($clientsNames, $data->{'response'}->{'contacts'}[$i]->{'name'});
                array_push($clientsPhones, unparseContactPhone($data->{'response'}->{'contacts'}[$i]->{'custom_fields'}));       
                
                array_push($clientsIds, $data->{'response'}->{'contacts'}[$i]->{'id'});       
            }

            for ($i = 0; $i < count($clientsIds); $i ++) {
                
                $post = new Contacts;
                $post->contact_id = $clientsIds[$i];
                $post->name = $clientsNames[$i];
                $post->phone = $clientsPhones[$i];
                $post->city = "City";

                $post->save();

            }

            sleep(2);

            //LEADS
            $offset = 0;
            for (;$offset < 1500; $offset += 500) {
                $link2 = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/list?limit_rows=500&limit_offset='.$offset;

                $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
                #Устанавливаем необходимые опции для сеанса cURL
                curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
                curl_setopt($curl,CURLOPT_URL,$link2);
                curl_setopt($curl,CURLOPT_HEADER,false);
                curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
                curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
                curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

                $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
                $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
                curl_close($curl);

                $data = json_decode($out);

                $leadsIds = [];
                $leadsDateCreate = [];
                $leadsDateClose = [];
                $leadsStatusId = [];
                $leadsSdekSumma = [];
                $leadsPrice = [];

                $clientsIds = [];
                $clientsCity = [];
                $amountLeads = count($data->{'response'}->{'leads'});
                $responsibles = [];

                for ($i = 0; $i < $amountLeads; $i ++) {
                    array_push($leadsIds, $data->{'response'}->{'leads'}[$i]->{'id'});
                    array_push($clientsIds, $data->{'response'}->{'leads'}[$i]->{'main_contact_id'});
                    // array_push($leadsDateCreate, date("d/m/Y H:i:s", $data->{'response'}->{'leads'}[$i]->{'date_create'}));
                    array_push($leadsDateCreate, $data->{'response'}->{'leads'}[$i]->{'date_create'});
                    array_push($leadsDateClose, $data->{'response'}->{'leads'}[$i]->{'date_close'});
                    array_push($leadsStatusId, $data->{'response'}->{'leads'}[$i]->{'status_id'});
                    array_push($leadsSdekSumma, unparseSdekSumma($data->{'response'}->{'leads'}[$i]->{'custom_fields'}));
                    array_push($clientsCity, unparseSdekCity($data->{'response'}->{'leads'}[$i]->{'custom_fields'}));
                    array_push($leadsPrice, unparsePrice($data->{'response'}->{'leads'}[$i]->{'custom_fields'}));

                    if ($data->{'response'}->{'leads'}[$i]->{'responsible_user_id'} == 1178568) {
                        array_push($responsibles, "Алексей Камышлов");
                    }


                }

                

                for ($i = 0; $i < count($clientsIds); $i ++) {
                    
                    $post = new Leads;
                    $post->lead_id = $leadsIds[$i];
                    $post->critical_acc = $responsibles[$i];
                    $post->contact_id = $clientsIds[$i];
                    
                    $post->lead_status = $leadsStatusId[$i];

                    $post->lead_summa = $leadsPrice[$i];
                    $post->sdek_summa = $leadsSdekSumma[$i];

                    $post->city = $clientsCity[$i];

                    $post->lead_date_close = $leadsDateClose[$i];
                    $post->lead_date_create = $leadsDateCreate[$i];

                    $post->save();

                }

                sleep(2);
            }

            $link2 = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/list?limit_rows=450&limit_offset='.$offset;

            $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
            #Устанавливаем необходимые опции для сеанса cURL
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
            curl_setopt($curl,CURLOPT_URL,$link2);
            curl_setopt($curl,CURLOPT_HEADER,false);
            curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

            $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
            $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
            curl_close($curl);

            $data = json_decode($out);

            $leadsIds = [];
            $leadsDateCreate = [];
            $leadsDateClose = [];
            $leadsStatusId = [];
            $leadsSdekSumma = [];
            $leadsPrice = [];

            $clientsIds = [];
            $clientsCity = [];
            $amountLeads = count($data->{'response'}->{'leads'});
            $responsibles = [];

            for ($i = 0; $i < $amountLeads; $i ++) {
                array_push($leadsIds, $data->{'response'}->{'leads'}[$i]->{'id'});
                array_push($clientsIds, $data->{'response'}->{'leads'}[$i]->{'main_contact_id'});
                
                array_push($leadsDateCreate, $data->{'response'}->{'leads'}[$i]->{'date_create'});
                array_push($leadsDateClose, $data->{'response'}->{'leads'}[$i]->{'date_close'});
                array_push($leadsStatusId, $data->{'response'}->{'leads'}[$i]->{'status_id'});
                array_push($leadsSdekSumma, unparseSdekSumma($data->{'response'}->{'leads'}[$i]->{'custom_fields'}));
                array_push($clientsCity, unparseSdekCity($data->{'response'}->{'leads'}[$i]->{'custom_fields'}));
                array_push($leadsPrice, unparsePrice($data->{'response'}->{'leads'}[$i]->{'custom_fields'}));

                 if ($data->{'response'}->{'leads'}[$i]->{'responsible_user_id'} == 1178568) {
                    array_push($responsibles, "Алексей Камышлов");
                }
            }

            

            for ($i = 0; $i < count($clientsIds); $i ++) {
                $post = new Leads;
                $post->lead_id = $leadsIds[$i];
                $post->critical_acc = $responsibles[$i];
                $post->contact_id = $clientsIds[$i];
                
                $post->lead_status = $leadsStatusId[$i];

                $post->lead_summa = $leadsPrice[$i];
                $post->sdek_summa = $leadsSdekSumma[$i];

                $post->city = $clientsCity[$i];

                $post->lead_date_close = $leadsDateClose[$i];
                $post->lead_date_create = $leadsDateCreate[$i];

                $post->save();

            }

            $check->meta_value = '1';
            $check->save();
        } else {
            $leads = Leads::find()->all();

            $leadsIds = [];
            $leadsDateCreate = [];
            $leadsDateClose = [];
            $leadsStatusId = [];
            $leadsSdekSumma = [];
            $leadsPrice = [];

            $clientsNames = [];
            $clientsPhones = [];
            $clientsCities = [];

            $clientsIds = [];
            $amountLeads = count($leads);

            foreach ($leads as $key) {
                array_push($leadsIds, $key->lead_id);
                
                array_push($leadsDateCreate, $key->lead_date_create);
                array_push($leadsDateClose, $key->lead_date_close);
                array_push($leadsStatusId, $key->lead_status);
                array_push($leadsSdekSumma, $key->sdek_summa);
                array_push($leadsPrice, $key->lead_summa);

                array_push($clientsIds, $key->contact_id);
            }
        }

        return $this->render('index',
        [
            'c' => $d,
            'amount' => $amountLeads,
            'ids' => $leadsIds,
            'dates' => $leadsDateCreate,
            'names' => $clientsIds
        ]);
    }
    
    
}

function unparseContactPhone($data){    
    $array = " ";
    
    for($i = 0; $i<count($data); $i++){
        if(strcmp($data[$i]->{'name'}, "Телефон")==0){
            $array = $data[$i]->{'values'}[0]->{'value'};
            break;
        }
    } 
    return $array;
}

function unparseSdekSumma($data){    
    $array = " ";
    
    for($i = 0; $i<count($data); $i++){
        if(strcmp($data[$i]->{'name'}, "Стоимость доставки")==0){
            $array = $data[$i]->{'values'}[0]->{'value'};
            break;
        }
    }
    return $array;
}

function unparseSdekCity($data){    
    $array = " ";
    
    for($i = 0; $i<count($data); $i++){
        if(strcmp($data[$i]->{'name'}, "Город получателя")==0){
            $array = $data[$i]->{'values'}[0]->{'value'};
            break;
        }
    }
    return $array;
}

function unparsePrice($data){ 
    $price= 0; 
   
    $array = []; 
    $g = 0; 
    for($i = 0; $i<count($data); $i++){ 
        if(strcmp($data[$i]->{'name'}, "Цена")==0){ 
            $price += intval($data[$i]->{'values'}[0]->{'value'}); 
            $g++; 
        } 
    }    
    return $price; 
}

function unparseStatus($data){    
        
        
        $data = json_decode($data);
        // echo $data['response']['leads'][0]['custom_fields'];
        $data = ($data->{'response'}->{'leads'}[0]->{'status_id'});
        return $data;
    }