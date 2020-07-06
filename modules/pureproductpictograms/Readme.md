##############################################################################
## HOW-TO GUIDE

1. Install the module
2. Place the desired pictograms in the /img/pureproductpictograms folder using
   an FTP client (you may need to create the folder).
3. Edit a product and add pictograms to it.
4. That's it, your product now has pictograms.
~5. If your current theme doesn't use the displayRightColumnProduct hook, or
   if you wish to place the pictograms somewhere else:
	a. Go to Modules -> Positions and remove pureproductpictograms from
	   the displayRightColumnProduct and/or displayProductAdditionalInfo hook.
	b. Using an FTP client, open your theme's product page :
    	   Prestashop < 1.7 :  /themes/YOUR_THEME/product.tpl
    	   Prestashop >= 1.7 : /themes/YOUR_THEME/templates/catalog/product.tpl
	   ... and paste the following code wherever you want the pictograms 
	   to be:
	   {hook h='displayPureProductPictograms'}
~6. If you wish to place the pictograms also on the product list page:
   Using an FTP client, open the corresponding theme's product-list page:
   Prestashop < 1.7 :  /themes/YOUR_THEME/product-list.tpl
   Prestashop >= 1.7 : /themes/YOUR_THEME/templates/catalog/_partials/miniatures/product.tpl
   ... and paste the following code wherever you want the pictograms to be:
   {hook h='displayPureProductPictograms' product=$product}

For detailed instructions, read the the readme PDF.


##############################################################################
## CHANGELOG

------------------------------------------------------------------------------
Version	: v1.5.1
Date	: 07-12-2018

- Changes to the translation system for Prestashop 1.6
------------------------------------------------------------------------------
Version	: v1.5.0
Date	: 19-10-2018

- Added an upload form for transferring pictograms directly from PrestaShop.
- Added an option for deleting pictograms.
- Added an option for defining whether a pictogram should be displayed 
depending on if there is any stock for the current product.
------------------------------------------------------------------------------
Version	: v1.4.3
Date	: 11-09-2018

- On multi-language shops, pictograms for the default language are now
displayed if no pictograms have been inserted for other languages.
------------------------------------------------------------------------------
Version	: v1.4.2
Date	: 29-07-2018

- Fixed an issue with pictograms not loading correctly if titles and/or links
weren't set.
------------------------------------------------------------------------------
Version	: v1.4.1
Date	: 03-07-2018

- Fixed an issue with pictograms not saving correctly and added better errors
handling.
------------------------------------------------------------------------------
Version	: v1.4.0
Date	: 18-06-2018

- Added multilanguage compatibility for pictogram titles and links.
------------------------------------------------------------------------------
Version	: v1.3.5
Date	: 28-05-2018

- Fixed an issue with pictogram filenames' encoding.
------------------------------------------------------------------------------
Version	: v1.3.4
Date	: 18-04-2018

- Transplanted the module to the displayProductAdditionalInfo hook on
Prestashop 1.7.x for default product page display.
------------------------------------------------------------------------------
Version	: v1.3.3
Date	: 16-04-2018

- Fixed a small visual issue in the backoffice product page.
- Added Prestashop 1.7.4 compatibility.
------------------------------------------------------------------------------
Version	: v1.3.2
Date	: 12-12-2017

- Fixed an issue with the backoffice not loading jQuery sortable on certain
server setups.
------------------------------------------------------------------------------
Version	: v1.3.1
Date	: 30-11-2017

- Fixed an issue with the latest version of Prestashop.
------------------------------------------------------------------------------
Version	: v1.3.0
Date	: 03-07-2017

- Added Prestashop 1.7 compatibility.
------------------------------------------------------------------------------
Version	: v1.2.2
Date	: 25-05-2016

- Fixed an issue when pictograms would not save when using different language
than store default.
------------------------------------------------------------------------------
Version	: v1.2.1
Date	: 22-04-2016

- Fixed an issue when pictograms would remain present after deletion.
------------------------------------------------------------------------------
Version	: v1.2.0
Date	: 05-04-2016

- Added support for Prestashop 1.5.6.2.
- Added support for PHP 5.3.
- New features: ability to give a title and a link to each pictogram.
- Fixed a bug when JS dependencies weren't loading correctly on a store
installed in a subdirectory.
- Fixed an issue when sorting pictograms didn't always save correctly.
------------------------------------------------------------------------------
Version	: v1.1.3
Date	: 10-12-2015

- Fixed a small issue in the code resulting in a warning on the product page.
------------------------------------------------------------------------------
Version	: v1.1.2
Date	: 02-11-2015

- Fixed an issue introduced by the previous update and preventing the
pictograms from saving correctly.
------------------------------------------------------------------------------
Version	: v1.1.1
Date	: 01-11-2015

- Fixed an issue with filenames containing special characters not displaying
correctly.
------------------------------------------------------------------------------
Version	: v1.1.0
Date	: 02-09-2015

- Added support for pictograms on the product list page.
- Fixed a bug when the admin stylesheet would load on front-office.
------------------------------------------------------------------------------