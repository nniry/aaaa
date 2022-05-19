CREATE TABLE IF NOT EXISTS stocks (
    symbol VARCHAR(8) PRIMARY KEY,
    name VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS stock_prices (
    stock_symbol VARCHAR(50) REFERENCES stocks(symbol) ON DELETE CASCADE,
    timestamp TIMESTAMPTZ,
    price REAL,
    PRIMARY KEY (stock_symbol, timestamp)
);

CREATE TABLE IF NOT EXISTS users (
    username VARCHAR(50) PRIMARY KEY,
    password_hash VARCHAR(64)
);

CREATE TABLE IF NOT EXISTS user_stocks (
    user_id VARCHAR(50) NOT NULL,
    stock_symbol VARCHAR(8) NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(username),
    FOREIGN KEY (stock_symbol) REFERENCES stocks(symbol)
);

CREATE TABLE sessions (
    username VARCHAR(50) NOT NULL,
    session VARCHAR(32) PRIMARY KEY,

    FOREIGN KEY (username) REFERENCES users(username)
);

CREATE OR REPLACE FUNCTION trigger_set_timestamp()
RETURNS TRIGGER AS $$
BEGIN
  NEW.timestamp = NOW();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER set_timestamp
BEFORE UPDATE ON stock_prices
FOR EACH ROW
EXECUTE PROCEDURE trigger_set_timestamp();