<?php
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function checkAdminAccess() {
    if (!isAdmin()) {
        header("Location: ../login.php");
        exit();
    }
}
?>