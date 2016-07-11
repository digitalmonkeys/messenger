<?php
require_once 'view_base.php';

class ViewNotificationItem extends ViewBase
{
  private $data;  
    
  public function __construct()
  {
    
  }
  
  public function get_output()
  {
    $this->logger->log('ViewNotificationItem::get_output()');
    
    $body = $this->data->senderName.' ('.$this->data->count.')';  
    $sender = $this->data->sender;
      
    $output = "
      <li class='notification-item contact-item' data-id='$sender'> $body </li>
    ";
    return $output;
  }
    
  public function init($data)
  {
      $this->data = $data;
  } 
}

?>