<?php
require_once 'view_base.php';

class ViewCreateAccount extends ViewBase
{
  public function __construct()
  {
    
  }
  
  public function get_output()
  {
    $output = 
      "
      <section class='create-account-window'>
        <h3 class='title'> Hi there! </h3>
        <h5 class='subtitle'> Tell us a little bit about you! </h5>
        <p> Choose a username </p>
        <div class='form-group'>
           <input type='text' placeholder='Username' class='form-control inputUsername' />
        </div>
        <p> Choose a password </p>
        <div class='form-group'>
           <input type='password' placeholder='Password' class='form-control inputPassword'/>
        </div>
        <p> Repeat the password </p>
        <div class='form-group'>
           <input type='password' placeholder='Password again' class='form-control inputPasswordAgain'/>
        </div>
        <p> Type your e-mail address </p>
        <div class='form-group'>
           <input type='email' placeholder='Email' class='form-control inputEmail'/>
        </div>
        <p class='btn btn-block btn-lg btn-inverse btnCreateAccount'> Create Account </p>
        <p class='or'> or </p>
        <p class='btn btn-block btn-lg btn-primary btnShowLogin'> Go to Login </p>
        
      </section>
      ";
    return $output;
  }
}

?>