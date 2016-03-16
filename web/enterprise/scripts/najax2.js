//----------------------------------------------------------------------------------------------------
// Стили списка и индикатора

document.write('<style type="text/css">               ');
document.write('<!--                                  ');
document.write('#popupList {                          ');
document.write('        position: absolute;           ');
document.write('        visibility: hidden;           ');
document.write('        z-index:100;                  ');
document.write('}                                     ');
document.write('                                      ');
document.write('#ldInd {                              ');
document.write('        position:absolute;            ');
document.write('        z-index:100;                  ');
document.write('        font-size: 11px;              ');
document.write('        border: #4c77b6 1px solid;    ');
document.write('        display: none;                ');
document.write('        font-size: 12px;              ');
document.write('        font-family: Verdana;         ');
document.write('        background: #e4eaf2;          ');
document.write('        text-align: center;           ');
document.write('}                                     ');
document.write('-->                                   ');
document.write('</style>                              ');


//----------------------------------------------------------------------------------------------------
// Запрос данных

var hReq;
var autoInd = 1;
var clientFunc = 'createPopupLst';

function recvAnsw()
{
  if ( autoInd ) showInd( hReq.readyState-1,4 );
  if ( hReq.readyState == 4 && hReq.status == 200 )
    eval( clientFunc+'( hReq )' );
}

function sendReq( url,param )
{
  if ( window.XMLHttpRequest ) hReq = new XMLHttpRequest();
  else if ( window.ActiveXObject ) hReq = new ActiveXObject( "Microsoft.XMLHTTP" );
  if ( hReq )
  {
   hReq.onreadystatechange = recvAnsw;
   hReq.open( "POST",url,true );
   hReq.setRequestHeader( 'Content-Type','application/x-www-form-urlencoded' );
   hReq.send( param );

  }
}


//----------------------------------------------------------------------------------------------------
// Индикатор загрузки данных

document.write('<div id="ldInd"></div>');

function showInd( Cur,Cnt )
{
	
  var bgTableObj = document.getElementById('bgTable');
  var ldIndObj   = document.getElementById('ldInd');

  var ldIndWidth = 80;
  

  var leftPos = getLeftPos( bgTableObj ) + bgTableObj.offsetWidth - ldIndWidth;
  var topPos  = getTopPos( bgTableObj );

  if ( !Cur )
  {
    ldIndObj.style.left  = leftPos - 5;
    ldIndObj.style.top   = topPos + 5;
    ldIndObj.style.width = ldIndWidth;

    ldIndObj.style.display = 'block';
  }
  if ( Cur == Cnt-1 ) ldIndObj.style.display = 'none';

  var BlockSize = Math.round(100 / Cnt);
  var Perc = Cur*BlockSize;
  if ( Perc > 99 ) Perc = 99;
  ldIndObj.innerHTML = 'load... '+Perc+'%'; 
}


//----------------------------------------------------------------------------------------------------
// Выпадающий список данных

var objText;

var leftLst;
var topLst;
var widthLst;
var sizeLst;

document.write('<iframe scrolling="no" frameborder="0" id="frameList" style=" visibility:hidden; position:absolute; z-index:0; '
			 + 'border:#000000 solid 0px;"></iframe>');
document.write('<span id="outList"></span>');

function createPopupLst( hReq )
{
  cur = -1;
  document.getElementById('outList').innerHTML = hReq.responseText;
  showPopupLst( objText );
}

function initPopupLst( objText,size )
{
  objText.select();

  sizeLst = size;
  leftLst = getLeftPos( objText );
  topLst  = getTopPos( objText ) + objText.offsetHeight;
  widthLst = objText.offsetWidth;
}

var PopupLstCount = 0;

function showPopupLst( obj )
{
  var objSelectList = document.getElementById('popupList');
  var objFrameList  = document.getElementById('frameList');

  objSelectList.width = widthLst;
  objSelectList.style.left = leftLst;
  objSelectList.style.top = topLst;
  objSelectList.style.visibility = 'visible';

  objFrameList.width = widthLst;
  objFrameList.style.height = objSelectList.offsetHeight; // + 30
  objFrameList.style.left = leftLst; //+250
  objFrameList.style.top = topLst;
  objFrameList.style.visibility = 'visible';

  //---ups

  PopupLstCount = objSelectList.firstChild.childNodes.length-1;

  //document.getElementById('Did3').value =      objSelectList.firstChild.firstChild.nodeName;
  //objSelectList.childNodes.length;
}

//----------------------------------------------------------------------------------------------------
// Абсолютные координаты элемента

function getLeftPos( obj )
{
  return obj ? obj.offsetLeft + getLeftPos( obj.offsetParent ) : 0;
}

function getTopPos( obj )
{
  return obj ? obj.offsetTop + getTopPos( obj.offsetParent ) : 0;
}

//----------------------------------------------------------------------------------------------------
// Поиск элемента по его имени

function getNodeByName( obj,name )
{
  if ( obj == null ) return null;
  if ( obj.name == name ) return obj;

  var res = getNodeByName( obj.firstChild,name );  if ( res != null ) return res;
  var res = getNodeByName( obj.nextSibling,name ); if ( res != null ) return res;

  return null;
}


var cur = -1;


function checkDown()
{
  //document.getElementById('Did2').value = event.keyCode;
  switch(event.keyCode)
  {
    case 13:
	  if(cur >= 0)
	  {
	    document.getElementById('tr' + cur).click();
		hideMenu();
		this.blur();
	  }
	break;
	case 38:
	  cur--;   //top
	  if(cur < 0)
          {
	    cur++;
	    break;
	  }
	  document.getElementById('tr' + cur).style.background = '#e4eaf2';
	  document.getElementById('tr' + (cur+1)).style.background = '#ffffff';
        break;
	case 40:   //down
	  cur++;
	  if(cur == PopupLstCount)
	  {
	    cur--;
	    break;
      } 
	  document.getElementById('tr' + cur).style.background = '#e4eaf2';
	  if(cur > 0)
	    document.getElementById('tr' + (cur-1)).style.background = '#ffffff';
	break;
  }
}

function chResult( Name,Id,Num )
{
  document.getElementById(m_spisok[Num][0]).value = Name;
  if(document.getElementById(m_spisok[Num][1]))document.getElementById(m_spisok[Num][1]).value = Id;
  if(typeof(ExternalSetID)!='undefined')ExternalSetID(m_spisok[Num][0]);
}

function initMenu()
{
  initPopupLst( this,m_spisok[this.alt][3] );
  sendRequest(this);
}

function hideMenu()
{
  document.getElementById('popupList').style.visibility = 'hidden';
  document.getElementById('frameList').style.visibility = 'hidden';
  cur = -1;
}


function checkMenu()
{
  myRe= new RegExp (".*(?:^|,)(" + event.keyCode + ")(?:$|,).*"); 
  if(!myRe.test("13,9,38,40,16,17,18,33,34,35,36,45,37,39"))
  {
    if(document.getElementById(m_spisok[this.alt][1]))document.getElementById(m_spisok[this.alt][1]).value = '';
    if (m_spisok[this.alt][4]) CleanDop(m_spisok[this.alt][0]);
    sendRequest(this);
  }
}

function Load()
{
  for(var i=0;i<m_spisok.length;i++)
  {
    document.getElementById(m_spisok[i][0]).onkeyup = checkMenu; 
    document.getElementById(m_spisok[i][0]).onfocus = initMenu;
    document.getElementById(m_spisok[i][0]).onblur = hideMenu;
    document.getElementById(m_spisok[i][0]).onkeydown = checkDown;
    document.getElementById(m_spisok[i][0]).alt = i;
  }
}