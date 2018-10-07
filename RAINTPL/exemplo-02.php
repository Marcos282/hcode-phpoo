<?php

require_once ("./vendor/autoload.php");

error_reporting(E_ALL & ~E_NOTICE);

// namespace
use Rain\Tpl;
use PHPMailer\PHPMailer\PHPMailer;

// config
$config = array(
    "base_url"      => null,
    "tpl_dir"       => "tpl/",
    "cache_dir"     => "cache/",
    "remove_comments" => true,
    "debug"         => true, // set to false to improve the speed
);

Tpl::configure( $config );


// set variables
$var = array(
    "variable"  => "Hello World!",
    "bad_variable"  => "<script>alert('evil javascript here');</script>",
    "safe_variable"  => "<script>console.log('this is safe')</script>",
    "version"   => "3.0 Alpha",
    "menu"		=> array(
        array("name" => "Home", "link" => "index.php", "selected" => true ),
        array("name" => "FAQ", "link" => "index.php/FAQ/", "selected" => null ),
        array("name" => "Documentation", "link" => "index.php/doc/", "selected" => null )
    ),
    "week"		=> array( "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday" ),
    "user"		=> (object) array("name"=>"Rain", "citizen" => "Earth", "race" => "Human" ),
    "numbers"	=> array( 3, 2, 1 ),
    "bad_text"	=> 'Hey this is a malicious XSS <script>alert("auto_escape is always enabled");</script>',
    "table"		=> array( array( "Apple", "1996" ), array( "PC", "1997" ) ),
    "title"		=> "Rain TPL 3 - Easy and Fast template engine",
    "copyright" => "Copyright 2006 - 2012 Rain TPL<br>Project By Rain Team",

);

// draw
$tpl = new Tpl;
$tpl->assign( $var );
$tpl->assign( nome, "Marcos Sousa" );
$tpl->assign( versao, PHP_VERSION );


$html = $tpl->draw("index" ,true);





//require '../vendor/autoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 2;

//Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;

//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = "novahost@gmail.com";

//Password to use for SMTP authentication
$mail->Password = "0000000000000000000";

//Set who the message is to be sent from
$mail->setFrom('marcos@novainfo.com.br', 'Marcos Sousa');

//Set an alternative reply-to address
$mail->addReplyTo('no-reply@novainfo.com.br', 'Marcos Sousa');

//Set who the message is to be sent to
$mail->addAddress('marcos.aurelio@novainfo.com.br', 'Marcos Sousa');

//Set the subject line
$mail->Subject = 'Testando email com o gmail';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML($html);

//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';

//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
    //Section 2: IMAP
    //Uncomment these to save your message in the 'Sent Mail' folder.
    #if (save_mail($mail)) {
    #    echo "Message saved!";
    #}
}

//Section 2: IMAP
//IMAP commands requires the PHP IMAP Extension, found at: https://php.net/manual/en/imap.setup.php
//Function to call which uses the PHP imap_*() functions to save messages: https://php.net/manual/en/book.imap.php
//You can use imap_getmailboxes($imapStream, '/imap/ssl') to get a list of available folders or labels, this can
//be useful if you are trying to get this working on a non-Gmail IMAP server.
function save_mail($mail)
{
    //You can change 'Sent Mail' to any other folder or tag
    $path = "{imap.gmail.com:993/imap/ssl}[Gmail]/Sent Mail";

    //Tell your server to open an IMAP connection using the same username and password as you used for SMTP
    $imapStream = imap_open($path, $mail->Username, $mail->Password);

    $result = imap_append($imapStream, $path, $mail->getSentMIMEMessage());
    imap_close($imapStream);

    return $result;
}





//class Test{
//    static public function method( $variable ){
//        echo "Hi I am a static method, and this is the parameter passed to me: $variable!";
//    }
//}

// end

?>