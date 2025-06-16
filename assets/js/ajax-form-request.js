const BUTTON_STATE_NORMAL = "normal";
const BUTTON_STATE_IN_PROGRESS = "in_progress";

/**
 * form_parent_id: Form parent element id
 * event: event argument
 *
 * Hanlde ajax form request
 */
function handleAjaxFormRequest(form_id, form_event, successCallBack,errorCallBack)
{
	 form_event.stopPropagation(); // Stop reloading page
	 form_event.preventDefault(); // Totally stop reloading page
	 var form = $("#"+form_id);
     var formData = new FormData();
	 var formValues = form.serializeArray();
 	var fileInputs = form.find('input[type=file]');
	 //appending input form elements
	 $.each(formValues,function(key,input_field){
		 //console.log(input_field)
		 formData.append(input_field.name,input_field.value);
	 });
	  //appending files to form if any
	 $.each(fileInputs,function(key,file_element){
		 	formData.append(file_element.name, file_element.files[0]);
	 });
	 //START loader here
	 updateSubmitButtonState(form,BUTTON_STATE_IN_PROGRESS);
	 //submitting the ajax request
    $.ajax({
        url:  form.attr("action"),
        type: form.attr("method"),
        data: formData,
        cache: false,
        dataType: 'json',
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(data, textStatus, jqXHR)
        {
				updateSubmitButtonState(form,BUTTON_STATE_NORMAL);
                removeFormErrorMessages();
                if(successCallBack)
                    successCallBack(data);
                else
                    ajaxSuccessHandler(form_id, data);
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
						updateSubmitButtonState(form,BUTTON_STATE_NORMAL);
            if(errorCallBack)
                errorCallBack(jqXHR.responseJSON.responseContent);
            else
								ajaxErrorHanlder(form_id, jqXHR.responseJSON);

        }
    });
}
/**
 * Ajax request default success callback
 */
function ajaxSuccessHandler(form_id,responseData)
{
	// Toast Notification
  window.location.reload();
}

/**
 * Ajax request default error handler
 */
function ajaxErrorHanlder(form_id, responseJson)
{
		var form = $("#"+form_id);
		removeFormErrorMessages();
    // message at top of the form
    var error_title = '<div class="status-message error-status-message">';
		error_title += responseJson.responseMessage;
		error_title += '</div >';
		form.prepend(error_title);
		// $(".error-status-message").fadeOut(5000);

    $.each(responseJson.responseContent, function(message_key,message_values){
        var error_block ='';
				if(message_key == 'message_title')
				 return;
				$.each(message_values,function(message_index,message_value){
					error_block += '<span class="error-explanation">'+message_value+'</span>';
				});
				form.find('.'+message_key).parent().append(error_block);
	
        form.find('.'+message_key).parent().addClass('error-field');
    });

}

/**
 * Cleaning old error messages from form
 */
function removeFormErrorMessages()
{
	  $('.error-field').removeClass('error-field');
		//remove field errors
		$('.error-explanation').remove();
		//remove error message title
		$('.error-status-message').remove();		
}

/**
 * When form submit change the state of button
 *  disabled and in progress
 *
 * */
function updateSubmitButtonState( form, button_state)
{

	if(button_state == BUTTON_STATE_IN_PROGRESS){
		form.find('input[type=submit]').val(form.find('input[type=submit]').val()+" ...");
		form.find('input[type=submit]').attr("disabled","disabled");
	}else{
		form.find('input[type=submit]').val(form.find('input[type=submit]').val().replace(' ...',''));
		form.find('input[type=submit]').removeAttr("disabled");

	}
}
