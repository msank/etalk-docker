var currentSnd = -1;
var player = document.getElementById('player');
var kFadeDuration = 200;
var kDisableRightClick = (window.location.href.indexOf('etalk.cc') === -1);
var kSpaceKeyCode = 32;

function play() {
	$('#bPlay').hide();
	document.getElementById("bPause").style.display="inline";
	player.play();
	$('body.viewer').removeClass('paused');
	$('#wait').fadeOut(function(){});
	return false;
}
function pause(showMenu) {
	document.getElementById("bPlay").style.display="inline";
	$('#bPause').hide();
	player.pause();
	if (showMenu) {
		$('#wait').fadeIn(function(){$('body.viewer').addClass('paused');});
	}
	return false;
}
function startedPlay() {
	$('html, body').animate({scrollTop: $('#a'+currentSnd).offset().top-50}, 1000);
}
function endedPlay() {
	next();
}
function setCurrentSnd(c) {
	currentSnd = c;
	location.hash = c;
	player.src = '/data/'+audioFiles[currentSnd]['snd'];
	$('#viz a').removeClass('current');
	$('#a'+currentSnd).addClass('current');
	if ('/tmp/'+audioFiles[currentSnd]['pict']!=$('#diaPict').attr('src')) {
		$('#dia>figure').fadeOut(kFadeDuration, function(){
			if (audioFiles[currentSnd]['pict']!=='') {
				$('#diaPict').attr('src', '/tmp/'+audioFiles[currentSnd]['pict']);
				var credits = '';
				if (audioFiles[currentSnd]['pict_cred'].length > 0) {
					credits+= '© '+audioFiles[currentSnd]['pict_cred'];
				}
				else {
					credits+= audioFiles[currentSnd]['pict_cred'];
				}
				if (audioFiles[currentSnd]['pict_link']!='') {
					credits+= '<br/><a href="'+audioFiles[currentSnd]['pict_link']+'" target="_blank">'+audioFiles[currentSnd]['pict_link']+'</a>';
				}
				$('#dia>figure>figcaption').html(credits);
				$('#dia>figure').fadeIn();
			}
		});
	}
	$('#links').fadeOut(kFadeDuration, function(){
		if (audioFiles[currentSnd]['link']!=='') {
			$('#links').html(audioFiles[currentSnd]['link']);
			$('#links').fadeIn();
		}
	});
	$('#links a.entity').bind('click touchstart', function(e){ e.preventDefault(); e.stopPropagation(); openLink($(this).attr('href')); });
}
function playTrack(index) {
	player.pause();
	setCurrentSnd(parseInt(index, 10));
	play();
	return false;
}
function next() {
	if (currentSnd < audioFiles.length-1) {
		setCurrentSnd(parseInt(currentSnd,10)+1);
		play();
	}
	else {
		alert('End of the track.');
	}
	return false;
}
function prev() {
	if (currentSnd > 0) {
		setCurrentSnd(parseInt(currentSnd,10)-1);
		play();
	}
	else {
		alert('Start of the track.');
	}
	return false;
}
function openLink(url) {
	pause(false);
	$('#overlay iframe').attr('src', url);
	$('#overlay').fadeIn();
	return false;
}
function hideOverlay() {
	$('#overlay').fadeOut();
	$('#overlay iframe').attr('src', '');
	return false;
}
function home() {
	window.location.href = "/";
}
function toggleMute() {
	player.muted = !player.muted;
	$('#bMute').attr('src', '/i/audio_'+(player.muted?'on':'mute')+'.png');
}
function toggleMode() {
	if ($('#viz').offset().left<0) {
		$('#bMode').attr('src', '/i/mode_full.png');
		$('#viz').animate({'margin-left':0});
		$('#dia').animate({'left':($('#viz').width()+60)});
	}
	else {
		$('#bMode').attr('src', '/i/mode_list.png');
		$('#viz').animate({'margin-left':-($('#viz').width()+60)});
		$('#dia').animate({'left':0});
	}
	return false;
}
function updateEmbedCode() {
	var serviceURL = 'http://'+window.location.host+'/api/embed.js?ref='+encodeURIComponent(window.location.search.replace('?dir=', ''))+'/'+window.location.hash.replace('#','')+'&w='+$('#embed_w').val()+'&h='+$('#embed_h').val()+'&l='+$('#embed_link').is(':checked')+'&d='+$('#embed_desc').is(':checked');
	var code = '<script type="text/javascript">';
		code+= 'document.write(unescape("%3Cscript src=\\\''+serviceURL+'\\\' type=\\\'text/javascript\\\'%3E%3C/script%3E"));';
		code+= '</script>';
	$('#embed_code').text(code);
	$('#fShareURL').val(window.location);
}
function showEmbed() {
	updateEmbedCode();
	$('#embed, #embed .close').click(function(){hideEmbed();});
	$('#embed').fadeIn();
}
function hideEmbed() {
	$('#embed').fadeOut();
}

if (window.location.hash) {
	playTrack(window.location.hash.replace('#',''));
}
else {
	setCurrentSnd(0);
}

$(document).ready(function(){
	$("#diaPict").load(function(){ $("#dia>figure").fadeIn(); });
	$('#bBack').bind('click touchstart', function(e){ e.preventDefault(); home(); });
	$('#bMode').bind('click touchstart', function(e){ e.preventDefault(); toggleMode(); });
	$('#bPlay').bind('click touchstart', function(e){ e.preventDefault(); play(); });
	$('#bPause').bind('click touchstart', function(e){ e.preventDefault(); pause(false); });
	$('#bStop').bind('click touchstart', function(e){ e.preventDefault(); pause(true); });
	$('#bNext').bind('click touchstart', function(e){ e.preventDefault(); next(); });
	$('#bPrev').bind('click touchstart', function(e){ e.preventDefault(); prev(); });
	$('#bMute').bind('click touchstart', function(e){ e.preventDefault(); toggleMute(); });
	$('#bShare').bind('click touchstart', function(e){ e.preventDefault(); showEmbed(); });
	$('#embed>div').bind('click touchstart', function(e){e.stopPropagation();});
	$('#embed_code,#fShareURL').bind('mouseup', function(){this.select();});
	$('#embed input').bind('input change', function(/*e*/){updateEmbedCode();});
	$('#wait a.vidPlay').bind('click touchstart', function(e){ e.preventDefault(); play();});
	$('#wait>nav>a:not(.doc)').bind('click touchstart', function(e){ e.preventDefault(); playTrack($(this).attr('href').replace('#',''));});
	$('#overlay .close').bind('click touchstart', function(e){ e.preventDefault(); hideOverlay(); });
	$('#viz>*').bind('dragstart', function(e){ e.preventDefault(); });		// Prevent HTML elements dragging
	$('#viz>a').bind('click touchstart', function(e){ e.preventDefault(); playTrack($(this).attr('href').replace('#', '')); });
	if (kDisableRightClick) {
		$(document.body).bind('contextmenu', function(e) {
			e.preventDefault();
		});
	}
	$(window).keydown(function(e) {
		console.log(e.keyCode);
		if (kSpaceKeyCode == e.keyCode) { // ESC
			pause(true);
			return false;
		}
		return true;
	});
});
