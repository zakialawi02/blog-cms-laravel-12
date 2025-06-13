# Blog CMS Laravel 12

Welcome to the Blog CMS for Laravel 12! This repository provides a comprehensive and solid foundation for building a robust and scalable blog or content management system using the latest features of Laravel 12.

---

## Features

This Content Management System is packed with features designed to provide a complete and flexible solution for managing your blog content.

### Core & Backend

- **Laravel 12**: Leverages the latest features and improvements from the Laravel framework.
- **Authentication**: A built-in user authentication system using UUIDs for the users table.
- **Authorization**: Role-based access control (SuperAdmin, Admin, Writer, User) for managing user permissions.
- **User Management**: Admins can perform complete CRUD operations on user accounts, managing roles and permissions.
- **API Support\***: Comes with integrated support for building and consuming APIs. A Postman collection is provided for easy testing.

### Content Management

- **Article Management**: Perform CRUD (Create, Read, Update, Delete) operations on blog articles.
- **Category and Tag Management**: Easily organize articles by creating and managing categories and tags to improve content discoverability.
- **Page Management**: Create and manage static pages (like "About Us" or "Contact") to complement your blog articles.
- **Page Layout Settings**: Customize the layout and content sections of your homepage directly from the dashboard.

### User Engagement & Analytics

- **Statistical Insights**: Analyze article performance with comprehensive statistics, including views filtered by article or geographical location.
- **Comment Management**: Monitor and manage user comments on each blog post to foster community engagement.
- **Newsletter Integration\***: Collect email addresses from users who subscribe to your newsletter for effective communication.

### Frontend & Customization

- **Navigation Menu Configuration**: Customize the navigation menus (header and footer) to tailor the user experience.
- **Web Settings**: Configure various site-wide settings such as the site name, logo, social media links, and more.
- **Blog Pages**:
    - **Main Blog Page**: Displays all articles in a visually appealing format.
    - **Category Page**: Filters articles by their assigned categories.
    - **Archive Page**: Offers filtering options based on tags, authors, or dates.
    - **Single Post Page**: Presents individual articles with a dedicated comment section to foster interaction.

---

## Prerequisites

Before you begin, make sure you have the following software installed on your machine:

- [PHP](https://www.php.net/) version 8.2 or higher
- [Composer](https://getcomposer.org/) (for PHP dependency management)
- [Node.js](https://nodejs.org/) and [npm](https://www.npmjs.com/) (for JavaScript and Vite dependencies)
- Database Server (e.g., [MySQL](https://www.mysql.com/) or MariaDB. [Recommendation use MySQL])
- [Git](https://git-scm.com/) (for cloning the repository)

---

## Installation

Follow these steps to set up the project on your local machine.

1.  **Clone the Repository**

    ```bash
    git clone [https://github.com/zakialawi02/blog-cms-laravel-12.git](https://github.com/zakialawi02/blog-cms-laravel-12.git)
    ```

2.  **Navigate to the Project Directory**

    ```bash
    cd blog-cms-laravel-12
    ```

3.  **Install Dependencies**

    ```bash
    composer install
    npm install
    ```

4.  **Environment Configuration**

    - Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    - Open the `.env` file and configure your database connection settings (DB_DATABASE, DB_USERNAME, DB_PASSWORD).
    - Generate the application key:
        ```bash
        php artisan key:generate
        ```

5.  **Database Setup**

    - **Option A (Recommended): Run Migrations & Seeders**
      This command will create all the necessary tables and populate your database with initial data, including user roles and default settings.
        ```bash
        php artisan migrate --seed
        ```
    - **Option B: Import SQL File**
      Alternatively, you can import the `db.sql` file provided in the repository into your database using a database management tool like phpMyAdmin.

6.  **Start the Development Server**

    ```bash
    php artisan serve
    ```

    Your application will be running at `http://localhost:8000`.

---

## Demo Credentials

After seeding the database, you can log in with the following default accounts:

| Role         | Username     | Password     |
| :----------- | :----------- | :----------- |
| Super Admin  | `superadmin` | `superadmin` |
| Admin        | `admin`      | `admin`      |
| Writer       | `writer`     | `writer`     |
| Regular User | `user`       | `user`       |

---

## API Documentation

API documentation is available through Postman. You can view the collection and test the endpoints here:

[**View API Docs on Postman**](https://documenter.getpostman.com/view/25223819/2sAYkLoHLh)

---

## License

This project is licensed under the **Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)**.

[![CC BY-NC-SA 4.0](https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png)](http://creativecommons.org/licenses/by-nc-sa/4.0/)

Under the following terms:

- **Attribution** — You must give appropriate credit, provide a link to the license, and indicate if changes were made.
- **NonCommercial** — You may not use the material for commercial purposes.
- **ShareAlike** — If you remix, transform, or build upon the material, you must distribute your contributions under the same license as the original.

For complete details, please see the [LICENSE](LICENSE) file.
