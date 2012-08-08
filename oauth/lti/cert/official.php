<?php
if ( !isset($_SESSION['ims:official']) ) {
?>
<p style="color:red">
You <b>cannot</b> claim compliance to this specification unless you 
are an IMS member and pass the official compliance tests. 
<?php
    if ( strpos($_SERVER['HTTP_HOST'],"www.imsglobal.org") !== false ) {
        echo('(<a href="http://www.imsglobal.org/developers/alliance/LTI/cert-v1p1/">Log in</a>)</p>');
        include "../common/footer.php";
        exit();
    } else {
        echo('(<a href="http://www.imsglobal.org/cc/statuschart.cfm" target="_new">About this</a>)</p>');
    }
}
?>

