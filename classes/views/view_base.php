<?php 
    class ViewBase
    {
        public $logger;
        public function __construct()
        {
            
        }
        
        public function set_logger($logger)
        {
            $this->logger = $logger;
        }
    }

?>