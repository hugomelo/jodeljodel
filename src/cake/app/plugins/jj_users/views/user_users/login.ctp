<?php
echo $session->flash('auth');    
echo $this->Form->create('UserUser', array('action' => 'login'));    
echo $this->Form->input('username');    
echo $this->Form->input('password');    
echo $this->Form->end('Login');