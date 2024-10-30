=== Bubo Insights - Easy Website Traffic Analytics Plug in for WP ===
Contributors: pizza2mozzarella
Tags: wordpress analytics, website analytics, statistics, privacy, website stats
Requires at least: 6.0.0
Tested up to: 6.6
Stable tag: 1.0.13
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Bubo Insights is a privacy-friendly WordPress plugin for website traffic data monitoring and analytics.

== Description ==
Bubo Insights is a self-hosted, privacy-friendly WordPress plugin designed for an easy monitoring of website traffic data. It tracks and displays the most useful user navigation data without using cookies or violating privacy. 

The intuitive dashboard allows you to access all the core metrics to make your website more performing (**Page Visits**, **Visitors' devices**, **Referrers**, **Outgoing Clicks**).  Simple, useful, and effective, this plugin will be a game changer for you website traffic!

**Bubo Insights is completely free and does not require an external account.** Enjoy unlimited visitor tracking, and full data ownership – all directly stored in your WordPress database. 

All features described below are included in the free plugin.

= Top Features =
* Tracks Visitors, Page Visits, Referrers and Outgoing Clicks
* Easy to install even with no coding or technical knowledge
* Embedded in your WP website admin dashboard, no need to visit another website
* Data is self hosted and stored in your WP website database
* Simple, clean and responsive analytics dashboard
* No cookie banner needed as it is cookieless
* Privacy Friendly: doesn’t store user personal information

= Simple yet Detailed =
Is your website doing well? Are (real) people visiting it? Are you getting conversions?
To know all of that you had to face hard times placing custom everywhere in your website, consult tutorials, deal with confusing metrics, set cookie banners and getting headaches… !

With Bubo Insights you just press Install and you are ready to know what people do on your website without leading their privacy.

**Visitors' devices**, **Page Visits**, **Referrers** and **Outgoing Clicks** are the core metrics everyone should know about their website without too much hassle. That's why you should install Bubo Insights right now!

= Responsive and Clean Dashboard =
The dashboard is designed to give quick access to the most important information, no matter if you are on desktop or on a mobile device.

= Self Hosted, No External Accounts, Unlimited Tracking =
What people do on your website should be stored in your website and not on a third party cloud space, don’t you agree?

Bubo Insights stores all the data collected in your Wordpress database.

Only logged in editors (not just subscribers) of your website can access them through the plugin’s dashboard.

There are no imposed limits to the amount of visits or visitors that can be recorded, but please notice that the Wordpress database is not designed to hold several billions of entries without lagging a bit.

= Privacy Friendly by Design =
100% data ownership. Data is entirely created and stored on your server.

The only sensible data of the user that is being initially collected is the user IP address.

The IP address is merged with the User Agent into a string “(IP+UA)” and then hashed with a md5 algorithm to generate a unique and anonymous ID.

This plugin stores this unique anonymous user ID and completely discards the IP address.

There is no way to trace back the user not even with quantistic computing.

In case you need a proof of the data collection, you can easily export ALL the database tables with ALL the data collected by this plugin in the plugin’s settings page.

Moreover, this plugin doesn’t use third party cookies to store information about the user.


== Installation ==
To easily install Bubo Insights enter the WordPress dashboard and select **Plugins > Add New Plugin**. Search for "Bubo Insights" and install the first result you see there.
To install the zip file downloaded from this page:
1. Login to your WordPress dashboard
2. Visit the **Plugins > Add New Plugin**
3. Click the **Upload Plugin** button at the top
4. In the upload form that appears, click the **Choose file** button and select the **bubo-insights.zip** file you downloaded here
5. Click the **Install Now** button
6. Once the page reloads, click the blue **Activate** link
7. Purge your website cache.
Please make sure the Date and Time are set correctly in WordPress.


== Frequently Asked Questions ==
= Is Bubo Insights free? =
Yes! Bubo Insights’ core features are free.
= Does it use Google Analytics? =
No, Bubo Insights is an alternative to Google Analytics.
= Can I use Bubo Insights and Google Analytics at the same time? =
Yes, you can run them both at the same time without any problems.
= Do I need an account? =
No, you don’t need an account. No data is sent to another website.
= Is technical knowledge required to operate Bubo Insights? =
No, Bubo Insights is super simple to use. You don’t need any coding skills to use it. Right from your WordPress dashboard, you can install and use the plugin.
= Does tracking start right away? =
Yes, the moment you install Bubo Insights it will start tracking views. If you don't see any views right away, clear your site's cache and then visit your site in a private browser tab to record your first view. 
= What metrics does it track? =
It tracks Visitors, Visits, Referrers, Outbound link clicks.
= Is there a tracking code? =
Yes, but you don't have to add it yourself. It gets included on all of your site's pages automatically once Bubo Insights is activated.
= Will Bubo Insights affect my site’s performance? =
No, the tracking script is less than 3kb and it is embedded on all the website pages. So the difference in your site's performance after installing Bubo Insights will be virtually zero.
= Are bot visits counted? =
We filter out bot visits using best practice techniques.
= Are logged-in users visits tracked? =
Yes, all users visits are tracked. You can easily filter them out when you checking the website traffic analytics.
= Is there a limit to the number of visitors I can track? =
No, there is no limit. The only limiting factor is your own database and server.
= Where is the data stored? =
The data is stored in your own WordPress database.
= Can I export data? =
Data can be exported to CSV files.
= Can I give feedback about the plugin? =
Yes please! We really value your feedback. If you have any doubts, you can submit a support request on the WordPress forums, and we will respond promptly.

== Screenshots ==
1. Default Dashboard
2. Filtering by Monthly visits
3. Settings

== Changelog ==

= 1.0.13 =
* Adding cache of the filters used per user in the Stats
* Minor bugfixes

= 1.0.12 =
* Minor Bugfixes
* Clicks without a href attribute are no more recorded in database
* Paused a nonce control that made admin menu pages expiring early for no reason.

= 1.0.11 =
* Stats page redone from scratch with similar but improved faster UI based on jQuery UI
* Tracking code now is inline instead of an additional .js file for better cache plugins compatibility

= 1.0.10 =
* Added a tip in the chart
* Minor Bugfixes

= 1.0.8 =
* Minor Bugfixes

= 1.0.8 =
* Pausing Nonces
* Minor Bugfixes

= 1.0.7 =
* Added ABSPATH check on external scripts
* Fixed a bug that sometimes blocked navigation in wp admin pages

= 1.0.6 =
* Minor Bugfixes

= 1.0.5 =
* Adding Nonces
* Minor Bugfixes

= 1.0.4 =
* Minor Bugfixes

= 1.0.3 =
* Minor Bugfixes

= 1.0.2 =
* Revised to further comply WP.org guidelines (escaping, validation, sanitization)
* Moved Inline Css and Inline Scripts to external files
* Moved Inline admin area pages to external php files in partials
* Removed unused old code debris
* Removed unused database columns
* Improved stats page visual style
* Changed order of code blocks inside plugin, improved navigability with gerarchical zones through comments
* Other minor bugfixes and tiding up of code

= 1.0.1 =
* Revised to comply WP.org guidelines.

= 1.0 =
* Plugin released to the public.
* Added jQuery script to collect user data via AJAX call.
* Added the interface to see the statistics.

== Upgrade Notice ==
= 1.0 =
* Plugin released to the public. Discard all the alphas.