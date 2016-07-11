$(document).ready(dashboardInit);

function dashboardInit()
{ 
    $('.emoji-container').html(window.generateEmojiList());
    
    if(window.getMessagesInterval)
    {
        clearInterval(window.getMessagesInterval);
    }
    
    if(window.getContactListInterval)
    {
        clearInterval(window.getContactListInterval);
    }
    
    if($('.dashboard').length > 0)
    {
        window.getMessagesInterval = setInterval(getMessages, 500);
        window.getContactListInterval = setInterval(window.getContactList, 500);
    }
        
    window.getContactList = function(){
        if($('.dashboard').length == 0)
        {
            return;   
        }
        $.ajax({
            method: 'POST',
            url: 'web_service.php',
            data: {action: 'get_contact_list'},
            success: onSuccess
        })
        
        function onSuccess(result){
            result = JSON.parse(result);
            if(result.status == 'ok')
            {
                if($('.popup-find-new-contact').css('display') == 'none' && $('.popup-add-new-contact').css('display') == 'none')
                {
                    $('.contact-list-container').html(result.content);
                }
                initViews();
            }
        }
        
    }
    
    window.scrollChatbox = function(){
        $('.conversation').animate({ scrollTop: $('.conversation').prop("scrollHeight")}, 1000);
    } 
    
    $('.btnGetMessages').unbind().click(getMessages);
    
    function getMessages()
    {   
        if($('.dashboard').length == 0)
        {
            return;   
        }
        //console.log('getMessages()');
        
        var requestData = {};
        requestData.action = 'get_messages';
        if($('.chatbox').length > 0)
        {   
           var openTabs = []; 
           $('.chatbox .chat-window-tab').each(getTabID);
           requestData.in_chat_with = openTabs; 
        }
        
        function getTabID()
        {
            var crtTabData = {ID: $(this).attr('data-id')};
            if($(this).hasClass('selected'))
            {
                 crtTabData.selected = true;   
            }
            openTabs.push(crtTabData);
        }
        
        
        $.ajax ({
            method: "POST",
            url: "web_service.php",
            data: requestData,
            success: onSuccess
        });
        
        function onSuccess(result)
        {
           //console.log('onSuccess() getMessages');
           result = JSON.parse(result);
           if(result.status == 'ok')
           {
               if(result.content == null)
               {
                    // If we have no content, we can't do anything, obviously
                    return;        
               }
               
               //console.log(JSON.stringify(result));
               
               console.log('getMessages() result.content.messages = ', result.content.messages);
               if(result.content.messages != null && result.content.messages.length > 0)
               {
                   if($('.chatbox').length > 0 )
                   {
                       $('.chatbox .conversation').append(result.content.messages);
                       $('.message-received').each(function(){
                           if($(this).attr('data-animation-done') == null)
                           {
                                TweenMax.from($(this), 0.5, {opacity: 0, marginLeft: -100});
                                $(this).attr('data-animation-done', true);
                           }        
                       });
                       parseMessages($('.message-sent, .message-received'));
                       scrollChatbox();
                   }
               }
               
               if(result.content.chat_window != null)
               {
                   console.log('onSuccess() getMessages chat window exists in response');
                   createNewChatbox(result.content.chat_window);       
               } 
               else if(result.content.tabs != null && String(result.content.tabs.length) > 0)
               {
                    updateChatWindowTabs(result.content.tabs);        
               }
               
               initViews();
           }
        }
    }
    
    function updateChatWindowTabs(htmlString)
    {
        if($('.chatbox').length > 0)
        {
            $('.chatbox .tab-container').html(htmlString);
            var currentRecipientID = $('.chatbox').attr('data-id');
            updateSelectedTab(currentRecipientID);
        }
    }
    
    $('.btnLogout').unbind().click(onBtnLogout);
    function onBtnLogout()
    {
      $.ajax({
        method: "POST",
        url: 'web_service.php',
        data: {action: 'logout'},
        success: onSuccess
      });

      function onSuccess(result)
      {
        $('.container-fluid').html(result);
        initViews();
      }
    }
    
    
    $('.btnAddContact1').unbind().click(onBtnAddContact1);
    function onBtnAddContact1()
    {
      $('.popup-find-new-contact').css('display', 'block');
      $('.btnAddContact1').css('display', 'none');
      
      $('.btnFindContact').unbind().click(onBtnFindContact);
      function onBtnFindContact() 
      {
          console.log('onBtnFindContact()');
          
          $('.popup-add-new-contact').css('display', 'none');
          $('.popup-find-new-contact').css('display', 'block');
          
          $.ajax({
             method: "POST",
             url: 'web_service.php', 
             data: {action: 'find_contact', contact: $('.inputAddContact').val()},
             success: onSuccess
         }); 
          
         function onSuccess(result)
         {
             console.log('onBtnFindContact() success - result = ', result);
             result = JSON.parse(result);
             if(result.status == 'ok')
             {
                 $('.popup-add-new-contact').css('display', 'block');
                 $('.popup-find-new-contact').css('display', 'none');
                 console.log('result.content = ', result.content);
                 $('.popup-add-new-contact .found-contacts-list').html(result.content);
                 initViews();
                 $('.btnAddContact2').unbind().click(onBtnAddContact2);
             }
             else
             {
                 alert(result.content);
             }
         }
      }
        
        
      $('.btnAddContact2').unbind().click(onBtnAddContact2);
      function onBtnAddContact2()
      {
         console.log('onBtnAddContact2()');
         $.ajax({
             method: "POST",
             url: 'web_service.php', 
             data: {action: 'add_contact', contact: $(this).attr('data-id')},
             success: onSuccess
         });
        
         function onSuccess(result)
         {
             result = JSON.parse(result);
             if(result.status == 'ok')
             {
                 $('.contact-list-box').parent().replaceWith(result.content);
                 $('.popup-add-new-contact').css('display', 'none');
                 $('.popup-find-new-contact').css('display', 'none');
                 $('.btnAddContact1').css('display', 'block');
                 initViews();
             }
         }  
      }
    }
    
    $('.btnCancelAddContact').unbind().click(onBtnCancelContactClick);
    function onBtnCancelContactClick()
    {
        $('.popup-add-new-contact').css('display', 'none');
        $('.popup-find-new-contact').css('display', 'none');
        $('.btnAddContact1').css('display', 'block');
    }
    
    $('.tab-item').unbind().click(onTabItemClick);
    function onTabItemClick()
    {
        
        console.log('onTabItemClick()');
        
        var oldRecipientID = $('.chatbox').attr('data-id');
        var newRecipientID = $(this).attr('data-id');
        var newRecipientUsername = $(this).attr('data-username');
        
        $('.chatbox .recipient-name').text(newRecipientUsername);
        $('.chatbox').attr('data-id', newRecipientID);
        $('.btnSendMessage').attr('data-id', newRecipientID);
        
        window.conversations[oldRecipientID] = $('.chatbox .conversation').html();
        $('.chatbox .conversation').html('');
        
        
        
        if(window.conversations[newRecipientID] != null)
        {
            $('.chatbox .conversation').html(window.conversations[newRecipientID]);        
        }
        updateSelectedTab(newRecipientID);
    }
    
    function updateSelectedTab(selectedID)
    {
        $('.tab-item.selected').removeClass('selected');
        $('.tab-item').each(function(){
            if($(this).attr('data-id') == selectedID)
            {
                $(this).addClass('selected');
                $('.chatbox .tab-container').append($(this).parent());
            }
        });   
    }
    
    function openNewTab(recipientID, recipientName)
    {
        var tabFound = false;
        
        $('.chatbox').find('.chat-window-tab').each(checkIfTabExists);
        
        if(!tabFound)
        {
           var newTab = $('.chatbox').find('.chat-window-tab').first().parent().clone();
           newTab.find('.chat-window-tab').attr('data-id', recipientID);
           newTab.find('.chat-window-tab').text(recipientName);
           $('.chatbox .tab-container').append(newTab);
           initViews();
        }
        
        updateSelectedTab(recipientID);            
        
        
        function checkIfTabExists()
        {
            if($(this).attr('data-id') == recipientID)
            {
                tabFound = true;    
            }
        }
    }
    
    $('.contact-item').unbind().click(onContactItemClick);
    function onContactItemClick()
    {
        //console.log('onContactItemClick()');
        if($('.chatbox').length == 0)
        {
            getNewChatWindow($(this).attr('data-id'));        
        }
        else
        {
            openNewTab($(this).attr('data-id'), $(this).text());
        }
    }
    
    function getNewChatWindow(recipientID)
    {
        $.ajax({
            method:"POST",
            url:"web_service.php",
            data: {action: 'get_new_chat_window', recipient: recipientID},
            success: onSuccess
        });
        
        function onSuccess(result)
        {
            result = JSON.parse(result);
            if(result.status == 'ok')
            {
                createNewChatbox(result.content);      
            }   
        }
    }
    
    function createNewChatbox(htmlString)
    {
        if($('.chatbox').length > 0)
        {
             TweenMax.to($('.chatbox'), 0.5, {opacity: 0, scaleX: 0.7, scaleY: 0.7, onComplete: drawChatbox, onCompleteParams: [htmlString]});        
        }
        else
        {
             drawChatbox(htmlString);
        }
    }
    
    
    // In order to create a new chatbox, use the createNewChatbox() function
    // This is just a callback for the createNewChatbox() function and you'll miss functionality
    // by using it on its own
    function drawChatbox(htmlString)
    {
        $('.chatbox-container').html(htmlString);

        $('.chatbox').css('opacity', 0);
        TweenMax.to($('.chatbox'), 0, {scaleX: 0.7, scaleY: 0.7});  
        TweenMax.to($('.chatbox'), 0.5, {scaleX: 1, scaleY: 1, opacity: 1, delay: 0.1});  
        initViews();   
    }
    
    $('.btnSendMessage').unbind().click(onBtnSendMessageClick);
    function onBtnSendMessageClick()
    {
      sendMessage();    
    }
      
    $('.input-message').unbind().keyup(onInputMessageEnter);
    function onInputMessageEnter(event)
    {
      if(event.which == '13')
      {
          var oldMessage = $('.input-message').val();
          var newMessage = oldMessage.substring(0, oldMessage.length - 1);
          $('.input-message').val(newMessage);
          
          var spaceCount = (newMessage.match(/ /g) || []).length;
          var charCount = newMessage.length;
          if(spaceCount == charCount)
          {
            $('.inputMessage').val('')        
          }
          
          
          if($('.input-message').val().length > 0 && spaceCount < charCount)
          {
            
            sendMessage();   
          }
          
      }
    }
    
    function hidePreloader()
    {
        $('.btnSendMessage').html(window.btnSendMessageContent);
        if(window.messagePreloaderTimeout)
        {
            clearTimeout(window.messagePreloaderTimeout);        
        }
    }
    
    function showPreloader()
    {
        $('.btnSendMessage').html('<div class="loader loader-inline opacity0"><div class="loader-inner line-scale"><div></div><div></div><div></div><div></div><div></div></div></div>');       
        if(window.messagePreloaderTimeout)
        {
            clearTimeout(window.messagePreloaderTimeout);        
        }
        
        window.messagePreloaderTimeout = setTimeout(hidePreloader, 5000);
    }
    
    
    
    function sendMessage(message)
    {
        if(message == null || $('.input-message').val().length > 0)
        {
            message = $('.input-message').val();      
        }
        //alert(message);
        
        if(!window.processMessage(message))
        {
            return;    
        }
        
        if(message != null)
        {
          window.btnSendMessageContent =  $('.btnSendMessage').html(); 
          var preloaderTimeout = setTimeout(showPreloader, 200);
         
          $.ajax({
              method: "POST",
              url: "web_service.php",
              data: {action: 'send_message', recipient: $('.btnSendMessage').attr('data-id'), message_body: message},
              success: onSuccess
          });
          $('.input-message').val(''); 
        }
        
        function onSuccess(result)
        {
            result = JSON.parse(result);
            console.log('sendMessage onSuccess() result = ', result);
            if(result.status == 'ok')
            {
                clearTimeout(preloaderTimeout);
                hidePreloader();
                console.log(result.content);
                $('.conversation').append(result.content);
                $('.message-sent').each(function(){
                   if($(this).attr('data-animation-done') == null)
                   {
                        TweenMax.from($(this), 0.5, {opacity: 0, marginRight: -100});
                        $(this).attr('data-animation-done', true);
                   }        

                });
                parseMessages($('.message-sent, .message-received'));
                initViews();
                scrollChatbox();
            }
        }
    }
    
    $('.btnEmojis').unbind().click(onBtnEmojisClick);
    function onBtnEmojisClick()
    {
      if($('.input-message').css('display') != 'none')
      {
        $('.input-message').css('display', 'none');
        $('.emoji-container').css('display', 'block');
      }
      else
      {
         $('.input-message').css('display', 'block');
         $('.emoji-container').css('display', 'none');
      }
    }    
  
    $('.emoji-item-button').unbind().click(onEmojiItemClick);
    function onEmojiItemClick()
    {
      
      var strEmoji = $(this).attr('data-text');
      $('.input-message').val($('.input-message').val() + strEmoji);
      
      $('.input-message').css('display', 'block');
      $('.emoji-container').css('display', 'none');  
    }
    
    $('.btnCloseChatbox').click(onBtnCloseChatboxClick);
    function onBtnCloseChatboxClick()
    {
        if($('.chatbox').length > 0)
        {
            TweenMax.to($('.chatbox'), 0.5, {scaleX: 0.7, scaleY: 0.7, opacity: 0, onComplete: removeChatbox});  
        }
        else
        {
            removeChatbox();        
        }
        function removeChatbox()
        {
            $('.chatbox').remove();
        }
    }
    
    window.parseMessages = function(element)
    {
        parseEmojis(element);
        parseBuzz(element);
    }
    
    function parseBuzz(element)
    {
        //console.log('parseBuzz() element = ', element);
        if(element != null)
        {
            element.each(parseBuzzIndividual);
        }
        function parseBuzzIndividual()
        {   
          var elemText = $(this).text();
          if(elemText.indexOf('BUZZ!') != -1)
          {
              if($(this).attr('data-buzz-done') == null || $(this).attr('data-buzz-done') == '0')
              {
                  TweenMax.to($('.chatbox'), 0.25, {scaleX: 1.2, scaleY: 1.2});    
                  TweenMax.to($('.chatbox'), 0.25, {scaleX: 1, scaleY: 1, delay: 0.25});
                  var audio = new Audio('audio/buzz.mp3');
                  audio.play();
                  $(this).attr('data-buzz-done', '1');
              }
          }
        }
    }
  
    $('.btnBuzz').unbind().click(onBtnBuzzClick);
    function onBtnBuzzClick()
    {
      sendMessage('BUZZ!');
    }
    
    function parseEmojis(element)
    {
        function parseEmojisIndividual()
        {
              var oldText = $(this).html();
              var newText = oldText;
              for(var i = 0; i < window.emojis.length; i++)
              {
                  var replacemenet = "<img class='emoji' src='" + window.emojis[i].path + "'>";
                  var emojiText = window.emojis[i].text;
                  newText = newText.split(emojiText).join(replacemenet);       
              }
              $(this).html(newText); 
        }
        
        //console.log('parseEmojis() element = ', element);
        if(element != null)
        {
          element.each(parseEmojisIndividual);
        }
        
        
    }
    
    
}