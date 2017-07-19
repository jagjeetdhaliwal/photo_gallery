# Photo Gallery

1. Lets the user login via Instagram
2. Stores the user info and instagram access tokens in a MySQL database.
3. Retrieves photos via API. (Fetching 6 photos at a time)
4. Paginates photos by triggering an api call using next_max_id whenever load more is clicked.
5. Shows them in a grid (using card components from Materialize)
6. Fallback images shown by default if a user hasn't logged in via Instagram yet.


# Contact Form

1. MailGun integration for sending notifications to customer support email (configurable in settings)
2. Airtable integration to save records.
3. Logs for each submission are stored in a MySQL database.

# Demo

Available at http://www.hipstermidtown.com/

# Notes

1. Materialize has been used for front end purposes.
2. Strong input validation done at both front end and back end.
3. PHP filter sanitisation functions used for guarding against XSS attacks.
4. POST requests used for any handlers that affect the database.
5. CSRF Tokens also used to prevent CSRF attacks.
6. Prepared Statements used for MySQL querying.
7. Guarded included files using .htaccess

# TO DO

1. Protecting Session Data using session_set_save_handler() and storing in a database/memcache or both.
2. Use masonry for better gridding.
3. Randomize fallback images to give a better experience to logged out users.
4. 



