<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login/index.php");
    exit;
}
