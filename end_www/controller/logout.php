<?php

setcookie('hash','');
setcookie('uid','');
unset($_SESSION['user']);
header('location: ./?p=login');