Multistep Webform Customization using Drupal Webform Module
===========================================================

Overview
--------
This project implements a multistep form using the Drupal Webform module, customized extensively to meet specific client requirements. Key customizations include theming, access control, wizard progress tracking, and enhanced confirmation pages.

Features & Customizations
-------------------------

1. Custom Theming and Access Control
------------------------------------
- A distinct custom theme is created exclusively for the multistep webform.
- Only registered users can access the webform.
- Separate registration and login pages are implemented using:
  - 'multiple_registration' (contributed module)
  - 'role_login_settings' (contributed module)
- A custom theme negotiator is developed to apply the theme on specific pages:
  - File: 'diagnostic_toolkit/src/Theme/ThemeNegotiator.php'
  - Service registered in: 'custom/diagnostic_toolkit/diagnostic_toolkit.services.yml'
  - Tagged with: 'theme_negotiator'

2. Multistep Form Elements and Progress Tracking
------------------------------------------------
- Each step (wizard page) includes various form elements:
  - Webform Table
  - Webform Likert
  - Text areas
  - Radios, checkboxes, etc.
- The 'webform_navigation' module enables wizard progress and allows users to move forward and backward through pages.

3. Custom Progress Tracker Status Colors
----------------------------------------
- Wizard progress tracker is customized to display:
  - **Green** for fully completed steps
  - **Grey** for partially completed steps
- Custom logic is implemented to determine completion status:
  - A hidden status field is added to each wizard page.
  - Instead of javaScript functions I have implemented a custom Webform Handler plugin updates the status field during the pre-save phase:
    - Plugin: 'diagnostic_toolkit/src/Plugin/WebformHandler/UpdateWizardElementWebformHandler.php'
    - Registered using 'hook_update'
- Wizard progress bar is customized based on status values using:
  - 'hook_preprocess_HOOK': 
    - Function: 'diagnostic_toolkit_preprocess_webform_progress_tracker(&$variables)' in the '.module' file

4. Customized Webform Confirmation Page
---------------------------------------
- A custom block is implemented to show wizard page completion details on the confirmation page:
  - Block Plugin: 'diagnostic_toolkit/src/Plugin/Block/DiagnosticToolkitWebformPageStatusBlock.php'

Credits
-------
- Drupal Webform module and related contributed modules.
- Custom code developed as part of the 'diagnostic_toolkit' custom module.

