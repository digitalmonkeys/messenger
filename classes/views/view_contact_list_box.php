<?php
require_once 'view_base.php';

class ViewContactListBox extends ViewBase
{
  private $arrContacts;  
    
  public function __construct()
  {
    
  }
  
  public function get_output()
  {
    $this->logger->log('ViewContactList::get_output()');
    
    
    $listOfItems = $this->generateContactList();
      
      
    $output = "
      <div class='row'>
        <section class='contact-list-box'>
          <div class='row'>
            <div class='col-sm-8'>
              <h6 class='title'> Contact list </h6>
            </div>
            <div class='col-sm-4'>
              <p class='btn btn-block btn-lg btn-primary btnAddContact1'>
                
                <i class='fa fa-user-plus'> </i>
              </p>
            </div>
            <!--
            <div class='col-sm-2'>
              <p class='btn btn-block btn-lg btn-info btnGetMessages'> 
                <i class='fa fa-refresh'> </i>
              </p>
            </div>
            -->
          </div>
          <ul class='contact-list'>
            $listOfItems
          </ul>
          <div class='popup-find-new-contact' style='display: none'>
            <section class='content'>
              <p>Type the contact's username/email here</p>
              <input type='text' placeholder='Username/Email' class='form-control inputAddContact' />
              <p class='btn btn-block btn-lg btn-info btnFindContact'> 
                Add contact
                <i class='fa fa-user-plus'> </i>
              </p>
              <p class='btn btn-block btn-lg btn-danger btnCancelAddContact'> 
                Cancel
              </p>
            </section>
          </div>
          <div class='popup-add-new-contact' style='display: none'>
            <section class='content'>
              <p>Here are the results of your search:</p>
              <ul class='found-contacts-list'>
              
              </ul>
              <p class='btn btn-block btn-lg btn-danger btnCancelAddContact'> 
                Cancel
              </p>
            </section>
          </div>
        </section>
      </div>
    ";
    return $output;
  }
    
  public function set_user_info($userInfo)
  {
      $this->arrContacts = $userInfo['contact_list'];
      $this->logger->log('ViewContactList::set_user_info() $userInfo = '.json_encode($userInfo));
      $this->logger->log('ViewContactList::set_user_info() $this->arrContacts =  '.$this->arrContacts);
  }
    
  private function generateContactList()
  {
      $this->logger->log('ViewContactList::generateContactList() $this->arrContacts = '.json_encode($this->arrContacts));
      
      // If we have no contacts, bail early
      if(!isset($this->arrContacts) || $this->arrContacts == false)
      {
          return '';
      }
      
      $this->arrContacts = $this->arrContacts;
      
      $strList = '';
      
      foreach($this->arrContacts as $itemData)
      {
        $this->logger->log('ViewContactList::generateContactList() in foreach, $itemData = '.json_encode($itemData));
      
        $ID = $itemData['ID'];
        $username = $itemData['username'];
          
        $onlineClass = '';
          
        $isOnline = $itemData['is_online'];  
          
        if($isOnline)
        {
            $onlineClass = 'color-online';
        }
        else
        {
            $onlineClass = 'color-offline';
        }
          
          
        $strList .= "<li class='contact-item' data-id='$ID'> 
                        <i class='fa fa-circle $onlineClass'></i>
                        $username 
                     </li>";
      }
      
      $this->logger->log('generateContactList() $strList = '.$strList);
      
      return $strList;
  }
    
  public function generateFoundContactList($arrContactData) {
      $strToReturn = '';
      foreach($arrContactData as $contactData)
      {
          $ID = $contactData['ID'];
          $username = $contactData['username'];
          $email = $contactData['email'];
          
          $onlineClass = '';
          
          $isOnline = $contactData['is_online'];  

          if($isOnline)
          {
            $onlineClass = 'color-online';
          }
          else
          {
             $onlineClass = 'color-offline';
          }
          
          
          
          $strToReturn .= 
              "
              <li class='contact-item-found' data-id='$ID'> 
                <b class='btnAddContact2' data-id='$ID'> 
                    <i class='fa fa-user-plus '></i>
                    Add 
                </b>
                <i class='fa fa-circle $onlineClass'></i>
                $username 
                </br>
                ($email)
              </li>
              ";
      }
      
      return $strToReturn;
  }  
}

?>