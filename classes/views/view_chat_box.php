<?php 
require_once 'view_base.php';

class ViewChatBox extends ViewBase
{
  private $userInfo;
  private $recipients;
  private $model;
    
  public function __construct()
  {
   
  }
  
  public function get_output()
  {
    
    if(isset($this->userInfo))
    {
        $ID = $this->userInfo['ID'];
        $username = $this->userInfo['username'];
        $status = '';
        if($ID == 0)
        {
            $status .= 'Say "info" to see what you can ask and "clear" to empty the chat window.';
        }
    }
    else
    {
        $ID = '';
        $username = '';
    }
      
    //$this->logger->log('ViewChatBox::get_output() $ID = '.$ID.', $username = '.$username);    
     
    $tabs = $this->generateTabs();  
    //$messages = $this->generateMessages();  
      
    $output = "
      <div class='row'> 
        <section class='chatbox' data-id='$ID'>
          <div class='row'>
            <div class='col-sm-10'>
             <h6 class='title'> Conversation with <b class='recipient-name'> $username </b></h6>
             <p class='subtitle'> $status</p>
            
            </div>
            <div class='col-sm-2'>
             <p class='btn btn-block btn-lg btn-danger btnCloseChatbox' data-id='$ID'> 
                 <i class='fa fa-close fa-lg'> </i>
               </p>
            </div>
          </div>
          <div class='row tab-container'>
          $tabs
          </div>
          <div class='conversation'>
          
            
          </div>
          <div class='row'>
            <div class='form-group input-container'>
                <textarea placeholder='Say something...' class='form-control input-message' />
            </div>
            <div class='emoji-container' style='display: none'>
            </div>
          </div>  
          <div class='row'>
            <div class='col-sm-4'>
              <p class='btn btn-block btn-lg btn-primary btnSendMessage' data-id='$ID'> 
                Send <i class='fa fa-send'> </i>
              </p>
            </div>
            <div class='col-sm-4'>
              <p class='btnEmojis' data-id='$ID'> 
                <img class='emoji' src='graphics/emojis/smile.png'>
              </p>  
            </div>
            <div class='col-sm-4'>
              <p class='btn btn-block btn-lg btn-danger btnBuzz' data-id='$ID'>  
               BUZZ  
              </p>  
            </div>
            <!--
            <div class='col-sm-2'>
              <p class='btn btn-block btn-lg btn-info btnGetMessages'> 
                Get Messages
              </p>
            </div>
            -->
          </div>
          
        </section>
      </div> 
    ";
    return $output;
  }
    
  public function set_data($model, $recipients)
  {
      $this->logger->log('ViewChatBox::set_data() $recipients = '.json_encode($recipients));
      $this->model = $model;
      
      if(gettype($recipients) == 'array')
      {
          $this->logger->log('ViewChatBox::set_data() recipients is array');
          $this->recipients = array();
          
          $firstFound = false;
          
          if(count($recipients) > 0)
          {
            foreach($recipients as $recipient)
            {
               $recipientInfo = $this->model->get_contact_info($recipient);
               $recipientInfo['selected'] = false; 
                
               if(!$firstFound)
               {
                  $firstFound = true;
                  $this->userInfo = $this->model->get_contact_info($recipient);
                  $recipientInfo['selected'] = true;
               }
                
               $this->recipients[] = $recipientInfo; 
            }
          }
          
      }
      else
      {
          // if the recipients parameter is not an array, 
          // it means it's a single string containing the ID of the recipient
          $this->logger->log('ViewChatBox::set_data() recipients is NOT array');
          $this->userInfo = $this->model->get_contact_info($recipients);
      }
      
      $this->logger->log('ViewChatBox::set_data() userInfo = '.json_encode($this->userInfo));
      
      $this->logger->log('ViewChatBox::set_data() userInfo = '.json_encode($this->userInfo));  
  }
    
  public function generateTabs()
  {
      $strToReturn = "";
      
      if(isset($this->recipients))
      {
          foreach($this->recipients as $recipient)
          {
              $ID = $recipient['ID'];
              $username = $recipient['username'];

              if($recipient['selected'] == true)
              {
                  $selectedClass = 'selected';
              }
              else
              {
                  $selectedClass = '';
              }

              $strToReturn .= 
                "
                <div class='col-sm-3'>
                    <p class='chat-window-tab tab-item $selectedClass' data-id='$ID' data-username='$username'> 
                        $username 
                    </p>
                </div>
               ";
          }
      }
      else
      {
          $ID = $this->userInfo['ID'];
          $username = $this->userInfo['username'];
          $strToReturn .= 
                "
                <div class='col-sm-3'>
                    <p class='chat-window-tab tab-item selected' data-id='$ID' data-username='$username'> 
                        $username 
                    </p>
                </div>
               ";
      }
      
      
      
      
      return $strToReturn;
  }
}

?>