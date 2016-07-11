<?php

require_once 'class_logger.php';
require_once 'class_db_manager.php';
require_once 'class_model.php';
require_once 'views/view_login_screen.php';
require_once 'views/view_dashboard.php';
require_once 'views/view_chat_box.php';
require_once 'views/view_create_account.php';
require_once 'views/view_contact_list_box.php';
require_once 'views/view_message.php';
require_once 'views/view_notifications.php';

class Controller 
{  
  public $logger;
  private $db_manager;
  private $model;
    
  
  public function __construct()
  {
    $this->logger = new Logger();
      
    if(!isset($_SESSION))
    {
      session_start();
      //$_SESSION['logged_in'] = false;
    }
    
    
    $this->db_manager = new DB_Manager($this->logger);
    $this->model = new Model($this->db_manager->db, $this->logger);
  }
  
  public function do_action($data)
  { 
    if(!isset($data['action'])) 
    {
      return; 
    }
    
    $action = $data['action'];
    
    if($action != 'get_messages' && $action != 'get_contact_list')
    {
        $this->logger->log('REQUEST = '. json_encode($data));
        $this->logger->log("\n\n\n", 0, false);  
    }
      
    switch($action)
    {
      case 'login':
        if($this->model->login($data))
        {
          $this->get_app();
        }
        else
        {
          $this->get_app('Failed to login');
        }

        break;
      case 'create_account':
        $this->create_account($data);
        break;
      case 'logout':
        $this->model->logout($data);
        $this->get_app();
        break;


      case 'send_message':
        $this->send_message($data);
        break;
      case 'get_new_chat_window':
        $this->get_new_chat_window($data['recipient']);
        break;
      case 'get_messages':
        $this->get_messages($data);
        break;
      case 'get_contact_list':
        $this->get_contact_list();
        break;
      case 'get_app':
        $this->get_app();
        break;
      case 'get_create_account':
        $this->get_create_account();
        break;
      case 'add_contact':
        $this->add_contact($data);
        break;
      case 'find_contact':
        $this->find_contact($data);
        break;
      case 'log_data':
        $this->logger->log($data);
        break;
    }  
  }
  
  private function create_account($data)
  {
      $response = new stdClass();
      
      if($this->model->create_account($data))
      {
          $response->status = 'ok';
      }
      else
      {
          $response->status = 'error';
      }
      
      echo json_encode($response);
    
  }

    
  private function get_contact_list()
  {
      $response = new stdClass();
      $response->status = 'ok';
      
      $viewContactList = new ViewContactListBox();
      $viewContactList->set_logger($this->logger);  
      
      $userInfo = $this->model->get_contact_info($_SESSION['ID']);
      $userInfo['contact_list'] = $this->model->get_contact_list_names_from_ids($userInfo['contact_list']);
      
      
      $viewContactList->set_user_info($userInfo); 
      $response->content = $viewContactList->get_output();
      
      $this->logger->log('Controller::get_contact_list() response = '.json_encode($response));
      echo json_encode($response);
  }
    
  public function check_login()
  {
    if(isset($_SESSION['ID']))
    {
      return true;
    }
    else 
    {
      return false;  
    }
  }
  
  public function get_app($message = null)
  {
    //$this->logger->log('get_app message = '.$message);
    //$this->logger->log('user login status = '. $isLoggedIn);
    
    if($this->check_login())
    {
      $this->get_dashboard();
    }
    else 
    {
      $this->prompt_login($message);
    }
  }
  
  private function prompt_login($message)
  {
      $loginScreen = new ViewLoginScreen();
      $loginScreen->set_logger($this->logger);
      echo $loginScreen->get_output($message);
  }
  
  private function get_dashboard()
  {
      $userInfo = $this->model->get_contact_info($_SESSION['ID']);
      $dashboard = new ViewDashboard();
      $dashboard->set_logger($this->logger);
      
      $userInfo['contact_list'] = $this->model->get_contact_list_names_from_ids($userInfo['contact_list']);
      $dashboard->set_data($this->model, $userInfo);
      echo $dashboard->get_output();
  }
  
    private function get_create_account()
    {
      $createAccountScreen = new ViewCreateAccount();
      $createAccountScreen->set_logger($this->logger);
      echo $createAccountScreen->get_output();
    }
    
    private function add_contact($data)
    {
      $this->logger->log('Controller::add_contact() data = '.json_encode($data));
      $response = $this->model->add_contact($data['contact']);
      if($response->status == 'ok')
      {
          $this->logger->log('Controller::add_contact() status is ok');
          $contactListData = $response->content;
          $this->logger->log('Controller::add_contact() response = '.json_encode($response));

          $contactListView = new ViewContactListBox();
          $contactListView->set_logger($this->logger);

          $userInfo = $this->model->get_contact_info($_SESSION['ID']);
          $userInfo['contact_list'] = $this->model->get_contact_list_names_from_ids($userInfo['contact_list']);
          $contactListView->set_user_info($userInfo);

          $response->content = $contactListView->get_output();   
      }
      $this->logger->log('Controller::add_contact() response = '.json_encode($response));
      echo json_encode($response);
    }
    
    private function find_contact($data)
    {
      $foundContacts = $this->model->find_contact($data['contact']);
      $response = new stdClass();
      
      if(isset($foundContacts) && count($foundContacts) > 0)
      {
          $response->status = 'ok';
          $contactListView = new ViewContactListBox();
          $contactListView->set_logger($this->logger);

          $response->content = $contactListView->generateFoundContactList($foundContacts);   
      }
      else
      {
          $contactName = $data['contact'];
          $response->status = 'error';
          $response->content = "There is no match in our database for the contact your were looking for: $contactName";
      }
        
      $this->logger->log('Controller::find_contact() response = '.json_encode($response));
      echo json_encode($response);
    }
    
