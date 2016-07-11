<?php
class Model
{
  private $db;
  protected $logger;
  
  public function __construct($db_received, $logger)
  {
    $this->logger = $logger;
    $this->db = $db_received;
  }
  
  public function login($data)
  {
    $query = "SELECT ID, email, username, password FROM
    users WHERE username='{$data['username']}';";
      
    $result = $this->db->query($query);
    $user = array();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if(password_verify($data['password'], $user['password']))
        {
          $_SESSION['ID'] = $user['ID'];
          $_SESSION['username'] = $user['username'];
          $this->set_user_as_logged_in($_SESSION['ID']);
          return true;
        }
        else
        {
          $_SESSION['ID'] = null;
          $_SESSION['username'] = null;
          return false;
        } 
    } 
    else
    {
        $_SESSION['logged_in'] = false;
        return false;
    }
    
  }
  
  private function set_user_as_logged_in($ID)
  {
      $query = "UPDATE users
                  SET is_online='1'
                  WHERE ID='{$ID}';";

       $result = $this->db->query($query);
  }
    
  private function set_user_as_logged_out($ID)
  {
      $query = "UPDATE users
                  SET is_online='0'
                  WHERE ID='{$ID}';";

       $result = $this->db->query($query);
  }
    
  public function logout($data)
  {
    $this->set_user_as_logged_out($_SESSION['ID']);
    $_SESSION['ID'] = null;
    return true;
  }
  
  public function create_account($data)
  {
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $query = "INSERT INTO users (email, username, password)
    VALUES ('{$data['email']}', '{$data['username']}', '{$data['password']}');";

    if ($this->db->query($query) === TRUE) {
        return "New record created successfully";
        //return true;
    } else {
        //return false;
        //print_r($data);
        return "Error: " . $query . "<br>" . $this->db->error;
    }
    
    return true;
  }
  
  public function check_login($data)
  {
    
  }
  
  
  public function send_message($data)
  {
    $setAt = date('Y-:m-d h:i:s');  
    $query = "INSERT INTO messages (body, sent_at, sender, recipient)
    VALUES ('{$data['message_body']}', '{$setAt}', '{$_SESSION['ID']}', '{$data['recipient']}');";

    if ($this->db->query($query) === TRUE) {
        return true; 
    } else {
        $this->logger->log('Model::send_message() error: '.$this->db->error);
        return false;
        //return "Error: " . $query . "<br>" . $this->db->error;
    }
    return true;
  }
  
  public function get_conversation($data)
  {
    
  }
  
  public function get_messages($recipientID)
  {
        $this->logger->log('Model::get_messages() ENTRY - recipient = '. $recipientID);
        $query = "SELECT ID, body, delivered, sent_at, read_at, sender, recipient FROM
        messages WHERE recipient='{$recipientID}' AND delivered='0';";
      
        $messages = array();
      
        $result = $this->db->query($query);
        $messages = array();
        if ($result->num_rows > 0)
        {
           $this->logger->log('Model::get_messages() 2 - num_rows = '. $result->num_rows); 
           while($row = $result->fetch_assoc()) {
                //$this->logger->log('Model::get_messages() 2 - num_rows = '. $result->num_rows); 
                $messages[] = $row;
           }
        } 
        else
        {
            //return null;
        }
      
        $this->logger->log('Model::get_messages() - messages = '. json_encode($messages));
        return $messages;      
  }
    
  public function extract_recipients($messages)
  {
      $arrToReturn = array();
      foreach($messages as $message)
      {
           $senderID =  $message['sender'];
          if(!array_key_exists($senderID, $arrToReturn))
          {
            $arrToReturn[$senderID] = $senderID;
          }
      }
      
      return $arrToReturn;
  }
    
  public function set_as_delivered($ID)
  {
        $query = "UPDATE messages
                  SET delivered='1'
                  WHERE ID='{$ID}';";

        $result = $this->db->query($query);
        
        $this->logger->log('Model::get_messages() - messages = '. json_encode($messages));
      
        /*
        if ($result->num_rows > 0)
        {
            return $result->fetch_assoc();   
        } 
        else
        {
            return null;
        }
        */
  }  
    
  public function find_contact($contact)
  {
        $query = "SELECT ID, username, is_online, email FROM
        users WHERE username LIKE '%{$contact}%' OR email LIKE '%{$contact}%';";
      
        $users = array();
      
        $result = $this->db->query($query);
        
        if ($result->num_rows > 0)
        {
           while($row = $result->fetch_assoc()) {
                //$this->logger->log('Model::get_messages() 2 - num_rows = '. $result->num_rows); 
                $users[] = $row;
           }
        } 
        else
        {
            //return null;
        }
        $this->logger->log('Model::find_contact() users = '.json_encode($users));
      
        return $users;
  }
    
  public function add_contact($contact)
  {
      $response = new stdClass();

      $contactToAdd = $this->get_contact_info($contact);
      $this->logger->log('Model::add_contact() contact = '.json_encode($contact));
      $this->logger->log('Model::add_contact() $contactToAdd = '.json_encode($contactToAdd));
      if(isset($contactToAdd))
      {
          $crtUser = $this->get_contact_info($_SESSION['ID']);
          if(isset($crtUser['contact_list']))
          {
            $contactList = json_decode($crtUser['contact_list']);
          }
          else 
          {
            $contactList = array();
          }
          
          $contactList[] = $contactToAdd['ID'];
          $contactList = json_encode($contactList);
          $crtUser['contact_list'] = $contactList;
          $this->update_contact_info($crtUser);
          
          $response->status = 'ok';
          $response->content = $this->get_contact_list_names_from_ids(json_decode($contactList));
      }
      else
      {
          $response->status = 'error';
          $response->content = 'The contact you are trying to add does not exist!';  
      }
      
      return $response;
  }
  
  public function get_contact_info($user)
  {
        $query = "SELECT ID, email, username, contact_list, is_online FROM
        users WHERE username='{$user}' OR email='{$user}' OR ID='{$user}';";
      
        $result = $this->db->query($query);
       
        if ($result->num_rows > 0)
        {
            return $result->fetch_assoc();   
        } 
        else
        {
            return null;
        }
  }
    
  public function get_contact_list($user)
  {
      return $this->get_contact_info($user)['contact_list'];
  }
  
  public function update_contact_info($data)
  {
      
        $query = "UPDATE users
                  SET email='{$data['email']}',username='{$data['username']}',contact_list='{$data['contact_list']}'
                  WHERE ID='{$data['ID']}';";
      
        $result = $this->db->query($query);
       
        if ($result->num_rows > 0)
        {
            return $result->fetch_assoc();   
        } 
        else
        {
            return null;
        }
  }
    
  public function get_contact_list_names_from_ids($contactsReceived)
  {
        $this->logger->log('Model::get_contact_list_names_from_ids() $arrContacts = '.$contactsReceived.' of type '.gettype($contactsReceived));
        $contactsReceived = str_replace('[', '(', $contactsReceived);
        $contactsReceived = str_replace(']', ')', $contactsReceived);
        $contactsReceived = str_replace('"', '', $contactsReceived);
        $this->logger->log('Model::get_contact_list_names_from_ids() $arrContacts = '.$contactsReceived.' of type '.gettype($contactsReceived));
        
        $query = "SELECT ID, username, is_online FROM users WHERE ID in {$contactsReceived} ;";
        $result = $this->db->query($query);
        
        $arrContacts = array();
       
        $contact = array();
        if ($result->num_rows > 0)
        {
            while($contact = $result->fetch_assoc())
            {
               $arrContacts[] = $contact; 
            }
            
            $this->logger->log('Model::get_contact_list_names_from_ids() return '.json_encode($arrContacts).' of type '.gettype($contactsReceived));
            return $arrContacts;
        } 
        else
        {
            $this->logger->log('Model::get_contact_list_names_from_ids() return null');
        
            return null;
        }
  }
}
?>