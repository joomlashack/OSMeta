function newAttribute()
{
	var d = document;
	
	// get field labels
	var lbl_attribute_new = d.adminForm.js_lbl_attribute_new.value;
	var lbl_attribute_del = d.adminForm.js_lbl_attribute_delete.value;
	var lbl_property_new  = d.adminForm.js_lbl_property_new.value;
	var lbl_price         = d.adminForm.js_lbl_price.value;
	var lbl_property      = d.adminForm.js_lbl_property.value;
	var lbl_title         = d.adminForm.js_lbl_title.value;
	
	
	var container = document.getElementById('attribute_container');
	var next_inc  = container.getElementsByTagName('table').length + 1;
	var toolbar   = "<a href='javascript:newAttribute();'>"+lbl_attribute_new+"</a> | <a href='javascript:deleteAttribute("+next_inc+")'>"+lbl_attribute_del+"</a> | <a href='javascript:newProperty("+next_inc+")'>"+lbl_property_new+"</a>";
	
	var table = d.createElement('table');
	    table.id  = 'attributeX_table_'+next_inc;
	    table.className = 'adminform';
	
	var tbody = d.createElement("tbody");
	var tr    = d.createElement('tr');
	var tr2   = d.createElement('tr');
	    tr2.id    = "attributeX_tr_"+next_inc+"_0";
	

	var td_01 = d.createElement('td');
	    td_01.style.width = '5%';
	    td_01.innerHTML = lbl_title;
	
	var td_02 = d.createElement('td');
	    td_02.colSpan = '2';
	    td_02.align = 'left';
	    td_02.innerHTML = '<input type="text" name="attributeX['+next_inc+'][name]" value="" size="60"/>';
	
	var td_03 = d.createElement('td');
	    td_03.colSpan = '3';
	    td_03.align = 'left';
	    td_03.innerHTML = toolbar;
	
	var td_04 = d.createElement('td');
	    td_04.style.width = '5%';
	    td_04.innerHTML = '&nbsp;';
	
	var td_05 = d.createElement('td');
	    td_05.style.width = '10%';
	    td_05.align = 'left';
	    td_05.innerHTML = lbl_property;
	
	var td_06 = d.createElement('td');
	    td_06.style.width = '20%';
	    td_06.align = 'left';
	    td_06.innerHTML = "<input type='text' name='attributeX["+next_inc+"][value][]' value='' size='40'/>";
	
	var td_07 = d.createElement('td');
	    td_07.style.width = '5%';
	    td_07.align = 'left';
	    td_07.innerHTML = lbl_price;
	
	var td_08 = d.createElement('td');
	    td_08.style.width = '30%';
	    td_08.align = 'left';
	    td_08.innerHTML = "<input type='text' name='attributeX["+next_inc+"][price][]' size='10' value=''/><a href='javascript:deleteProperty("+next_inc+",\""+next_inc+"_0\");'>X</a>";
	
	
	table.appendChild(tbody);
	   tbody.appendChild(tr);
	      tr.appendChild(td_01);
	      tr.appendChild(td_02);
	      tr.appendChild(td_03);
	   tbody.appendChild(tr2);  
	      tr2.appendChild(td_04); 
	      tr2.appendChild(td_05);
	      tr2.appendChild(td_06);
	      tr2.appendChild(td_07);
	      tr2.appendChild(td_08);
	
	container.appendChild(table);
}


function deleteAttribute(attribute_id)
{
	var container = document.getElementById('attribute_container');
	
	var table = document.getElementById("attributeX_table_"+attribute_id);
	
	container.removeChild(table);
}


function newProperty(attribute_id)
{
	var d = document;
	
	// get field labels
    var lbl_property      = d.adminForm.js_lbl_property.value;
    var lbl_price         = d.adminForm.js_lbl_price.value;

    
	var table = document.getElementById("attributeX_table_"+attribute_id);
	var tbody = table.getElementsByTagName('tbody')[0];
	var tr_id = table.getElementsByTagName('tr').length + 1;
	
	// create new HTML elements
	var tr = d.createElement('tr');
	    tr.id = "attributeX_tr_"+attribute_id+"_"+tr_id;
	
	var td_01 = d.createElement('td');
	    td_01.style.width = '5%';
	    td_01.innerHTML = '&nbsp;'+tr_id;
	
	var td_02 = d.createElement('td');
	    td_02.style.width = '10%';
	    td_02.align = 'left';
	    td_02.innerHTML = lbl_property;
	
	var td_03 = d.createElement('td');
	    td_03.style.width = '20%';
	    td_03.align = 'left';
	    td_03.innerHTML = "<input type='text' name='attributeX["+attribute_id+"][value][]' value='' size='40'/>";
	
	var td_04 = d.createElement('td');
	    td_04.style.width = '5%';
	    td_04.align = 'left';
	    td_04.innerHTML = lbl_price;
	
	var td_05 = d.createElement('td');
	    td_05.style.width = '30%';
	    td_05.align = 'left';
	    td_05.innerHTML = "<select name='attributeX["+attribute_id+"][price][]' ><option value='0' >in the same window</option><option value='1' >new window</option></select><a href='javascript:deleteProperty("+attribute_id+",\""+attribute_id+"_"+tr_id+"\");'>&nbsp;X</a>";
	
	// append new elements    
	tbody.appendChild(tr);
	   tr.appendChild(td_01);
	   tr.appendChild(td_02);
	   tr.appendChild(td_03);
	   tr.appendChild(td_04);
	   tr.appendChild(td_05);
}


function deleteProperty(attribute_id, property_id)
{
	var d     = document;
	var table = document.getElementById("attributeX_table_"+attribute_id);
	var tbody = table.getElementsByTagName('tbody')[0];
	var tr    = d.getElementById("attributeX_tr_"+property_id);
	
	tbody.removeChild(tr);
}