    private function processMessage($data)
    {
        // first we check if we're talking to the bot
        if($data['recipient'] == '0')
        {
            if($data['message_body'] == 'weather')
            {
                $data['message_body'] = '25° C in Bucharest';
            }
            else if($data['message_body'] == 'info')
            {
                $data['message_body'] = 
                  'Here are some things you can ask me:
                    <pre>
                    - "weather in X" (X being the city/region/country)
                    - "Math: 4 * 7" (I will solve your question)
                    - "random thought"
                    </pre>';
            }
        }
        
        return $data;
    }
    
    
    
    private function send_message($data)
    {
        //$data = $this->processMessage($data);
        
        $response = new stdClass();
        if($this->model->send_message($data))
        {
            $newMessage = new ViewMessage();
            $newMessage->set_logger($this->logger);
            $newMessage->set_type('sent');
            $newMessage->set_content($data['message_body']);

            $response->status = 'ok';
            $response->content = $newMessage->get_output();
        }
        else
        {
            $response->status = 'error';
            $response->content = 'There has been an error while sending your message';
        }

        $this->logger->log('Controller::send_message() response = '.json_encode($response));

        echo json_encode($response);
    }
    
    private function get_new_chat_window($recipientID)
    {
        $this->logger->log('Controller::get_new_chat_window() $recipientID = '.$recipientID);
        
        $response = new stdClass();
        $response->status = 'ok';
        $response->content = $this->get_chat_window_view($recipientID);
        
        $this->logger->log('Controller::get_new_chat_window() response = '.json_encode($response));
        
        echo json_encode($response);
    }
    
    private function get_chat_window_view($recipients)
    {
        try
        {
            $this->logger->log('Controller::get_new_chat_window() $recipients'.json_encode($recipients));
        
            $chatWindow = new ViewChatBox();
            $chatWindow->set_logger($this->logger);
            $this->logger->log('Controller::get_new_chat_window() in try 2');
            
            $chatWindow->set_data($this->model, $recipients);
            $this->logger->log('Controller::get_new_chat_window() in try 3');
            $this->logger->log('Controller::get_new_chat_window() ViewChatBox instantiated');
        }
        catch(Exception $e)
        {
            $this->logger->log('Controller::get_new_chat_window() error = '.json_encode($e));   
        }
        
        return $chatWindow->get_output();
    }
    
    private function get_chat_tabs($recipients)
    {
        $chatWindow = new ViewChatBox();
        $chatWindow->set_logger($this->logger);
        $chatWindow->set_data($this->model, $recipients);
        return $chatWindow->generateTabs();
    }
    
    private function get_messages($data)
    {
        $this->logger->log('Controller::get_messages() 1 $data = '.json_encode($data));
        $response = new stdClass();
        
        $messages = $this->model->get_messages($_SESSION['ID']);
        
        $this->logger->log('Controller::get_messages() 2 $data = '.$_SESSION['ID']);
        $response->status = 'ok';
        $responseContent = new stdClass();
        
        if(isset($data['in_chat_with']))
        {
           $this->logger->log('Controller::get_messages() A in chat');
        
           $selectedRecipient = 0;
           foreach($data['in_chat_with'] as $tab)
           {
               $this->logger->log('Controller::get_messages() tab = '.json_encode($tab));
               if(isset($tab['selected']))
               {
                   $selectedRecipient = $tab['ID'];
               }
           } 
            
           $responseContent->messages = '';
           if(isset($messages) && count($messages) > 0)
           {
               
               $this->logger->log('Controller::get_messages() 3 message count = '.count($messages));
               
               
               foreach($messages as $message)
               {
                   if($message['delivered'] == 0 && $message['sender'] == $selectedRecipient)
                   {
                       $newMessage = new ViewMessage();
                       $newMessage->set_logger($this->logger);
                       $newMessage->set_type('received');
                       $newMessage->set_content($message['body']);
                       $responseContent->messages .= $newMessage->get_output();
                       if(isset($data['mark_as_delivered']) && $data['mark_as_delivered'] == false)
                       {

                       }
                       else
                       {
                            $this->model->set_as_delivered($message['ID']);    
                       }
                   }
                   
                   
               }
               $recipients = $this->model->extract_recipients($messages);
               foreach($data['in_chat_with'] as $tab)
               {
                   $recipients[] = $tab['ID'];
               }
               $responseContent->tabs = $this->get_chat_tabs($recipients); 
           } 
           else
           {
              $this->logger->log('Controller::get_messages() Z $selectedRecipient = '.$selectedRecipient);
               
               
              $responseContent->tabs = $this->get_chat_tabs($selectedRecipient); 
           }
           
        }
        else
        {
           if(count($messages) > 0)
           {
                $recipients = $this->model->extract_recipients($messages); 
                $responseContent->chat_window = $this->get_chat_window_view($recipients);
           }
        }
        
        $response->content = $responseContent;
        
        // response content contain:
        // messages
        // tabs
        // chat_window
       
        /*
        $viewNotifications = new ViewNotifications();
        $viewNotifications->set_logger($this->logger);
        $viewNotifications->init($messages, $this->model);
        $responseContent->notifications = $viewNotifications->get_output();
        $response->content = $responseContent;
        */
        
        $this->logger->log('Controller::get_messages() response = '.json_encode($response));
        echo json_encode($response);
    }
}
  

?>