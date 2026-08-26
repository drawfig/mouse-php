<p align="center">
   <img src="/public_html/resources/images/mouse-php-logo-cropped.png" alt="mouse-php Logo" width="450" >
</p>

`mouse-php` is a lightweight, zero-dependency PHP framework designed to deliver an exceptional developer experience without the overhead of magic obfuscation. It strikes the perfect balance between robust tooling and architectural transparency, allowing you to build web applications and RESTful APIs quickly while maintaining full visibility into your code structure.

---

## Key Features
* **Production-Ready Out of the Box:** Zero boilerplate configuration required. Install, initialize, and start writing your business logic immediately.
* **Easy to learn:** Built on standard PHP syntax and clean OOP principles—no complex abstraction layers to learn.
* **Lightning-Fast Deployments:** Features a built-in `deploy` command via `squeak` to generate lightweight, distribution-ready application packages in seconds, complete with version rollback support.
* **Robust CLI Tooling (`squeak`):** Includes a powerful companion CLI utility handling scaffolding, routing management, middleware registration, and application bootstrapping.

### **Eliminating the "F5" Fatigue**
A standout feature of the `mouse-php` ecosystem is its integrated development server, which **automatically reloads your application upon code changes**. Initiated via `squeak serve`, this brings the rapid, hot-reloading iteration workflow popularized by the JS/Vite ecosystem directly to PHP development.
****

## Getting Started

### 1. Prerequisites
* **PHP 8.3+**
* **Extensions:** `PDO`, `sqlite3`, `php-sqlite3`, `php-pdo-sqlite`
* **Database:** MariaDB or MySQL
* **Web Server:** A CGI-compatible web server like Apache or NGINX (Recommended for production, not required for local development)


### 2. Database Set Up
1. Install and configure your preferred database server (MariaDB/MySQL or SQLite).
2. Create a dedicated database and user for your application.
3. Retain these credentials for the configuration wizard in step 4.

Now that you have all the prerequisites installed and set up you can start building your first application.There are two ways to get started:

* [create-mouse-app Install](#create-mouse-app-Installation)
* [Manunal Install](#Manual-Installation)

##  create-mouse-app Installation

Before bootstrapping a project, ensure Composer is installed globally on your system and that its global binary directory is present in your shell `PATH`.

### 1. Global Requirements

If you do not already have Composer installed globally, run the installer script to place the binary into /usr/local/bin:  
```Bash
# Download and execute the official Composer installer
curl -sS https://getcomposer.org/installer | php

# Move the executable to your global path
sudo mv composer.phar /usr/local/bin/composer
```

Next, ensure Composer's global vendor binary directory is added to your environment `PATH` (typically `~/.config/composer/vendor/bin` on Linux or `~/.composer/vendor/bin`):  

```Bash
# Add this line to your ~/.bashrc or ~/.zshrc file:
export PATH="$(composer config -g home)/vendor/bin:$PATH"

# Apply the changes
source ~/.bashrc
```

### 2. Installing create-mouse-app

Install the CLI tool globally using Composer:

```Bash
composer global require mouse-php/create-mouse-app
```

### 3. Bootstrapping a New Project

Run create-mouse-app followed by your desired project directory name. This command leverages Squeak's initialization engine to scaffold your directory structure, create an .env file, and generate authentication code if your database configuration is set up:
```Bash
create-mouse-app my-app
```

### 4. Post-Initialization Setup

Once the scaffolding script finishes running, move into your new project directory and launch the local development server. Launch the `squeak` CLI Shell to bootstrap your project:

```bash
    php squeak
```

Start the hot-reloading development server by running the following command inside the squeak shell:

```bash
    > serve
```

You will be prompted to select your .env configuration, target host, and port. Once active, navigate to your configured address (e.g. `http://localhost:9000`).

If you see the default welcome page shown below, you are ready to start developing!

<p align="center">
<img width="80%" alt="Screenshot_20260611_095840" src="https://github.com/user-attachments/assets/6ca1b753-21e1-4f7c-8bd0-631b46fed166" />
</p>

##  Manual Installation

Clone the repository directly:
```bash
   git clone https://github.com/drawfig/mouse-php.git
   cd mouse-php
```
Alternatively, download the compiled archive from the [Releases Page](https://github.com/drawfig/mouse-php/releases).


### 4. Initialize the Application
Launch the 'squeak' CLI tool to bootstrap your environment:
```bash
   php squeak
```
inside the interactive CLI shell, run the initialization command:
```bash
   > init
```

This wizard walkthrough will configure your environment variables and prompt you to optionally generate the **authentication scaffolding** (which instantly creates the auth controllers, models, and migrations for the `users` table).


### 5. Launch the Development Server
**Start the hot-reloading development environment:**

```bash
   > serve
```
You will be prompted to select your .env configuration, target host, and port. Once active, navigate to your configured address (e.g., `http://localhost:9000`).

You should now be able to see the default welcome page, if so Congratulations! You've successfully installed and configured `mouse-php` and can begin developing your application.
   
****

## How to Contribute

mouse-php is an open-source project, and we welcome contributions from the community! If you'd like to get involved, here are a few ways you can help:

* Report bugs and suggest features using the [issue tracker](https://github.com/drawfig/mouse-php/issues).
* Contribute code and documentation by forking the repository and submitting a pull request.
* Join the conversation on the [Discord server](https://discord.gg/zvacxjNCU6).

Your contributions are greatly appreciated and help make mouse-php better for everyone.
****

## License

mouse-php is open-source software licensed under the Apache-2.0 License. A copy of the license is included in the root directory of this project.
