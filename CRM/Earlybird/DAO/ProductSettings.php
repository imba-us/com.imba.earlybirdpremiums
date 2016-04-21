<?php

require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';

class CRM_Earlybird_DAO_ProductSettings extends CRM_Core_DAO
{
  /**
   * static instance to hold the table name
   *
   * @var string
   */
  static $_tableName = 'civicrm_product_earlybird';
  /**
   * static instance to hold the field values
   *
   * @var array
   */
  static $_fields = null;
  /**
   * static instance to hold the keys used in $_fields for each field.
   *
   * @var array
   */
  static $_fieldKeys = null;
  /**
   * static instance to hold the FK relationships
   *
   * @var string
   */
  static $_links = null;
  /**
   * static instance to hold the values that can
   * be imported
   *
   * @var array
   */
  static $_import = null;
  /**
   * static instance to hold the values that can
   * be exported
   *
   * @var array
   */
  static $_export = null;
  /**
   * static value to see if we should log any modifications to
   * this table in the civicrm_log table
   *
   * @var boolean
   */
  static $_log = false;
  /**
   *  Id
   *
   * @var int unsigned
   */
  public $id;
  /**
   * Product Id
   *
   * @var int unsigned
   */
  public $pid;
  /**
   * Enabled?
   *
   * @var int unsigned
   */
  public $is_active;
  /**
   * Strict?
   *
   * @var int unsigned
   */
  public $is_strict;
  /**
   * Membership Type(s)
   *
   * @var string
   */
  public $membership_types;
  /**
   * Membership Status(es)
   *
   * @var string
   */
  public $membership_statuses;
  /**
   * Product to hide
   *
   * @var int unsigned
   */
  public $hide_product;

  function __construct()
  {
    $this->__table = 'civicrm_product_earlybird';
    parent::__construct();
  }
  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('ID') ,
          'description' => 'Id',
          'required' => TRUE,
        ),
        'pid' => array(
          'name' => 'pid',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Product ID') ,
          'description' => 'Product Id',
          'required' => TRUE,
        ),
        'is_active' => array(
          'name' => 'is_active',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Enabled?'),
          'description' => 'Enabled?',
          'default' => 0,
        ),
        'is_strict' => array(
          'name' => 'is_strict',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Strict?'),
          'description' => 'Strict?',
          'default' => 0,
        ),
        'membership_types' => array(
          'name' => 'membership_types',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Membership Types') ,
          'description' => 'Membership types',
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ),
        'membership_statuses' => array(
          'name' => 'membership_statuses',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Membership Statuses') ,
          'description' => 'Membership statuses',
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
        ),
        'hide_product' => array(
          'name' => 'hide_product',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Hide Product'),
          'description' => 'Hide Product',
          'default' => 0,
        ),
      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'pid' => 'pid',
        'is_active' => 'is_active',
        'is_strict' => 'is_strict',
        'membership_types' => 'membership_types',
        'membership_statuses' => 'membership_statuses',
        'hide_product' => 'hide_product',
      );
    }
    return self::$_fieldKeys;
  }
  /**
   * Returns the names of this table
   *
   * @return string
   */
  static function getTableName()
  {
    return self::$_tableName;
  }
  /**
   * Returns if this table needs to be logged
   *
   * @return boolean
   */
  function getLog()
  {
    return self::$_log;
  }
  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  static function &import($prefix = false)
  {
    return self::$_import;
  }
  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  static function &export($prefix = false)
  {
    return self::$_export;
  }
}
