<?php

namespace SampleProject;

class Auth
{
    public static function isEmployee() {
        return ($_SESSION['role'] == 'employee') ? true : false;
    }

    public static function isManager() {
        return ($_SESSION['role'] == 'manager') ? true : false;
    }

    // for this sample project, the default employeeId will be 1 
    // and the default managerId will be 4
    public static function getId() {
        if(self::isEmployee()) {
            return 1;
        }
        else {
            return 4;
        }
    }

    public function flagAsEmployee() {
        $_SESSION['role'] = 'employee';
    }

    public function flagAsManager() {
        $_SESSION['role'] = 'manager';
    }
}
