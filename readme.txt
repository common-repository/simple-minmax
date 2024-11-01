=== Simple MinMax  ===
Contributors: nicnetza
Tags: woocommerce, minimum, maximum, product limit, per product, per category maximum, per category minimum, hide category
Requires at least: 2.0
Tested up to: 6.6.1
Stable tag: 3.0.1
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A plugin to set the minimum and/or maximum :- order quantities on a WooCommerce product by product basis, and/or minimum and maximum quantity per category or value per category. The minimum and/or maximum per product values can optionally be displayed to the customer on the product page. The plugin also provides the option to hide a product category on the shop page.


== Description ==

This plugin extension for WooCommerce allows for the setting, on a product by product basis,  of the  minimum and/or maximum quantities that the product can be ordered (added to cart). This information can optionally be displayed on the product page. The minimum and/or maximum quanity per category or value per category can also be set. The plugin also provides the option to hide a product category on the shop page.

The plugin os provided AS IS, with no warranty.

== Configuration ==
There is only one setting to configure, whether to display the Maximum and/or Minimum Quatities for a product to the user.

In Worpress Admin Console, Select Plugins on the left.

Select Settings under the Simple MinMax Plugin.  

If no Settings option appears, this means that WooCommerce is not installed AND activated.

The Option allows to select whether to display the Minimum and/or Maximum quantities on the product page if they are configured for a product.


The Default option is Do Not Display Min/Max - this means nothing will be displayed


Option Above The Add To Cart Button, will display any Minimum and/or Maximum information Above the Add To cart Button 

Option Under The Add To Cart Button, will display any Minimum and/or Maximum information Under the Add To cart Button 

= Setting Min/Max Per Product =
To add a Minimum and/or Maximum quantity to a product, go to WooCommerce Products and select inventory. 

A box will be displayed to optionally set a Minimum or Maximum add to cart quantity.

= Setting Min/Max Per Category =
When creating a new category or editing a ctaegory, the Minimum and Maximum order value or quanity can be set. Ideal for limiting the number or value of free samples etc!

== Frequently Asked Questions ==

= There is no Settings Option for the Simple MinMax Plugin. =
This means WooCommerce is not installed or not activated. Please install and activate WooCommerce.  

= Minimum/Maximum Quantity Logic. =
The plugin dose not verify that the minimum quantity is less than the maximum quantity and vice versa. This logic is left to the user.

= Is there multi-language support for Simple MinMax Plugin. =
Unfortunately not at the moment!

= How can the style of the Minimum and Maximum input boxes be set =
A div of class smm_options_group is used, so this can be styled with CSS.

= How can the style of the Minimum and Maximum quantities displayed to the user on the product page be set =
A div of class smm_quantity_content is used, so this can be styled with CSS.

== Changelog ==

= 3.0.1 = 2024-08-20

**Simple MinMax**
  * Add - Wordpress 6.6.1

= 3.0.0 =

**Simple MinMax**
  * Some minor bug fixes
  * More vigorous sanitizing, escaping, and validating
  * Dynamic currency symbol, delimiter and precession

= 2.1.3  2023-08-10 =

**Simple MinMax**
  * Add - Wordpress 6.3

= 2.1.2  2023-01-23 =

**Simple MinMax**

  * Add - Wordpress 6.1.1
  * Fix - contact details

= 2.1.1  2022-10-18 =

**Simple MinMax**

  * Add - Wordpress 6.0.3

= 2.1.0  2022-08-26 =

**Simple MinMax**

= 2.1.0 =
  * Add - Category Do Not Display On Shop Page (for Monique)
  * Add - Wordpress 6.0.2

= 2.0.0  2022-08-26 =

**Simple MinMax**

  * Add - Minimum/Maximum Category Value (for Monique)
  * Add - Minimum/Maximum Category Quantity

= 1.1.0  2022-07-20 =

**Simple MinMax**

  * Add - Updated documentation
  * Fix - variable checking

= 1.0.0  2022-07-10 =

**Simple MinMax**
  * Initial Release

