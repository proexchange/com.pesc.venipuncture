<?php

require_once 'venipuncture.civix.php';
use CRM_Venipuncture_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/ 
 */

function venipuncture_civicrm_config(&$config) {
  _venipuncture_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function venipuncture_civicrm_xmlMenu(&$files) {
  _venipuncture_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function venipuncture_civicrm_install() {
  _venipuncture_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function venipuncture_civicrm_postInstall() {
  _venipuncture_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function venipuncture_civicrm_uninstall() {
  _venipuncture_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function venipuncture_civicrm_enable() {
  _venipuncture_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function venipuncture_civicrm_disable() {
  _venipuncture_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function venipuncture_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _venipuncture_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function venipuncture_civicrm_managed(&$entities) {
  _venipuncture_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function venipuncture_civicrm_caseTypes(&$caseTypes) {
  _venipuncture_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function venipuncture_civicrm_angularModules(&$angularModules) {
  _venipuncture_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function venipuncture_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _venipuncture_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function venipuncture_civicrm_entityTypes(&$entityTypes) {
  _venipuncture_civix_civicrm_entityTypes($entityTypes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function venipuncture_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function venipuncture_civicrm_navigationMenu(&$menu) {
  _venipuncture_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _venipuncture_civix_navigationMenu($menu);
} // */

function venipuncture_civicrm_post($op, $objectName, $objectId, &$objectRef){
  if($op != 'create') return;
  if($objectName != 'Participant') return;

  $contact_id = $objectRef->contact_id;
  $event_id = $objectRef->event_id;
  $fee_level = $objectRef->fee_level;

  try {
    $event = civicrm_api3('Event', 'getsingle', [
      'id' => $event_id,
      'return' => [ 'event_type_id' ]
    ]);
    if($event['event_type_id'] != 9) return;
  }
  catch(CiviCRM_API3_Exception $e) {
    \Drupal::logger('com.pesc.venipuncture')->error($e->getMessage());
    return;
  }

  //Create radiologic membership if not already a member
  if(strpos($fee_level, 'Not a CSRT Member') !== false) {
    $result = civicrm_api3('Membership', 'get', [
      'sequential' => 1,
      'contact_id' => $contact_id,
      'active_only' => 1,
    ]);
    $has_membership = $result['count'] > 0 ? true : false;
    if(!$has_membership) {
      \Drupal::logger('com.pesc.venipuncture')->notice('Creating membership for Venipuncture Course participant, see details below:<br><pre>'.print_r($objectRef, 1).'</pre>'));
      $result = civicrm_api3('Membership', 'create', [
        'membership_type_id' => "Radiologic Technologist Member",
        'contact_id' => $contact_id,
      ]);
    }
    else {
      \Drupal::logger('com.pesc.venipuncture') 'Venipuncture Course participant already has a membership, see details below:<br><pre>'.print_r($objectRef, 1).'</pre>');
    }
  }
  //Create student membership != already has one
  if(strpos($fee_level, 'Not a CSRT Student Member') !== false) {
    $result = civicrm_api3('Membership', 'get', [
      'sequential' => 1,
      'contact_id' => $contact_id,
      'active_only' => 1,
    ]);
    $has_membership = $result['count'] > 0 ? true : false;
    if(!$has_membership) {
      \Drupal::logger('com.pesc.venipuncture') 'Creating membership for Venipuncture Course participant, see details below:<br><pre>'.print_r($objectRef, 1).'</pre>');
      $result = civicrm_api3('Membership', 'create', [
        'membership_type_id' => "RT or XT Student Member",
        'contact_id' => $contact_id,
      ]);
    }
    else {
      \Drupal::logger('com.pesc.venipuncture') 'Venipuncture Course participant already has a membership, see details below:<br><pre>'.print_r($objectRef, 1).'</pre>');
    }
  }
}
