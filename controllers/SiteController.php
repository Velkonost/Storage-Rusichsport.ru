<?php

namespace app\controllers;

use Yii;
use yii\helpers\Html;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Things;
use app\models\Deals;
use app\models\WpPostmeta;
use app\models\WpPosts;
use app\models\Meta;
use app\models\AddThingForm;
use app\models\FormAdd;
use app\models\EditForm;


class SiteController extends Controller
{
    
    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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
    
    public function actionIndex() {
        $this->actionWelcome();
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionStorage()
    {
        
        $russiaArmoring = Meta::find()->where("meta_key='russia_armoring'")->one()->meta_value;
        $ussrArmoring = Meta::find()->where("meta_key='ussr_armoring'")->one()->meta_value;
        $olympiadArmoring = Meta::find()->where("meta_key='olympiad_armoring'")->one()->meta_value;
 
        $russiaNames = Things::find()->where("category='russia'")->all();
        $ussrNames = Things::find()->where("category='ussr'")->all();
        $olympiad80Names = Things::find()->where("category='olympiad80'")->all();       

        $russiaExist = array();
        $ussrExist = array();
        $olympiad80Exist = array();
        
        $russiaExistArt = array();
        $ussrExistArt = array();
        $olympiad80ExistArt = array();

        $russiaAmount = 0; $ussrAmount = 0; $olympiad80Amount = 0;

        foreach ($russiaNames as $key) {
            array_push($russiaExist, $key->article);
            $russiaAmount += $key->amount;
        }

        foreach ($ussrNames as $key) {
            array_push($ussrExist, $key->article);
            $ussrAmount += $key->amount;

        }

        foreach ($olympiad80Names as $key) {
            array_push($olympiad80Exist, $key->article);
            $olympiad80Amount += $key->amount;
        }

        $form = new FormAdd();
        if (($form->load(Yii::$app->request->post())) && ($form->validate())){

            $i = 0;

            for(; $i < 3; $i++) {
                $name = Html::encode($form->names[$i]);
                $s = Html::encode($form->ss[$i]);
                $m = Html::encode($form->ms[$i]);
                $l = Html::encode($form->ls[$i]);
                $xl = Html::encode($form->xls[$i]);
                $xxl = Html::encode($form->xxls[$i]);
                $xxxl = Html::encode($form->xxxls[$i]);
                $x4xl = Html::encode($form->x4xls[$i]);
                $x5xl = Html::encode($form->x5xls[$i]);
                $price = Html::encode($form->prices[$i]);
                $article = empty($form->article[$i]) ? NULL : Html::encode($form->article[$i]);
                // $dropDownList = Html::encode($form->dropDownList);

                $cont = true;

                if ($article == NULL || $name == NULL || ($s == 0 && $m == 0 && $l == 0 && $xl == 0 && $xxl == 0 && $xxxl == 0 && $x4xl == 0 && $x5xl == 0)) $cont = false;
                
                
                if($cont && (in_array($article, $russiaExist) || in_array($article, $ussrExist) || in_array($article, $olympiad80Exist))) { //tut
                    $update = Things::find()->where("article='$article'")->one();
                    // $update->name = $name;
                    $update->s += $s;
                    $update->m += $m;
                    $update->l += $l;
                    $update->xl += $xl;
                    $update->xxl += $xxl;
                    $update->xxxl += $xxxl;
                    $update->x4xl += $x4xl;
                    $update->x5xl += $x5xl;
                    $update->amount += $s + $m + $l + $xl + $xxl + $xxxl + $x4xl + $x5xl;
                    $update->price += $price;

                    $update->save();
                    
                    // $form->name = '0';
                    // $form->s = '0';
                    // $form->m = '0';
                    // $form->l = '0';
                    // $form->xl = '0';
                    // $form->xxl = '0';
                    // $form->xxxl = '0';
                    // $form->price = '0';
                    // $form->article = '';


                    $wpPosts = WpPostmeta::find()->where("(meta_key='article') AND (meta_value='$article') ")->one();
                    $postId = $wpPosts->post_id;
                    
                    $sizes = WpPostmeta::find()->where("(meta_key='sizes') AND (post_id='$postId')")->one();

                    $newSizes = [];

                    if ($update->s > 0) {
                        array_push($newSizes, 1);
                    } 
                    if ($update->m > 0) {
                        array_push($newSizes, 2);
                    }
                    if ($update->l > 0) {
                        array_push($newSizes, 3);
                    }
                    if ($update->xl > 0) {
                        array_push($newSizes, 4);
                    }
                    if ($update->xxl > 0) {
                        array_push($newSizes, 5);
                    }
                    if ($update->xxxl > 0) {
                        array_push($newSizes, 6);
                    }
                    if ($update->x4xl > 0) {
                        array_push($newSizes, 7);
                    }
                    if ($update->x5xl > 0) {
                        array_push($newSizes, 8);
                    }


                    $sizes->meta_value = serialize($newSizes);
                    $sizes->save();
                } else { // tut
                    // $post = new Things;
                    // $post->name = $name;
                    // $post->article = $article;
                    // $post->s = $s;
                    // $post->m = $m;
                    // $post->l = $l;
                    // $post->xl = $xl;
                    // $post->xxl = $xxl;
                    // $post->xxxl = $xxxl;
                    // $post->amount = $s + $m + $l + $xl + $xxl + $xxxl;
                    // $post->price = $price;
                    // $post->category = $dropDownList;
                    // $post->save(); 

                    // $form->name = '0';
                    // $form->s = '0';
                    // $form->m = '0';
                    // $form->l = '0';
                    // $form->xl = '0';
                    // $form->xxl = '0';
                    // $form->xxxl = '0';
                    // $form->price = '0'; 
                    // $form->article = '';    
                }
            }
        } else {
            $name = '';
            $s = '';
            $m = '';
            $l = '';
            $xl = '';
            $xxl = '';
            $xxxl = '';
            $x4xl = '';
            $x5xl = '';
            $amount = '';
            $price = '';
            $article = '';
        }

        $editForm = new EditForm();
        if (($editForm->load(Yii::$app->request->post())) && ($editForm->validate())){
            $amountThings = Things::find()->all();
            $amountRussia = count(Things::find()->where("category='russia'")->all());
            $amountUssr = count(Things::find()->where("category='ussr'")->all());
            $amountOlympiad80 = count(Things::find()->where("category='olympiad80'")->all());

            Things::deleteAll();
            for ($i = 0; $i < $amountRussia; $i++) { // tut
                
                $post = new Things;
                $post->article = Html::encode($editForm->editArticle[$i]);
                $post->name = Html::encode($editForm->editNames[$i]);
                $post->s = (Html::encode($editForm->editSs[$i]) < 0 ? 0 : Html::encode($editForm->editSs[$i]));
                $post->m = (Html::encode($editForm->editMs[$i]) < 0 ? 0 : Html::encode($editForm->editMs[$i]));
                $post->l = (Html::encode($editForm->editLs[$i]) < 0 ? 0 : Html::encode($editForm->editLs[$i]));
                $post->xl = (Html::encode($editForm->editXls[$i]) < 0 ? 0 : Html::encode($editForm->editXls[$i]));
                $post->xxl = (Html::encode($editForm->editXxls[$i]) < 0 ? 0 : Html::encode($editForm->editXxls[$i]));
                $post->xxxl = (Html::encode($editForm->editXxxls[$i]) < 0 ? 0 : Html::encode($editForm->editXxxls[$i]));
                $post->x4xl = (Html::encode($editForm->editx4xls[$i]) < 0 ? 0 : Html::encode($editForm->editx4xls[$i]));
                $post->x5xl = (Html::encode($editForm->editx5xls[$i]) < 0 ? 0 : Html::encode($editForm->editx5xls[$i]));
                $post->price = Html::encode($editForm->editPrices[$i]);
                $post->category = 'russia';
                $amount = ($post->s + $post->m + $post->l + $post->xl + $post->xxl + $post->xxxl + $post->x4xl + $post->x5xl);
                $post->amount = $amount; 

                $post->save();
                $article = $editForm->editArticle[$i];

                $wpPosts = WpPostmeta::find()->where("(meta_key='article') AND (meta_value='$article') ")->one();
                $postId = $wpPosts->post_id;
                
                $sizes = WpPostmeta::find()->where("(meta_key='sizes') AND (post_id='$postId')")->one();

                $newSizes = [];
                

                if ($editForm->editSs[$i] > 0) {
                    array_push($newSizes, 1);
                
                } 
                if ($editForm->editMs[$i] > 0) {
                    array_push($newSizes, 2);
                
                }
                if ($editForm->editLs[$i] > 0) {
                    array_push($newSizes, 3);
                
                }
                if ($editForm->editXls[$i] > 0) {
                    array_push($newSizes, 4);
                
                }
                if ($editForm->editXxls[$i] > 0) {
                    array_push($newSizes, 5);
                
                }
                if ($editForm->editXxxls[$i] > 0) {
                    array_push($newSizes, 6);
                
                }
                if ($editForm->editx4xls[$i] > 0) {
                    array_push($newSizes, 7);
                
                }
                if ($editForm->editx5xls[$i] > 0) {
                    array_push($newSizes, 8);
                
                }

                $sizes->meta_value = serialize($newSizes);
                
                $sizes->save();

            }
            for ($i = $amountRussia; $i < ($amountRussia + $amountUssr); $i++) {
                $post = new Things;
                $post->article = Html::encode($editForm->editArticle[$i]);
                $post->name = Html::encode($editForm->editNames[$i]);
                $post->s = (Html::encode($editForm->editSs[$i]) < 0 ? 0 : Html::encode($editForm->editSs[$i]));
                $post->m = (Html::encode($editForm->editMs[$i]) < 0 ? 0 : Html::encode($editForm->editMs[$i]));
                $post->l = (Html::encode($editForm->editLs[$i]) < 0 ? 0 : Html::encode($editForm->editLs[$i]));
                $post->xl = (Html::encode($editForm->editXls[$i]) < 0 ? 0 : Html::encode($editForm->editXls[$i]));
                $post->xxl = (Html::encode($editForm->editXxls[$i]) < 0 ? 0 : Html::encode($editForm->editXxls[$i]));
                $post->xxxl = (Html::encode($editForm->editXxxls[$i]) < 0 ? 0 : Html::encode($editForm->editXxxls[$i]));
                $post->x4xl = (Html::encode($editForm->editx4xls[$i]) < 0 ? 0 : Html::encode($editForm->editx4xls[$i]));
                $post->x5xl = (Html::encode($editForm->editx5xls[$i]) < 0 ? 0 : Html::encode($editForm->editx5xls[$i]));
                $post->price = Html::encode($editForm->editPrices[$i]);
                $post->category = 'ussr';
                $amount = ($post->s + $post->m + $post->l + $post->xl + $post->xxl + $post->xxxl + $post->x4xl + $post->x5xl);
                $post->amount = $amount; 

                $post->save();
                $article = $editForm->editArticle[$i];

                $wpPosts = WpPostmeta::find()->where("(meta_key='article') AND (meta_value='$article') ")->one();
                $postId = $wpPosts->post_id;
                
                $sizes = WpPostmeta::find()->where("(meta_key='sizes') AND (post_id='$postId')")->one();

                $newSizes = [];


                if ($editForm->editSs[$i] > 0) {
                    array_push($newSizes, 1);
                } 
                if ($editForm->editMs[$i] > 0) {
                    array_push($newSizes, 2);
                }
                if ($editForm->editLs[$i] > 0) {
                    array_push($newSizes, 3);
                }
                if ($editForm->editXls[$i] > 0) {
                    array_push($newSizes, 4);
                }
                if ($editForm->editXxls[$i] > 0) {
                    array_push($newSizes, 5);
                }
                if ($editForm->editXxxls[$i] > 0) {
                    array_push($newSizes, 6);
                }
                if ($editForm->editx4xls[$i] > 0) {
                    array_push($newSizes, 7);
                }
                if ($editForm->editx5xls[$i] > 0) {
                    array_push($newSizes, 8);
                }

                $sizes->meta_value = serialize($newSizes);
                $sizes->save();

            }
            for ($i = ($amountUssr + $amountRussia); $i < ($amountRussia + $amountUssr + $amountOlympiad80); $i++) {
                $post = new Things;
                $post->article = Html::encode($editForm->editArticle[$i]);
                $post->name = Html::encode($editForm->editNames[$i]);
                $post->s = (Html::encode($editForm->editSs[$i]) < 0 ? 0 : Html::encode($editForm->editSs[$i]));
                $post->m = (Html::encode($editForm->editMs[$i]) < 0 ? 0 : Html::encode($editForm->editMs[$i]));
                $post->l = (Html::encode($editForm->editLs[$i]) < 0 ? 0 : Html::encode($editForm->editLs[$i]));
                $post->xl = (Html::encode($editForm->editXls[$i]) < 0 ? 0 : Html::encode($editForm->editXls[$i]));
                $post->xxl = (Html::encode($editForm->editXxls[$i]) < 0 ? 0 : Html::encode($editForm->editXxls[$i]));
                $post->xxxl = (Html::encode($editForm->editXxxls[$i]) < 0 ? 0 : Html::encode($editForm->editXxxls[$i]));
                $post->x4xl = (Html::encode($editForm->editx4xls[$i]) < 0 ? 0 : Html::encode($editForm->editx4xls[$i]));
                $post->x5xl = (Html::encode($editForm->editx5xls[$i]) < 0 ? 0 : Html::encode($editForm->editx5xls[$i]));
                $post->price = Html::encode($editForm->editPrices[$i]);
                $post->category = 'olympiad80';
                $amount = ($post->s + $post->m + $post->l + $post->xl + $post->xxl + $post->xxxl + $post->x4xl + $post->x5xl);
                $post->amount = $amount; 

                $post->save();

                $article = $editForm->editArticle[$i];

                $wpPosts = WpPostmeta::find()->where("(meta_key='article') AND (meta_value='$article') ")->one();
                $postId = $wpPosts->post_id;
                
                $sizes = WpPostmeta::find()->where("(meta_key='sizes') AND (post_id='$postId')")->one();

                $newSizes = [];

                if ($editForm->editSs[$i] > 0) {
                    array_push($newSizes, 1);
                } 
                if ($editForm->editMs[$i] > 0) {
                    array_push($newSizes, 2);
                }
                if ($editForm->editLs[$i] > 0) {
                    array_push($newSizes, 3);
                }
                if ($editForm->editXls[$i] > 0) {
                    array_push($newSizes, 4);
                }
                if ($editForm->editXxls[$i] > 0) {
                    array_push($newSizes, 5);
                }
                if ($editForm->editXxxls[$i] > 0) {
                    array_push($newSizes, 6);
                }
                if ($editForm->editx4xls[$i] > 0) {
                    array_push($newSizes, 7);
                }
                if ($editForm->editx5xls[$i] > 0) {
                    array_push($newSizes, 8);
                }

                $sizes->meta_value = serialize($newSizes);
                $sizes->save();

            }
        }
        
        $allarticles = [];
        $artics = Things::find()->all();
        foreach($artics as $key){
            $allarticles[$key->article] = $key->article;
        }
        
        $russia = Things::find()->where("category='russia'")->all();
        $ussr = Things::find()->where("category='ussr'")->all();
        $olympiad80 = Things::find()->where("category='olympiad80'")->all();
        $allclths = Things::find()->all();
        $allclothes = [];
        foreach($allclths as $key){
            $allclothes[$key->name] = $key->name;
        }
        
        

        return $this->render('storage', [
            'russia' => $russia,
            'ussr' => $ussr,
            'olympiad80' => $olympiad80,
            'russiaNames' => $russiaExist,
            'ussrNames' => $ussrExist,
            'olympiad80Names' => $olympiad80Exist,
            'form' => $form,
            'editForm' => $editForm,
            'russiaAmount' => $russiaAmount,
            'ussrAmount' => $ussrAmount,
            'olympiad80Amount' => $olympiad80Amount,
            'allarticles' => $allarticles,
            'allclothes' => $allclothes,
            'russiaArmoring' => $russiaArmoring,
            'ussrArmoring' => $ussrArmoring,
            'olympiadArmoring' => $olympiadArmoring
        ]);

    }


    public function actionWelcome()
    {

       if (!Yii::$app->user->isGuest) {
           return Yii::$app->response->redirect('storage');
        }

       $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            
            
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => 'cook',
                'value' => $model->username ,
            ]));
           return Yii::$app->response->redirect('storage');
        }
       return $this->render('welcome', [
            'model' => $model
        ]);
    }
    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (Yii::$app->getRequest()->getCookies()->has('cook')){
            Yii::$app->response->redirect('welcome');
        }
        if (!Yii::$app->user->isGuest) {
            Yii::$app->response->redirect('storage');
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (!isset($_COOKIE['cook'])){
            //  Yii::app()->request->cookies['cook'] = new CHttpCookie('cook', 'cook');
            }
           // Yii::$app->response->redirect('index.php?r=site%2Fstorage');
        }
        
        return $this->render('login', [
            'model' => $model
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    public function actionWebhookaddlead() {

        $post = Meta::find()->where("meta_key='12988851'")->one();
        $request = Yii::$app->request;
        $post = $request->post('leads');
        $post = substr($post, 1, 10);
        $post->meta_value = $post;
        $post->save();

        // #Массив с параметрами, которые нужно передать методом POST к API системы
        // $user = array(
        //   'USER_LOGIN'=>'kranfear@mail.ru', #Ваш логин (электронная почта)
        //   'USER_HASH'=>'38f2e3461e664db15032bcab8b7c26e8' #Хэш для доступа к API (смотрите в профиле пользователя)
        // );
 
        // $subdomain='new584549b112ca4'; #Наш аккаунт - поддомен
 
        // #Формируем ссылку для запроса
        // $link='https://'.$subdomain.'.amocrm.ru/private/api/auth.php?type=json';

        // $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
        // #Устанавливаем необходимые опции для сеанса cURL
        // curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        // curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
        // curl_setopt($curl,CURLOPT_URL,$link);
        // curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
        // curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($user));
        // curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
        // curl_setopt($curl,CURLOPT_HEADER,false);
        // curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        // curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        // curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        // curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
         
        // $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
        // $code=curl_getinfo($curl,CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
        // curl_close($curl); #Завершаем сеанс cURL

        // sleep(2);

        // $link2 = 'https://'.$subdomain.'.amocrm.ru/private/api/v2/json/leads/list?id='.$id;

        // $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
        // #Устанавливаем необходимые опции для сеанса cURL
        // curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        // curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
        // curl_setopt($curl,CURLOPT_URL,$link2);
        // curl_setopt($curl,CURLOPT_HEADER,false);
        // curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        // curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        // curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        // curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

        // $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
        // $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
        // curl_close($curl);

        // $dd = unparse($out);
        
        // $post = new Deals;
        // $text = unparse5($out);
        // $post->id=$text;
        // for($i = 0; $i< count($dd); $i++){
            
        //     if($i==0){
        //             $post->cat1=$dd[$i]['brand'];
        //     }else if($i==1){
        //             $post->cat2=$dd[$i]['brand'];
        //     }else if($i==2){
        //           $post->cat3=$dd[$i]['brand'];
        //     }else if($i==3){
        //             $post->cat4=$dd[$i]['brand'];
        //     }
            
        // }        
        // $post->save();   
        
        
        // for($i = 0; $i< count($dd); $i++){
            
        //     if(strcmp($dd[$i]['brand'],"Олимпиада 80")==0){
        //         $post = Meta::find()->where("meta_key='olympiad_armoring'")->one();
        //         $post->meta_value+=1;
        //         $post->save();
        //     }else if(strcmp($dd[$i]['brand'],"Россия")==0){
        //         $post = Meta::find()->where("meta_key='russia_armoring'")->one();
        //         $post->meta_value+=1;
        //         $post->save();
        //     }
        //     else if(strcmp($dd[$i]['brand'],"СССР")==0){
        //         $post = Meta::find()->where("meta_key='ussr_armoring'")->one();
        //         $post->meta_value+=1;
        //         $post->save();
        //     }
        // }

        return $this->render('webhookaddlead');
    }



     /**
     * Webhooks AMO CRM
     */
    public function actionWebhook()
    {

        $Response = [];
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
            
                    $dd = unparse($out);
                    
                    $post = new Deals;
                    $text = unparse5($out);
                    $post->id=$text;
                    for($i = 0; $i< count($dd); $i++){
                        
                        if($i==0){
                                $post->cat1=$dd[$i]['brand'];
                        }else if($i==1){
                                $post->cat2=$dd[$i]['brand'];
                        }else if($i==2){
                              $post->cat3=$dd[$i]['brand'];
                        }else if($i==3){
                                $post->cat4=$dd[$i]['brand'];
                        }
                        
                    }        
                    $post->save();   
                    
                    
                    for($i = 0; $i< count($dd); $i++){
                        
                        if(strcmp($dd[$i]['brand'],"Олимпиада 80")==0){
                            $post = Meta::find()->where("meta_key='olympiad_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        }else if(strcmp($dd[$i]['brand'],"Россия")==0){
                            $post = Meta::find()->where("meta_key='russia_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        }
                        else if(strcmp($dd[$i]['brand'],"СССР")==0){
                            $post = Meta::find()->where("meta_key='ussr_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        }
                    }
                });
                $listener -> listen();
        } catch (\AmoCRM\Exception $e) {
            printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
        }
        
        try{
        
            $listenerSec = new \AmoCRM\Webhooks\Listener();

            // Добавление обработчика на уведомление contacts->add
            $listenerSec->on('status_lead', function ($domain, $id, $data) {
                // $domain Поддомен amoCRM
                // $id Id объекта связанного с уведомление
                
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

                if(unparse4($out) == 12988845)
                    {
                            $d = unparse($out);
                            $d2 = unparse3($out);
                            $d3 = unparse2($out);
                            
                             for($i = 0; $i<count($d); $i++){
                                
                                if(strcmp($d[$i]['brand'],"Олимпиада 80")==0){
                                    
                                    $post = Meta::find()->where("meta_key='olympiad_armoring'")->one();
                                    $post->meta_value-=1;
                                    if ($post->meta_value < 0) $post->meta_value = 0;
                                    $post->save();

                                    
                                    $post = Things::find()->where("article = '".$d2[$i]['name']."'")->one();
                                    switch($d3[$i]['size']){
                                        case 'S-46':
                                            $post->s-=1;
                                            if ($post->s < 0) $post->s = 0;
                                            break;
                                        case 'M-48':
                                            $post->m-=1;
                                            if ($post->m < 0) $post->m = 0;
                                            break;
                                        case 'L-50':
                                            $post->l-=1;
                                            if ($post->l < 0) $post->l = 0;
                                            break;
                                        case 'XL-52':
                                            $post->xl-=1;
                                            if ($post->xl < 0) $post->xl = 0;
                                            break;
                                        case 'XXL-54':
                                            $post->xxl-=1;
                                            if ($post->xxl < 0) $post->xxl = 0;
                                            break;
                                        case 'XXXL-56':
                                            $post->xxxl-=1;
                                            if ($post->xxxl < 0) $post->xxxl = 0;
                                            break;
                                        case '4XL-58':
                                            $post->x4xl-=1;
                                            if ($post->x4xl < 0) $post->x4xl = 0;
                                            break;
                                        case '5XL-60':
                                            $post->x5xl-=1;
                                            if ($post->x5xl < 0) $post->x5xl = 0;
                                            break;
                                    }
                                    
                                    $post->amount -=1;
                                    if ($post->amount < 0) $post->amount = 0;
                                    $post->save();
                                    
                                    $thingsPost = Things::find()->where("article = '".$d2[$i]['name']."'")->one();
                                    $wpPosts = WpPostmeta::find()->where("(meta_key='article') AND (meta_value='".$d2[$i]['name']."') ")->one();
                                    $postId = $wpPosts->post_id;
                                    
                                    $sizes = WpPostmeta::find()->where("(meta_key='sizes') AND (post_id='$postId')")->one();
                
                                    $newSizes = [];
                
                                    if ($thingsPost->s > 0) {
                                        array_push($newSizes, 1);
                                    } 
                                    if ($thingsPost->m > 0) {
                                        array_push($newSizes, 2);
                                    }
                                    if ($thingsPost->l > 0) {
                                        array_push($newSizes, 3);
                                    }
                                    if ($thingsPost->xl > 0) {
                                        array_push($newSizes, 4);
                                    }
                                    if ($thingsPost->xxl > 0) {
                                        array_push($newSizes, 5);
                                    }
                                    if ($thingsPost->xxxl > 0) {
                                        array_push($newSizes, 6);
                                    }
                                    if ($thingsPost->x4xl > 0) {
                                        array_push($newSizes, 7);
                                    }
                                    if ($thingsPost->x5xl > 0) {
                                        array_push($newSizes, 8);
                                    }
                
                                    $sizes->meta_value = serialize($newSizes);
                                    $sizes->save();
                                    
                                }else if(strcmp($d[$i]['brand'],"Россия")==0){
                                    $post = Meta::find()->where("meta_key='russia_armoring'")->one();
                                    $post->meta_value-=1;
                                    if ($post->meta_value < 0) $post->meta_value = 0;
                                    $post->save();
                                    
                                    $post = Things::find()->where("article = '".$d2[$i]['name']."'")->one();
                                    
                                    switch($d3[$i]['size']){
                                        case 'S-46':
                                            $post->s-=1;
                                            if ($post->s < 0) $post->s = 0;
                                            break;
                                        case 'M-48':
                                            $post->m-=1;
                                            if ($post->m < 0) $post->m = 0;
                                            break;
                                        case 'L-50':
                                            $post->l-=1;
                                            if ($post->l < 0) $post->l = 0;
                                            break;
                                        case 'XL-52':
                                            $post->xl-=1;
                                            if ($post->xl < 0) $post->xl = 0;
                                            break;
                                        case 'XXL-54':
                                            $post->xxl-=1;
                                            if ($post->xxl < 0) $post->xxl = 0;
                                            break;
                                        case 'XXXL-56':
                                            $post->xxxl-=1;
                                            if ($post->xxxl < 0) $post->xxxl = 0;
                                            break;
                                        case '4XL-58':
                                            $post->x4xl-=1;
                                            if ($post->x4xl < 0) $post->x4xl = 0;
                                            break;
                                        case '5XL-60':
                                            $post->x5xl-=1;
                                            if ($post->x5xl < 0) $post->x5xl = 0;
                                            break;
                                    }
                                    $post->amount -=1;
                                    if ($post->amount < 0) $post->amount = 0;
                                    $post->save();
                                    
                                    
                                    $thingsPost = Things::find()->where("article = '".$d2[$i]['name']."'")->one();
                                    $wpPosts = WpPostmeta::find()->where("(meta_key='article') AND (meta_value='".$d2[$i]['name']."') ")->one();
                                    $postId = $wpPosts->post_id;
                                    
                                    $sizes = WpPostmeta::find()->where("(meta_key='sizes') AND (post_id='$postId')")->one();
                
                                    $newSizes = [];
                
                                    if ($thingsPost->s > 0) {
                                        array_push($newSizes, 1);
                                    } 
                                    if ($thingsPost->m > 0) {
                                        array_push($newSizes, 2);
                                    }
                                    if ($thingsPost->l > 0) {
                                        array_push($newSizes, 3);
                                    }
                                    if ($thingsPost->xl > 0) {
                                        array_push($newSizes, 4);
                                    }
                                    if ($thingsPost->xxl > 0) {
                                        array_push($newSizes, 5);
                                    }
                                    if ($thingsPost->xxxl > 0) {
                                        array_push($newSizes, 6);
                                    }
                                    if ($thingsPost->x4xl > 0) {
                                        array_push($newSizes, 7);
                                    }
                                    if ($thingsPost->x5xl > 0) {
                                        array_push($newSizes, 8);
                                    }
                
                                    $sizes->meta_value = serialize($newSizes);
                                    $sizes->save();
                                }
                                else if(strcmp($d[$i]['brand'],"СССР")==0){
                                    $post = Meta::find()->where("meta_key='ussr_armoring'")->one();
                                    $post->meta_value-=1;
                                    if ($post->meta_value < 0) $post->meta_value = 0;
                                    $post->save();
                                    
                                    $post = Things::find()->where("article = '".$d2[$i]['name']."'")->one();
                                    
                                    switch($d3[$i]['size']){
                                        case 'S-46':
                                            $post->s-=1;
                                            if ($post->s < 0) $post->s = 0;
                                            break;
                                        case 'M-48':
                                            $post->m-=1;
                                            if ($post->m < 0) $post->m = 0;
                                            break;
                                        case 'L-50':
                                            $post->l-=1;
                                            if ($post->l < 0) $post->l = 0;
                                            break;
                                        case 'XL-52':
                                            $post->xl-=1;
                                            if ($post->xl < 0) $post->xl = 0;
                                            break;
                                        case 'XXL-54':
                                            $post->xxl-=1;
                                            if ($post->xxl < 0) $post->xxl = 0;
                                            break;
                                        case 'XXXL-56':
                                            $post->xxxl-=1;
                                            if ($post->xxxl < 0) $post->xxxl = 0;
                                            break;
                                        case '4XL-58':
                                            $post->x4xl-=1;
                                            if ($post->x4xl < 0) $post->x4xl = 0;
                                            break;
                                        case '5XL-60':
                                            $post->x5xl-=1;
                                            if ($post->x5xl < 0) $post->x5xl = 0;
                                            break;
                                    }
                                    $post->amount -=1;
                                    if ($post->amount < 0) $post->amount = 0;
                                    $post->save();
                                    
                                    
                                    $thingsPost = Things::find()->where("article = '".$d2[$i]['name']."'")->one();
                                    $wpPosts = WpPostmeta::find()->where("(meta_key='article') AND (meta_value='".$d2[$i]['name']."') ")->one();
                                    $postId = $wpPosts->post_id;
                                    
                                    $sizes = WpPostmeta::find()->where("(meta_key='sizes') AND (post_id='$postId')")->one();
                
                                    $newSizes = [];
                
                                    if ($thingsPost->s > 0) {
                                        array_push($newSizes, 1);
                                    } 
                                    if ($thingsPost->m > 0) {
                                        array_push($newSizes, 2);
                                    }
                                    if ($thingsPost->l > 0) {
                                        array_push($newSizes, 3);
                                    }
                                    if ($thingsPost->xl > 0) {
                                        array_push($newSizes, 4);
                                    }
                                    if ($thingsPost->xxl > 0) {
                                        array_push($newSizes, 5);
                                    }
                                    if ($thingsPost->xxxl > 0) {
                                        array_push($newSizes, 6);
                                    }
                                    if ($thingsPost->x4xl > 0) {
                                        array_push($newSizes, 7);
                                    }
                                    if ($thingsPost->x5xl > 0) {
                                        array_push($newSizes, 8);
                                    }
                
                                    $sizes->meta_value = serialize($newSizes);
                                    $sizes->save();
                                }
                            }
                    } else if (unparse4($out) == 12998565){
                            $d = unparse($out);
                            $d2 = unparse3($out);
                            $d3 = unparse2($out);
                            
                             for($i = 0; $i<count($d); $i++){
                                
                                if(strcmp($d[$i]['brand'],"Олимпиада 80")==0){
                                    
                                    $post = Meta::find()->where("meta_key='olympiad_armoring'")->one();
                                    $post->meta_value+=1;
                                    $post->save();
                                    
                                    $post = Things::find()->where("article = '".$d2[$i]['name']."'")->one();
                                    switch($d3[$i]['size']){
                                        case 'S-46':
                                            $post->s+=1;
                                            break;
                                        case 'M-48':
                                            $post->m+=1;
                                            break;
                                        case 'L-50':
                                            $post->l+=1;
                                            break;
                                        case 'XL-52':
                                            $post->xl+=1;
                                            break;
                                        case 'XXL-54':
                                            $post->xxl+=1;
                                            break;
                                        case 'XXXL-56':
                                            $post->xxxl+=1;
                                            break;
                                        case '4XL-58':
                                            $post->x4xl+=1;
                                            break;
                                        case '5XL-60':
                                            $post->x5xl+=1;
                                            break;
                                    }
                                    
                                    $post ->amount +=1;
                                    $post->save();
                                    
                                    $thingsPost = Things::find()->where("article = '".$d2[$i]['name']."'")->one();
                                    $wpPosts = WpPostmeta::find()->where("(meta_key='article') AND (meta_value='".$d2[$i]['name']."') ")->one();
                                    $postId = $wpPosts->post_id;
                                    
                                    $sizes = WpPostmeta::find()->where("(meta_key='sizes') AND (post_id='$postId')")->one();
                
                                    $newSizes = [];
                
                                    if ($thingsPost->s > 0) {
                                        array_push($newSizes, 1);
                                    } 
                                    if ($thingsPost->m > 0) {
                                        array_push($newSizes, 2);
                                    }
                                    if ($thingsPost->l > 0) {
                                        array_push($newSizes, 3);
                                    }
                                    if ($thingsPost->xl > 0) {
                                        array_push($newSizes, 4);
                                    }
                                    if ($thingsPost->xxl > 0) {
                                        array_push($newSizes, 5);
                                    }
                                    if ($thingsPost->xxxl > 0) {
                                        array_push($newSizes, 6);
                                    }
                                    if ($thingsPost->x4xl > 0) {
                                        array_push($newSizes, 7);
                                    }
                                    if ($thingsPost->x5xl > 0) {
                                        array_push($newSizes, 8);
                                    }
                
                                    $sizes->meta_value = serialize($newSizes);
                                    $sizes->save();
                                    
                                    
                                }else if(strcmp($d[$i]['brand'],"Россия")==0){
                                    $post = Meta::find()->where("meta_key='russia_armoring'")->one();
                                    $post->meta_value+=1;
                                    $post->save();
                                    
                                    $post = Things::find()->where("article = '".$d2[$i]['name']."'")->one();
                                    
                                    switch($d3[$i]['size']){
                                        case 'S-46':
                                            $post->s+=1;
                                            break;
                                        case 'M-48':
                                            $post->m+=1;
                                            break;
                                        case 'L-50':
                                            $post->l+=1;
                                            break;
                                        case 'XL-52':
                                            $post->xl+=1;
                                            break;
                                        case 'XXL-54':
                                            $post->xxl+=1;
                                            break;
                                        case 'XXXL-56':
                                            $post->xxxl+=1;
                                            break;
                                        case '4XL-58':
                                            $post->x4xl+=1;
                                            break;
                                        case '5XL-60':
                                            $post->x5xl+=1;
                                            break;
                                    }
                                    $post ->amount +=1;
                                    $post->save();
                                    
                                    
                                    $thingsPost = Things::find()->where("article = '".$d2[$i]['name']."'")->one();
                                    $wpPosts = WpPostmeta::find()->where("(meta_key='article') AND (meta_value='".$d2[$i]['name']."') ")->one();
                                    $postId = $wpPosts->post_id;
                                    
                                    $sizes = WpPostmeta::find()->where("(meta_key='sizes') AND (post_id='$postId')")->one();
                
                                    $newSizes = [];
                
                                    if ($thingsPost->s > 0) {
                                        array_push($newSizes, 1);
                                    } 
                                    if ($thingsPost->m > 0) {
                                        array_push($newSizes, 2);
                                    }
                                    if ($thingsPost->l > 0) {
                                        array_push($newSizes, 3);
                                    }
                                    if ($thingsPost->xl > 0) {
                                        array_push($newSizes, 4);
                                    }
                                    if ($thingsPost->xxl > 0) {
                                        array_push($newSizes, 5);
                                    }
                                    if ($thingsPost->xxxl > 0) {
                                        array_push($newSizes, 6);
                                    }
                                    if ($thingsPost->x4xl > 0) {
                                        array_push($newSizes, 7);
                                    }
                                    if ($thingsPost->x5xl > 0) {
                                        array_push($newSizes, 8);
                                    }
                
                                    $sizes->meta_value = serialize($newSizes);
                                    $sizes->save();
                                }
                                else if(strcmp($d[$i]['brand'],"СССР")==0){
                                    $post = Meta::find()->where("meta_key='ussr_armoring'")->one();
                                    $post->meta_value+=1;
                                    $post->save();
                                    
                                    $post = Things::find()->where("article = '".$d2[$i]['name']."'")->one();
                                    
                                    switch($d3[$i]['size']){
                                        case 'S-46':
                                            $post->s+=1;
                                            break;
                                        case 'M-48':
                                            $post->m+=1;
                                            break;
                                        case 'L-50':
                                            $post->l+=1;
                                            break;
                                        case 'XL-52':
                                            $post->xl+=1;
                                            break;
                                        case 'XXL-54':
                                            $post->xxl+=1;
                                            break;
                                        case 'XXXL-56':
                                            $post->xxxl+=1;
                                            break;
                                        case '4XL-58':
                                            $post->x4xl+=1;
                                            break;
                                        case '5XL-60':
                                            $post->x5xl+=1;
                                            break;
                                    }
                                    $post ->amount +=1;
                                    $post->save();
                                    
                                    
                                    $thingsPost = Things::find()->where("article = '".$d2[$i]['name']."'")->one();
                                    $wpPosts = WpPostmeta::find()->where("(meta_key='article') AND (meta_value='".$d2[$i]['name']."') ")->one();
                                    $postId = $wpPosts->post_id;
                                    
                                    $sizes = WpPostmeta::find()->where("(meta_key='sizes') AND (post_id='$postId')")->one();
                
                                    $newSizes = [];
                
                                    if ($thingsPost->s > 0) {
                                        array_push($newSizes, 1);
                                    } 
                                    if ($thingsPost->m > 0) {
                                        array_push($newSizes, 2);
                                    }
                                    if ($thingsPost->l > 0) {
                                        array_push($newSizes, 3);
                                    }
                                    if ($thingsPost->xl > 0) {
                                        array_push($newSizes, 4);
                                    }
                                    if ($thingsPost->xxl > 0) {
                                        array_push($newSizes, 5);
                                    }
                                    if ($thingsPost->xxxl > 0) {
                                        array_push($newSizes, 6);
                                    }
                                    if ($thingsPost->x4xl > 0) {
                                        array_push($newSizes, 7);
                                    }
                                    if ($thingsPost->x5xl > 0) {
                                        array_push($newSizes, 8);
                                    }
                
                                    $sizes->meta_value = serialize($newSizes);
                                    $sizes->save();
                                }
                            }   
                    }
            });
         
         $listenerSec -> listen();
        
        } catch (\AmoCRM\Exception $e) {
            printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
        }
        
        try {
                $listenerThird = new \AmoCRM\Webhooks\Listener();

                // Добавление обработчика на уведомление contacts->add
                $listenerThird->on('update_lead', function ($domain, $id, $data) {
                
        

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
            
                    $dd = unparse($out);
                    
                    $text = unparse5($out);
                    $post = Deals::find()->where("id='$text'")->one();
                    $cat1 = $post->cat1;
                    $cat2 = $post->cat2;
                    $cat3 = $post->cat3;
                    $cat4 = $post->cat4;
                    
                    $nCat1 = ""; $nCat2 = ""; $nCat3 = ""; $nCat4 = "";
                    
                    for($i = 0; $i< count($dd); $i++){
                        
                        if($i==0){
                            $nCat1 = $dd[$i]['brand'];
                        }else if($i==1){
                            $nCat2 = $dd[$i]['brand'];
                        }else if($i==2){
                            $nCat3 = $dd[$i]['brand'];
                        }else if($i==3){
                            $nCat4 = $dd[$i]['brand'];
                        }
                        
                    }        
                    
                    $post->cat1 = $nCat1;
                    $post->cat2 = $nCat2;
                    $post->cat3 = $nCat3;
                    $post->cat4 = $nCat4;
                    
                    $post->save();   

                    
                    if (strcmp($cat1, $nCat1) != 0) {
                        if(strcmp($nCat1,"Олимпиада 80")==0) {
                            $post = Meta::find()->where("meta_key='olympiad_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        } else if(strcmp($nCat1,"Россия")==0) {
                            $post = Meta::find()->where("meta_key='russia_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        } else if(strcmp($nCat1,"СССР")==0) {
                            $post = Meta::find()->where("meta_key='ussr_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        }
                    }
                 
                    if (strcmp($cat2, $nCat2) != 0) {
                        if(strcmp($nCat2,"Олимпиада 80")==0) {
                            $post = Meta::find()->where("meta_key='olympiad_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        } else if(strcmp($nCat2,"Россия")==0) {
                            $post = Meta::find()->where("meta_key='russia_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        } else if(strcmp($nCat2,"СССР")==0) {
                            $post = Meta::find()->where("meta_key='ussr_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        }
                    }
                    
                    if (strcmp($cat3, $nCat3) != 0) {
                        if(strcmp($nCat3,"Олимпиада 80")==0) {
                            $post = Meta::find()->where("meta_key='olympiad_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        } else if(strcmp($nCat3,"Россия")==0) {
                            $post = Meta::find()->where("meta_key='russia_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        } else if(strcmp($nCat3,"СССР")==0) {
                            $post = Meta::find()->where("meta_key='ussr_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        }
                    }
                    
                    if (strcmp($cat4, $nCat4) != 0) {
                        if(strcmp($nCat1,"Олимпиада 80")==0) {
                            $post = Meta::find()->where("meta_key='olympiad_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        } else if(strcmp($nCat4,"Россия")==0) {
                            $post = Meta::find()->where("meta_key='russia_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        } else if(strcmp($nCat4,"СССР")==0) {
                            $post = Meta::find()->where("meta_key='ussr_armoring'")->one();
                            $post->meta_value+=1;
                            $post->save();
                        }
                    }
                 
                    
                   
                });
                $listenerThird -> listen();
        } catch (\AmoCRM\Exception $e) {
            printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
        }
        
        return $this->render('webhook',['resp'=>$Response]);
    
    }
  }  
    function unparse($data){    
        
        
        $data = json_decode($data);
        $data = ($data->{'response'}->{'leads'}[0]->{'custom_fields'});
        echo "<br/>";
        $array = [];
        $g = 0;

        for($i = 0; $i<count($data); $i++){
            if(strcmp($data[$i]->{'name'}, "Бренд")==0){
                $array[$g]['brand'] = $data[$i]->{'values'}[0]->{'value'};
                $g++;
            }
        }
        return $array;
    }
    function unparse5($data){    
        
        $data = json_decode($data);
        $data = $data->{'response'}->{'leads'}[0]->{'id'};
        return $data;
    }
    
    function unparse2($data){    
        
        
        $data = json_decode($data);
        // echo $data['response']['leads'][0]['custom_fields'];
        $data = ($data->{'response'}->{'leads'}[0]->{'custom_fields'});
        echo "<br/>";
        $array = [];
        $g = 0;

        for($i = 0; $i<count($data); $i++){
            if(strcmp($data[$i]->{'name'}, "Размер")==0){
                $array[$g]['size'] = $data[$i]->{'values'}[0]->{'value'};
                $g++;
            }
        }
        return $array;
    }
    
    function unparse3($data){    
        
        
        $data = json_decode($data);
        // echo $data['response']['leads'][0]['custom_fields'];
        $data = ($data->{'response'}->{'leads'}[0]->{'custom_fields'});
        echo "<br/>";
        $array = [];
        $g = 0;

        for($i = 0; $i<count($data); $i++){
            if(strcmp($data[$i]->{'name'}, "Артикул")==0){
                $array[$g]['name'] = $data[$i]->{'values'}[0]->{'value'};
                $g++;
            }
        }
        return $array;
    }
    
    function unparse4($data){    
        $data = json_decode($data);
        // echo $data['response']['leads'][0]['custom_fields'];
        $data = ($data->{'response'}->{'leads'}[0]->{'status_id'});
        return $data;
    }
    




    


    
    
    


