<?php
/**
 * Money view
 *
 * @var $this \yii\web\View
 * @var $models \app\models\Money[]
 * @var $user \app\models\User
 *
 * @var $model \app\models\Money (Inside loops)
 */

 
 
use app\models\Money;
use yii\helpers\Url;
use yii\helpers\Html;
// use app\assets\MoneyAsset;
use app\models\Payment;
// use app\components\MoneyHelper;

// MoneyAsset::register($this);

$this->registerCssFile("https://fonts.googleapis.com/css?family=Open+Sans:400,300&subset=latin,cyrillic",
	[], 'open-sans-google');
$this->title = 'Финансовый мониторинг';

$year = date('Y');
$month = date('m');
?>

<meta charset="<?= Yii::$app->charset ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?= Html::csrfMetaTags() ?>
<?php $this->head() ?>
<style type="text/css">
	body {
    overflow-y:hidden;
	}
	::-webkit-scrollbar { 
    display: none; 
}
</style>



<script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap/3/css/bootstrap.css" />
 
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

<select onchange="call();" style = "width:300px; margin:30px;" class = "form-control date-period-selector'" id = "months">

</select>

<form id="money-search-form" action="<?= $_SERVER['REQUEST_URI'] ?>" method="get">


<div class = "idClient" style ="position: fixed; display:inline-block;margin-bottom:5px;width:550px; text-align:center; height:24px; background-color:#ffd37b;"><span>ID клиента</span></div>
<table style ="background-color: #ffffff;display:inline-block;font-size:12px;width: 550px; border-collapse: separate;">
	
	<tr style="position: fixed; margin-top:25px;">
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:102px;">Ответственный</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:98px;">Имя Фамилия</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:106px;">Телефон</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">Город</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:115px;">Статус</td>
			<td style ="margin-top: 5px;font-size:14px;line-height: 1.42857143;height:50px;background-color: #fff8cc; float: left;margin-left:2.5px ;width:33.83px;">CRM</td>
		</tr>	
	<tr><td  class = "tableIdClient"  style ="background-color:#ffffff; line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:102px;">
		<input style = "margin-top:10px;position: fixed;width: 102px" id="critical_acc" name="critical_acc" type = "text" class = "form-control text-input" onchange="callFilter()"></td>  
		<td  class = "tableIdClient"  style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:98px;"><input onchange="callFilter()" style = "margin-top:10px;position: fixed; width: 98px" type = "text" id="contact_name" name="contact_name" class = "form-control text-input"></td> 
		<td  class = "tableIdClient" style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:106px;"><input onchange="callFilter()" style = "margin-top:10px;position: fixed;width: 106px" type = "text" id="phone" name="phone" class = "form-control text-input"></td> 
		<td  class = "tableIdClient" style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:80px;"><input onchange="callFilter()" style = "margin-top:10px;position: fixed;width: 80px" type = "text" id="city" name="city" class = "form-control text-input"></td> 
		<td  class = "tableIdClient" style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:115px;"><select onchange="callFilter()" style = "margin-top:10px;position: fixed;width: 115px" type = "text" id="status" name="status" class = "form-control date-period-selector"></td> 
		<td></td>
	</tr> 

</table>
<div class = "idClient" style ="position: fixed; display:inline-block;margin-bottom:5px;width:825px; text-align:center; height:24px; background-color:#ffd37b;"><span>Информация по сделке</span></div>
<table style ="background-color: #ffffff;display:inline-block;font-size:12px;width: 1000px; border-collapse: separate; margin-top:-124px;margin-left:555px">
	
	<tr style="position: fixed; margin-top:25px;">
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:80px;">Дата создания</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:80px;">Дата закрытия</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:80px;">Дата отправки</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:80px;">Дата доставки</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:80px;">Дата успешной доставки</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:80px;">Дата возврата</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:80px;">Дата возврата товара</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:80px;">Стоимость</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:80px;">Стоимость СДЕК</td>
			<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:80px;">Че-то с процентами</td>
	</tr>
		<td class = "tableIdClient" style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:80px;"><input  id = "date_create" style = "margin-top:10px;position: fixed;width: 80px" type = "text" class = "form-control text-input"></td> 
		<td  class = "tableIdClient"  style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:80px;"><input id = "date_close" style = "margin-top:10px;position: fixed;width: 80px" type = "text" class = "form-control text-input"></td> 
		<td  class = "tableIdClient" style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:80px;"><input id = "date_send"  style = "margin-top:10px;position: fixed;width: 80px" type = "text" class = "form-control text-input"></td> 
		<td  class = "tableIdClient"  style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:80px;"><input id = "date_dost" style = "margin-top:10px;position: fixed;width: 80px" type = "text" class = "form-control text-input"></td> 
		<td  class = "tableIdClient"  style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:80px;"><input id = "dateSuc_dost" style = "margin-top:10px;position: fixed;width: 80px" type = "text" class = "form-control text-input"></td> 
		<td  class = "tableIdClient"  style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:80px;"><input id = "date_return" style = "margin-top:10px;position: fixed;width: 80px" type = "text" class = "form-control text-input"></td> 
		<td  class = "tableIdClient"  style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:80px;"><input id = "date_return_thing" style = "margin-top:10px;position: fixed;width: 80px" type = "text" class = "form-control text-input"></td> 
		<td  class = "tableIdClient" style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:80px;"><input onchange="callFilter()" id="price" style = "margin-top:10px;position: fixed;width: 80px" type = "text" class = "form-control text-input"></td> 
		<td  class = "tableIdClient" style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:80px;"><input onchange="callFilter()" id="sdek_summa" style = "margin-top:10px;position: fixed;width: 80px" type = "text" class = "form-control text-input"></td> 
		<td  class = "tableIdClient" style ="background-color:#ffffff;line-height: 1.42857143;margin-top:70px; float: left;margin-left:2.5px ;font-size: 12px; width:80px;"><input disabled="disabled"  style = "margin-top:10px;position: fixed;width: 80px" type = "text" class = "form-control text-input"></td> 
		
	</tr> 
	

