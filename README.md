
# Laravel API Project

This is a php project that provides api functionality for Import Product.

1. [Project Setup](#project-setup)
2. [Install Dependencies](#install-dependencies)
3. [Running the Development Server](#running-the-development-server)


## Project Setup

Follow the steps below to get the project up and running on your local machine.

    ###Clone the Repository

    To get started, first clone the repository using `git clone`.
    
    `git clone https://github.com/ap-prabhu/mozilor_assignment_api.git`.
    `cd mozilor_assignment_api`


### Install Dependencies

    Make sure you have php8.1 and composerv 2.8.2 and  mysql installed. If not, you can download and install all.
    Once you have installed, run the following command in your terminal to install the necessary dependencies:
   
   `composer install`

### Running the Development Server
    1. Set Up Environment Variables 
        In .env and config mysql DB details all.

    2. Generate the Application Key
   
   `php artisan key:generate`

    3. Migrate the Database

   `php artisan migrate`

    And run  the seeder for admin user
   `php artisan db:seed --class=UsersTableSeeder`

    4. Serve the Laravel Application

   `php artisan serve`

    The API will be available at http://localhost:8000 or http://127.0.0.1:8000
    and make sure to add in REACT APP - .env
