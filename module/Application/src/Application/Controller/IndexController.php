<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class IndexController extends AbstractActionController
{
    private $numtobeguessed;
    private $guess;
    private $count = 0;
    private $message = "";
    private $statusMessage = "";
    public function indexAction()
    { 
        $session = new Container('base');
        $vm = array();
        $request = $this->getRequest();
        $guessError = "";
        if ($request->isPost()) {
            $this->guess = $request->getPost('guess');//get guess post param
            $this->numtobeguessed = $session->offsetGet('numtobeguessed');//get numtobeguessed session value
            $this->count = $session->offsetGet('count');//get count session value
            if(empty($this->guess)) {
                $guessError = "Guess is required";
            } else {
                $this->gameCheck("session");
            }
        } else {
            $this->numtobeguessed = rand(1,10);//generating random number from 1 to 10
            $session->offsetSet('numtobeguessed',$this->numtobeguessed);
            $session->offsetSet('count',$this->count);
        }
        $vm['guess'] = $this->guess;
        $vm['numtobeguessed'] = $this->numtobeguessed;
        $vm['count'] = $this->count;
        $vm['guessError'] = $guessError;
        $vm['statusMessage'] = $this->statusMessage;
        $vm['message'] = $this->message;
        return new ViewModel($vm);
    }
    
    public function hiddenAction()
    {        
        $vm = array();
        $request = $this->getRequest();
        $guessError = "";
        if ($request->isPost()) {
            $this->guess = $request->getPost('guess');//get guess post param
            $this->numtobeguessed = $request->getPost('numtobeguessed');//get numtobeguessed post param
            $this->count = $request->getPost('count');//get count post param
            if (empty($this->guess)) {
                $guessError = "Guess is required";
            } else {
                $this->gameCheck();
            }
        } else {
            $this->numtobeguessed = rand(1,10);//generating random number from 1 to 10
        }
        $vm['guess'] = $this->guess;
        $vm['numtobeguessed'] = $this->numtobeguessed;
        $vm['count'] = $this->count;
        $vm['guessError'] = $guessError;
        $vm['statusMessage'] = $this->statusMessage;
        $vm['message'] = $this->message;
        return new ViewModel($vm);
    }
    
    public function cookieAction()
    {        
        $vm = array();
        $request = $this->getRequest();
        $guessError = "";
        $this->message = "";
        $this->statusMessage = "";
        if ($request->isPost()) {
            $this->guess = $request->getPost('guess');//get guess post param
            $this->numtobeguessed = $_COOKIE["numtobeguessed"];//get numtobeguessed cookie value
            $this->count = $_COOKIE["count"];//get count cookie value
            if (empty($this->guess)) {
                $guessError = "Guess is required";
            } else {
                $this->gameCheck("cookie");
            }
        } else {
            $this->numtobeguessed = rand(1,10);//generating random number from 1 to 10
            //setting numtobeguessed and count cookie
            setcookie("numtobeguessed", $this->numtobeguessed, time() + ( 60 * 60), "/", false);
            setcookie("count", $this->count, time() + ( 60 * 60), "/", false);
        }
        $vm['guess'] = $this->guess;
        $vm['numtobeguessed'] = $this->numtobeguessed;
        $vm['count'] = $this->count;
        $vm['guessError'] = $guessError;
        $vm['statusMessage'] = $this->statusMessage;
        $vm['message'] = $this->message;
        return new ViewModel($vm);
    }    

    public function gameCheck($type = ""){
        //checking the guess count and guess value
        if (isset($this->count) && $this->count < 3 && isset($this->guess)) {
            $diff = 0;
            $msgCount = "";

            if ($this->guess > $this->numtobeguessed ) { //greater than
                $diff = $this->guess - $this->numtobeguessed ;
            } else if ($this->guess < $this->numtobeguessed ) { //less than
                $diff = $this->numtobeguessed  - $this->guess ;
            }

            if ($this->count == 0) {
                $msgCount = "first";
            } else if ($this->count == 1) {
                $msgCount = "second";
            } else {
                $msgCount = "last";
            }

            $this->message = "Your ". $msgCount ." guess is: ".$this->guess;
            $this->count++;
            if ($type == "cookie") {
                //updating guess count cookie value
                setcookie("count", $this->count, time() + ( 60 * 60), "/", false);
            } else if ($type == "session") {
                $session = new Container('base');
                //updating guess count session value
                $session->offsetSet('count',$this->count);
            }
            
            if ($diff >= 3 ) {//guess is 3 or more away from the correct answer
                $this->message .= " (cold)";
            } else if ($diff == 2) {//guess is 2 away
                $this->message .= " (warm)";
            } else if ($diff == 1) {//guess is 1 away
                $this->message .= " (hot)";
            } else {// user guess the correct number
                $this->statusMessage = "Right! You have won the game";
            }
            //user fails to enter the correct number in three guesses
            if ($diff > 0 && $this->count == 3) {
                $this->statusMessage = "You have lost the game";
            }
            if ($this->statusMessage != "") {//unset if win / fails
                if ($type == "cookie") {
                    //unset cookies if win / fails
                    unset($_COOKIE["count"]);
                    unset($_COOKIE["numtobeguessed"]);
                } else if ($type == "session") {
                    //unset session if win / fails
                    $session->offsetUnset('count');
                    $session->offsetUnset('numtobeguessed');
                }
            }
        }
    }
}
