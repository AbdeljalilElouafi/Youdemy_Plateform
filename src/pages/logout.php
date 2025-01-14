<?php

session_unset();
session_destroy();
header('Location: /Youdemy_Plateform/src/pages/login.php');
exit;