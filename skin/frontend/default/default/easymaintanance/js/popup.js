function openFeedbackWindow(ele,upd_ele,id)
{
    Effect.Appear(ele);
    var back1 = document.getElementById ('backgroundpopup');
    back1.style.display = "block";
}
function closeFeedbackWindow(ele1){
    var val1=document.getElementById(ele1);    
    var background=document.getElementById('backgroundpopup');
    Effect.Fade(val1);
    Effect.Fade(background);
    $$('div.error-massage').each(function(ele){
            ele.hide();
    });
}
function sendFeedback(url){
    if(feedback_form && feedback_form.validate()){
        $('loader').show();
        $('btnsubmit').setAttribute('disabled', true);
        var parameters=$('frm_feedback').serialize(true);
        new Ajax.Request(url, {
                method: 'post',
                dataType: 'json',
                parameters: parameters,
                onSuccess: function(transport) {
                    if(transport.status == 200) {
                        var response=transport.responseText.evalJSON();
                        $('success_message').innerHTML=response.message;
                        if(response.result=='success'){
                            $('success_message').removeClassName('feedback-error-msg');
                            $('success_message').addClassName('feedback-success-msg');
                        }
                        else{
                            $('success_message').removeClassName('feedback-success-msg');
                            $('success_message').addClassName('feedback-error-msg');
                        }
                        $('loader').hide();
                        $('success_message').show();
                        Effect.toggle('success_message', 'appear',{ duration: 5.0});
                        setTimeout(function (){
                                closeFeedbackWindow('feedback_information');
                                $('frm_feedback').reset();
                                $('btnsubmit').removeAttribute('disabled');
                            },6000);
                        return false;
                    }
                }
        });
        return false;
    }
}

function easysiteNotify(notifyurl){
    if(notify_form && notify_form.validate()){
        $('loader_notify').show();
        $('notifybtnsubmit').setAttribute('disabled', true);
        var parameters=$('frm_notify').serialize(true);
        new Ajax.Request(notifyurl, {
                method: 'post',
                dataType: 'json',
                parameters: parameters,
                onSuccess: function(transport) {
                    if(transport.status == 200) {
                        var response=transport.responseText.evalJSON();
			
                        $('success_message_notify').innerHTML=response.message;
                        if(response.result=='success'){
                            $('success_message_notify').removeClassName('error-msg');
                            $('success_message_notify').addClassName('success-msg');
                            //alert(response.message);
                        }
                        else{
                            $('success_message_notify').removeClassName('success-msg');
                            $('success_message_notify').addClassName('error-msg');
                            //alert(response.message);
                        }
                        $('loader_notify').hide();
                        $('success_message_notify').show();
                        Effect.toggle('success_message_notify', 'appear',{ duration: 5.0});
                        setTimeout(function (){
                                //closeFeedbackWindow('feedback_information');
                                $('frm_notify').reset();
                                $('btnsubmit').removeAttribute('disabled');
                            },6000);
                        return false;
                    }
                }
        });
        return false;
    }
}
