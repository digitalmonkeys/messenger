<?php 
require_once 'view_base.php';

class ViewMessage extends ViewBase
{
  private $type;
  private $content;
    
  public function __construct()
  {
   
  }
  
  public function get_output()
  {
    if(isset($this->recipientID))
    {
        $ID = $this->recipientID;
    }
    else
    {
        $ID = '';
    }
      
    //$type = $this->type;  
      
    $output = "
      <p class='message-$this->type'> 
        <span class='message-content'>
            $this->content
        </span>
      </p>
    ";
    return $output;
  }
    
  public function set_type($type)
  {
      if($type == 'sent')
      {
          $this->type = $type;
      }
      else
      {
          $this->type = 'received';
      }
  }
    
    public function set_content($content)
  {
      $this->content = $content;
  }
}

?>