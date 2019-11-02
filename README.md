# Mozilla Community Portal

This repo will contain all the theme files for the new Mozilla community portal.

## Requirements
* Node version 8.9.3
* Running instance of Wordpress. For development and deployment to wpengine purposes we are currently using wpe-devkit (https://wpengine.com/devkit/).
* All the required wordpress plugins. (buddypress v4.4.0, events manager v5.9.5, advanced custom fields v5.8.2)

## Getting Started
1. Clone the repo into the wp-content/themes folder of the wordpress instance.
2. Install all the node dependencies by running the following command ```npm install```

### Compile
To compile the sass files run ```npm run compile```

### Build
To build the project run the following command ```npm run build```

### Watch
To live update the styles run ```npm run watch```

### Activate
To activate the theme through the Wordpress admin panel.

## Getting started with docker-compose

### Requirements

* Node version 8.9.3
* Docker and docker-compose
* No need to have a wordpress instance running, docker-compose will take care of that

### Start Wordpress instance

To start the necessary docker containers (Wordpress and database) run ```docker-compose up```.

### Set up Wordpress and BuddyPress

Once the containers are started, head to http://localhost:8000 and set up your Wordpress instance. Additionally install and activate the required plugins through Wordpress' interface directly.

Once this is done, you can run through the `Getting started` section above, but without cloning the repository (as docker-compose linked the file here into Wordpress' themes folder). This is only needed if you want to change any code.

### Stopping the Wordpress containers

To stop the containers, run ```docker-compose down```. This will delete all the containers, but it will preserve your database so these changes do not get lost.