</table>
		<table id = "tableAll" style ="background-color: #ffffff;display:inline-block;font-size:12px;width: 1550px; border-collapse: separate; height:60%; position: fixed; overflow: scroll">
		</table>
<style>
	.btn-dateSuccess{
		color: black;
		font-size: 14px;
		width: 80px;
		height: 40px;
		background-color:#00FF7F;
		text-align:center;
	}
	.btn-dateCancel{
		color: black;
		font-size: 14px;
		width: 80px;
		height: 40px;
		background-color: #ADD8E6;
		text-align:center;
	}
</style>


<script type="text/javascript" language="javascript">
		
	var opt = document.createElement('option');
	opt.innerHTML = 'СТАТУС';
	opt.backgroundColor = "";
	opt.value = '';
	$('#status').append(opt);

	var opt = document.createElement('option');
	opt.innerHTML = 'ВОЗВРАТ НА СКЛАД';
	opt.color = "ffd9ff";
	opt.value = '15756256';
	$('#status').append(opt);
	
	var opt = document.createElement('option');
	opt.innerHTML = 'ОЖИДАЕТ';
	opt.value = '12988848';
	opt.color = "ff7bff";
	$('#status').append(opt);
	
	var opt = document.createElement('option');
	opt.innerHTML = 'ГОТОВ К ОТПРАВКЕ';
	opt.value = '15756388';
	opt.color = "fcf700";
	$('#status').append(opt);
	
	var opt = document.createElement('option');
	opt.innerHTML = 'ЗАЯВКА';
	opt.color = "8ec9ff";
	opt.value = '12988842';
	$('#status').append(opt);
	
	var opt = document.createElement('option');
	opt.innerHTML = 'ОТЛОЖЕННЫЙ ЗАКАЗ';
	opt.value = '15756253';
	opt.color = "d2e9ff";
	$('#status').append(opt);
	
	var opt = document.createElement('option');
	opt.innerHTML = 'ДОСТАВЛЕН';
	opt.color = "49fac3";
	opt.value = '12988851';
	$('#status').append(opt);
	
	var opt = document.createElement('option');
	opt.innerHTML = 'УСПЕШНО ЗАВЕРШЕНО';
	opt.value = '142';
	opt.color = "b4ff62"
	$('#status').append(opt);
	
	var opt = document.createElement('option');
	opt.innerHTML = 'ЗАКРЫТО И НЕ РЕАЛИЗОВАНО';
	opt.value = '143';
	opt.color = "d4d8db";
	$('#status').append(opt);
	
	var opt = document.createElement('option');
	opt.innerHTML = 'НЕ ВРУЧЕН';
	opt.color = "ff838b";
	opt.value = '12998565';
	$('#status').append(opt);
	
	var opt = document.createElement('option');
	opt.innerHTML = 'ОТПРАВЛЕН';
	opt.value = '12988845';
	opt.color = "49fac3";
	$('#status').append(opt);
						
	var opt = document.createElement('option');
	opt.innerHTML = 'ПРЕДЗАКАЗ';
	opt.value = '15756250';
	opt.color = "ffdf77";
	$('#status').append(opt);
						
						
	
	$('#date_create').daterangepicker({
		autoUpdateInput: false,
		locale: {
          cancelLabel: 'Clear'
      	},
		"showCustomRangeLabel": false,
		"alwaysShowCalendars": true,
		"opens": "right",
		"drops": "down",
		"applyClass":"btn-dateSuccess",
		"cancelClass":"btn-dateCancel"
	}, function(start, end, label) {
	  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
	});

	$('#date_create').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        callFilter();
  	});


  	$('#date_create').on('cancel.daterangepicker', function(ev, picker) {
    	$(this).val('');
  	});

	$('#date_close').daterangepicker({
		autoUpdateInput: false,
		locale: {
          cancelLabel: 'Clear'
      	},
		"showCustomRangeLabel": false,
		"alwaysShowCalendars": true,
		"opens": "right",
		"drops": "down",
		"applyClass":"btn-dateSuccess",
		"cancelClass":"btn-dateCancel"
	}, function(start, end, label) {
	  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
	});

	$('#date_close').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        callFilter();
  	});

  	$('#date_close').on('cancel.daterangepicker', function(ev, picker) {
    	$(this).val('');
  	});

	$('#date_send').daterangepicker({
		autoUpdateInput: false,
		locale: {
          cancelLabel: 'Clear'
      	},
		"showCustomRangeLabel": false,
		"alwaysShowCalendars": true,
		"opens": "left",
		"drops": "down",
		"applyClass":"btn-dateSuccess",
		"cancelClass":"btn-dateCancel"
	}, function(start, end, label) {
	  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
	});

	$('#date_send').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        callFilter();
  	});

  	$('#date_send').on('cancel.daterangepicker', function(ev, picker) {
    	$(this).val('');
  	});

	$('#date_dost').daterangepicker({
		autoUpdateInput: false,
		locale: {
          cancelLabel: 'Clear'
      	},
		"showCustomRangeLabel": false,
		"alwaysShowCalendars": true,
		"opens": "left",
		"drops": "down",
		"applyClass":"btn-dateSuccess",
		"cancelClass":"btn-dateCancel"
	}, function(start, end, label) {
	  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
	});

	$('#date_dost').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        callFilter();
  	});

  	$('#date_dost').on('cancel.daterangepicker', function(ev, picker) {
    	$(this).val('');
  	});

	$('#dateSuc_dost').daterangepicker({
		autoUpdateInput: false,
		locale: {
          cancelLabel: 'Clear'
      	},
		"showCustomRangeLabel": false,
		"alwaysShowCalendars": true,
		"opens": "left",
		"drops": "down",
		"applyClass":"btn-dateSuccess",
		"cancelClass":"btn-dateCancel"
	}, function(start, end, label) {
	  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
	});

	$('#dateSuc_dost').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        callFilter();
  	});

  	$('#dateSuc_dost').on('cancel.daterangepicker', function(ev, picker) {
    	$(this).val('');
  	});

	$('#date_return').daterangepicker({
		autoUpdateInput: false,
		locale: {
          cancelLabel: 'Clear'
      	},
		"showCustomRangeLabel": false,
		"alwaysShowCalendars": true,
		"opens": "left",
		"drops": "down",
		"applyClass":"btn-dateSuccess",
		"cancelClass":"btn-dateCancel"
	}, function(start, end, label) {
	  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
	});

	$('#date_return').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        callFilter();
  	});

  	$('#date_return').on('cancel.daterangepicker', function(ev, picker) {
    	$(this).val('');
  	});

	$('#date_return_thing').daterangepicker({
		autoUpdateInput: false,
		locale: {
          cancelLabel: 'Clear'
      	},
		"showCustomRangeLabel": false,
		"alwaysShowCalendars": true,
		"opens": "left",
		"drops": "down",
		"applyClass":"btn-dateSuccess",
		"cancelClass":"btn-dateCancel"
	}, function(start, end, label) {
	  console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
	});

	$('#date_return_thing').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        callFilter();
  	});

  	$('#date_return_thing').on('cancel.daterangepicker', function(ev, picker) {
    	$(this).val('');
  	});
	
	startCall();
	
 	function call() {
		var val1 = (document.getElementById('months').value);
		var val2 = "";
		var val3 = val1.slice(0, -5);
		var date2 = ""

		var price_itog = 0;
		var sdek_itog = 0;

		switch(val3){
			case "Январь":
				val2 = val1.slice(7, 11);
				val3 = "01";
				date2 = "31";
				break;
			case "Февраль":
				val3 = "02";
				val2 = val1.slice(8, 12);
				if(Number(val2)%4 == 0){
					date2 = "29";
				}else{
					date2 = "28";
				}
				break;
			case "Март":
				val3 = "03";
				val2 = val1.slice(5, 9);
				date = "31";
				break;
			case "Апрель":
				val3 = "04";
				val2 = val1.slice(7, 11);
				date = "30";
				break;
			case "Май":
				val3 = "05";
				val2 = val1.slice(4, 8);
				date = "31";
				break;
			case "Июнь":
				val3 = "06";
				val2 = val1.slice(5, 9);
				date = "30";
				break;
			case "Июль":
				val3 = "07";
				val2 = val1.slice(5, 9);
				date = "31";
				break;
			case "Август":
				val3 = "08";
				val2 = val1.slice(7, 11);
				date = "31";
				break;
			case "Сентябрь":
				val3 = "09";
				val2 = val1.slice(9, 13);
				date = "30";
				break;
			case "Октябрь":
				val3 = "10";
				val2 = val1.slice(8, 12);
				date = "31";
				break;
			case "Ноябрь":
				val2 = val1.slice(7, 11);
				val3 = "11";
				date = "30";
				break;
			case "Декабрь":
				val2 = val1.slice(8, 12);
				val3 = "12";
				date = "31";
				break;
		}
		var fulldate = "01-"+val3+"-"+val2;
		var fulldate2 = date+"-"+val3+"-"+val2;
		fulldate = fulldate.split("-");
		fulldate2 = fulldate2.split("-");
		
		fulldate =  new Date(fulldate[1]+"/"+fulldate[0]+"/"+fulldate[2]).getTime()/1000;
		fulldate2 =   new Date(fulldate2[1]+"/"+fulldate2[0]+"/"+fulldate2[2]).getTime()/1000 + 86400;
		
        $.ajax({
          type: 'GET',
          url: '/money/getpost?date='+fulldate+'&date2='+fulldate2,
          success: function(data, itog_summa) {
			
            //alert($.trim(data));
			//data = JSON.stringify(data);
			data = $.trim(data);
			data=data.slice(1, -146);
			data = $.trim(data);
			data = data.substring(1);
			data = data.substring(0, data.length - 1);

			var json_texts = data.split('},{');

			// console.log(json_texts.length);
			// console.log();
			// alert(data[0]['lead_date_create']);
			var table = document.getElementById('tableAll');
			while(table.rows[0]) table.deleteRow(0);

			// var table = document.getElementById('tableLeads');
			// while(table.rows[0]) table.deleteRow(0);
			
			
			for(var i = 0; i<json_texts.length; i++){
				data = JSON.parse("{" + json_texts[i] + "}");
				
				var color = "";
				var elem  = document.createElement('elem'+i);
				elem.className = 'tableIdClient';
				switch(data['status']){
					case "12988851":
						data['status'] = "ДОСТАВЛЕН";
						color = "49fac3";
						break;
					case "142":
						data['status'] = "УСПЕШНО ЗАВЕРШЕНО";
						color = "b4ff62"
						break;
					case "143":
						data['status'] = "ЗАКРЫТО И НЕ РЕАЛИЗОВАНО";
						color = "d4d8db";
						break;
					case "12998565":
						data['status'] = "НЕ ВРУЧЕН";
						color = "ff838b";
						break;
					case "12988845":
						data['status'] = "ОТПРАВЛЕН";
						color = "49fac3";
						break;
					case "15756250":
						data['status'] = "ПРЕДЗАКАЗ";
						color = "ffdf77";
						break;
					case "15756253":
						data['status'] = "ОТЛОЖЕННЫЙ ЗАКАЗ";
						color = "d2e9ff";
						break;
					case "12988842":
						data['status'] = "ЗАЯВКА";
						color = "8ec9ff";
						break;
					case "15756388":
						data['status'] = "ГОТОВ К ОТПРАВКЕ";
						color = "fcf700";
						break;
					case "12988848":
						data['status'] = "ОЖИДАЕТ";
						color = "ff7bff";
						break;
					case "15756256":
						data['status'] = "ВОЗВРАТ НА СКЛАД";
						color = "ffd9ff"
						break;
				}
				
				// elem.innerHTML = data[i]['lead_date_create'];
				$('#tableAll').append('<tr style = "margin-top:-200px;">'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:102px;">'+data['main']+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:98px;">'+data['name']+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:106px;">'+data['phone']+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+data['city']+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: '+color+'; float: left;margin-left:2.5px ;font-size: 12px;width:115px;">'+data['status']+'</td>'
				+'<td style ="width:33.83px;" class = "tableIdClient"><a target="_blank" href="https://new584549b112ca4.amocrm.ru/leads/detail/'+data['id']+'"><img src = "../web/img/money_arr.png" style="max-width:100%;"/></a></td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:80px;">'+timeConverter(data['lead_date_create'])+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+timeConverter(data['lead_date_close'])+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+timeConverter(data['lead_date_send'])+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+timeConverter(data['lead_date_delivered'])+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+timeConverter(data['lead_date_success_delivered'])+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+timeConverter(data['lead_date_reset'])+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+timeConverter(data['lead_date_reset_thing'])+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+data['lead_summa']+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+data['sdek_summa']+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+'%'+'</td>'
				+'</tr>');

				price_itog += parseInt(data['lead_summa']);
				sdek_itog += data['sdek_summa'] == " " ? 0 : parseInt(data['sdek_summa']);

			}
			$('#tableAll').append('<tr style = "margin-top:-200px;">'
				+'<td  class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; margin-left:2.5px ;font-size: 12px; width:1122px;">'+'Сумма'+'</td>'
				+'<td id="price_itog" class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; margin-left:2.5px ;font-size: 12px;width:80px;">'+(price_itog + "").replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ')+'</td>'
				+'<td id="sdek_itog" class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; margin-left:2.5px ;font-size: 12px;width:80px;">'+(sdek_itog + "").replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ')+'</td>'
				+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; margin-left:2.5px ;font-size: 12px;width:80px;"></td>'
				+'</tr>');
          },
          error:  function(xhr, str){
			alert('Возникла ошибка: ' + xhr.responseCode);
          }
        });
    }
	
	function startCall() {
        $.ajax({
          type: 'GET',
          url: '/money/getpost',
          success: function(data) {
            //alert($.trim(data));
			//data = JSON.stringify(data);
			data = $.trim(data);
			data = data.slice(1, -146);
			data = $.trim(data);
			data = data.substring(1);
			data = data.substring(0, data.length - 1);
			
			var json_texts = data.split('},{');
			
			var month = [];
			
			var selectMonths = document.getElementById('months');
			//alert(data.length);
			//console.log(json_texts);
			for(var i = 0; i<json_texts.length; i++){
				//console.log(json_texts[i]);
				data = JSON.parse("{" + json_texts[i] + "}");
				switch(data['month']){
					case "01":
						var opt = document.createElement('option');
						opt.innerHTML = 'Январь '+data['year'];
						opt.value = 'Январь '+data['year'];
						$('#months').append(opt);
						break;
					case "02":
						var opt = document.createElement('option');
						opt.innerHTML = 'Февраль '+data['year'];
						opt.value = 'Февраль '+data['year'];
						$('#months').append(opt);
						break;
					case "03":
						var opt = document.createElement('option');
						opt.innerHTML = 'Март '+data['year'];
						opt.value = 'Март '+data['year'];
						$('#months').append(opt);
						break;
					case "04":
						var opt = document.createElement('option');
						opt.innerHTML = 'Апрель '+data['year'];;
						opt.value  = 'Апрель '+data['year'];
						$('#months').append(opt);
						break;
					case "05":
						var opt = document.createElement('option');
						opt.innerHTML = 'Май '+data['year'];
						opt.value = 'Май '+data['year'];
						$('#months').append(opt);
						break;
					case "06":
						var opt = document.createElement('option');
						opt.innerHTML =  'Июнь '+data['year'];
						opt.value =  'Июнь '+data['year'];
						$('#months').append(opt);
						break;
					case "07":
						var opt = document.createElement('option');
						opt.innerHTML = 'Июль '+data['year'];
						opt.value =  'Июль '+data['year'];
						$('#months').append(opt);
						break;
					case "08":
						var opt = document.createElement('option');
						opt.innerHTML ='Август '+data['year'];
						opt.value = 'Август '+data['year'];
						$('#months').append(opt);
						break;
					case "09":
						var opt = document.createElement('option');
						opt.innerHTML = 'Сентябрь '+data['year'];
						opt.value = 'Сентябрь '+data['year'];
						$('#months').append(opt);
						break;
					case "10":
						var opt = document.createElement('option');
						opt.innerHTML = 'Октябрь '+data['year'];
						opt.value ='Октябрь '+data['year'];
						$('#months').append(opt);
						break;
					case "11":
						var opt = document.createElement('option');
						opt.innerHTML ='Ноябрь '+data['year'];
						opt.value ='Ноябрь '+data['year'];
						$('#months').append(opt);
						break;
					case "12":
						var opt = document.createElement('option');
						opt.innerHTML ='Декабрь '+data['year'];
						opt.value = 'Декабрь '+data['year'];
						$('#months').append(opt);
						break;
				}
			}
			
			call();
			// console.log(json_texts.length);
			
			// alert(data[0]['lead_date_create']);
			
			
			
          },
          error:  function(xhr, str){
			alert('Возникла ошибка: ' + xhr.responseCode);
          }
        });
    }

    function timeConverter(UNIX_timestamp){
		var a = new Date(UNIX_timestamp * 1000);
	  	if (UNIX_timestamp == null || UNIX_timestamp == 0) return '';
	  	var months = ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];
	  	var year = a.getFullYear();
	  	var month = months[a.getMonth()];
	  	var date = a.getDate();
	  	var hour = a.getHours();
	  	var min = a.getMinutes();
	  	var sec = a.getSeconds();
	  	var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
	  	return time;
	}

	function callFilter() {
		var val1 = (document.getElementById('months').value);
		var val2 = "";
		var val3 = val1.slice(0, -5);
		var date2 = ""

		var price_itog = 0;
		var sdek_itog = 0;
		switch(val3){
			case "Январь":
				val2 = val1.slice(7, 11);
				val3 = "01";
				date2 = "31";
				break;
			case "Февраль":
				val3 = "02";
				val2 = val1.slice(8, 12);
				if(Number(val2)%4 == 0){
					date2 = "29";
				}else{
					date2 = "28";
				}
				break;
			case "Март":
				val3 = "03";
				val2 = val1.slice(5, 9);
				date = "31";
				break;
			case "Апрель":
				val3 = "04";
				val2 = val1.slice(7, 11);
				date = "30";
				break;
			case "Май":
				val3 = "05";
				val2 = val1.slice(4, 8);
				date = "31";
				break;
			case "Июнь":
				val3 = "06";
				val2 = val1.slice(5, 9);
				date = "30";
				break;
			case "Июль":
				val3 = "07";
				val2 = val1.slice(5, 9);
				date = "31";
				break;
			case "Август":
				val3 = "08";
				val2 = val1.slice(7, 11);
				date = "31";
				break;
			case "Сентябрь":
				val3 = "09";
				val2 = val1.slice(9, 13);
				date = "30";
				break;
			case "Октябрь":
				val3 = "10";
				val2 = val1.slice(8, 12);
				date = "31";
				break;
			case "Ноябрь":
				val2 = val1.slice(7, 11);
				val3 = "11";
				date = "30";
				break;
			case "Декабрь":
				val2 = val1.slice(8, 12);
				val3 = "12";
				date = "31";
				break;
		}
		var fulldate = "01-"+val3+"-"+val2;
		var fulldate2 = date+"-"+val3+"-"+val2;
		fulldate = fulldate.split("-");
		fulldate2 = fulldate2.split("-");
		
		fulldate = new Date(fulldate[1]+"/"+fulldate[0]+"/"+fulldate[2]).getTime()/1000;
		fulldate2 = new Date(fulldate2[1]+"/"+fulldate2[0]+"/"+fulldate2[2]).getTime()/1000;

		var critical_acc = document.getElementById('critical_acc').value;
		var contact_name = document.getElementById('contact_name').value;
		var phone = document.getElementById('phone').value;
		var city = document.getElementById('city').value;
		var status = document.getElementById('status').value;
		var date_create_interval = document.getElementById('date_create').value;
		var date_close_interval = document.getElementById('date_close').value;
		var date_send_interval = document.getElementById('date_send').value;
		var date_dost_interval = document.getElementById('date_dost').value;
		var dateSuc_dost_interval = document.getElementById('dateSuc_dost').value;
		var date_return_interval = document.getElementById('date_return').value;
		var date_return_thing_interval = document.getElementById('date_return_thing').value;

		var date_create_first, date_create_second;
		var date_close_first, date_close_second;
		var date_send_first, date_send_second;
		var date_dost_first, date_dost_second;
		var dateSuc_dost_first, dateSuc_dost_second;
		var date_return_first, date_return_second;
		var date_return_thing_first, date_return_thing_second;

		var price = document.getElementById('price').value;
		var sdek_summa = document.getElementById('sdek_summa').value;

		if (date_create_interval == "") {
			date_create_first = -1;
			date_create_second = 15014490130;
		} else {
			date_create_first = date_create_interval.substring(1, 10);
			date_create_first = date_create_first.split("/");
			date_create_first = new Date(date_create_first[0] + "/" + date_create_first[1] + "/" + date_create_first[2]).getTime()/1000;

			date_create_second = date_create_interval.substring(13);
			date_create_second = date_create_second.split("/");
			date_create_second = new Date(date_create_second[0] + "/" + date_create_second[1] + "/" + date_create_second[2]).getTime()/1000;	
		}

		if (date_close_interval == "") {
			date_close_first = -1;
			date_close_second = 15014490130;
		} else {
			date_close_first = date_close_interval.substring(1, 10);
			date_close_first = date_close_first.split("/");
			date_close_first = new Date(date_close_first[0] + "/" + date_close_first[1] + "/" + date_close_first[2]).getTime()/1000;

			date_close_second = date_close_interval.substring(13);
			date_close_second = date_close_second.split("/");
			date_close_second = new Date(date_close_second[0] + "/" + date_close_second[1] + "/" + date_close_second[2]).getTime()/1000;	
		}

		if (date_send_interval == "") {
			date_send_first = -1;
			date_send_second = 15014490130;
		} else {
			date_send_first = date_send_interval.substring(1, 10);
			date_send_first = date_send_first.split("/");
			date_send_first = new Date(date_send_first[0] + "/" + date_send_first[1] + "/" + date_send_first[2]).getTime()/1000;

			date_send_second = date_send_interval.substring(13);
			date_send_second = date_send_second.split("/");
			date_send_second = new Date(date_send_second[0] + "/" + date_send_second[1] + "/" + date_send_second[2]).getTime()/1000;	
		}

		if (date_dost_interval == "") {
			date_dost_first = -1;
			date_dost_second = 15014490130;
		} else {
			date_dost_first = date_dost_interval.substring(1, 10);
			date_dost_first = date_dost_first.split("/");
			date_dost_first = new Date(date_dost_first[0] + "/" + date_dost_first[1] + "/" + date_dost_first[2]).getTime()/1000;

			date_dost_second = date_dost_interval.substring(13);
			date_dost_second = date_dost_second.split("/");
			date_dost_second = new Date(date_dost_second[0] + "/" + date_dost_second[1] + "/" + date_dost_second[2]).getTime()/1000;	
		}
		
		if (dateSuc_dost_interval == "") {
			dateSuc_dost_first = -1;
			dateSuc_dost_second = 15014490130;
		} else {
			dateSuc_dost_first = dateSuc_dost_interval.substring(1, 10);
			dateSuc_dost_first = dateSuc_dost_first.split("/");
			dateSuc_dost_first = new Date(dateSuc_dost_first[0] + "/" + dateSuc_dost_first[1] + "/" + dateSuc_dost_first[2]).getTime()/1000;

			dateSuc_dost_second = dateSuc_dost_interval.substring(13);
			dateSuc_dost_second = dateSuc_dost_second.split("/");
			dateSuc_dost_second = new Date(dateSuc_dost_second[0] + "/" + dateSuc_dost_second[1] + "/" + dateSuc_dost_second[2]).getTime()/1000;	
		}

		if (date_return_interval == "") {
			date_return_first = -1;
			date_return_second = 15014490130;
		} else {
			date_return_first = date_return_interval.substring(1, 10);
			date_return_first = date_return_first.split("/");
			date_return_first = new Date(date_return_first[0] + "/" + date_return_first[1] + "/" + date_return_first[2]).getTime()/1000;

			date_return_second = date_return_interval.substring(13);
			date_return_second = date_return_second.split("/");
			date_return_second = new Date(date_return_second[0] + "/" + date_return_second[1] + "/" + date_return_second[2]).getTime()/1000;	
		}

		if (date_return_thing_interval == "") {
			date_return_thing_first = -1;
			date_return_thing_second = 15014490130;
		} else {
			date_return_thing_first = date_return_thing_interval.substring(1, 10);
			date_return_thing_first = date_return_thing_first.split("/");
			date_return_thing_first = new Date(date_return_thing_first[0] + "/" + date_return_thing_first[1] + "/" + date_return_thing_first[2]).getTime()/1000;

			date_return_thing_second = date_return_thing_interval.substring(13);
			date_return_thing_second = date_return_thing_second.split("/");
			date_return_thing_second = new Date(date_return_thing_second[0] + "/" + date_return_thing_second[1] + "/" + date_return_thing_second[2]).getTime()/1000;	
		}


		// console.log('/money/find?date='+fulldate+'&date2='+fulldate2 + '&critical_acc=' + critical_acc + '&contact_name=' + contact_name + '&phone=' + phone + '&city=' + city + '&status=' + status + '&date_create_first=' + date_create_first + '&date_create_second=' + date_create_second);

        $.ajax({
          type: 'GET',
          url: '/money/find?date='+fulldate+'&date2='+fulldate2 + '&critical_acc=' + critical_acc + '&contact_name=' + contact_name + '&phone=' + phone + '&city=' + city + '&status=' + status + '&date_create_first=' + date_create_first + '&date_create_second=' + date_create_second + '&date_close_first=' + date_close_first + '&date_close_second=' + date_close_second + '&date_send_first=' + date_send_first + '&date_send_second=' + date_send_second + '&date_dost_first=' + date_dost_first + '&date_dost_second=' + date_dost_second + '&dateSuc_dost_first=' + dateSuc_dost_first + '&dateSuc_dost_second=' + dateSuc_dost_second + '&date_return_first=' + date_return_first + '&date_return_second=' + date_return_second + '&date_return_thing_first=' + date_return_thing_first + '&date_return_thing_second=' + date_return_thing_second + '&price=' + price + '&sdek_summa=' + sdek_summa,
          success: function(data) {
			
            
			data = $.trim(data);
			data=data.slice(1, -146);
			data = $.trim(data);
			data = data.substring(1);
			data = data.substring(0, data.length - 1);

			var isNull = false;
			var json_texts = data.split('},{');

			var table = document.getElementById('tableAll');
			while(table.rows[0]) table.deleteRow(0);

			
			for(var i = 0; i<json_texts.length; i++){
				data = JSON.parse("{" + json_texts[i] + "}");
				
				var color = "";
				var elem  = document.createElement('elem'+i);
				elem.className = 'tableIdClient';
				switch(data['status']){
					case "12988851":
						
						data['status'] = "ДОСТАВЛЕН";
						color = "49fac3";
						break;
					case "142":
						data['status'] = "УСПЕШНО ЗАВЕРШЕНО";
						color = "b4ff62";
						break;
					case "143":
						data['status'] = "ЗАКРЫТО И НЕ РЕАЛИЗОВАНО";
						color = "d4d8db";
						break;
					case "12998565":
						data['status'] = "НЕ ВРУЧЕН";
						color = "ff838b";
						break;
					case "12988845":
						data['status'] = "ОТПРАВЛЕН";
						color = "49fac3";
						break;
					case "15756250":
						data['status'] = "ПРЕДЗАКАЗ";
						color = "ffdf77";
						break;
					case "15756253":
						data['status'] = "ОТЛОЖЕННЫЙ ЗАКАЗ";
						color = "d2e9ff";
						break;
					case "12988842":
						data['status'] = "ЗАЯВКА";
						color = "8ec9ff";
						break;
					case "15756388":
						data['status'] = "ГОТОВ К ОТПРАВКЕ";
						color = "fcf700";
						break;
					case "12988848":
						data['status'] = "ОЖИДАЕТ";
						color = "ff7bff";
						break;
					case "15756256":
						data['status'] = "ВОЗВРАТ НА СКЛАД";
						color = "ffd9ff";
						break;
				}

				if (data['id'] != undefined) {
					// elem.innerHTML = data[i]['lead_date_create'];
					$('#tableAll').append('<tr style = "margin-top:-200px;">'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:102px;">'+data['main']+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:98px;">'+data['name']+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:106px;">'+data['phone']+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+data['city']+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: '+color+'; float: left;margin-left:2.5px ;font-size: 12px;width:115px;">'+data['status']+'</td>'
					+'<td style ="width:33.83px;" class = "tableIdClient"><a target="_blank" href="https://new584549b112ca4.amocrm.ru/leads/detail/'+data['id']+'"><img src = "../web/img/money_arr.png" style="max-width:100%;"/></a></td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px; width:80px;">'+timeConverter(data['lead_date_create'])+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+timeConverter(data['lead_date_close'])+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+timeConverter(data['lead_date_send'])+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+timeConverter(data['lead_date_delivered'])+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+timeConverter(data['lead_date_success_delivered'])+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+timeConverter(data['lead_date_reset'])+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+timeConverter(data['lead_date_reset_thing'])+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+data['lead_summa']+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+data['sdek_summa']+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; float: left;margin-left:2.5px ;font-size: 12px;width:80px;">'+'%'+'</td>'
					+'</tr>');

					price_itog += parseInt(data['lead_summa']);
					sdek_itog += data['sdek_summa'] == " " ? 0 : parseInt(data['sdek_summa']);
				} else {
					isNull = true;
					break;
				}
			}

			if (!isNull) 
				$('#tableAll').append('<tr style = "margin-top:-200px;">'
					+'<td  class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; margin-left:2.5px ;font-size: 12px; width:1122px;">'+'Сумма'+'</td>'
					+'<td id="price_itog" class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; margin-left:2.5px ;font-size: 12px;width:80px;">'+(price_itog + "").replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ')+'</td>'
					+'<td id="sdek_itog" class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; margin-left:2.5px ;font-size: 12px;width:80px;">'+(sdek_itog + "").replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ')+'</td>'
					+'<td class = "tableIdClient" style ="line-height: 1.42857143; background-color: #fff8cc; margin-left:2.5px ;font-size: 12px;width:80px;"></td>'
					+'</tr>');
          },
          error:  function(xhr, str){
			alert('Возникла ошибка: ' + xhr.responseCode);
          }
        });
    }
</script>

<style>
.tableIdClient {
	line-height: 1.42857143;
	height:50px;
	background-color: #fff8cc; 
	float: left;
	margin-left:2.5px ;
	margin-top:5px;
}
</style>