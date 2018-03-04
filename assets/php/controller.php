<?php

include_once 'dbTools.php';

function posts() {
    $tool = new dbTools ();
    $query = "SELECT
	c.username,
	c.title,
	d.COMMENT 
FROM
	( SELECT * FROM users a LEFT JOIN posts b ON a.id_users = b.createdBy ) c
	LEFT JOIN comments d ON c.id_posts = d.postId";
    $result = $tool->query($query);
    echo json_encode($result);
}

if ($_GET) {
    $f = $_GET ['action'];
    if (function_exists($f)) {
        $f();
    }
}
?>