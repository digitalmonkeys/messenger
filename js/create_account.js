$(document).ready(createAccountInit);
function createAccountInit()
{
  var contentWindow = $('.create-account-window');
  var contentWindowTop = (window.innerHeight - parseInt(contentWindow.height())) / 2;
  contentWindow.css('margin-top', (contentWindowTop + 40) + 'px');
  contentWindow.css('opacity', 0);
  TweenMax.to(contentWindow, 0.4, {opacity: 1, marginTop: contentWindowTop});
  
  $('.btnShowLogin').click(onShowLoginClick);
  function onShowLoginClick()
  {
    $.ajax({
      method: "POST",
      url: 'web_service.php',
      data: {action: 'get_app'},
      success: onSuccess
    });

    function onSuccess(result) 
    {
      $('.container-fluid').html(result);
      initViews();
    }   
  }
  
  function validateEmail(email) {
    var regexp = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regexp.test(email);
  }
  
  $('.btnCreateAccount').click(onCreateAccountClick);
  function onCreateAccountClick()
  {
    if($('.inputUsername').val().length < 4)
    {
      alert('Username must be at least 4 characters!');
      return; 
    }
    else if($('.inputPassword').val().length < 6)
    {
      alert('Password must be at least 6 characters!');
      return; 
    }
    else if($('.inputPasswordAgain').val() != $('.inputPassword').val()) 
    {
      alert('Passwords don\'t match!');
      return;
    }
    else if(!validateEmail($('.inputEmail').val())) 
    {
      alert('Email is not valid!');
      return;
    }
      
    
    $.ajax({
      method: "POST",
      url: 'web_service.php',
      data: {
        action: 'create_account', 
        email: $('.inputEmail').val(), 
        username: $('.inputUsername').val(), 
        password: $('.inputPassword').val()
      },
      success: onSuccess
    });

    function onSuccess(result) 
    {
      result = JSON.parse(result);
      var strToPrint = '';
      if(result.status == 'ok')
      {
        strToPrint = '<h4 style="color: #1abc9c;">Your account has been created. You will now be redirected to the login page.</h4>';
      }
      else
      {
        strToPrint = '<h4 style="color: #c0392b;">Your account has NOT been created. You will be redirected to the login page.</h4>';
      }
      $('.container-fluid').html(strToPrint);
      setTimeout(function(){
        location.reload();
        initViews();
      }, 3500);
      
    }   
  }
}