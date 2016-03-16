//////////////////////////////////////////////////////////
//
//	CalendarJS 1.01
//
//	Автор: Александр Седунов (kxmep@mail.ru)
//
//	freeware
//	
//////////////////////////////////////////////////////////
var version = '1.01';

//////////////////////////////////////////////////////////
// Инициализация
//////////////////////////////////////////////////////////

var IE = document.all?true:false;
if (!IE)
{
	
    document.captureEvents(Event.MOUSEDOWN);
	document.onmousedown = getMousePos;
}

var mouseX = 0;
var mouseY = 0;
var offsetX = 0;
var offsetY = 0;

//Цвета календаря
var clBackground = '#F8F8FF';
var clFrame = '#B9B9FF';
var clSelect = '#E3E3FF';

//Размеры календаря
var calendarWidth = 182;
var calendarHeight = 194;

//Размеры кнопки, которая вызывает календарь
var buttonWidth = 12;
var buttonHeight = 12;

//увеличить это значение, если календарь перекрывают другие элементы
var zindex = 1000;

var tempEdit;
var selectDate = new Date();

 
document.write('<iframe name="Calendar" id="Calendar" frameborder=0 scrolling=no width=100% height=100% style="position: absolute; left: 348; top: 53; width: '  + calendarWidth + '; height: ' + calendarHeight + '; z-index: ' + zindex + '; visibility: hidden; border: 1px outset ' + clFrame + '"></iframe>');
document.write('<div id="Shadow" style="VISIBILITY: hidden; z-index:1; filter: progid:DXImageTransform.Microsoft.Blur(PixelRadius=4,MakeShadow=true,ShadowOpacity=0.6);  POSITION: absolute; left: 348; top: 53; " align="center"><table bgcolor=#555555 width=182 height=194><tr><td bgcolor=#555555></td></tr></table></div>');

//////////////////////////////////////////////////////////
// Функции
//////////////////////////////////////////////////////////
function getMousePos(e) 
{
	if (!IE)
	{
		offsetX = e.clientX - e.target.x;
		offsetY = e.clientY - e.target.y;
		mouseX = e.clientX;
		mouseY = e.clientY;
	}
}

//Спрятать календарь
function hideCalendar()
{
  if (document.getElementById('Calendar').style.visibility == 'visible')
    {
      document.getElementById('Calendar').style.visibility = 'hidden';
      document.getElementById('Shadow').style.visibility   = 'hidden';
    }
}

//Показать календарь
function showCalendar(aEditID,Root) 
{
	var aEdit = document.getElementById(aEditID);
    
    hideCalendar();
    
    if (aEdit.disabled == true)
    	return;
		
	var docWidth = document.body.offsetWidth;
	var docHeight = document.body.offsetHeight;
    var calendarLeft = 0;
	var calendarTop = 0;
	
    if (IE)
    {
	    offsetX = event.offsetX;
		offsetY = event.offsetY;
		mouseX = event.clientX + document.body.scrollLeft;
		mouseY = event.clientY + document.body.scrollTop;
    }
    else
    {
		mouseX += self.scrollX;
		mouseY += self.scrollY;
    }
			
	calendarLeft = mouseX + (buttonWidth - offsetX);
	calendarTop = mouseY + (buttonHeight - offsetY);
	
    if ((calendarLeft - document.body.scrollLeft) + calendarWidth > docWidth)
		calendarLeft = mouseX - calendarWidth - offsetX;

	if ((calendarTop - document.body.scrollTop) + calendarHeight > docHeight)
           calendarTop = mouseY - calendarHeight - offsetY;
    
    if (typeof aEdit == 'object')
    {
    	selectDate = StrToDate(aEdit.value);
        tempEdit = aEdit;
    }
    else
    	return;
    
	initCalendar( Root );
    
	document.getElementById('Calendar').style.left = calendarLeft;
	document.getElementById('Calendar').style.top = calendarTop;
	document.getElementById('Calendar').style.visibility = 'visible';
  
    document.getElementById('Shadow').style.left         = calendarLeft;
    document.getElementById('Shadow').style.top          = calendarTop;
    document.getElementById('Shadow').style.visibility   = 'visible'
  
	
}

//получение даты из строки
function StrToDate(aValue)
{
	var inDate;
	var result = new Date();
    
    inDate = aValue.replace('-', '.');
	inDate = inDate.replace('/', '.');
    
    if (inDate.indexOf('.'))
    {
    	var inDay = Number(inDate.substring(0, inDate.indexOf('.')));
        var inMonth = Number(inDate.substring(inDate.indexOf('.') + 1, inDate.lastIndexOf('.')));
        var inYear = Number(inDate.substring(inDate.lastIndexOf('.') + 1, inDate.length));

        if (inDay > 0 && inDay < 32 && inMonth > 0  && inMonth < 13 && inYear > 0) 
        	result = new Date(inYear, inMonth - 1, inDay);
		else
        	result = new Date();
    }

    return(result);
}

