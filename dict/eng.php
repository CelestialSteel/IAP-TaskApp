<?php
  //english language

    $lang = array(
        'welcome_message' => 'Welcome to our website!',
        'login' => 'Login',
        'logout' => 'Logout',
        'register' => 'Register',
        'username' => 'Username',
        'password' => 'Password',
        'email' => 'Email',
        'submit' => 'Submit',
        'cancel' => 'Cancel',
        'search' => 'Search',
        'home' => 'Home',
        'about_us' => 'About Us',
        'contact_us' => 'Contact Us',
        'privacy_policy' => 'Privacy Policy',
        'terms_of_service' => 'Terms of Service',
        // Add more key-value pairs as needed
    );

      $lang['email_subject'] = 'Welcome to {{site_name}}!';
        $lang['email_body'] = 'Congratulations {{username}}, you have successfully registered at {{site_name}}.Please use this activation link to activate your account: {{activation_link}}.
         We are excited to have you on board!
         
         Regards,
         System Admin
         {{site_name}} Team'
         
         ;
         $lang['reset_password_subject'] = 'Password Reset Request';
    return $lang;