-- Remote Database Sample Schema
-- This has intentional differences from local for testing

-- Create schemas
CREATE SCHEMA IF NOT EXISTS public;
CREATE SCHEMA IF NOT EXISTS auth;
CREATE SCHEMA IF NOT EXISTS data;

-- Public Schema Tables (some differences)
CREATE TABLE IF NOT EXISTS public.users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL,  -- Different length!
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),                -- Extra column!
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS public.posts (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES public.users(id),
    title VARCHAR(200) NOT NULL,
    content TEXT,
    status VARCHAR(20) DEFAULT 'draft',
    published_at TIMESTAMP,
    views INTEGER DEFAULT 0            -- Extra column!
);

-- Missing the following table from local:
-- CREATE TABLE IF NOT EXISTS public.comments (...)

-- Auth Schema Tables (identical to local)
CREATE TABLE IF NOT EXISTS auth.sessions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id INTEGER NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS auth.permissions (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

-- Extra table not in local
CREATE TABLE IF NOT EXISTS auth.roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    level INTEGER DEFAULT 0
);

-- Data Schema Tables (some differences)
CREATE TABLE IF NOT EXISTS data.analytics (
    id BIGSERIAL PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    event_data JSONB,
    user_id BIGINT,                    -- Different type! (INTEGER -> BIGINT)
    ip_address VARCHAR(45),            -- Extra column!
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS data.logs (
    id BIGSERIAL PRIMARY KEY,
    level VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    context JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data (same as local)
INSERT INTO public.users (username, email) VALUES
    ('alice', 'alice@example.com'),
    ('bob', 'bob@example.com'),
    ('charlie', 'charlie@example.com');

INSERT INTO public.posts (user_id, title, content, status, views) VALUES
    (1, 'First Post', 'Hello World!', 'published', 100),
    (2, 'Second Post', 'Testing...', 'draft', 0);

INSERT INTO auth.roles (name, level) VALUES
    ('admin', 100),
    ('user', 10);

COMMENT ON DATABASE platformdb IS 'Remote test database for schema comparison';
