# Mozilla Community Portal 

This repo will contain all the theme files for the new Mozilla community portal.  

## Setup Wordpress and Install the Theme

- Keep in mind that when you’re serving the site, your server needs to be running at the root of the Wordpress install. The URL in the address bar when you view the site should not contain any sub-directories.
- During development it’s recommended that you use a local URL.
	- NOTE: If you have Docker running you may need to quit it, as it may cause port conflicts with Local or your virtual host. 
- In a new directory, set up a fresh Wordpress install (wordpress.org)
- Go to the Mozilla community portal repo, and clone or download the theme files. They need to go into the `wp-content/themes` directory of the Wordpress install you just created.
- Navigate to this theme folder in your terminal, and run `npm install` to install dependencies.
- Run `npm run compile` to compile the .scss files and generate a `style.css` file for the theme, to live update the styles run `npm run watch`.
- Depending on how you are running Wordpress, you may need to create a new database for the site (or you can use the DB dump at the bottom of this readme). 
- Run the site in the browser. Depending on your set up, you may be lead through the typical Wordpress site setup.
- Login to the site as an administrator, and go to `appearance > themes` and activate the Mozilla theme. It has no preview screenshot. 

## Install Plugins
- Once the site is created, the following plugins need to be installed:
    - [Auth0](https://auth0.com/wordpress) (only on production)
    - [BuddyPress](https://buddypress.org/)
    - [Events Manager (for BuddyPress)](http://wp-events-plugin.com/)
    - [ACF Pro](https://www.advancedcustomfields.com/pro/) (free version works)
    - [WPML](https://wpml.org/) (works not always without but in case of error is enough to remove `/en/` from the URL)
    
## Create the required Pages
- Before creating pages, activate BuddyPress. This will automatically create some pages, and reduce those you need to manually create

| Page Name | URL Slug | Page Parent |
| --------- | -------- | ----------- |  
| Activate | `/activate` | n/a |
| Community Portal | set to front page in `Settings > Reading` | n/a |
| Events | `/events` | n/a |
| Categories | `/categories` | Events |
| Edit | `/edit` | Events | 
| Locations | `/locations` | Events |
| My Bookings | `/my-bookings` | Events |
| Tags |	`/tags` |	Events |
| Groups |	`/groups` |	n/a |
| Members |	`/people` |	n/a |
| Register |	`/register` |	n/a |

## Add the Theme Settings
- Go to `Mozilla Settings` in the left sidebar to add the following settings.

| Setting | Value |
| ------- | ----- |
| Report Group / Event Email | `<Your email>` |
| Github Link |	https://github.com/mozilla/community-portal |
| Community Portal Discourse |  |
| Mailchimp API Key	| `<a test API key>` |
| Company |	Mozilla |
| Address | |
| City | Toronto |
| State / Province |  |
| Postal / Zip |  |
| Country |  |
| Phone |  |
| Google Analytics ID |  | (only on production)
| Discourse API Key |	 | (only on production)
| Discourse API URL |	 | (only on production)
| Discourse URL |  (only on production)
| Mapbox Access Token |  | (only on production)
| Default Open Graph Title | Community Portal - Mozilla |
| Default Open Graph Description | Community Portal - Mozilla |
| Max Image Filesize Upload (KB) | 250 |
| 404 Error Title | Page not Found |
| 404 Error Copy | Oops, we got lost! |

## Set up Theme Menus
- Next set up the menus `Appearance > Menus`:
    - Create 4 new Menus with the followign items:
        - Main
          - Sign Up / Log Out (#)
          - Search (#)
        - Mozilla
          - About (#)
          - Mission (#)
          - Contact (#)
          - Donate (#)
        - Mozilla Main Menu
          - Campaigns (/campaigns)
          - Activities (/activities)
          - Events (/events)
          - Groups (/groups)
          - People (/people)
          - * Assign this menu to the Display locations of ‘Mozilla Custom Theme Menu’
        - Resources
          - Code of Conduct (#)
          - Privacy Policy (#)
          - Creative License (#)
          - Legal Notices (#)
### Settings

## Auth0 Setup (only production)
- Go to the Plugins page and activate the Auth0 plugin
- In 1password there is a `domain`, `client ID` and `client Secret` in the secure note - configure the plugin with this information.

## Setup BuddyPress
- Activate BuddyPress if you have not already
- Go to Plugins > BuddyPress > Settings
- Components Tab
  - everything except ‘Site Tracking’ should be enabled
- Options Tab
  - Set the template to ‘BuddyPress Legacy’
  - everything else here should be checked except for ‘Post Comments’
- Pages Tab
  - Set Members -> Members
  - Set Activity Streams -> Activity
  - Set User Groups -> Groups

## Setup ACF Pro (free version works)
- Activate the plugin
- Import the ACF fields
  - Go to `Custom Fields > Tools`
  - We’re going to import the field settings via a JSON import
  - In the theme files, there is a top-level directory called ACF, this contains JSON files with the info ACF needs to set up our fields
   - Under `Import Field Groups` choose the oldest file in this directory and import it.
   - Repeat this step for the remaining files, in chronological order.

Require various plugin settings and also to check the code about what is doing. The first step is required that you user accept the T&C or you cannot create events.

### Style rules enforcement

```
composer update # To download PHPCS
./vendor/bin/phpcs --standard=WordPress .
./vendor/bin/phpcbf --standard=WordPress .
```

### Sample data

This is a MySQL dump with credential as `admin/password` with admin rights and plugins configured [community.tar.gz](https://github.com/mozilla/community-portal/files/6901006/community.tar.gz) as host `community.test`.
