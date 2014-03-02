<?php
include "db/conn.php";
if ($result = mysqli_query($dbconn, "SELECT * FROM cal")) {
    printf("Select returned %d rows.\n", mysqli_num_rows($result));
    mysqli_free_result($result);
}
$dbconn->close();
?>