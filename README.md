# Local plugin for moodle 4.1.3+ ( version: 2022112803.06)
User registers through a custom form

 # Startpage
 After installation,   open manually   **/local/christest/registerpage.php**  <ins>without being logged in</ins>  and fill the form
 
 # Note
 - On Form Success, you shoud receive a email with login credentials. For testing purposes, in case your STMP has problems, this message is also printed in browser.
 
 # Learning tips for moodle plugins in general.
   - Disabling cache is usefull to get_string()  when you use different languages https://docs.moodle.org/dev/Developer_Mode\
   - Opening Moodle files will cover all the things which are missing from docs(and they are a lot).
   - generated forms are styled with **bootstrap**  classnames, grids. etc.
   - Filenames are not just filenames. Parts of filenames may be treated as paths.
   - It's always good to check responsive support, even in responsive elements/components. It may needs  changes or extra css.
 
 # Sources and usefull links
 Video series:
 - https://www.youtube.com/watch?v=RED3SJYjkZU&list=PLnNniujrnp0mFwUNszRcI2OBCiBAh9Iqs&index=4 
 
 Plugin related Moodle docs:
  - https://moodledev.io/general/development/policies/codingstyle
  - https://moodledev.io/general/development/policies/codingstyle/frankenstyle
  
  - https://moodledev.io/docs/apis/commonfiles

  - https://moodledev.io/docs/apis/commonfiles/version.php

  - https://docs.moodle.org/dev/version.php 
  
  - https://moodledev.io/docs/apis/subsystems/admin 
  
  - https://moodledev.io/docs/apis/subsystems/form
  - https://github.com/moodle/moodle/blob/master/user/editadvanced.php
  - https://github.com/moodle/moodle/blob/master/user/editadvanced_form.php 
  
  - https://moodledev.io/docs/guides/templates
  - https://docs.moodle.org/dev/Element_HTML_and_CSS_guidelines
