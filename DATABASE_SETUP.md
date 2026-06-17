# Database Setup for Stories in Whispers

This document explains how to set up the PostgreSQL database for storing completed poems.

## Prerequisites

- PostgreSQL server running
- PHP with PDO PostgreSQL extension (`pdo_pgsql`)
- Web server (Apache/Nginx) with PHP support

## Setup Instructions

### 1. Create Role, Database, and Table

Provision the role and database once as the postgres superuser, then load the schema:

```bash
sudo -u postgres psql -c "CREATE ROLE stories_in_whispers LOGIN PASSWORD 'CHANGE_ME';"
sudo -u postgres psql -c "CREATE DATABASE stories_in_whispers OWNER stories_in_whispers ENCODING 'UTF8' TEMPLATE template0 LC_COLLATE 'C.UTF-8' LC_CTYPE 'C.UTF-8';"
psql -h 127.0.0.1 -U stories_in_whispers -d stories_in_whispers -f setup_database.sql
```

### 2. Configure Database Connection

Edit `config.php` and update the database credentials:

```php
$host = '127.0.0.1';
$port = '5432'; // Default PostgreSQL port
$dbname = 'stories_in_whispers';
$username = 'stories_in_whispers'; // Replace with your PostgreSQL role
$password = 'your_password'; // Replace with your PostgreSQL password
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

- `id`: Auto-incrementing primary key (SERIAL)
- `player_name`: The player's assigned name
- `poem_text`: The formatted poem text
- `poem_lines`: JSONB data of the structured poem
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
   - Check PostgreSQL credentials in `config.php`
   - Ensure PostgreSQL server is running
   - Verify the database exists and `pg_hba.conf` permits the connection

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
