/************************************
     Edit in Place for Mootools
      Programmed by Renzoster
     http://www.etececlub.com	  
*************************************
  Please do not remove this credits
*************************************/

var editin_url;
var editin_empty = '';
var editin_saving = 'Saving...';
var editin_save = 'Save';
var editin_or = ' OR ';
var editin_cancel = 'Cancel';

function editin_init(){
	var tags = ['p', 'div', 'span'];
	tags.forEach(function(eli){
		$$(eli).each(function(el){
			if (el.getProperty('rel') == 'editin'){
				if (el.innerHTML == '') el.setHTML(editin_empty);
				editin_makeEditable(el.getProperty('id'));
			}
		});
	});
}

			
function editin_makeEditable(id){
	$(id).addEvent('click', function(e){editin_form(id);});
	$(id).addEvent('mouseover', function(e){editin_showAsEditable(id);});
	$(id).addEvent('mouseout', function(e){editin_showAsEditable(id, true);});
}

function editin_select(obj){
	$(obj).focus(); $(obj).select();
} 

function editin_form(obj){
	var editin_value = $(obj).getText();
	
	$(obj).setStyles({'visibility':'hidden','display':'none'});
	$(obj).setOpacity(0);
	
	if(editin_value == editin_empty){var obj_value = '';} else {var obj_value = editin_value;}
	
	var e_div = new Element('div',{id: obj+'_editor','class' : 'editin_div'});
	var e_textarea = new Element('textarea',{'name' : obj,'id' : obj + '_edit','class' : 'editin_textarea','value' : obj_value});
	var e_save = new Element('input',{'type' : 'button', 'id' : obj + '_save','class' : 'editin_save', 'value' : editin_save});
	var e_cancel = new Element('input',{'events': {'click': function(){editin_cleanUp(obj)}},'type' : 'button', 'id' : obj + '_cancel','class' : 'editin_cancel', 'value' : editin_cancel});
	var e_subdiv = new Element('div');
	
	var e_buttons = e_save + editin_or + e_cancel;

	e_div.injectAfter(obj);
	e_textarea.injectInside(e_div);
	e_subdiv.injectAfter(e_textarea);
	e_save.injectInside(e_subdiv);
	e_save.injectInside(e_subdiv);
	sd_content = e_subdiv.innerHTML;
	e_subdiv.innerHTML = sd_content + editin_or;
	e_cancel.injectInside(e_subdiv);
	
	editin_select(obj+'_edit');
	$(obj+'_save').addEvent('click', function(){editin_saveChanges(obj)});
	
}

function editin_showAsEditable(obj, clear){
	if (!clear){
		$(obj).addClass('editin');
	}else{
		$(obj).removeClass('editin');
	}
}

function editin_saveChanges(obj){
	
	var new_content	= escape($(obj+'_edit').getValue());
	$(obj).innerHTML	= editin_saving;

	editin_cleanUp(obj, true);

	var pars = 'id='+obj+'&content='+new_content;
	var myAjax = new Ajax(editin_url, {method: 'post', data:pars, update: $(obj), onComplete: function(){if ($(obj).innerHTML == '') $(obj).setHTML(editin_empty);}});
	myAjax.request();

}

function editin_cleanUp(obj, keepEditable){
	$(obj+'_editor').remove();
	$(obj).setStyles({'visibility':'','display':''});
	$(obj).setOpacity(1);
	if (!keepEditable) editin_showAsEditable(obj, true);
}

window.addEvent('domready', function() {editin_init();});