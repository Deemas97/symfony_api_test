-- Создание пользователя
CREATE USER user WITH PASSWORD '!ChangeMe!';

-- Создание БД с указанием владельца
CREATE DATABASE db0_test OWNER user;

-- Выдача прав (опционально, OWNER уже имеет полный доступ)
GRANT ALL PRIVILEGES ON DATABASE db0_test TO user;
