# Wordpress on Docker

Run the latest version of [WordPress](https://wordpress.org) with Docker and Docker Compose, providing the ability to manage your local environment efficiently.

## Philosophy

The WordPress on Docker project embraces a streamlined approach to deploying WordPress environments. Utilizing the power of Docker and orchestrated with docker-compose, this project integrates essential components, including Nginx, PHP, WordPress, and MySQL. Notably, the deployment includes the establishment of an SSL certificate using mkcert.

---

## Contents

- [Wordpress on Docker](#wordpress-on-docker)
  - [Philosophy](#philosophy)
  - [Contents](#contents)
  - [Requirements](#requirements)
    - [Host setup](#host-setup)
    - [Docker Desktop](#docker-desktop)
      - [Windows](#windows)
      - [macOS](#macos)
  - [Usage](#usage)
    - [Bringing up the stack](#bringing-up-the-stack)
    - [Using the WordPress-CLI](#using-the-wordpress-cli)
    - [Cleanup](#cleanup)
  - [Configuration](#configuration)
    - [How to configure the init file](#how-to-configure-the-init-file)
    - [How to configure the init update-databse file](#how-to-configure-the-init-update-databse-file)
  - [How to execute commands on the cosole](#how-to-execute-commands-on-the-cosole)

## Requirements

### Host setup

* [Docker Engine][docker-install] version **18.06.0** or newer
* [Docker Compose][compose-install] version **1.28.0** or newer (including [Compose V2][compose-v2])
* [mkcert][mkcert-install] Install mkcert on your host. Ensure to restart your Web browser after the installation.

> [!NOTE]
> Especially on Linux, ensure your user has the [required permissions][linux-postinstall] to interact with the Docker daemon.

By default, the stack exposes the following ports:

* 443: Nginx
* 8000: Nginx
* 8080: phpMyAdmin
* 3306: MySQL

### Docker Desktop

#### Windows

The environment has not been tested on Windows yet.

#### macOS

The environment has not been tested on macOS yet.

## Usage

> [!NOTE]
> I provided a make file to simplify the docker-compose commands. Ensure you are in the correct directory in your terminal.

### Bringing up the stack

Clone this repository onto the Docker host that will run the stack with the command below:

```console
git clone git@github.com:MaximilianPfitzenmaier/docker-wordpress.git
```

Now, set your **LOCAL_DOMAIN** and **REMOTE_DOMAIN** in the `.env` file:

```sh
# change these to your domain
LOCAL_DOMAIN=website.local
REMOTE_DOMAIN=www.website.com
```
> [!WARNING]
> Do not change the other variables for the initial database connection.


> [!NOTE]
> If you have a dump of your Database, you can also put a copy into the `/docker/temp/` 
> folder. The build process in the next step will look for a **dump.sql** file and import 
> it automatically. It will also perform the search-replace process for you.


Then, initialize your environment by executing the command:

```sh
make build
```

If everything went well and the setup completed without error, you should now see that the init script added your local domain to your hosts file, and two certificates are generated in the `certs` folder for SSL.


Start the environment:

```sh
make up
```

Access the Website `https://<YOUR_LOCAL_DOMAIN>` in a web browser.
Follow the installation instructions if you didn't put a dump.sql file in the `/docker/temp/` folder.


You can also access the phpMyAdmin page by opening `http://<YOUR_LOCAL_DOMAIN>:8080`
* user: *wp*
* password: *root*


### Using the WordPress-CLI

You can always use the WP-CLI to executed some commands. For example if you want to list all your user you can use the following command:


```sh
# List all your users
make wp "user list"
```

You can also load the sample data provided by your Kibana installation.

### Cleanup

In order to entirely shutdown the stack and remove all persisted data, use the following command:

```sh
make down-v
```

> ![WARNING]
> If you shutdown your environment you have to use the **build command** `make build` the next time you want to spin-up your environment

## Configuration

### How to configure the init file

Acually you don't need the `init.sh` file. But make sure to have a valid certificate and an entry in `/etc/hosts` file!

* The init file will create a certificate for you this is important to know because without a cert the environment will not work! The Nginx is configured to directly reroute to https!
* It also creates a entry in your `/etc/hosts` file  

### How to configure the init update-databse file
You just need this file if you have a sql dump. 

* You can add a connection to your server to pull a databse dump automatically and save it in the temp folder.
* you can add commands to disable plugings or clear transients and cash 


## How to execute commands on the cosole

* Just check out the Makefile for some examples

[docker-install]: https://docs.docker.com/get-docker/
[compose-install]: https://docs.docker.com/compose/install/
[compose-v2]: https://docs.docker.com/compose/compose-v2/
[linux-postinstall]: https://docs.docker.com/engine/install/linux-postinstall/
[mkcert-install]: https://github.com/FiloSottile/mkcert
