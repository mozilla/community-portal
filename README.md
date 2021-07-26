# Mozilla Community Portal 

This repo will contain all the theme files for the new Mozilla community portal.  

## Requirements
* Node version 8.9.3
* Running instance of Wordpress.  For development and deployment to wpengine purposes we are currently using wpe-devkit (https://wpengine.com/devkit/).  
* All the required wordpress plugins. (buddypress v4.4.0, eventmanager v5.9.5, advanced custom fields v5.8.2) 
* WPML is required if you will use the link of the theme, otherwise you need to remove manually the `/en/` to work

## Getting Started
1. Clone the repo into the wp-content/themes folder of the wordpress instance.  
2. Install all the node dependences by running the following command ```npm install```
3. Compile the assets or you cannot enable the theme

### Compile
To compile the sass files run ```npm run compile```

### Build
To build the project run the following command ```npm run build```

### Watch
To live update the styles run ```npm run watch```

### Activate
To activate the theme through the Wordpress admin panel.

### Wordpress
The following pages need to be created along with their corresponding slugs

Title: Activate  
URL slug: activate  

Title: Community Portal (This page should be set to the front page)

Title: Events  
URL slug: events  

Title: Categories  
URL slug: categories  
Parent Page: Events  

Title: Edit  
URL slug: edit-event  
Parent Page: Events  -- Set that page in Event Manager as editing for events

Title: Locations  
URL slug: locations  
Parent Page: Events  

Title: My Bookings  
URL slug: my-bookings  
Parent Page: Events  

Title: Tags  
URL slug: tags  
Parent Page: Events

TItle: Groups  
URL slug: groups  

Title: People  
URL slug: people  -- Take the page from Buddypress that is Members and rename it

Title: Register  
URL slug: register  

### Settings

Require various plugin settings and also to check the code about what is doing. The first step is required that you user accept the T&C or you cannot create events.

### Sample data

This is a MySQL dump with credential as admin/password with admin rights and plugins configured [community.tar.gz](https://github.com/mozilla/community-portal/files/6880233/community.tar.gz).