function formatNumber(num,dec,thou,pnt,curr1,curr2,n1,n2) 
{
  num = num.replace(" ","");
  var x = Math.round(num * Math.pow(10,dec));
  if (x >= 0) n1=n2='';
 
  var y = (''+Math.abs(x)).split('');
  var z = y.length - dec;
 
  if (z<0) z--;
 
  for(var i = z; i < 0; i++) 
    y.unshift('0');
 
  y.splice(z, 0, pnt);
  if(y[0] == pnt) y.unshift('0');
 
  while (z > 3) 
  {
    z-=3;
    y.splice(z,0,thou);
  } 
 
  var r = curr1+n1+y.join('')+n2+curr2;
  return r;
}

function valueFormat(obj,dec) {
	var v = obj.val();
	if(typeof v == 'undefined') { v = ''};
	v = v.replace(",",".").replace(" ","");
	obj.val( formatNumber(v,dec," ",".","","","","") );
	if(dec < 1){
		obj.val( (obj.val()).split(".")[0] );
	}
}

function getFieldFloatValue(obj){
	var formattedValue = obj.val();
	if(typeof formattedValue == 'undefined'){
		formattedValue = 0;
	}else{
		formattedValue =  formattedValue.replace(",", ".").replace(" ", "");
	}
	return parseFloat(formattedValue);
}
