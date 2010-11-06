<?php
class form {
  
  private $fields = array();
  private $requied = array();
  private $table = false;
  private $email = false;
  private $error = false;
  private $subject = 'Form Submission';
  private $thankyou = 'Your infomration has been successfully submitted';
  private $field_id = 1;
  private $request_variables = array();
  
  
  function __construct() {
    // Nothing... YET
  }
  
  
  function database($table) {
     $this->table = $table;
  }
  
  
  function email($to,$subject) {
      $this->email = $to;
      $this->subject = $subject;
  }
  
  
  function success($message) {
      $this->thankyou = $message;
  }
    
    
  function add($field_type,$db_column,$label,$default_value,$required=false,$error='',$add_text,$options) {
    $this->fields[$this->field_id] = array('type'=>$field_type,'column'=>$db_column,'label'=>$label,'value'=>$default_value,'required'=>$required,'error_msg'=>$error,'add_text'=>$add_text,'options'=>$options);
    if($required) { $this->required[] = $this->field_id; }
    $this->field_id++;
  }
  
  
  function add_by_array($array) {
    $this->fields[$this->field_id] = $array;
    if($array['required']) { $this->required[] = $this->field_id; }
    $this->field_id++;
  }
  
  
  function content($content) {
    $this->fields[$this->field_id] = array('type'=>'content','value'=>$content);
    $this->field_id++;
  }
  
  
  function build($action='',$method='post',$enctype='multipart/form-data',$name='form',$id='form') {
    if(empty($action)) { $action = $_SERVER['PHP_SELF']; }
    
    $request_array = 'int_get_'.strtolower($method);
    $this->request_variables = $this->$request_array();
    
    if(count($this->request_variables) == 0 || !$this->int_validate()) {
       $this->int_build($action,$method,$enctype,$name,$id);
    } else {
        $this->int_send();
    }
  }
  
  // END USER ACCESSIBLE FUNCTIONS, START INTERNAL FUNCTIONS
  
  
  private function int_validate($method) {
    $send = true;
    $this->error = '';
    
    foreach($this->fields as $key=>$field) {
      if(in_array($key,$this->required) && empty($this->request_variables[$key])) {
        $send = false;
        $this->error .= (isset($field['error_msg']) && !empty($field['error_msg'])?$field['error_msg']:'Please fill in "'.$field['label'].'"').'<br />';
      }
    }
    return $send;
  }


  private function int_send($method) {
    
    if($this->table) {
        $columns = array();
        $insert = array();
        $values = array();
        mysql_query('CREATE TABLE IF NOT EXISTS '.$this->table);
        $description = mysql_query('DESCRIBE '.$this->table);
        while($col = mysql_fetch_array($description)) {
            $columns[] = $col['Field'];
        }
        
        foreach($this->fields as $key=>$field) {
         if($field['column'] !== false) {
          if(empty($field['column'])) {
            $field['column'] = preg_replace('[^a-z]','_',$field['label']); 
         } 
          if(!in_array($field['column'],$columns)) {
            mysql_query('ALTER '.$this->table.' ADD '.$field['column'].' TEXT');
          }
          
          $insert[] = $field['column'];
          $value[] = mysql_real_escape_string((is_array($this->request_variables[$key])?implode(', ',$this->request_variables[$key]):$this->request_variables[$key]));
        }

        mysql_query('INSERT INTO '.$this->table.' ('.implode(',',$insert).') VALUES ("'.implode(',"',$value).'")');
       }
    }
        
    if($this->email) {
      $from = '';
      $msg .= '';
      foreach($this->fields as $key=>$field) {
        if(preg_match('/email/i',$field['label'])) { $from = $this->request_variables[$key]; }
        $msg.= $field['label'].': '.(is_array($this->request_variables[$key])?implode(', ',$this->request_variables[$key]):$this->request_variables[$key])." \n \n";
      }

      mail($this->email,$this->subject,$msg,'FROM: <'.$from.'>');
    } 
    
    echo '<div class="form_success">'.$this->thankyou.'</div>';
  }


  private function int_build($action,$method,$enctype,$name,$id) {
    if($this->error) { echo '<div class="form_error">'.$this->error.'</div>'; }
    
    echo '<form name="'.$name.'" id="'.$id.'" action="'.$action.'" method="'.$method.'" enctype="'.$enctype.'">';
    foreach($this->fields as $key=>$field) {
        echo '<div class="form_row">'.$this->int_field($key,$field).'</div>';
    }
    echo '<input type="reset" value="Reset Form" /><input type="submit" value="Submit Form" />';
    echo '</form>';
  }
  
  
  private function int_field($key,$field) {
    switch($field['type']) {
      case 'content':
        echo '<div class="form_content">'.$field['value'].'</div>';
      break;
      case 'text':
        echo '<div class="form_label">'.$field['label'].'</div>';
        echo '<div class="form_field"><input type="text" name="'.$key.'" value="'.(isset($this->request_variables[$key])?$this->request_variables[$key]:$field['value']).'" class="textbox" />'.$field['add_text'].'</div>';
      break;
      case 'password':
        echo '<div class="form_label">'.$field['label'].'</div>';
        echo '<div class="form_field"><input type="text" name="'.$key.'" value="" class="passwordbox" />'.$field['add_text'].'</div>';
      break;
      case 'textarea':
        echo '<div class="form_label">'.$field['label'].'</div>';
        echo '<div class="form_field"><textarea name="'.$key.'" class="textarea">'.(isset($this->request_variables[$key])?$this->request_variables[$key]:$field['value']).'</textarea>'.$field['add_text'].'</div>';
      break;
      case 'select':
        echo '<div class="form_label">'.$field['label'].'</div>';
        echo '<div class="form_field"><select name="'.$key.'" class="select">';
        $selected = (isset($this->request_variables[$key])?$this->request_variables[$key]:$field['value']);
        foreach($field['options'] as $ikey=>$ival) {
          echo '<option value="'.$ikey.'" '.($ikey == $selected?'selected':'').'>'.$ival.'</option>';
        }
        echo '</select>'.$field['add_text'].'</div>';
      break;
      case 'radio':
        echo '<div class="form_label">'.$field['label'].'</div>';
        echo '<div class="form_field">';
        $selected = (isset($this->request_variables[$key])?$this->request_variables[$key]:$field['value']);
        foreach($field['options'] as $ikey=>$ival) {
          echo '<input type="radio" name="'.$key.'" class="radio" value="'.$ikey.'" '.($ikey == $selected?'checked':'').' /> '.$ival.'<br />';
        }
        echo '</div>';
      break;
      case 'checkbox':
        echo '<div class="form_label">'.$field['label'].'</div>';
        echo '<div class="form_field">';
        $selected = (array)(isset($this->request_variables[$key])?$this->request_variables[$key]:$field['value']);
        foreach($field['options'] as $ikey=>$ival) {
          echo '<input type="checkbox" name="'.$key.'[]" class="checkbox" value="'.$ikey.'" '.(in_array($ikey,$selected)?'checked':'').' /> '.$ival.'<br />';
        }
        echo '</div>';
      break;
    }
  }
  
  
  private function  int_get_post() {
    return $_POST;
  }
  
  
  private function  int_get_get() {
    return $_GET;
  }
}
?>