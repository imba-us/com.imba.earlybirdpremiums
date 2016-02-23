<?php

require_once 'earlybird.civix.php';

function earlybird_civicrm_buildForm($formName, &$form) {

  switch ($formName) {

    case 'CRM_Contribute_Form_ManagePremiums':

      $form->add('checkbox', 'eb_is_active', ts('Enabled?'));
      $form->add('checkbox', 'eb_is_strict', ts('Strict?'));

      $options = array();
      $result = civicrm_api3('MembershipType', 'get', array(
        'sequential' => 1,
        'options' => array(
          'limit' => 0,
          'sort' => 'name',
        ),
        'return' => 'id,name',
        'is_active' => 1,
      ));
      if (!$result['is_error']) {
        foreach($result['values'] as $type) {
          $options[$type['id']] = $type['name'];
        }
      }
      $form->add('select', 'eb_membership_types', ts('Membership Type(s)'), $options, FALSE, array('class' => 'crm-select2', 'multiple' => TRUE));
      
      $options = array();
      $result = civicrm_api3('MembershipStatus', 'get', array(
        'sequential' => 1,
        'options' => array(
          'limit' => 0,
          'sort' => 'name',
        ),
        'return' => 'id,name',
        'is_active' => 1,
      ));
      if (!$result['is_error']) {
        foreach($result['values'] as $status) {
          $options[$status['id']] = $status['name'];
        }
      }
      $form->add('select', 'eb_membership_statuses', ts('Membership Status(es)'), $options, FALSE, array('class' => 'crm-select2', 'multiple' => TRUE));

      CRM_Core_Region::instance('contribute-form-managepremiums-other-fields')->add(array(
        'template' => 'Earlybird/Contribute/Form/ManagePremiums.tpl',
        'name' => 'earlybird',
      ));

      $dao = new CRM_Earlybird_DAO_ProductSettings();
      $dao->pid = $form->getVar('_id');
      if ($dao->find(TRUE)) {
        $defaults = array(
          'eb_is_active' => $dao->is_active,
          'eb_is_strict' => $dao->is_strict,
          'eb_membership_types' => explode(',', $dao->membership_types),
          'eb_membership_statuses' => explode(',', $dao->membership_statuses),
        );
        $form->setDefaults($defaults);
      }

      break;

    case 'CRM_Contribute_Form_Contribution_Main':

      $memberships = array();
      
      if ($contactID = $form->getContactID()) {

        $result = civicrm_api3('Membership', 'get', array(
          'sequential' => 1,
          'options' => array(
            'limit' => 0,
          ),
          'return' => 'status_id,membership_type_id',
          'contact_id' => $contactID,
        ));
        if (!$result['is_error']) {
          $memberships = $result['values'];
        }
      }

      $price_fields = $types = array();

      $dao = new CRM_Price_DAO_PriceSetEntity();
      $dao->entity_id = $form->getVar('_id');
      $dao->entity_table = 'civicrm_contribution_page';

      if ($dao->find(TRUE)) {

        $result = civicrm_api3('PriceField', 'get', array(
          'sequential' => 1,
          'options' => array(
            'limit' => 0,
          ),
          'return' => 'id',
          'price_set_id' => $dao->price_set_id,
        ));
        if (!$result['is_error']) {

          $price_fields = $result['values'];
          foreach($price_fields as &$price_field) {
            $price_field['earlybird'] = '';
            $result = civicrm_api3('PriceFieldValue', 'get', array(
              'sequential' => 1,
              'options' => array(
                'limit' => 0,
              ),
              'return' => 'membership_type_id',
              'price_field_id' => $price_field['id'],
            ));
            if ($result['is_error'] || ($result['count'] == 0)) {
              continue;
            }
            foreach($result['values'] as $value) {
              if ($value['membership_type_id']) {
                $types[] = $value['membership_type_id'];
                $price_field['earlybird'] = 1;
              }
            }
          }
          $types = array_values(array_unique($types));
        }
      }

      $products = $earlybird = array();

      $_products = $form->get_template_vars('products');
      foreach($_products as $key => $product) {
        $dao = new CRM_Earlybird_DAO_ProductSettings();
        $dao->pid = $product['id'];
        $dao->is_active = 1;
        if ($dao->find(TRUE)) {
          $t = explode(',', $dao->membership_types);
          $s = explode(',', $dao->membership_statuses);
          $q = FALSE;
          foreach($memberships as $membership) {
            if (in_array($membership['membership_type_id'], $t) && in_array($membership['status_id'], $s)) {
              $q = TRUE;
              break;
            }
          }
          if (!$q) {
            continue;   // no qualifying membership
          }
          if ($dao->is_strict) {
            if (!array_intersect($types, $t)) {
              continue;   // no price set fields with the configured membership types
            }
            $earlybird['premium_id-' . $product['id']] = $dao->membership_types;
          }
        }
        $products[$key] = $product;
      }
      $form->assign('products', $products);

      if ($earlybird) {
        CRM_Core_Resources::singleton()->addScriptFile('com.imba.earlybirdpremiums', 'earlybird.js');
        CRM_Core_Resources::singleton()->addSetting(array(
          'earlybird' => array(
            'premiums' => $earlybird,
            'price_fields' => $price_fields,
          ),
        ));
      }

      break;
  }
}

function earlybird_civicrm_postProcess($formName, &$form) {

  if ($formName == 'CRM_Contribute_Form_ManagePremiums') {
    $values = $form->exportValues();

    $dao = new CRM_Earlybird_DAO_ProductSettings();
    $dao->pid = $form->getVar('_id');
    $dao->find(TRUE);
    $dao->is_active = !empty($values['eb_is_active']) ? 1 : 0;
    $dao->is_strict = !empty($values['eb_is_strict']) ? 1 : 0;
    $dao->membership_types = implode(',', $values['eb_membership_types']);
    $dao->membership_statuses = implode(',', $values['eb_membership_statuses']);
    $dao->save();
  }

}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function earlybird_civicrm_config(&$config) {
  _earlybird_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function earlybird_civicrm_xmlMenu(&$files) {
  _earlybird_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function earlybird_civicrm_install() {
  _earlybird_civix_civicrm_install();

  CRM_Core_DAO::executeQuery("CREATE TABLE IF NOT EXISTS `civicrm_product_earlybird` (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `pid` int(11) unsigned NOT NULL,
                              `is_active` tinyint(4) NOT NULL DEFAULT '0',
                              `is_strict` tinyint(4) NOT NULL DEFAULT '0',
                              `membership_types` varchar(255),
                              `membership_statuses` varchar(255),
                              PRIMARY KEY (`id`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function earlybird_civicrm_uninstall() {
  _earlybird_civix_civicrm_uninstall();

  CRM_Core_DAO::executeQuery("DROP TABLE `civicrm_product_earlybird`");
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function earlybird_civicrm_enable() {
  _earlybird_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function earlybird_civicrm_disable() {
  _earlybird_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function earlybird_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _earlybird_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function earlybird_civicrm_managed(&$entities) {
  _earlybird_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function earlybird_civicrm_caseTypes(&$caseTypes) {
  _earlybird_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function earlybird_civicrm_angularModules(&$angularModules) {
_earlybird_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function earlybird_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _earlybird_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function earlybird_civicrm_preProcess($formName, &$form) {

}

*/
