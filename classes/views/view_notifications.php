<?php
require_once 'view_base.php';
require_once 'view_notification_item.php';


class ViewNotifications extends ViewBase
{
  private $messages; 
  private $model;
    
  public function __construct()
  {
    
  }
  
  public function get_output()
  {
    $this->logger->log('ViewNotifications::get_output()');
    
    $listOfItems = $this->generateList();
      
    $output = "
      <div class='row'>
        <section class='notifications-box'>
          <h6 class='title'> Notifications </h6>
    
          <ul class='notifications-list'>
            $listOfItems
          </ul>
        </section>
      </div>
    ";
    return $output;
  }
    
  private function generateList()
  {
      $this->logger->log('ViewNotifications::generateList()');
      
      $arrNotifications = array();
      
      $strList = '';
      
      if(isset($this->messages) && count($this->messages) > 0)
      {
        $this->logger->log('ViewNotifications::generateList() messages = '.json_encode($this->messages));
            
        foreach($this->messages as $message)
        {
            $senderID = $message['sender'];
            if($message['delivered'] == 0)
            {
                if(!array_key_exists($senderID, $arrNotifications))
                {
                    $newNotification = new stdClass();
                    $newNotification->count = 1;
                    $newNotification->senderID = $senderID;
                    $newNotification->senderName = $this->model->get_contact_info($senderID)['username'];
                    $arrNotifications[$senderID] = new stdClass();

                    $arrNotifications[$senderID] = $newNotification;
                }
                else
                {
                   $arrNotifications[$senderID]->count++;
                }
            }
        }
          
        $this->logger->log('ViewNotifications::generateList() arrNotifications = '.json_encode($arrNotifications));
          
        foreach($arrNotifications as $notification)
        {
            $newNotificationItemView = new ViewNotificationItem();
            $newNotificationItemView->set_logger($this->logger);
            $newNotificationItemView->init($notification);
            $strList .= $newNotificationItemView->get_output();
        }
      }
      else
      {
          
      }
      
      $this->logger->log('ViewNotifications::generateList() strList = '.$strList);
        
      return $strList;
  }
    
  public function init($messages, $model)
  {
      $this->model = $model;
      $this->messages = $messages;   
  }
}

?>