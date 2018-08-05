<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Things;
use yii\helpers\Url;
use yii\helpers;
use yii\web\helpers\CHtml;

$this->title = 'Storage';

$this->registerCssFile('css/style.css');

$cookies = Yii::$app->request->cookies;
$n = 1;


function logoutT(){
    Yii::$app->response->cookies->remove('cook');
    Yii::$app->response->redirect('welcome');
}
?>
<script>
    setInterval(request, 5000);
</script>
<script> function request(){<?php
    if (!Yii::$app->getRequest()->getCookies()->has('cook')){
        Yii::$app->response->redirect('welcome');
}?>}</script>

<style type="text/css">
    table {width: 900px;}

    .warhouse {
        font-size: 30px;
    }

    .reserv {
        font-size: 30px;
    }

    #hidden {
        display: none;
        overflow:hidden;

        width: 0px !important;
        height: 0px !important;
        padding: 0 !important;
        font-size: 0%;
        position: absolute !important;
        margin: 0 !important;
    }
    .form-group {
        
        padding: 0;
        margin: 0;
        text-align: center;
        vertical-align: middle;
        display: inline-block;

    }
    div[name='showField'] {
        display: inline-block;
    }
    

</style> 
    <div class="wrapper">
        <div class="header">
            <div class="logo">
                <a href="http://rusichsport.ru/" target="_blank"><img src="../web/img/logo.png" alt="logo"></a>
            </div>

                     <div class="center" style="display: inline-block; width: 520px">
                        <h1>Склад</h1>
                        <button style = "margin-top:5%" name="button" id="add">Добавить товар</button>
                        <?php if(strcmp(Yii::$app->request->cookies->getValue('cook'), "manager")==0 && Yii::$app->getRequest()->getCookies()->has('cook')){ }else{ echo '<button style = "margin-top:5%" name="button" id="inventar">Инвентаризация</button>'; }?>
                    </div>
                    <div class="exit">
                        <a onclick = 'return location.href = "<?php Yii::$app->user->logout();?>"' href="welcome?log=true" title="">
                            <span color="red">Выход</span>
                                <img src="../web/img/exit.png" alt="" />
                        </a>
                    </div>  
        </div>
        <div class="clear"></div>
    
        <?php $f2 = ActiveForm::begin();?>
        <!--tableRussia-->
        <div class="" style="margin-top: 40px">
            <div class="table-title" id="headRu">
                <span class="country">Россия</span>
                <div class="warhouse"><span class="sklad"><?=$russiaAmount?></span>
                    <span class="reserv">(<?=$russiaArmoring?>)</span></div>

            </div>
            <div class="table-wrap" id="tableRussia">
                <table name="russiaContent" class="Russia" style="border-collapse: separate; border-spacing: 3px;">
                    <thead>
                        <tr>
                            <td style="text-align: center; width:20%"><span>Артикул</span></td>
                            <td style="width:35%"><span>Название</span></td>
                            <td>S-46</td>
                            <td>M-48</td>
                            <td>L-50</td>
                            <td>XL-52</td>
                            <td>XXL-54</td>
                            <td>XXXL-56</td>
                            <td>4XL-58</td>
                            <td>5XL-60</td>
                            <td>к-во</td>
                            <td>цена</td>
                        </tr>
                    </thead>
                    <tbody>
                       <?php
                        foreach ($russia as $thing) { ?>
                        
                        <tr>
                            <td><span>
                            <? echo '<div class="" name="showField" style="margin-left:5%">'.$thing->article.'</div>'; ?>
                            <?=$f2->field($editForm, 'editArticle[]')->textInput(['style'=>'width:98%' ,'value' => $thing->article, 'class' => 'inputField'])->label('')?>
                            </span></td>
                            <td style="background-color: #f7f6e7; width: 25%"><span>
                            <? echo '<div class="" name="showField" style="margin-left:5%">'.$thing->name.'</div>'; ?>
                            <?=$f2->field($editForm, 'editNames[]')->textInput(['style'=>'width:98%' ,'value' => $thing->name, 'class' => 'inputField'])->label('')?>
                            </span></td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->s.'</div>'; ?>
                            <?=$f2->field($editForm, 'editSs[]')->textInput(['style'=>'width:98%' ,'value' => $thing->s, 'type'=>'number', 'class' => 'inputField'])->label('')?>                            
                            </td> 
                           <td>
                            <? echo '<div class="" name="showField">'.$thing->m.'</div>'; ?>
                            <?=$f2->field($editForm, 'editMs[]')->textInput(['style'=>'width:98%' ,'value' => $thing->m, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->l.'</div>'; ?>
                            <?=$f2->field($editForm, 'editLs[]')->textInput(['style'=>'width:98%' ,'value' => $thing->l, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                             <td>
                            <? echo '<div class="" name="showField">'.$thing->xl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editXls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->xl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->xxl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editXxls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->xxl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->xxxl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editXxxls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->xxxl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                             <td>
                            <? echo '<div class="" name="showField">'.$thing->x4xl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editx4xls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->x4xl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                             <td>
                            <? echo '<div class="" name="showField">'.$thing->x5xl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editx5xls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->x5xl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->amount.'</div>'; ?>
                            <?=$f2->field($editForm, 'editAmounts[]')->textInput(['style'=>'width:98%' ,'value' => $thing->amount, 'type'=>'number', 'class' => 'inputField', 'disabled' => true])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->price.'</div>'; ?>
                            <?=$f2->field($editForm, 'editPrices[]')->textInput(['style'=>'width:98%' ,'value' => $thing->price, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!--tableCCCP-->

            <div class="table-title" id="headCccp">
                <span class="country">СССР</span>
                <div class="warhouse"><span class="sklad"><?=$ussrAmount ?></span>
                    <span class="reserv">(<?=$ussrArmoring?>)</span></div>
            </div>
            <div class="table-wrap" id="tableCccp">
            
            
               <table name="ussrContent" class="Russia" style="border-collapse: separate; border-spacing: 3px;"> 
                    <thead>
                        <tr>
                            <td style="text-align: center; width:20%"><span>Артикул</span></td>
                            <td style="width:35%"><span>Название</span></td>
                                <td>S-46</td>
                            <td>M-48</td>
                            <td>L-50</td>
                            <td>XL-52</td>
                            <td>XXL-54</td>
                            <td>XXXL-56</td>
                            <td>4XL-58</td>
                            <td>5XL-60</td>
                            <td>к-во</td>
                            <td>цена</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($ussr as $thing) { ?>
                        <tr>
                            <td><span>
                            <? echo '<div class="" name="showField" style="margin-left:5%">'.$thing->article.'</div>'; ?>
                            <?=$f2->field($editForm, 'editArticle[]')->textInput(['style'=>'width:98%' ,'value' => $thing->article, 'class' => 'inputField'])->label('')?>
                            </span></td>
                            <td style="background-color: #f7f6e7; width: 25%"><span>
                            <? echo '<div class="" name="showField" style="margin-left:5%">'.$thing->name.'</div>'; ?>
                            <?=$f2->field($editForm, 'editNames[]')->textInput(['style'=>'width:98%' ,'value' => $thing->name, 'class' => 'inputField'])->label('')?>
                            </span></td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->s.'</div>'; ?>
                            <?=$f2->field($editForm, 'editSs[]')->textInput(['style'=>'width:98%' ,'value' => $thing->s, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                           <td>
                            <? echo '<div class="" name="showField">'.$thing->m.'</div>'; ?>
                            <?=$f2->field($editForm, 'editMs[]')->textInput(['style'=>'width:98%' ,'value' => $thing->m, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                           <td>
                            <? echo '<div class="" name="showField">'.$thing->l.'</div>'; ?>
                            <?=$f2->field($editForm, 'editLs[]')->textInput(['style'=>'width:98%' ,'value' => $thing->l, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->xl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editXls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->xl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                           <td>
                            <? echo '<div class="" name="showField">'.$thing->xxl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editXxls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->xxl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->xxxl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editXxxls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->xxxl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                             <td>
                            <? echo '<div class="" name="showField">'.$thing->x4xl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editx4xls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->x4xl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                             <td>
                            <? echo '<div class="" name="showField">'.$thing->x5xl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editx5xls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->x5xl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->amount.'</div>'; ?>
                            <?=$f2->field($editForm, 'editAmounts[]')->textInput(['style'=>'width:98%' ,'value' => $thing->amount, 'type'=>'number', 'class' => 'inputField', 'disabled' => true])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->price.'</div>'; ?>
                            <?=$f2->field($editForm, 'editPrices[]')->textInput(['style'=>'width:98%' ,'value' => $thing->price, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                        </tr>
                        <?php } ?>
                    
                    </tbody>
                </table>
            
            </div>

            <!--tableОлимпиада-->

            <div class="table-title" id="headOlimp">
                <span class="country">Олимпиада 80</span>
                <div class="warhouse"><span class="sklad"><?=$olympiad80Amount?></span>
                    <span class="reserv">(<?=$olympiadArmoring?>)</span></div>
            </div>
            <div class="table-wrap" id="tableOlimpiada">
                <table name="olympiad80Content" class="Russia" style="border-collapse: separate; border-spacing: 3px;">
                    <thead>
                        <tr>
                            <td style="text-align: center; width:20%"><span>Артикул</span></td>
                            <td style="width:35%"><span>Название</span></td>
                                <td>S-46</td>
                            <td>M-48</td>
                            <td>L-50</td>
                            <td>XL-52</td>
                            <td>XXL-54</td>
                            <td>XXXL-56</td>
                            <td>4XL-58</td>
                            <td>5XL-60</td>
                            <td>к-во</td>
                            <td>цена</td>
                        </tr>
                    </thead>
                    <tbody>
                       <?php
                        foreach ($olympiad80 as $thing) { ?>
                        <tr>
                            <td><span>
                            <? echo '<div class="" name="showField" style="margin-left:5%">'.$thing->article.'</div>'; ?>
                            <?=$f2->field($editForm, 'editArticle[]')->textInput(['style'=>'width:98%' ,'value' => $thing->article, 'class' => 'inputField'])->label('')?>
                            </span></td>
                            <td style="background-color: #f7f6e7; width: 25%"><span>
                            <? echo '<div class="" name="showField" style="margin-left:5%">'.$thing->name.'</div>'; ?>
                            <?=$f2->field($editForm, 'editNames[]')->textInput(['style'=>'width:98%' ,'value' => $thing->name, 'class' => 'inputField'])->label('')?>
                            </span></td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->s.'</div>'; ?>
                            <?=$f2->field($editForm, 'editSs[]')->textInput(['style'=>'width:98%' ,'value' => $thing->s, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                           <td>
                            <? echo '<div class="" name="showField">'.$thing->m.'</div>'; ?>
                            <?=$f2->field($editForm, 'editMs[]')->textInput(['style'=>'width:98%' ,'value' => $thing->m, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->l.'</div>'; ?>
                            <?=$f2->field($editForm, 'editLs[]')->textInput(['style'=>'width:98%' ,'value' => $thing->l, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->xl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editXls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->xl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->xxl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editXxls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->xxl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->xxxl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editXxxls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->xxxl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                             <td>
                            <? echo '<div class="" name="showField">'.$thing->x4xl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editx4xls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->x4xl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                             <td>
                            <? echo '<div class="" name="showField">'.$thing->x5xl.'</div>'; ?>
                            <?=$f2->field($editForm, 'editx5xls[]')->textInput(['style'=>'width:98%' ,'value' => $thing->x5xl, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->amount.'</div>'; ?>
                            <?=$f2->field($editForm, 'editAmounts[]')->textInput(['style'=>'width:98%' ,'value' => $thing->amount, 'type'=>'number', 'class' => 'inputField', 'disabled' => true])->label('')?>
                            </td>
                            <td>
                            <? echo '<div class="" name="showField">'.$thing->price.'</div>'; ?>
                            <?=$f2->field($editForm, 'editPrices[]')->textInput(['style'=>'width:98%' ,'value' => $thing->price, 'type'=>'number', 'class' => 'inputField'])->label('')?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        <?= Html::submitButton('Сохранить', ['style' => 'margin-top: 50px; margin-left:40%', 'name' => 'btn_save', 'id' => 'btnSave', 'class' => 'hidden']) ?>
    <?php ActiveForm::end();?>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>


    <div id="dark">
    <?php $f = ActiveForm::begin()?>
    
        <div class="spaceWrapper">
           
            <button name="button_close" id="close">X</button>
            <div id="modal-table">
                <div class="table-wrap-hidden">
                        <table class="Russia" style="border-collapse: separate; border-spacing: 1px;">
                            <thead>
                                <tr>
                                    <td style="text-align: center;  width: 25%"><span>Артикул</span></td>
                                    <td style="width: 25%">Название</td>
                                        <td>S-46</td>
                            <td>M-48</td>
                            <td>L-50</td>
                            <td>XL-52</td>
                            <td>XXL-54</td>
                            <td>XXXL-56</td>
                            <td>4XL-58</td>
                            <td>5XL-60</td>
                                    <td>цена</td>
                                </tr>
                            </thead>
                            
                            <tbody class="hidden_table">
                                    
                                <?php for($i = 0; $i < 3; $i++){?>
                                        <tr class='hidden-row'>
                                            <td><?=$f->field($form, 'article[]')->dropDownList($allarticles, ['id' => "selectName$i", 'style'=>'width:205px; margin-left:5px;','options' => ['0'=>['selected'=>true]]])->label('');?></td>
                                            <td style="background-color: #f7f6e7"><?=$f->field($form, 'names[]')->dropDownList($allclothes, ['id' => "selectArticle$i", 'style'=>' width: 200px; margin-left:5px', 'options' => ['0'=>['selected'=>true]]])->label('');?></td>

                                            <td><?=$f->field($form, 'ss[]')->textInput(['style'=>'width:98%' ,'value' =>'0', 'type'=>'number', 'min' => '0'])->label('')?></td>
                                            <td><?= $f->field($form, 'ms[]')->textInput(['style'=>'width:98%' ,'value' =>'0', 'type'=>'number', 'min' => '0'])->label('')?></td>
                                            <td><?= $f->field($form, 'ls[]')->textInput(['style'=>'width:98%' , 'value' =>'0','type'=>'number', 'min' => '0'])->label('')?></td>
                                            <td><?= $f->field($form, 'xls[]')->textInput(['style'=>'width:98%' ,'value' =>'0', 'type'=>'number', 'min' => '0'])->label('')?></td>
                                            <td><?= $f->field($form, 'xxls[]')->textInput(['style'=>'width:98%' , 'value' =>'0','type'=>'number', 'min' => '0'])->label('')?></td>
                                            <td><?= $f->field($form, 'xxxls[]')->textInput(['style'=>'width:98%' , 'value' =>'0','type'=>'number', 'min' => '0'])->label('')?></td>
                                            <td><?= $f->field($form, 'x4xls[]')->textInput(['style'=>'width:98%' , 'value' =>'0','type'=>'number', 'min' => '0'])->label('')?></td>
                                            <td><?= $f->field($form, 'x5xls[]')->textInput(['style'=>'width:98%' , 'value' =>'0','type'=>'number', 'min' => '0'])->label('')?></td>
                                            <td><?= $f->field($form, 'prices[]')->textInput(['style'=>'width:98%' , 'value' =>'0','type'=>'number', 'min' => '0'])->label('')?></td>
                                            
                                        </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                </div>
            </div><!--<button type="submit" name="button_add" id="future">Добавить</button>-->
            <?= Html::submitButton('Добавить', ['id'=>'future', 'name' => 'button_save']) ?>
            <?php ActiveForm::end(); ?>
           <!-- <button name="row_add" id="add_row">+</button>-->
        </div>
    </div>

    <script>
  
    $(function() {

        var open = false;
        var show = []; var hide = [];

        show = Array.from(document.getElementsByClassName('inputField'));

        show.forEach(function(entry) {
            entry.setAttribute('class', 'hidden');
        });

        $('#inventar').click(function(event) {
                hide = Array.from(document.getElementsByName('showField'));

                if (!open) {
                    hide.forEach(function(entry) {
                        entry.setAttribute('class', 'hidden');
                        
                    });
                    show.forEach(function(entry) {
                        entry.setAttribute('class', 'inputField');
                        
                    });

                    document.getElementById('btnSave').setAttribute('class', '');

                    open = true;
                } else {

                    hide.forEach(function(entry) {
                        entry.setAttribute('class', '');
                        
                    });
                    show.forEach(function(entry) {
                        entry.setAttribute('class', 'hidden');
                        entry.style.height = "50px";

                    });
                    document.getElementById('btnSave').setAttribute('class', 'hidden');

                    open = false;
                }

                $('#tableRussia').toggle(
                    function () {
                        if ($("russiaContent").is(':visible') && open) {
                            $('#tableRussia').toggle();
                        } 
                    }
                );
                $('#tableCccp').toggle(
                    function () {
                        if ($("ussrContent").is(':visible') && open) {
                            $('#tableRussia').toggle();
                        } 
                    }
                );
                $('#tableOlimpiada').toggle(
                    function () {
                        if ($("olympiad80Content").is(':visible') && open) {
                            $('#tableRussia').toggle();
                        } 
                    }
                );

                // open = true;
        });


        $('#selectName0').on("change", function()
        {
            var selectName = document.getElementById('selectName0');
            var selectArticle = document.getElementById('selectArticle0');
            $('#selectArticle0').val(selectArticle[selectName.options.selectedIndex].value);
        });
        $('#selectArticle0').on("change", function()
        {
            var selectArticle = document.getElementById('selectArticle0');
            var selectName = document.getElementById('selectName0');
            $('#selectName0').val(selectName[selectArticle.options.selectedIndex].value);
        }); 

        $('#selectName1').on("change", function()
        {
            var selectName = document.getElementById('selectName1');
            var selectArticle = document.getElementById('selectArticle1');
            $('#selectArticle1').val(selectArticle[selectName.options.selectedIndex].value);
        });
        $('#selectArticle1').on("change", function()
        {
            var selectArticle = document.getElementById('selectArticle1');
            var selectName = document.getElementById('selectName1');
            $('#selectName1').val(selectName[selectArticle.options.selectedIndex].value);
        });  

        $('#selectName2').on("change", function()
        {
            var selectName = document.getElementById('selectName2');
            var selectArticle = document.getElementById('selectArticle2');
            $('#selectArticle2').val(selectArticle[selectName.options.selectedIndex].value);
        });
        $('#selectArticle2').on("change", function()
        {
            var selectArticle = document.getElementById('selectArticle2');
            var selectName = document.getElementById('selectName2');
            $('#selectName2').val(selectName[selectArticle.options.selectedIndex].value);
        });   
    });

    </script>
    

    <style>     
       a {
        color: #000000; /* Цвет обычной ссылки */ 
        text-decoration: none; /* Убираем подчеркивание у ссылок */
       }

       a:hover {
        color: #000000; /* Цвет обычной ссылки */ 
        text-decoration: none; /* Убираем подчеркивание у ссылок */
       }
    </style>
