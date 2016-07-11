<?php
class DB_Manager 
{
  public $db;
  protected $logger;
    
  public function __construct($logger) 
  {
      $this->logger = $logger;
      $servername = "localhost";
      $username = "r44770di_digital";
      $password = "Dreamweaving1";

      // Create connection
      $this->db = new mysqli($servername, $username, $password);

      // Check connection
      if ($this->db->connect_error) 
      {
        die("Connection failed: " . $conn->connect_error);
      } 
    
      $this->db->select_db('r44770di_messenger');
  }
}
?>