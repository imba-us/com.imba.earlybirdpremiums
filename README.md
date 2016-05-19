# com.imba.earlybirdpremiums
<h3>Early Bird Premiums extension for CiviCRM</h3>

This extension adds the ability to display a premium on a contribution page only when a certain set of membership type and status criteria are met. This enables an organization to offer "member only" premiums for early renewal or other promotions. The extension only has parameters for membership status and type, and they can be used independently from each other. There is also the functionality to hide a different premium if the Early Bird premium is being shown, which is helpful if you want to run a promotion where a certain premium is being offered at a lower membership fee. Works with both logged in and checksum users.

This extension implements the following php overrides:
/templates/CRM/Contribute/Form/ManagePremiums.tpl

It is highly recommended that you use the Extension File Overrides extension to keep track of all template overrides:
https://civicrm.org/extensions/extension-file-overrides
