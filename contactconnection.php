<?php
    $con=mysqli_connect("localhost","root","","demo_travel");

    if(isset($_POST['submit'])){

        $n=$_POST['Name'];
        $e=$_POST['Email'];
        $sub=$_POST['Subject'];
        $msg=$_POST['Message'];

        $ins="INSERT INTO contact SET name='$n',email='$e',sub='$sub',message='$msg'";
        if($con->query($ins))
       {
            header("location:contact.php");
       } 
    }
    else{
        echo "403 Forbidden";
    }
?>