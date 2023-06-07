<?php 

/**
 * @package   local_christest
 * @author    2023, Chrik_B
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* reading sources
 https://moodledev.io/docs/apis/subsystems/admin 
 https://moodledev.io/docs/apis/subsystems/form 
 https://github.com/moodle/moodle/blob/master/user/editadvanced_form.php 
 */


require_once($CFG->dirroot. '/lang/en/countries.php');
require_once("$CFG->libdir/formslib.php");


class registerForm extends moodleform {

    public function definition() {

        global $CFG;
       
        $mform = $this->_form; 

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'email', get_string('email'), array(
            'placeholder' => "enter your email"
        )); 
        $mform->setType('email', PARAM_NOTAGS);                   
     
           
        $mform->addElement('text', 'firstname', get_string('name'), array(
            'placeholder' => "enter your Name"
        ));  
        $mform->setType('firstname', PARAM_TEXT);                   
      

        $mform->addElement('text', 'lastname', get_string('surname', 'local_christest'), array(
            'placeholder' => "enter your surname"
        ));  
        $mform->setType('lastname', PARAM_TEXT);                   
  

        $mform->addElement('hidden', 'username', 'username', array(
            'disabled'=>true
        ));  
        $mform->setType('username', PARAM_NOTAGS);  


        $mform->addElement('hidden', 'auth', 'auth', array(
            'disabled'=>true
        ));  
        $mform->setType('auth', PARAM_NOTAGS);   
        $mform->setDefault('auth', 'email'); 


        $countries = get_string_manager()->get_list_of_countries();
        $mform->addElement('select', 'country', 'Country', $countries);


        $mform->addElement('text', 'phone2', 'Mobile', array(
            'placeholder' => "enter your mobile"
        ));  
        $mform->setType('phone2', PARAM_INT);                   
          


        $this->add_action_buttons(false, 'Register with Chris');
    }


    function validation($usernew, $files) {
        global $CFG, $DB;

        $usernew = (object)$usernew;

        $user = $DB->get_record('user', array('id' => $usernew->id));

        $err = array();

        if (empty(trim($usernew->country))) {
            $err['country'] = get_string('required');
        }

        if (empty(trim($usernew->email))) {
            $err['email'] = get_string('required');
        }


        if (empty(trim($usernew->firstname))) {
            $err['firstname'] = get_string('required');
        }
        if(preg_match('/[^a-zA-Z\s]/i',$usernew->firstname)){
            $err['firstname'] = 'only letters and spaces  are allowed';
        }


        if (empty(trim($usernew->lastname))) {
            $err['lastname'] = get_string('required');
        }
        if(preg_match('/[^a-zA-Z\s]/i',$usernew->lastname)){
            $err['lastname'] = 'only letters and spaces  are allowed';
        }


        $trimmedPhone = trim($usernew->phone2);
        if (empty($trimmedPhone)) {
            $err['phone2'] = get_string('required');
        }
        if(strlen($trimmedPhone) < 5){
            $err['phone2'] = 'phone needs minimum 5 digits';
        }


        if (!$user or (isset($usernew->email) && $user->email !== $usernew->email)) {
            if (!validate_email($usernew->email)) {
                $err['email'] = get_string('invalidemail');
            } else if (empty($CFG->allowaccountssameemail)) {
                // Make a case-insensitive query for the given email address.
                $select = $DB->sql_equal('email', ':email', false) . ' AND mnethostid = :mnethostid AND id <> :userid';
                $params = array(
                    'email' => $usernew->email,
                    'mnethostid' => $CFG->mnet_localhost_id,
                    'userid' => $usernew->id
                );
                // If there are other user(s) that already have the same email, show an error.
                if ($DB->record_exists_select('user', $select, $params)) {
                    $err['email'] = get_string('emailexists');
                }
            }
        }


        if (count($err) == 0) {
            return true;
        } else {
            return $err;
        }
    }
}
