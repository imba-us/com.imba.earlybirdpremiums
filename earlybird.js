CRM.$(function($) {
  
  find_price_field = function(id) {
    var i, pf, fid;
    for(i = 0; i < CRM.earlybird.price_fields.length; i++) {
      pf = CRM.earlybird.price_fields[i];
      if (!pf.earlybird) {
        continue;
      }
      for(fid in pf.fields) {
        if (fid == id) {
          return pf.fields[fid];
        }
      }
    }
  }

  price_set_change = function(evt) {
    var pf, cls;
    pf = find_price_field(evt.val);
    cls = '.earlybird-' + pf.membership_type_id;

    $('.earlybird').hide();
    $(cls).show();

    id = $('#selectProduct').val();
    if (id != 'no_thanks') {
      sel = $('#premium_id-' + id);
      if (sel.hasClass('earlybird') && !sel.hasClass(cls)) {
        $('#premium_id-no_thanks .premium-short').trigger('click');
      }
    }
  }

  // classify the premiums

  for(id in CRM.earlybird.premiums) {
    types = CRM.earlybird.premiums[id].split(',');
    $prem = $('#' + id);
    $prem.addClass('earlybird');
    for(i = 0; i < types.length; i++) {
      $prem.addClass('earlybird-' + types[i]);
    }
  }

  // build/connect price set fields

  for(i = 0; i < CRM.earlybird.price_fields.length; i++) {
    pf = CRM.earlybird.price_fields[i];
    if (pf.earlybird) {
      pf.$ = $('#price_' + pf.id);
      pf.$.change(price_set_change);
      pf.fields = $.parseJSON(pf.$.attr('data-price-field-values'));
      price_set_change({val: pf.$.val()});
    }
  }

});