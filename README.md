## Features

### User Side

*   **User Authentication:**
    *   User registration with form validation and password hashing.
    *   User login with secure session management.
*   **Product Browsing:**
    *   Homepage to display products.
    *   Product filtering by category.
    *   Product sorting by price and name.
*   **Shopping Cart:**
    *   Add products to cart.
    *   Update product quantities in the cart.
    *   Remove products from the cart.
    *   View cart summary.
*   **Checkout:**
    *   Enter shipping information.
    *   Cash on Delivery (COD) payment method.
    *   Order confirmation page with product details and total price.
*   **User Profile:** (Basic)
    *   View and update user profile information (name, email).

### Admin Side

*   **Admin Panel:**
    *   Dashboard with summary statistics (e.g., total users, products, categories).
    *   Chart.js integration for data visualization (e.g., sales chart).
*   **Product Management:**
    *   Add new products with image upload.
    *   Edit existing products.
    *   Delete products.
*   **Category Management:**
    *   Add new categories.
    *   Edit existing categories (inline editing).
    *   Delete categories.

## Technologies Used

*   **HTML:** Structure of web pages.
*   **CSS:** Styling of web pages (including Bootstrap 4 for layout and components).
*   **JavaScript:** Client-side interactions (e.g., mobile menu toggle).
*   **PHP:** Server-side scripting for database interaction, user authentication, and dynamic content.
*   **MySQL:** Database to store user data, product information, categories, shopping cart, and order details.
*   **Chart.js:** JavaScript library for creating charts on the admin dashboard.
*   **jQuery (optional):** Used for some Bootstrap components.
*   **Popper.js (optional):** Used for some Bootstrap components.

## Setup Instructions

1.  **Database:**
    *   Create a MySQL database named `ecommerce_db`.
    *   Import the database schema provided in the `ecommerce_db.sql` file (or copy and paste the SQL code into your database management tool).

2.  **Database Connection:**
    *   Update the database credentials in `includes/db_connection.php`:

        ```php
        $host = "your_database_host";
        $db_name = "ecommerce_db";
        $username = "your_database_username";
        $password = "your_database_password";
        ```

3.  **File Structure:**
    *   Make sure the files and folders are organized as shown in the "Project Structure" section above.

4.  **Web Server:**
    *   Place the project folder in your web server's document root (e.g., `htdocs` for XAMPP, `www` for WAMP).

5.  **Admin User:**
    *   Run the following SQL query in your database to create an admin user:

        ```sql
        INSERT INTO users (username, password, email, full_name, role) VALUES
        ('admin', '$2y$10$BwANRQuL.dytx6Pj/IL2cOD9m/fhq5/xkvl.c8uB4b3XgWcOCnF/y', 'admin@example.com', 'Admin User', 'admin');
        ```

        (Password for the admin user is `adminpassword`)

6.  **Uploads Folder:**
    *   Ensure that the `uploads` folder has write permissions for the webserver so that product images can be uploaded.

## Usage

*   **User Side:**
    *   Access the website through your web browser (e.g., `http://localhost/ecommerce/`).
    *   Register for an account or log in.
    *   Browse products, add them to your cart, and proceed to checkout.
*   **Admin Side:**
    *   Log in using the admin credentials (username: `admin`, password: `adminpassword`).
    *   Access the admin panel through `http://localhost/ecommerce/admin/`.
    *   Manage products, categories, and view dashboard statistics.

## Further Development

*   **Payment Integration:** Integrate other payment gateways (e.g., PayPal, Stripe).
*   **Order Management:** Implement order management features in the admin panel (view orders, update order status, etc.).
*   **Search Functionality:** Add a search bar to allow users to search for products.
*   **Advanced Filtering:** Add more filtering options (e.g., price range, brand).
*   **User Reviews:** Allow users to leave reviews and ratings for products.
*   **Recommendations:** Implement a product recommendation system.
*   **Email Notifications:** Send email notifications to users for order confirmation, shipping updates, etc.
*   **Security:** Enhance security measures to protect against common vulnerabilities (SQL injection, cross-site scripting, etc.).
*   **Performance:** Optimize website performance for faster loading times.

## Contributing

Contributions to this project are welcome. Please feel free to submit pull requests or open issues on the project's repository (if hosted on a platform like GitHub).





