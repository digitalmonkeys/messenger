window.processMessage = function(message)
{
    $('.input-message').val('');

    if(message == 'clear')
    {
        $('.chatbox .conversation').html('');
        return false;
    }
    else if($('.btnSendMessage').attr('data-id') == '0')
    {
        var messageReturned = '';
        if(message == 'info')
        {
            messageReturned = 'Here are some things you can ask me';
            messageReturned += '<pre>';
            messageReturned += '<br>- "weather in X" (X being the city/region/country)';
            messageReturned += '<br>- "math: 3 + 7"';
            messageReturned += '<br>- "random thought", "amaze me", "another one"';
            messageReturned += '</pre>';
        }
        else if(message.toLowerCase().indexOf('weather') == 0)
        {
            var city = message.substring(11);
            console.log(city);
            $.ajax({
                method: 'GET',
                async: false,
                url: 'http://digital-monkeyz.com/weather/web_service.php?city=' + city,
                success: function(result){
                    console.log(result);
                    messageReturned += '<img class="weather-icon" src="http://digital-monkeyz.com/weather/graphics/weather_icons/'+ result.weather[0].icon +'.png">';
                    messageReturned += '<br> ' + result.weather[0].description;
                    messageReturned += '<br> Temperature: ' + Math.round(result.main.temp - 273.15) + '° C';
                    messageReturned += '<br> Clouds: ' + result.clouds.all + '%';
                    messageReturned += '<br> Humidity: ' + result.main.humidity + '%';
                    messageReturned += '<br> Pressure: ' + result.main.pressure + 'hPa';
                    }
            })
        }
        else if(message.toLowerCase() == 'random thought' || message.toLowerCase() == 'another one' || message.toLowerCase() == 'amaze me')
        {
           var arrThoughts = [];
           arrThoughts.push('We eat pizza from the inside out.');
           arrThoughts.push('If you live to be 70 years old you will spend TEN YEARS of your life on Monday.');
           arrThoughts.push('Sometime in the future, someone will say your name for the last time.');
           arrThoughts.push('The word ambiguous only has one meaning');
           arrThoughts.push('Outer space isn’t empty, it literally contains everything there is.');
           arrThoughts.push('When jogging, we put on special clothes so people don’t think we are running from or to something.');
           arrThoughts.push('How do vampires always look so neat and tidy if they can’t see themselves in the mirror?');
           arrThoughts.push('If you drop an Oreo you can still safely eat two thirds of it.');
           arrThoughts.push('There’s only one sunset, and it’s been going around the earth for billions of years.');
           arrThoughts.push('What does my mirror look like when I’m not looking at it?');
           arrThoughts.push('Your stomach thinks all potatoes are mashed.');
           arrThoughts.push('The Swiss must’ve been pretty confident in their chances of victory if they included a corkscrew on their army knife.');
           arrThoughts.push('In order to fall asleep, you have to pretend to be asleep');
           arrThoughts.push('Wrong is spelled wrong in the dictionary.');
           arrThoughts.push('Mothers only get a day, but sharks get a whole week.');
           arrThoughts.push('Nothing is on fire, fire is on things.');
           arrThoughts.push('Morgan Freeman’s voice sounds even better in his own head.');
           arrThoughts.push('Wake up earlier on weekends. Now you get to sleep in for 5 days a week instead of two.');
           arrThoughts.push('Nikola Tesla is now best known for not being well known.');
            
           messageReturned = arrThoughts[Math.floor(Math.random() * arrThoughts.length)];
                         
        }
        else if(message.toLowerCase().indexOf('math:') == 0)
        {
            var mathQuestion = message.substring(5);
            var result = math.eval(mathQuestion);
            messageReturned = result.toString();
        }
        else
        {
            var arrResponses = [' to you too', ' said you', ' you say?', ' so what?'];
            var responseIndex = Math.floor(Math.random() * arrResponses.length);
            messageReturned = message + arrResponses[responseIndex];
        }
        var strToPrint = 
            '<p class="message-sent">' + 
                '<span class="message-content">' +
                    message
                '</span>' +
            '</p>';

        strToPrint += 
            '<p class="message-received">' + 
                '<span class="message-content">' +
                    messageReturned
                '</span>' +
            '</p>';

        $('.conversation').append(strToPrint);
        window.parseMessages($('.message-sent, .message-received'));


        $('.message-received').each(function(){
           if($(this).attr('data-animation-done') == null)
           {
                TweenMax.from($(this), 0.5, {opacity: 0, marginLeft: -100});
                $(this).attr('data-animation-done', true);
           }        
        });

        $('.message-sent').each(function(){
           if($(this).attr('data-animation-done') == null)
           {
                TweenMax.from($(this), 0.5, {opacity: 0, marginRight: -100});
                $(this).attr('data-animation-done', true);
           }        
        });


        initViews();
        $('.conversation').animate({ scrollTop: $('.conversation').prop("scrollHeight")}, 1000);

        return false;
    }
    else
    {
        return true;
    }
}