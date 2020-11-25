<?php 

    $route->set([
    	'/join-to-pera-e/[:name :password]' => 'Login@create',
    	'/wallet-send'    => 'Wallet@create'
    ]);

