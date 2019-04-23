<?php
/**
 * A simple class for drawing balls and powerball(s)
 * @author Jackson Kambaragye
 */
require('config/config.php');
class RandomData {

    private $mainBall;
    private $powerBall;
    private $minMainBall;
    private $maxMainBall;
    private $minPowerBall;
    private $maxPowerBall;
    private $unique;
    public $results = array(
    'main_balls' => array(),
    'power_balls' => array()
    );

    public function __construct($mainBall,$powerBall, $minMainBall,$maxMainBall, $minPowerBall,$maxPowerBall,$results){
        $this->mainBall     = $mainBall;
        $this->powerBall    = $powerBall;
        $this->minMainBall  = $minMainBall;
        $this->maxMainBall  = $maxMainBall;
        $this->minPowerBall = $minPowerBall;
        $this->maxPowerBall = $maxPowerBall;
        $this->results      = $results;
    }
    #Get the MainBall.
    public function getMainBall(){

        if($this->mainBall==NULL){
            throw new Exception('mainBall must not be null!');
        }
        return $this->mainBall;
    }
    #Get the powerBall.
    public function getPowerBall(){
        return $this->powerBall;
    }
    #Get the MinMainBall.
    public function getMinMainBall(){
        if($this->maxMainBall==NULL){
            throw new Exception('maxMainBall must not be null!');
        }
        return $this->minMainBall;
    }
    #Get the MaxMainBall.
    public function getMaxMainBall(){
        if($this->maxMainBall==NULL){
            throw new Exception('maxMainBall must not be null!');
        }
        return $this->maxMainBall;
    }
    #Get the MinPowerBall.
    public function getMinPowerBall(){
        
        return $this->minPowerBall;
    }
    #Get the MaxPowerBall.
    public function getMaxPowerBall(){
        
        return $this->maxPowerBall;
    }
    #Generate main balls
    public function generateMainBall($mainBalls,$minMainBall,$maxMainBall,$draws){
 
        $i=0;
        while($i<$mainBalls){
            #Generate Main Balls
            $genMainBall = mt_rand($this->getMinMainBall(), $this->getMaxMainBall());
            if(!in_array($genMainBall, $draws['main_balls'])){
                array_push($draws['main_balls'], $genMainBall);
            }else{
                $i--;
            }
            $i++;
        }
      
        return $draws['main_balls'];
    }
    #Generate Powerball(s)
    public function generatePowerBall($powerBalls,$minPowerBall,$maxPowerBall,$draws,$generateMain){
        $i=0;
        while($i<$powerBalls){
            #Generate MainBalls
            $genPowerBall = mt_rand($this->getMinPowerBall(), $this->getMaxPowerBall());
            #insert the new random number to power ball array && the power ball number should be unique from the Main ball number 
            if(!in_array($genPowerBall, $draws['power_balls']) && !in_array($genPowerBall, $generateMain) ){
                array_push($draws['power_balls'], $genPowerBall);
            }else{
                $i--;
            }
            $i++;
        }

        return $draws['power_balls'];

    }
    public function displayOnScreen($draws){
       
        $drawNumber    = count($draws['main_balls']);
        $output = '';
        $output .='<div class="col">';
        for($i=$drawNumber-1;$i>0;$i--){
           
            $output .='<div id='.$i.' class="mx-auto w-100 p-4 bg-dark text-white text-right margin-top-2">';
            foreach ($draws['main_balls'][$i] as $key => $ball){
                    $output.='<div class="circle circle-text">'.$ball.'</div>';
            }
            foreach ($draws['power_balls'][$i] as $key => $power_ball){
                    $output.='<div class="circle powerball-text">'.$power_ball.'</div>';
            }
                    $output.='<div class="draw-time">'.$draws['draws_time'][$i].'</div>';
            $output .='</div>';
            $output .='<div class="clear">';
        } 
        $output.='</div></div>'; 
        echo $output;

    }
    public function draw($generateMain ,$generatePower){
        
        session_start();
        $draws = array(
            'main_balls' => array(),
            'power_balls' => array(),
            'draws_time' => array()
        );
         $draw = array(
            'main_balls' => array(),
            'power_balls' => array(),
            'draws_time' => array()
        );

        $time =str_replace(","," ",date("F d, Y h:i:s A"));
        if(!empty($generateMain) && !empty($generatePower))
            sort($generateMain);
            sort($generatePower);
            
            $_SESSION['main_balls'][]  = $generateMain;
            $_SESSION['power_balls'][] = $generatePower;
            $_SESSION['draws_time'][]  = $time;
           
            if(!in_array($generateMain,$_SESSION['main_balls']))
                array_push($_SESSION['main_balls'],$generateMain);
                $draws['main_balls']= $_SESSION['main_balls'];
             
            if(!in_array($generatePower,$_SESSION['power_balls']))
                array_push($_SESSION['power_balls'],$generatePower);
                $draws['power_balls']= $_SESSION['power_balls']; 
               
            if(!in_array($time,$_SESSION['draws_time']))
                array_push($_SESSION['draws_time'],$time);
                $draws['draws_time']= $_SESSION['draws_time']; 

            $count = count($_SESSION['main_balls']);
            #check if the last drawn number is 100
            if($count>=100){
                
                #Insert the Last 100 Winning Combination in the databases 
                $db    = config::getInstance();
                $conn  = $db->getConnection(); 
                $db->createLottoDrawTable();
                $table ="lotto_draws";

                for($i=0;$i<count($draws['main_balls']);$i++){
                    $m_ball = implode(' ',$draws['main_balls'][$i]);
                    $p_ball = implode(' ',$draws['power_balls'][$i]);
                    $d_time = $draws['draws_time'][$i];
                    
                    $sql    = "INSERT INTO lotto_draws (main_balls,power_balls,draw_time) VALUES('".$m_ball."','".$p_ball."','".$d_time."')";
                    mysqli_query($conn, $sql)or die(mysqli_error($conn));
                    //$count = (int) (mysqli_affected_rows($conn)); 
                    //echo  $count.' record inserted successfully.';
                    //echo $m_ball."--".$p_ball."->".$d_time."<br>";  
                }
                #unset session
                unset($_SESSION['main_balls']);
                unset($_SESSION['power_balls']);
                unset($_SESSION['draws_time']);

            }
            
        #Display the last 10 drawn combinations on the screen
        $arrMain  = array_slice($draws['main_balls'], -10); 
        $arrPower = array_slice($draws['power_balls'], -10); 
        $arrTime  = array_slice($draws['draws_time'], -10); 
        $draw['main_balls'] = $arrMain;
        $draw['power_balls'] = $arrPower;
        $draw['draws_time'] = $arrTime;
    
       return $draw;
    }

}

$mainBall     =  mt_rand(5, 7);
$powerBall    =  mt_rand(0, 3);
$minMainBall  =  mt_rand(1, 39);
$maxMainBall  =  mt_rand(40, 49);
$minPowerBall =  mt_rand(1, 4);
$maxPowerBall =  mt_rand(5, 49);
$draws        = array(
    'main_balls' => array(),
    'power_balls' => array()
    );
# Create a new Draw Object mc
$mc            = new RandomData($mainBall,$powerBall,$minMainBall,$maxMainBall,$minPowerBall,$maxPowerBall,$draws);
$main          = $mc->getMainBall();
$minMain       = $mc->getminMainBall();
$maxMain       = $mc->getmaxMainBall();
$power         = $mc->getPowerBall();
$minPower      = $mc->getminPowerBall();
$maxPower      = $mc->getmaxPowerBall();
$generateMain  = $mc->generateMainBall($main,$minMain,$maxMain,$draws);
$generatePower = $mc->generatePowerBall($power,$minPower,$maxPower,$draws,$generateMain );
$draw          = $mc->draw($generateMain,$generatePower);
# Display on Screen
$mc->displayOnScreen($draw);

?>