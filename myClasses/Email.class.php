<?php 
namespace myClasses;

class Email{

    private $to; //email
    private $from; //email
    private $subject;
    private $body;
    private $cc; //array?
    private $bcc; //array?

    public function __construct(){

    }

    public function set_to(string $to) : Email{
        $this->to = $to;
        return $this;
    }

    public function get_to(){
        return $this->to;
    }

    public function set_from(string $from) : Email{
        $this->from = $from;
        return $this;
    }

    public function get_from(){
        return $this->from;
    }

    public function set_subject(string $subject) : Email{
        $this->subject = $subject;
        return $this;
    }

    public function get_subject(){
        return $this->subject;
    }

    public function set_body(string $body) : Email{
        $this->body = $body;
        return $this;
    }

    public function get_body(){
        return $this->body;
    }

    public function add_cc(string $cc) : Email{
        $this->cc[] = $cc;
        return $this;
    }

    public function remove_cc(string $cc){
        //CODE HERE
    }

    public function add_bcc(string $bcc) : Email{
        $this->bcc[] = $bcc;
        return $this;
    }

    public function remove_bcc(){
        //CODE HERE
    }

};