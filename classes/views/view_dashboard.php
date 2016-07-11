<?php
  require_once 'view_contact_list_box.php';
  require_once 'view_base.php';

  class ViewDashboard extends ViewBase
  {
    private $model;
    private $userInfo; 
   
    public function __construct()
    {
      
    }
    
    public function get_output()
    {
      $viewContactList = new ViewContactListBox();
      $viewContactList->set_logger($this->logger);  
      $viewContactList->set_user_info($this->userInfo); 
      $viewContactListOutput = $viewContactList->get_output();
      
      $username = $this->userInfo['username'];
      $ID = $this->userInfo['ID'];  
      
      $output =  
      "
      <div class=' dashboard'> 
        
        <div class='row'>
            <div class='col-sm-7 chatbox-container'>

            </div>
            <div class='col-sm-5'>
                
                
                
                
                <div class='row profile-info-container'>
                    <div class='col-sm-6'>
                        <p class='profile-info'>
                            <i class='fa fa-user fa-lg'></i>
                            Logged in as <b data-id='$ID'>$username</b>
                        </p>
                    </div>
                    <div class='col-sm-6'>
                        <p class='btn btn-block btn-lg btn-primary btnLogout'> 
                            <i class='fa fa-sign-out fa-lg'> </i>
                            Log Out
                        </p>  
                    </div>
                </div>
                
                
                
                
                
                
                <div class='contact-list-container'>
                    $viewContactListOutput
                </div>
                <div class='notifications-container'>

                </div>

                
            </div>
        </div>
      </div>
      ";
      return $output;
    }
      
    public function set_data($model, $userInfo)
    {
        $this->logger->log('ViewDashboard::set_user_info() userInfo = '.json_encode($userInfo));
        $this->model;
        $this->userInfo = $userInfo;
    }
  }

?>