-- Создание пользователя, если он не существует
DO $$
BEGIN
  IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'dmitry') THEN
    CREATE USER dmitry WITH PASSWORD '!ChangeMe!';
  END IF;
END
$$;

-- Создание базы данных, если она не существует
DO $$
BEGIN
  IF NOT EXISTS (SELECT FROM pg_database WHERE datname = 'db0_test') THEN
    CREATE DATABASE db0_test OWNER dmitry;
  END IF;
END
$$;

-- Подключение к базе данных и создание схемы, если её нет
\c db0_test;

DO $$
BEGIN
  IF NOT EXISTS (SELECT FROM pg_namespace WHERE nspname = 'public') THEN
    CREATE SCHEMA public;
    GRANT ALL ON SCHEMA public TO dmitry;
  END IF;
END
$$;