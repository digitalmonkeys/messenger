$(document).ready(loginInit);
function loginInit()
{
  var contentWindow = $('.login-window');
  var contentWindowTop = contentWindow.css('margin-top');//window.innerHeight / 2 - contentWindow.height() / 2;
  if(contentWindow.css('opacity') == 0)
  {
      //contentWindow.css('margin-top', (contentWindowTop + 40) + 'px');
      //contentWindow.css('opacity', 0);
      TweenMax.to(contentWindow, 0, {opacity: 0, scaleX: 0.7, scaleY: 0.7});
      TweenMax.to(contentWindow, 0.6, {opacity: 1, scaleX: 1, scaleY: 1, delay: 0.0001});
  }
  $('.btnLogin').unbind().click(onBtnLoginClick);
  function onBtnLoginClick()
  {
      loginAccount();
  }
  
  $('.login-window .inputUsername, .login-window .inputPassword').unbind().keyup(onLoginEnter);
  function onLoginEnter(event)
  {
    if(event.which == '13')
    {
      loginAccount();
    }
  }
  
  
  
   function loginAccount()
   { 
      $.ajax({
        method: "POST",
        url: 'web_service.php',
        data: {action: 'login', username: $('.inputUsername').val(), password: $('.inputPassword').val()},
        success: onSuccess
      });

      function onSuccess(result) 
      {
        $('.container-fluid').html(result);
        initViews();

      }
  }
    
  
  $('.btnShowCreateAccount').click(onCreateAccountClick);
  function onCreateAccountClick()
  {
    $.ajax({
      method: "POST",
      url: 'web_service.php',
      data: {action: 'get_create_account'},
      success: onSuccess
    });

    function onSuccess(result) 
    {
      $('.container-fluid').html(result);
      initViews();
    }   
  }
}