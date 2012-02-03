<style type="text/css">

.form_error { width: 40%; margin: 0px auto; background: #f3f3f3; border: 1px solid #990000; color: #990000; font-weight: bold; padding: 10px;}

.form_success { width: 40%; margin: 0px auto; background: #f3f3f3; border: 1px solid #009900; color: #009900; font-weight: bold; padding: 10px;}

.form_label { width: 150px; float: left; margin-right: 10px;}

.form_field { float: left; }

.form_row { clear: both; border-bottom: 1px dotted #ccc; margin-bottom: 5px; padding-bottom: 5px; }
</style>

<?php
require_once('form.php');
$form = new form;

//            SEND FORM TO               SUBJECT
$form->email('email@emailaddress.com','Form Test');

//  FIELD TYPE   DB COLUMN   LABEL     DEFAULT VALUE  REQUIRED  REQUIRED MESSAGE    ADD TEXT     OPTIONS (ARRAY)
$form->content('<h2 style="margin-bottom: 0px;">Welcome to My Form</h2><span style="font-family: arial; font-size: 12px; color: #666;">8 Fields, 2 content blocks... 14 lines of code<br /></span>');
$form->add('text',false,'What\'s Your Name','Bob',true,'Please Fill in Your Name','*');
$form->add('text',false,'What\'s Your Email','',true,'Please Fill in Your Email','*<br /><small><em>note: this script will automatically detect this when sending the email!</em></small>');
$form->add('text',false,'What\'s Your Name','Bob3',false,'Please Fill in Your Name');
$form->add('text',false,'What\'s Your Name','Bob4',false,'Please Fill in Your Name');
$form->add('text',false,'What\'s Your Name','Bob5',false,'Please Fill in Your Name');
$form->add('select',false,'What\'s Your Name','Bob5',false,'Please Select in Your Name','*',array('Mike'=>'Mike','Bob'=>'Bob','John'=>'John','Bob5'=>'Bob5'));
$form->add('radio',false,'What\'s Your Name','Bob5',true,'Please Select in Your Name','*',array('Mike'=>'Mike','Bob'=>'Bob','John'=>'John','Bob5'=>'Bob5'));
$form->add('checkbox',false,'What\'s Your Name','',true,'Please Check Your Name','*',array('Mike'=>'Mike','Bob'=>'Bob','John'=>'John','Bob5'=>'Bob5'));
$form->content('<span style="color: #990000;">By clicking the submit button you are agreeing that this is the coolest form class ever...</span>');
$form->build();
?>