<?php
function adminer_object() {
    class AdminerSoftware extends Adminer {
        function name() {
            return 'Addresses';
        }
        function credentials() {
            require 'config.php';
            return array( $db_hostname, $db_database, $db_password );
        }
        function database() {
            return 'maps';
        }
        function login($login,$password) {
            require 'config.php';
            return( $login == 'admin' && $password == $web_password );
        }
    }
    return new AdminerSoftware;
}
    include 'editor-4.2.1-mysql-en.php';
?>
