<?php 

/**
 * @package   local_christest
 * @author    2023, Chrik_B
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* reading sources
 https://moodledev.io/docs/apis/subsystems/admin 
 https://github.com/moodle/moodle/blob/master/user/editadvanced.php
 */

require_once(__DIR__ . '/../../config.php');

require_once($CFG->libdir.'/gdlib.php');
require_once($CFG->libdir.'/adminlib.php');

require_once($CFG->dirroot.'/user/editadvanced_form.php');
require_once($CFG->dirroot.'/user/editlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/lib.php');

require_once($CFG->dirroot.'/webservice/lib.php');



// get user id from url
$id  = optional_param('id', $USER->id, PARAM_INT);    


require_once($CFG->dirroot .'/local/christest/classes/form/create.php'); 

$PAGE->set_url(new moodle_url('/local/christest/registerpage.php'),  array('id' => $id)); 
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('Register Form Page');


if (!isset($id) || $id == -1 || empty($id)) {

    $user = new stdClass();
    $user->id = -1;
    $user->auth = 'email';
    $user->password = '';

    $user->firstname = '';
    $user->lastname = '';   
    $user->email = '';
    $user->phone2 = '';    
    $user->country = 'GR';  

} else {

    $user = $DB->get_record('user', array('id' => $id), '*', MUST_EXIST);
    $user->firstname = json_encode($id);
}



$mform = new registerForm();


$templatecontext = [
    "userid"=> $id 
];

$mform->set_data($user);

if ($mform->is_cancelled()){
    

} else if($newUser = $mform->get_data()) {

    $usercreated = false;

    $newUser->timemodified = time();

    $isNewUser = false;
    if ($newUser->id == -1) {
        /* add new user */
        unset($newUser->id);
        unset($newUser->createpassword);

        $newUser->mnethostid = $CFG->mnet_localhost_id; // Always local user.
        $newUser->confirmed  = 1;
        $newUser->password = hash_internal_user_password('');

        $newUser->auth = "email";
        $authplugin = get_auth_plugin($newUser->auth);

        $newUser->username = strtolower($newUser->email);


        $newUser->id = user_create_user($newUser, false, false);


        $isNewUser = true;
    } else {
        /* update existing user password */
        user_update_user($newUser, false, false);
    }


    $newUser = $DB->get_record('user', array('id' => $newUser->id));

    if ($isNewUser === true) {
        /* info from  https://github.com/moodle/moodle/blob/master/lib/moodlelib.php   */
        ___setnew_password_and_mail___($newUser);
        unset_user_preference('create_password', $newUser);
        set_user_preference('auth_forcepasswordchange', 1, $newUser);

    }


    $templatecontext['Form_validation_success'] = "Email Sent!";
    $templatecontext['Form_validation_smtp_notice'] = "if 'Simple Mail Transfer Protocol' works, you should receive the following message(printed also here for testing purposes):";

} else {
    
    $templatecontext['Form_validation_error'] = 'Form submission failed';

}


echo $OUTPUT->header();

//$mform->display();

$templatecontext['formHTML'] = $mform->render();

echo $OUTPUT->render_from_template('local_christest/registerpage', (object)$templatecontext);



echo $OUTPUT->footer();








function ___setnew_password_and_mail___($user, $fasthash = false) {
    global $CFG, $DB;
    global $templatecontext;
    // We try to send the mail in language the user understands,
    // unfortunately the filter_string() does not support alternative langs yet
    // so multilang will not work properly for site->fullname.
    $lang = empty($user->lang) ? get_newuser_language() : $user->lang;

    $site  = get_site();

    $supportuser = core_user::get_support_user();

    $newpassword = generate_password();

    update_internal_user_password($user, $newpassword, $fasthash);

    $a = new stdClass();
    $a->firstname   = fullname($user, true);
    $a->sitename    = format_string($site->fullname);
    $a->username    = $user->username;
    $a->newpassword = $newpassword;
    $a->link        = $CFG->wwwroot .'/login/?lang='.$lang;
    $a->signoff     = generate_email_signoff();

    $message = (string)new lang_string('newusernewpasswordtext', '', $a, $lang);

    $subject = format_string($site->fullname) .': '. (string)new lang_string('newusernewpasswordsubj', '', $a, $lang);

    // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    $templatecontext['emailmessage'] = $message;

    return email_to_user($user, $supportuser, $subject, $message);

}