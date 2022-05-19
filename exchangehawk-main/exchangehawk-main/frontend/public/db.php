<?php

function run_sql_query($query, ...$params)
{
    $servername = $_ENV['POSTGRES_HOST'];
    $username = $_ENV['POSTGRES_USER'];
    $password = $_ENV['POSTGRES_PASSWORD'];

    $conn_string = "host=$servername port=5432 dbname=db user=$username password=$password";
    $dbconn = pg_connect($conn_string);

    $result = pg_fetch_row(pg_query_params($dbconn, $query, $params));
    return $result;
}

function fetch_rows($query, ...$params)
{
    $servername = $_ENV['POSTGRES_HOST'];
    $username = $_ENV['POSTGRES_USER'];
    $password = $_ENV['POSTGRES_PASSWORD'];

    $conn_string = "host=$servername port=5432 dbname=db user=$username password=$password";
    $dbconn = pg_connect($conn_string);

    $result = pg_fetch_all(pg_query_params($dbconn, $query, $params));
    return $result;
}
