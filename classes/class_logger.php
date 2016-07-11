<?php
    class Logger
    {
        private $logDirectory = './logs/';
        
        public function __construct()
        {
            
        }
        
        public function log($message, $level = 0, $printInfo = true)
        {
            // Decomment if you want to clear the log for the current day
            //file_put_contents($this->get_log_file_path(), "");
            
            $arrLevels = array('DEBUG', 'DATABASE', 'IMPORTANT', 'ERROR');
            // Levels:
            // 0 - debugging
            // 1 - database communication
            // 2 - critical
            // 3 - error messages
            
            if($printInfo)
            {
                $actualMessage  = date('h:i:s').' ';
                $actualMessage .= $arrLevels[$level].' ';
                $actualMessage .= $message;
                //$actualMessage .= '     |'.$this->getCaller();
                $actualMessage .= "\n";
            }
            else
            {
                $actualMessage = $message."\n";
            }
            
            if(file_exists($this->get_log_file_path()))
            {
                file_put_contents($this->get_log_file_path(), $actualMessage, FILE_APPEND);
            }
            else
            {
                file_put_contents($this->get_log_file_path(), $actualMessage);
            }
            
        }
        
        private function get_log_file_path()
        {
            $today = date('Y-m-d');
            return $this->logDirectory.$today.'.txt';
        }
        
        private function getCaller()
        {
            return $this->generateCallTrace(1);
        }
        
        private function generateCallTrace($stackLength = 0)
        {
            $e = new Exception();
            $trace = explode("\n", $e->getTraceAsString());
            // reverse array to make steps line up chronologically
            $trace = array_reverse($trace);
            array_shift($trace); // remove {main}
            array_pop($trace); // remove call to this method
            
            if($stackLength == 0)
            {
                $length = count($trace);
            }
            else
            {
                $length = min($stackLength, count($trace));
            }
            
            $result = array();

            for ($i = 0; $i < $length; $i++)
            {
                //$result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' '));
                $arrPathComponents = explode('/', $trace[$i]);
                $result[] = $arrPathComponents[count($arrPathComponents) - 1];
            }

            return "\t" . implode("\n\t", $result);
        }
    }

?>