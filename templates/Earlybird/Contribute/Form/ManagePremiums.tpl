<fieldset id="time-delimited" class="crm-collapsible collapsed">
  <legend class="collapsible-title">{ts}Membership Status/Type Display{/ts}</legend>
  <div>
    <table class="form-layout-compressed">
      <tr class="crm-contribution-form-block-eb_is_active">
         <td class="label">{$form.eb_is_active.label}</td>
         <td class="html-adjust">{$form.eb_is_active.html}</td>
      </tr>
      <tr class="crm-contribution-form-block-eb_is_strict">
         <td class="label">{$form.eb_is_strict.label}</td>
         <td class="html-adjust">{$form.eb_is_strict.html}
          <span class="description">Hide premium when the selected price field does not match qualifying membership type(s).</span>
         </td>
      </tr>
      <tr class="crm-contribution-form-block-eb_membership_types">
         <td class="label">{$form.eb_membership_types.label}</td>
         <td class="html-adjust">{$form.eb_membership_types.html}<br />
            <span class="description">{ts}Select the membership types this premium should be displayed for.{/ts}</span>
         </td>
      </tr>
      <tr class="crm-contribution-form-block-eb_membership_statuses">
         <td class="label">{$form.eb_membership_statuses.label}</td>
         <td class="html-adjust">{$form.eb_membership_statuses.html}<br />
            <span class="description">{ts}Select the membership statuses this premium should be displayed for.{/ts}</span>
         </td>
      </tr>
      <tr class="crm-contribution-form-block-eb_hide_product">
         <td class="label">{$form.eb_hide_product.label}</td>
         <td class="html-adjust">{$form.eb_hide_product.html}<br />
            <span class="description">{ts}Hide the selected premium when this premium is displayed.{/ts}</span>
         </td>
      </tr>
    </table>
  </div>
</fieldset>
