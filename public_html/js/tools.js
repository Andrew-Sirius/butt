/**
 * Created by andrew on 24.11.15 19:01.
 */
function showMessage(m,s){$('#message-box').append('<div class="message '+s+'">'+m+'</div>');setTimeout(function(){$('#message-box .message:first-child').fadeOut('777',function(){$(this).remove()})},4000)}
function pushHistoryState(a){window.history.pushState({},'',a)}
function getMyPoints(){var a=$("nav #myPoints");$.ajax({type:"post",dataType:"json",url:a.data("get-my-points-link"),success:function(b){a.text(b.sum)},error:function(b){console.log(b)}})};

String.prototype.ucfirst=function(){var a=this;if(a.length){a=a.charAt(0).toUpperCase()+a.slice(1).toLowerCase()}return a};