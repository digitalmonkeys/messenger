<?php
require_once 'view_base.php';

class ViewLoginScreen extends ViewBase
{
  public function __construct()
  {
    
  }
  
  public function get_output($message = null)
  {
    if(isset($message))
    {
      $message = "<h6 class='error-message'> </i> $message </h6>";
      $inputClasses = ' has-error';
    }
    else
    {
      $message = '';
      $inputClasses = '';
    }
    
    $output = 
      "
      <section class='login-window' style='opacity: 0'>
        <h3 class='title'> Hi there! </h3>
        <h5 class='subtitle'> Log in and join the fun! </h5>
        $message
        <p> Type your username </p>
        <div class='form-group $inputClasses'>
           <input type='text' placeholder='Username' class='form-control inputUsername' />
        </div>
        <p> Type your password </p>
        <div class='form-group $inputClasses'>
           <input type='password' placeholder='Password' class='form-control inputPassword'/>
        </div>
        <p class='btn btn-block btn-lg btn-primary btnLogin'> Login </p>
        <p class='or'> or </p>
        <p class='btn btn-block btn-lg btn-inverse btnShowCreateAccount'> Create Account </p>
      </section>
      ";
    return $output;
  }
}

?>