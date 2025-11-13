# Docker Setup for Database Comparison Tool

This directory contains Docker configuration files for easy setup and testing.

## Quick Start with Docker

### 1. Start All Services

```bash
docker-compose up -d
```

This will start:
- **Web Server** (http://localhost:8000) - PHP application
- **Local Database** (localhost:5432) - PostgreSQL with sample data
- **Remote Database** (localhost:5433) - PostgreSQL with different sample data

### 2. Configure the Application

```bash
# Copy docker config to main config
cp docker/config.docker.php config.php
```

### 3. Access the Application

Open your browser and go to:
```
http://localhost:8000
```

You should see the schema comparison between the two databases!

## Optional: pgAdmin

To start pgAdmin for database management:

```bash
docker-compose --profile tools up -d
```

Access pgAdmin at:
```
http://localhost:5050
Email: admin@admin.com
Password: admin
```

### Connect to Databases in pgAdmin

**Local Database:**
- Host: local-db
- Port: 5432
- Database: platformdb
- Username: admin
- Password: admin_pass

**Remote Database:**
- Host: remote-db
- Port: 5432
- Database: platformdb
- Username: postgres
- Password: remote_pass

## Sample Data

The Docker setup includes sample databases with intentional differences for testing:

### Local Database
- **public.users** - 5 columns
- **public.posts** - 6 columns
- **auth.sessions** - 5 columns
- **auth.permissions** - 3 columns
- **data.analytics** - 5 columns
- **data.logs** - 5 columns

### Remote Database (Differences)
- **public.users** - 6 columns (has `phone` column)
  - `username` VARCHAR(100) instead of VARCHAR(50)
- **public.posts** - 7 columns (has `views` column)
- **auth.sessions** - 5 columns (same)
- **auth.permissions** - 3 columns (same)
- **auth.roles** - EXTRA TABLE (not in local)
- **data.analytics** - 6 columns (has `ip_address` column)
  - `user_id` is BIGINT instead of INTEGER
- **data.logs** - 5 columns (same)

## Stopping Services

```bash
# Stop all services
docker-compose down

# Stop and remove volumes (deletes all data)
docker-compose down -v
```

## Rebuilding

If you make changes to Dockerfiles or init scripts:

```bash
docker-compose down -v
docker-compose up -d --build
```

## Troubleshooting

### Connection Errors

If you get database connection errors:
1. Make sure config.php is using docker service names (`local-db`, `remote-db`)
2. Wait a few seconds for databases to initialize
3. Check logs: `docker-compose logs -f`

### Port Already in Use

If ports 5432 or 5433 are already in use:
1. Edit `docker-compose.yml` to change ports
2. Update `config.php` accordingly

### Reset Everything

```bash
docker-compose down -v
docker-compose up -d
cp docker/config.docker.php config.php
```

## Custom Data

To load your own schema:

1. Edit `docker/init-local.sql` and `docker/init-remote.sql`
2. Rebuild: `docker-compose down -v && docker-compose up -d`

## Production Use

**⚠️ WARNING:** This Docker setup is for **development and testing only**.

For production:
- Use secure passwords
- Don't expose database ports
- Use SSL/TLS connections
- Use proper database backups
- Follow security best practices
