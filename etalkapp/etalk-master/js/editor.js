/* Save & Undo methods */

var kKeepAliveInterval = 2*60*1000;
var currentButtonForImageEdit = null;
var currentFormForImageEdit = null;
var currentFormInput = null;

function notifyChange(input) {
	input.closest('form').addClass('dirty');
	$('#btnSave').addClass('dirty');
}

function showImageForm(inputId, boundToLink) {
	currentFormInput = inputId;
	currentButtonForImageEdit = $(boundToLink)
	currentFormForImageEdit = currentButtonForImageEdit.closest('form');
	currentFileName = currentFormForImageEdit.find('input[name=file]').val();
	if (currentFileName==='') {
		resetFile(inputId.substring(9));
	}
	else {
		showFile(inputId.substring(9), currentFileName, false);
	}
	fileFormCopy(currentFormForImageEdit, $('#fileEdit'));
	$('#fileEdit').fadeIn();
	return false;
}

function fileFormCopy(source, target) {
	target.find('input[name=file_credits]').val(source.find('input[name=file_credits]').val());
	target.find('input[name=file_link]').val(source.find('input[name=file_link]').val());
}

function closeImageForm(save) {
	if (save) {
		var newFileName = $('#'+currentFormInput).val();
		currentFormForImageEdit.find('input[name=file]').val(newFileName);
		fileFormCopy($('#fileEdit'), currentFormForImageEdit);
		notifyChange(currentFormForImageEdit);
		if (newFileName!=='') {
			currentButtonForImageEdit.addClass('filled');
		}
		else {
			currentButtonForImageEdit.removeClass('filled');
		}
	}
	$('#fileEdit').fadeOut();
	return false;
}

function updatePreviewContents() {
	$('#previewContents>div').html('<img src="/i/loading.gif" class="loading" />');
	$.getJSON('index.php', {'f':'getStructure', 'dir':$('header form select[name=dir]').val()}, function(ret){
		var toc = '';
		for (var i=0; i<ret.data.length; i++) {
			toc+= '<a href="#c'+i+'"><h2>'+ret.data[i].title+'</h2>';
			var cj = (ret.data[i].p.length);
			for (var j=0; j<cj; j++) {
				toc+= '<p class="'+ret.data[i].p[j].type+'">'+ret.data[i].p[j].text+'</p>';
			}
			toc+= '</a>';
		}
		$('#previewContents>div').html(toc);
		$('#previewContents a').click(function(/*e*/){ $('#previewContents').fadeOut(); });
	});
}

function structureChanged() {
	$('.pSepTarget.section').each(function(idx){
		$(this).attr('id', 'c'+idx);
	});
	updatePreviewContents();
}

function save(form, callback) {
	var postStr = form.serialize();
	$.post("?save=", postStr, function(data){
		if (callback!==undefined) {
			callback.apply(this, arguments);
		}
	});
	return false;
}

function saveAll() {
	$('form.dirty').each(function(/*idx*/){
		save($(this), function(){
			structureChanged();
		});
		$(this).removeClass('dirty');
	});
	$('#btnSave').removeClass('dirty');
}

function keepAlive() {
	$.get('index.php', {'f':'keepAlive'});
}

/* Drag&drop support methods **********************************************************************************************************************************/

var lockDragTargetTimer = null;
var kDragLockDelay = 400;
var kDragLockAnimDuration = 200;
var kRemoveAnimationDuration = 300;

function setDragTarget(value, drag) {
	clearTimeout(lockDragTargetTimer);
	drag.stop(false,true);
	if (value) {
		drag.addClass('targetLocked', kDragLockAnimDuration);
	}
	else {
		drag.removeClass('targetLocked', kDragLockAnimDuration);
	}
}
function initDroppables() {
	$('.pSepTarget.ui-droppable').droppable('destroy');
	$('.pSepTarget.continue').droppable({
		accept:$('.separator'),
		drop: function(e,ui){
			var sepType = ui.helper.data('type');		// Determine if we just dropped a `section` or a `paragraph` template
			ui.helper.remove();
			$(this).addClass(sepType, kDragLockAnimDuration);
			$(this).find('input[name=chaptering]').val(sepType);
			$(this).droppable('disable');
			structureChanged();
			notifyChange($(this));
		},
		over: function(e,ui) {
			lockDragTargetTimer = setTimeout(function(){setDragTarget(true, ui.helper);}, kDragLockDelay);
		},
		out: function(e,ui) {
			setDragTarget(false, ui.helper);
		}
	});
}
function removeSeparator(button) {
	var sep = button.parent('.pSepTarget');
	sep.find('input[name=section_title]').val('');
	sep.find('input[name=chaptering]').val('continue');
	sep.removeClass('section paragraph', kRemoveAnimationDuration).addClass('continue', kRemoveAnimationDuration);
	notifyChange(sep);
	initDroppables();
}

/**************************************************************************************************************************************************************/

$(document).ready(function(){
	$('#tools .separator').draggable({
		axis:'y',
		helper:'clone',
		revert:'invalid',
		scroll:true,
		snap:'.pSepTarget.continue',
		snapMode:'inner',
		snapTolerance:20
	});
	initDroppables();
	$('.pSepTarget>img').click(function(/*e*/){
		removeSeparator($(this));
	});
	$('input,textarea').bind('input', function(){
		notifyChange($(this));
	});
	$('#btnSave').click(function(){saveAll();});
	$('#btnPreviewContents').click(function(){ $('#previewContents').fadeToggle('fast'); });
	structureChanged();
	setInterval(function(){keepAlive();}, kKeepAliveInterval);
});