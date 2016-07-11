$(document).ready(onPageLoad);
function onPageLoad()
{
  get_app();
  function get_app()
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
}

window.initViews = function()
{
  loginInit();
  createAccountInit();
  dashboardInit(); 
}

window.emojis = [
    {text: ':smile:', path:'graphics/emojis/smile.png'},
    {text: ':wink:', path:'graphics/emojis/wink.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    {text: ':blush:', path:'graphics/emojis/blush.png'},
    
   
];

window.generateEmojiList = function()
{
  var strToReturn = '';
  for(var i = 0; i < window.emojis.length; i++)
  {
    strToReturn += '<img src ="' + window.emojis[i].path + '" class="emoji emoji-item-button" data-text="' + window.emojis[i].text +'"/>'; 
  }
  return strToReturn;
}

window.conversations = [];