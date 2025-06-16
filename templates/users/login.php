<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<form action="<?= route('app_authenticate')  ?>"  method="post" id="login_form"  onsubmit="handleSignIn('login_form',event)">

 <small>* indicates field is compulsory</small>
  <p >
    <label class="form_label"  for="email">User Id *</label><br>
      <input type="text" class="user_id" name="user_id" id="user_id">

  </p>
  <p>
    <label class="form_label"  for="password">Password *</label><br>
    <input type="password" name="password" class="password">
  </p>
  <p class="button"><input type="submit" name="commit" value="Log in" data-disable-with="Log in"></p>
</form>
<script src="<?= assetPath('js/vendor/jquery.min.js') ?>" type="text/javascript"></script>
<script src="<?= assetPath('js/ajax-form-request.js') ?>" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $("#user_id").focus();
    });
  function handleSignIn(form_id,event)
  {
      handleAjaxFormRequest(form_id, event, successSignInCallBack);
  }
  function successSignInCallBack(responseData)
  {
    console.log(responseData);
    if(responseData.responseContent.authenticated)
      window.location  = '<?= route('app_dashboard')  ?>';
  }

</script>
</body>
</html>