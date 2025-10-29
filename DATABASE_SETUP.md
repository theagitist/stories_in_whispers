# Database Setup for Stories in Whispers

This document explains how to set up the MySQL database for storing completed poems.

## Prerequisites

- MySQL server running
- PHP with PDO MySQL extension
- Web server (Apache/Nginx) with PHP support

## Setup Instructions

### 1. Create Database and Table

Run the SQL commands in `setup_database.sql`:

```bash
mysql -u your_username -p < setup_database.sql
```

Or manually execute the SQL commands in your MySQL client.

### 2. Configure Database Connection

Edit `config.php` and update the database credentials:

```php
$host = 'localhost';
$port = '3306'; // Default MySQL port
$dbname = 'stories_in_whispers';
$username = 'your_username'; // Replace with your MySQL username
$password = 'your_password'; // Replace with your MySQL password
$charset = 'utf8mb4';
```

### 3. Set File Permissions

Make sure the web server can read the PHP files:

```bash
chmod 644 *.php
chmod 644 config.php
```

### 4. Test the Setup

1. Start the game and complete a poem
2. Check the browser console for "Poem saved successfully" message
3. Visit `view_poems.php` to see saved poems

## Database Schema

The `poems` table stores:

- `id`: Auto-incrementing primary key
- `player_name`: The player's assigned name
- `poem_text`: The formatted poem text
- `poem_lines`: JSON data of the structured poem
- `syllables_count`: Total syllable count
- `created_at`: Timestamp when the poem was created

## API Endpoints

### Save Poem
- **URL**: `save_poem.php`
- **Method**: POST
- **Content-Type**: application/json
- **Body**: 
  ```json
  {
    "player_name": "string",
    "poem_text": "string", 
    "poem_lines": "array",
    "syllables_count": "integer"
  }
  ```

### View Poems
- **URL**: `view_poems.php`
- **Description**: Displays the 50 most recent saved poems

## Troubleshooting

### Common Issues

1. **Database connection failed**
   - Check MySQL credentials in `config.php`
   - Ensure MySQL server is running
   - Verify database exists

2. **Poems not saving**
   - Check browser console for JavaScript errors
   - Verify PHP error logs
   - Ensure `save_poem.php` is accessible

3. **Permission denied**
   - Check file permissions
   - Ensure web server can read PHP files

### Debug Mode

To enable debug logging, add this to `config.php`:

```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Security Notes

- Change default database credentials
- Consider using environment variables for sensitive data
- Implement rate limiting for the API
- Add input validation and sanitization
- Use HTTPS in production
