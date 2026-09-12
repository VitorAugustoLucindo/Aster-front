<?php
session_start();
unset($_SESSION['aster_jwt']);
header("Location: index.php");
exit;
