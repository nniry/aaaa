<?php
include_once("db.php");
require "misc.php";

function save_session($username)
{
    $session_id = generate_random_string(32);
    run_sql_query("INSERT INTO sessions VALUES ($1, $2)", $username, $session_id);
    return $session_id;
}

function get_user_by_session($session_id)
{
    $session = run_sql_query("SELECT username FROM sessions WHERE session=$1", $session_id);
    if (empty($session)) {
        return $session;
    }

    return run_sql_query("SELECT * FROM users WHERE username=$1", $session[0]);
}