//Заполнение строки лидирующим нулем
function firstZero(aValue)
{

	if (('' + aValue).length == 1)
    {
		return('0' + aValue);
    }
	return(aValue);
}

//Today If Empty - Вставляет текущую дату, если поле пустое
function tie(aEdit)
{
	if (aEdit.value == '' && aEdit.disabled == false)
	{
		hideCalendar();
        
        var s = '', now = new Date();
		
		s = firstZero(now.getDate().toString())  + '.';
		s += firstZero((now.getMonth() + 1).toString())  + '.';
		s += now.getFullYear();
					
		aEdit.value = s;
	}
}

//Инициализация календаря
function initCalendar( Root )
{
    var frmCalendar = self.Calendar.document;

    frmCalendar.open('text/html', 'replace'); 

	frmCalendar.writeln('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'); 
	frmCalendar.writeln('<html>'); 
	frmCalendar.writeln('	<head>'); 
	frmCalendar.writeln('		<META http-equiv=Content-Type content="text/html; charset=windows-1251">');

	frmCalendar.writeln('		<style type="text/css">');
	frmCalendar.writeln('			<!--');

	frmCalendar.writeln('			td {');
	frmCalendar.writeln('				font-family : Arial, Helvetica, sans-serif;');
	frmCalendar.writeln('				font-size : 8pt;');
	frmCalendar.writeln('				color : Black;');
	frmCalendar.writeln('				cursor: default;');
	frmCalendar.writeln('				border: 1px solid ' + clBackground + ';');
	frmCalendar.writeln('				height : 20px;');
	frmCalendar.writeln('			}');

	frmCalendar.writeln('			td.Item {');
	frmCalendar.writeln('				background-color: ' + clBackground + ';');
	frmCalendar.writeln('				width : 20px;');
	frmCalendar.writeln('			}');

	frmCalendar.writeln('			td.Weekday {');
	frmCalendar.writeln('				width : 20px;');
	frmCalendar.writeln('				border: 0px;');
	frmCalendar.writeln('				border-bottom: 1px solid black;');
	frmCalendar.writeln('			}');
    
    frmCalendar.writeln('			body {');
	frmCalendar.writeln('				margin: 0px;');
	frmCalendar.writeln('				padding: 0px;');
	frmCalendar.writeln('				scroll: no;');
	frmCalendar.writeln('				background-color: ' + clBackground + ';');
	frmCalendar.writeln('				font-family : Arial, Helvetica, sans-serif;');
	frmCalendar.writeln('				font-size: 8pt;');
	frmCalendar.writeln('			}');

	frmCalendar.writeln('			-->');
	frmCalendar.writeln('		</style>');

	frmCalendar.writeln('		<script language="javascript">');
	frmCalendar.writeln('			<!--');

	frmCalendar.writeln('			var currentDate = new Date();');
	frmCalendar.writeln('			var selectDate = window.top.selectDate;');
	frmCalendar.writeln('			var tempEdit = window.top.tempEdit;');

	frmCalendar.writeln('			function fillCalendar()');
	frmCalendar.writeln('			{');

	frmCalendar.writeln('				var dayInMonth = getDaysInMonth(selectDate.getMonth() + 1, selectDate.getFullYear());');
	frmCalendar.writeln('				var dayInPrevMonth = getDaysInMonth(selectDate.getMonth(), selectDate.getFullYear());');

   	frmCalendar.writeln('				var firstMonthDay = new Date(selectDate);');
   	frmCalendar.writeln('				firstMonthDay.setDate(1);');

   	frmCalendar.writeln('				var firstWeekDay = firstMonthDay.getDay();');
   	frmCalendar.writeln('				if (firstWeekDay == 0)');
   	frmCalendar.writeln('					firstWeekDay = 7;');

   	frmCalendar.writeln('				var Month = new Array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");');

   	frmCalendar.writeln('				document.getElementById("MonthYear").innerHTML = Month[selectDate.getMonth()] + "<br>" + selectDate.getFullYear();');

   	frmCalendar.writeln('				var td = "i";');
   	frmCalendar.writeln('				var n = 1;');
   	frmCalendar.writeln('				var i;');

   	frmCalendar.writeln('				while (n < firstWeekDay)');
   	frmCalendar.writeln('				{');

   	frmCalendar.writeln('					document.getElementById(td + n).innerHTML = "";');
   	frmCalendar.writeln('					n++;');
   	frmCalendar.writeln('				}');

   	frmCalendar.writeln('				var day = 1;');
   	frmCalendar.writeln('				for (i = firstWeekDay; i <= 7; i++)');
   	frmCalendar.writeln('				{');
   	frmCalendar.writeln('					if (i == 6 || i == 7)');
   	frmCalendar.writeln('						document.getElementById(td + n).innerHTML = "<font color=\'#FF0000\'>" + firstZero(day) + "</font>";');
   	frmCalendar.writeln('					else');
   	frmCalendar.writeln('						document.getElementById(td + n).innerHTML = firstZero(day);');
   	frmCalendar.writeln('					day++;');
   	frmCalendar.writeln('					n++;');
   	frmCalendar.writeln('				}');

   	frmCalendar.writeln('				while (day <= dayInMonth)');
   	frmCalendar.writeln('				{');
   	frmCalendar.writeln('					var tempDate = new Date(selectDate);');
   	frmCalendar.writeln('					var lu;');
   	frmCalendar.writeln('					var ru;');
   	frmCalendar.writeln('					i = 1;');
   	frmCalendar.writeln('					while (i < 8 && day <= dayInMonth)');
   	frmCalendar.writeln('					{');
   	frmCalendar.writeln('						tempDate.setDate(day);');
   	frmCalendar.writeln('						if (compareDate(tempDate, currentDate))');
   	frmCalendar.writeln('						{');
   	frmCalendar.writeln('							lu = "<div style=\'background-color: ' + clSelect + '\'>";');
   	frmCalendar.writeln('							ru = "</div>";');
   	frmCalendar.writeln('						}');
   	frmCalendar.writeln('						else');
   	frmCalendar.writeln('						{');
   	frmCalendar.writeln('							lu = "";');
   	frmCalendar.writeln('							ru = "";');
   	frmCalendar.writeln('						}');
   	frmCalendar.writeln('						if (i == 6 || i == 7)');
   	frmCalendar.writeln('							document.getElementById(td + n).innerHTML = "<font color=\'#FF0000\'>" + lu + firstZero(day) + ru + "</font>";');
   	frmCalendar.writeln('						else');
   	frmCalendar.writeln('							document.getElementById(td + n).innerHTML = lu + firstZero(day) + ru;');
   	frmCalendar.writeln('						i++;');
   	frmCalendar.writeln('						day++;');
   	frmCalendar.writeln('						n++;');
   	frmCalendar.writeln('					}');
   	frmCalendar.writeln('				}');

   	frmCalendar.writeln('				while (n < 43)');
   	frmCalendar.writeln('				{');
   	frmCalendar.writeln('					document.getElementById(td + n).innerHTML = "";');
   	frmCalendar.writeln('					n++;');
   	frmCalendar.writeln('				}');
   	frmCalendar.writeln('			}');

   	frmCalendar.writeln('			function DateToStr(aValue)');
   	frmCalendar.writeln('			{');
   	frmCalendar.writeln('				var s = "";');
   	frmCalendar.writeln('				result = firstZero(aValue.getDate().toString())  + ".";');
   	frmCalendar.writeln('				result += firstZero((aValue.getMonth() + 1).toString())  + ".";');
   	frmCalendar.writeln('				result += aValue.getFullYear();');
   	frmCalendar.writeln('				return(result);');
   	frmCalendar.writeln('			}');
    
   	frmCalendar.writeln('			function compareDate(date1, date2)');
   	frmCalendar.writeln('			{');
   	frmCalendar.writeln('				return((date1.getYear() == date2.getYear()) && (date1.getMonth() == date2.getMonth()) && (date1.getDate() == date2.getDate()));');
   	frmCalendar.writeln('			}');

   	frmCalendar.writeln('			function firstZero(aValue)');
   	frmCalendar.writeln('			{');
   	frmCalendar.writeln('				if (("" + aValue).length == 1)');
   	frmCalendar.writeln('					return("0" + aValue);');
   	frmCalendar.writeln('				return(aValue);');
   	frmCalendar.writeln('			}');

   	frmCalendar.writeln('			function SelectItem(itemID)');
   	frmCalendar.writeln('			{');
   	frmCalendar.writeln('				if (itemID.innerHTML.length > 0)');
   	frmCalendar.writeln('				{');
   	frmCalendar.writeln('					itemID.style.backgroundColor = "' + clSelect + '";');
   	frmCalendar.writeln('					itemID.style.border = "1px solid ' + clFrame + '";');
   	frmCalendar.writeln('				}');
   	frmCalendar.writeln('			}');

	frmCalendar.writeln('			function NormalItem(itemID)');
	frmCalendar.writeln('			{');
   	frmCalendar.writeln('				if (itemID.innerHTML.length > 0)');
   	frmCalendar.writeln('				{');
	frmCalendar.writeln('					itemID.style.backgroundColor = "' + clBackground + '";');
	frmCalendar.writeln('					itemID.style.border = "1px solid ' + clBackground + '";');
   	frmCalendar.writeln('				}');
	frmCalendar.writeln('			}');

   	frmCalendar.writeln('			function ChangeMonth(step)');
   	frmCalendar.writeln('			{');
   	frmCalendar.writeln('				selectDate.setMonth(selectDate.getMonth() + step);');
   	frmCalendar.writeln('				fillCalendar();');
   	frmCalendar.writeln('			}');
    
   	frmCalendar.writeln('			function SetDate(itemID)');
   	frmCalendar.writeln('			{');
   	frmCalendar.writeln('				if (itemID.innerHTML.length > 0)');
   	frmCalendar.writeln('				{');
   	frmCalendar.writeln('					if (itemID.id == "btnClose")');
   	frmCalendar.writeln('					{');
   	frmCalendar.writeln('						window.top.hideCalendar();');
   	frmCalendar.writeln('						return;');
   	frmCalendar.writeln('					}');
   	frmCalendar.writeln('					if (itemID.id == "btnToday")');
   	frmCalendar.writeln('					{');
	frmCalendar.writeln('						tempEdit.value = DateToStr(currentDate);');
   	frmCalendar.writeln('						window.top.hideCalendar();');
   	frmCalendar.writeln('						return;');
   	frmCalendar.writeln('					}');

	frmCalendar.writeln('					if (itemID.innerHTML.length > 2)');
   	frmCalendar.writeln('						newDate = itemID.firstChild.innerHTML;');
   	frmCalendar.writeln('					else');
   	frmCalendar.writeln('						newDate = itemID.innerHTML;');
   	frmCalendar.writeln('					if (newDate.substring(0, 1) == "0" && newDate.length > 1)');
   	frmCalendar.writeln('						newDate = newDate.substring(1);');
   	frmCalendar.writeln('					selectDate.setDate(newDate);');
	frmCalendar.writeln('					tempEdit.value = DateToStr(selectDate);');
	frmCalendar.writeln('					window.top.hideCalendar();');
   	frmCalendar.writeln('				}');
   	frmCalendar.writeln('			}');

   	frmCalendar.writeln('			function getDaysInMonth(aMonth, aYear)');
   	frmCalendar.writeln('			{');
   	frmCalendar.writeln('				var Days;');
   	frmCalendar.writeln('				if (aMonth == 1 || aMonth == 3 || aMonth == 5 || aMonth == 7 || aMonth == 8 || aMonth == 10 || aMonth == 12)');
   	frmCalendar.writeln('					Days = 31;');
   	frmCalendar.writeln('				else if (aMonth == 4 || aMonth == 6 || aMonth == 9 || aMonth == 11)');
   	frmCalendar.writeln('					Days = 30;');
   	frmCalendar.writeln('				else if (aMonth == 2)');
   	frmCalendar.writeln('				{');
   	frmCalendar.writeln('					if (isLeapYear(aYear))');
   	frmCalendar.writeln('						Days = 29;');
   	frmCalendar.writeln('					else');
   	frmCalendar.writeln('						Days = 28;');
   	frmCalendar.writeln('				}');
   	frmCalendar.writeln('				return (Days);');
   	frmCalendar.writeln('			}');

   	frmCalendar.writeln('			function isLeapYear(aYear)');
   	frmCalendar.writeln('			{');
   	frmCalendar.writeln('				return(((aYear % 4) == 0) && ((aYear % 100) != 0) || ((aYear % 400) == 0))');
   	frmCalendar.writeln('			}');

	frmCalendar.writeln('			//-->');
	frmCalendar.writeln('		</script>');

	frmCalendar.writeln('	</head>'); 

   	frmCalendar.writeln('	<body onLoad="fillCalendar();">');
   	frmCalendar.writeln('		<table border="0" cellspacing="0" cellpadding="2" onselectstart="return false">');

   	frmCalendar.writeln('			<tr align="center">');
   	frmCalendar.writeln('				<td class="Item" title="Предыдущий год" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onMouseUp="ChangeMonth(-12);"><img src="'+Root+'images/dleft.gif" width="9" height="15"></td>');
    frmCalendar.writeln('				<td class="Item" title="Предыдущий месяц" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onMouseUp="ChangeMonth(-1);"><img src="'+Root+'images/left.gif" width="9" height="15"></td>');
   	frmCalendar.writeln('				<td id="MonthYear" colspan="3" style="font-weight:bold">Месяц, год</td>');
   	frmCalendar.writeln('				<td class="Item" title="Следующий месяц" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onMouseUp="ChangeMonth(1);"><img src="'+Root+'images/right.gif" width="9" height="15"></td>');
   	frmCalendar.writeln('				<td class="Item" title="Следующий год" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onMouseUp="ChangeMonth(12);"><img src="'+Root+'images/dright.gif" width="9" height="15"></td>');
   	frmCalendar.writeln('			</tr>');

   	frmCalendar.writeln('			<tr align="center">');
   	frmCalendar.writeln('				<td class="Weekday">Пн</td>');
   	frmCalendar.writeln('				<td class="Weekday">Вт</td>');
   	frmCalendar.writeln('				<td class="Weekday">Ср</td>');
   	frmCalendar.writeln('				<td class="Weekday">Чт</td>');
   	frmCalendar.writeln('				<td class="Weekday">Пт</td>');
   	frmCalendar.writeln('				<td class="Weekday"><font color="#FF0000">Сб</font></td>');
   	frmCalendar.writeln('				<td class="Weekday"><font color="#FF0000">Вс</font></td>');
   	frmCalendar.writeln('			</tr>');

   	frmCalendar.writeln('			<tr align="center">');
   	frmCalendar.writeln('				<td id="i1" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i2" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i3" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i4" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i5" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i6" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);"><font color="#FF0000">x</font></td>');
   	frmCalendar.writeln('				<td id="i7" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);"><font color="#FF0000">x</font></td>');
   	frmCalendar.writeln('			</tr>');

   	frmCalendar.writeln('			<tr align="center">');
   	frmCalendar.writeln('				<td id="i8" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i9" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i10" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i11" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i12" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i13" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);"><font color="#FF0000">x</font></td>');
   	frmCalendar.writeln('				<td id="i14" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);"><font color="#FF0000">x</font></td>');
   	frmCalendar.writeln('			</tr>');

   	frmCalendar.writeln('			<tr align="center">');
   	frmCalendar.writeln('				<td id="i15" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i16" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i17" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i18" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i19" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i20" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);"><font color="#FF0000">x</font></td>');
   	frmCalendar.writeln('				<td id="i21" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);"><font color="#FF0000">x</font></td>');
   	frmCalendar.writeln('			</tr>');

   	frmCalendar.writeln('			<tr align="center">');
   	frmCalendar.writeln('				<td id="i22" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i23" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i24" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i25" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i26" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i27" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);"><font color="#FF0000">x</font></td>');
   	frmCalendar.writeln('				<td id="i28" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);"><font color="#FF0000">x</font></td>');
   	frmCalendar.writeln('			</tr>');

   	frmCalendar.writeln('			<tr align="center">');
   	frmCalendar.writeln('				<td id="i29" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i30" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i31" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i32" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i33" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i34" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);"><font color="#FF0000">x</font></td>');
   	frmCalendar.writeln('				<td id="i35" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);"><font color="#FF0000">x</font></td>');
   	frmCalendar.writeln('			</tr>');

   	frmCalendar.writeln('			<tr align="center">');
   	frmCalendar.writeln('				<td id="i36" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i37" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i38" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i39" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i40" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);">x</td>');
   	frmCalendar.writeln('				<td id="i41" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);"><font color="#FF0000">x</font></td>');
   	frmCalendar.writeln('				<td id="i42" class="Item" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);"><font color="#FF0000">x</font></td>');
   	frmCalendar.writeln('			</tr>');

   	frmCalendar.writeln('				<tr align="center">');
   	frmCalendar.writeln('					<td id="btnToday" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);" colspan="3" style="font-weight:bold">Сегодня</td>');
   	frmCalendar.writeln('					<td class="Item"></td>');
   	frmCalendar.writeln('					<td id="btnClose" onMouseOver="SelectItem(this);" onMouseOut="NormalItem(this);" onClick="SetDate(this);" colspan="3" style="font-weight:bold">Закрыть</td>');
   	frmCalendar.writeln('				</tr>');
    
   	frmCalendar.writeln('		</table>');

   	frmCalendar.writeln('	</body>');

	frmCalendar.writeln('</html>'); 

	frmCalendar.close();
    
}

