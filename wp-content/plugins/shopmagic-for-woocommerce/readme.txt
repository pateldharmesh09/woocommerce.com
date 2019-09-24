=== ShopMagic: Marketing Automation and Custom Email Designer for WooCommerce ===
Contributors: ristrettoapps
Tags: woocommerce,automation,workflows,email designer,abandoned cart,recent order popup, aweber,active campaign
Author URL: http://shopmagic.app/
Requires at least: 4.0
Tested up to: 4.9.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html



== Description ==
Design Custom Emails for WooCommerce | Add New Customers to Mailchimp, Aweber & Active Campaign | Delay WooCommerce Emails Days, Weeks, and Years  and lots more Magic Automations!

**NOTE: This plugin is no longer under active development. If you are interested in contributing to the maintenance of this project, please [contact us](http://ristrettoapps.com/contact/) and we will gladly add you as a contributor to this project.**

= Current Features: =

* Design custom emails based on any order status (eg. Processing, Completed, Failed)
* Review Request Emails: Automatically send an email after purchase requesting customer to leave reviews for products purchased, including links to each product (*Add-On*)
* Email Templates: Easily create an Thank You email & Review Request email
* Delayed Emails: Delay custom emails after purchase for a specified amount of time. (eg. 3 days after purchase) (*Add-On*)
* Add Customers to Mailchimp, Aweber, Active Campaign: Automatically subscribe new customers to your mailing list provider after purchase
* Personalized Customer Discounts: Give customers a unique, 1 time-only coupon code automatically after purchase (*Add-on*)
* Redirect to any page to upsell after specific product purchased

= Example Use Cases: =
* Add new customers to your Active Campaign list after they checkout
* Send customized thank-you emails to customers
* Send a review request email 3 days after their purchase
* Send an email based upon any order status
* Redirect to upset page after specific product purchased
* Send an email to potential customer after cart abandonment (coming soon)


> **Like The Idea? Help Us Improve It!!**<br /><br />
> Let us know of any issues, bugs, or concerns by creating a thread in the forum so we can respond to it in a timely >manner.

= What's ShopMagic Do? =
The goal of ShopMagic is to build a strong platform to empower their e-commerce business. We want to make it easy for shop owners to create powerful workflows based on a number of triggers or events in WooCommerce which can lead to an unlimited number of actions. Beyond that, ShopMagic allows you to replace the code-heavy WooCommerce email template system with your own custom HTML emails for order notifications to customers and admins using an easy email interface

= What's an event? =
An event is a trigger or an activity that takes place in WooCommerce. For instance, when a customer checks out, that's an event. When an order changes from pending to complete, that's an event.

= What's an action? =
An action is anything that you decide you want to happen following an event. For instance, maybe you want a specific email to go out to new customers 3 days after they checkout. This email would be setup as an action using you easy user interface.

= What's a filter? =
A filter is a number of conditions that you can include to ensure that the action you want to take place only occurs if the conditions are met. For example: a specific product purchased


== Installation ==
1. Install either via the WordPress.org plugin directory, or by uploading the files to your server.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to WooCommerce->Automations in your Wordpress admin area and add a new automation
1. Choose an event and an action for automation based on your preferences.

== Frequently Asked Questions ==

= I’ve setup automations to go out after a WooCommerce purchase, but I’m not receiving the automation emails =
If you’ve demonstrated that other system emails from your WordPress site are working, but ShopMagic emails are not, this may be due to email providers filtering the emails as spam.  However, you may be puzzled because other WordPress emails **are** being received! Some email providers recognize WordPress & WooCommerce system emails (due to their formatting) and will let them pass, but since they’ve not seen a ShopMagic system email, they hold is up in suspicion. That being said, this usually only happens when you haven’t properly taken the important steps to ensure your WordPress system emails are verified to send on behalf of your domain name.

**Solving the Problem**
First, get an idea for how your system emails are scoring in terms of spam by sending an email from your Wordpress site to a testing address at [mail-tester.com](https://www.mail-tester.com). If you’re getting low scores, this is likely why you’re not receiving ShopMagic emails.

Next, choose a method to improve your system email delivery method from among the following options:

1. Use a third party transactional email provider to send your WP system emails through. A few popular options are: [SendinBlue](https://www.sendinblue.com/), [MailGun](https://www.mailgun.com/), [Sparkhost](https://www.sparkpost.com/), [PostMark](https://postmarkapp.com/) (*Easiest*)
1. Setup your WordPress site to send out emails via your SMTP provider. This is a great option if you use Google Apps or another provider that can send emails on behalf of your domain name. You’ll need to install for an [SMTP plugin](https://wordpress.org/plugins/search.php?q=smtp) for this to work however (*Medium difficulty*)
1. Use the suggestions at [mail-tester.com](https://www.mail-tester.com) to improve your score by directly adding certain TXT records to through your domain name provider (*Hardest*)

= Achhhh. Why do I have to do extra work just for ShopMagic emails to stop getting suspected as spam? =
It’s actually a good thing! The fact that even some of your website’s system emails are being blocked by your email provider or landing in a spam folder says that you have a bigger problem that could affect your customers. By improving the validity of your website’s system emails going out, you’re helping to ensure that any WordPress or WooCommerce emails to your customers will be received. If you’ve properly done a [mail-tester.com](https://www.mail-tester.com) test by sending an email from your website to the address they’ve specified (not from your personal email client), and you’ve received a score of 5 or lower, chances are that many of your customers won’t consistently get emails from your WordPress site (whether they’re from ShopMagic or not)

= What's in store for the future? =
Simply more and more actions! We want to add as many different types of actions as possible so that ShopMagic becomes super helpful to you. We want to get heavy into marketing automation including stuff like instant upsells after purchase, personalized coupon generation, customer segmentation for mailing lists, and more.

== Changelog ==

= 1.3.1 =
* Fixed issue with text editor not being able to toggle between Visual and Text mode for automation emails
* Fixed bug that would sometimes prevent New Order automation from firing
* Fixed bug that prevented {{customer_first}} and {{customer_last}} placeholders from working when checkout is by guest instead of WP user
* Fixed issue with plugin generating white space in some admin files which caused issues with Updraft backup plugin
* Fixed bug that de-activated plugin when the View Details link was clicked for plugin on Plugins admin page

= 1.3.0 =
* -Added new Filters feature! Now you can add a condition that must be met before any action is taken
* -Added "Products Purchased" placeholder to the free version - now you can list all products purchased in your customized confirmation email
* -Improved deactivation survey feature
* -Improved Welcome screen to include more useful getting started information


= 1.2.5 =
* -Fixed formatting in pre-written email templates
* -Added optional deactivation survey to send back completely anonymous data to help us learn how we can improve the plugin

= 1.2.4 =
* -Fixed bug which prevented admin from choosing a product as an event to trigger automation
* -Updated links to correctly point to shopmagic.app for support and pro upgrades

= 1.2.3 =
* -Fixed small bug causing warnings on activation for some users

= 1.2.2 =
* -Fixed issue with not being able to name new automations

= 1.2.0 =
* -Updates to Readme - Change of Plugin Name
* -Updates to URLs

= 1.1.8 =
* -Fixes to welcome page and admin banners

= 1.1.7 =
* Fixed Add Media button issue
* Fixed Product Purchase in Draft mode issue - where products added wouldn’t stay unless published
* Email formatting improvements

= 1.1.6 =
* Fixed compatibility issue with Gantry 5 based themes (Helium and Hydrogen)

= 1.1.5 =
* Fixed media button issue on Send Email automations where media button did not bring up media browser
* Fixed issue where automation emails would not send to guests after checkouts
* Changed placeholder prefix to {{ customer_ from {{ user
* Added admin messages and pointers


= 1.1.3 =
* Further improvements to email sending compatibility for more server environments
* Fixed bug which displayed warnings on checkout confirmation for Guests if wp debug was enabled

= 1.1.3 =
* Further improvements to email sending compatibility for more server environments
* Fixed bug which displayed warnings on checkout confirmation for Guests if wp debug was enabled

= 1.1.2 =
* Fixed issue of emails not being sent consistently in all environmental setups

= 1.1.1 =
* Updated plugin description
* Fixed issue where emails weren’t sent out if guest checkout enabled

= 1.1.0 =
* Added Predefined Email Template block feature so that you can add prewritten text to your emails with ease
* Improved email formatting and fixed bugs
* Added live descriptions for events next to dropdown menu
* Added Welcome Page after activation

= 1.0.2 =
* Fixed plugin activation issue

= 1.0.1 =
* Fixed JS bug that automatically closed download link information in backend for orders

= 1.0.0 =
* Released first version
