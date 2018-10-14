<?php

namespace Hcode;

use Rain\Tpl;

use PHPMailer\PHPMailer;

class Mailer {
    
    private $mail;


    public function __construct($toAddress,$toName,$Subject,$tplName,$data = array()) {
        
        
          
	$config = array(
            "tpl_dir"    => BASEDIR . "/views/email/",
            "cache_dir"  => BASEDIR . "/views-cache/",
            "debug"      => false // set to false to improve the speed
	);

	Tpl::configure( $config );        
        
        $tpl =  new Tpl;
        
        foreach ($data as $key => $value) {
            
            $tpl->assign($key,$value);
            
        }
        
        $html = $tpl->draw($tplName,true);
        
        //Create a new PHPMailer instance
        $this->mail = new \PHPMailer;

        //Tell PHPMailer to use SMTP
        $this->mail->isSMTP();

        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->mail->SMTPDebug = 0;

        //Set the hostname of the mail server
        $this->mail->Host = 'smtp.gmail.com';
        // use
        // $this->mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6

        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $this->mail->Port = 587;

        //Set the encryption system to use - ssl (deprecated) or tls
        $this->mail->SMTPSecure = 'tls';

        //Whether to use SMTP authentication
        $this->mail->SMTPAuth = true;

        //Username to use for SMTP authentication - use full email address for gmail
        $this->mail->Username = LOGIN_EMAIL;

        //Password to use for SMTP authentication
        $this->mail->Password = PASSWORD_EMAIL;

        //Set who the message is to be sent from
        $this->mail->setFrom(LOGIN_EMAIL, NAME_FROM);

        //Set an alternative reply-to address
        $this->mail->addReplyTo('no-reply@novainfo.com.br', 'Marcos Sousa');

        //Set who the message is to be sent to
        $this->mail->addAddress($toAddress, $toName);

        //Set the subject line
        $this->mail->Subject = $Subject;

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $this->mail->msgHTML($html);

        //Replace the plain text body with one created manually
        $this->mail->AltBody = 'This is a plain-text message body';

        //Attach an image file
        //$this->mail->addAttachment('images/phpmailer_mini.png');

        //send the message, check for errors
//        if (!$mail->send()) {
//            echo "Mailer Error: " . $mail->ErrorInfo;
//        } else {
//            echo "Message sent!";
//            //Section 2: IMAP
//            //Uncomment these to save your message in the 'Sent Mail' folder.
//            #if (save_mail($mail)) {
//            #    echo "Message saved!";
//            #}
//        }

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


        
        
    }
    
    public function send(){
        
        return $this->mail->send();
        
    }
    
}

?>