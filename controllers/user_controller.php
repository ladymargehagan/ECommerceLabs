<?php

require_once '../classes/user_class.php';

//Controller functions for user operations
function register_user_ctr($name, $email, $password, $phone_number, $role, $country, $city, $image = null)
{
    $user = new User();
    $user_id = $user->addUser($name, $email, $password, $phone_number, $role, $country, $city, $image);
    if ($user_id) {
        return $user_id;
    }
    return false;
}
//Controller function to get user by email
function get_user_by_email_ctr($email)
{
    $user = new User();
    return $user->getUserByEmail($email);
}