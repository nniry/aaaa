"""Database connection control"""
import psycopg2
from os import getenv
from utils import get_sql
from typing import List
import logging

celery_logger = logging.getLogger("celery")


class DatabaseCursor:
    DATABASE = "db"
    USERNAME = getenv("POSTGRES_USER")
    PASSWORD = getenv("POSTGRES_PASSWORD")
    HOST = getenv("POSTGRES_HOST")

    def _connect(self):
        self.connection = psycopg2.connect(
            host=self.HOST,
            database=self.DATABASE,
            user=self.USERNAME,
            password=self.PASSWORD,
        )
        self.cursor = self.connection.cursor()

    def _disconnect(self):
        self.connection.close()

    def __enter__(self):
        self._connect()
        return self.cursor

    def __exit__(self, exc_type, exc_value, trace):
        self._disconnect()

    @classmethod
    def database_inited(cls) -> bool:
        """Test whether the database is initialized"""
        with cls() as cursor:
            cursor.execute(get_sql("database_inited"))
            return cursor.fetchone()


def get_stocks() -> List[str]:
    """Get list of stocks

    Returns:
        List[str]: List of stock symbols
    """
    if not DatabaseCursor.database_inited():
        celery_logger.error("Database schema has not been initialized")
        return []

    with DatabaseCursor() as cursor:
        cursor.execute(get_sql("list_stocks"))
        return cursor.fetchall()


def save_stock_price(symbol: str, price: float) -> None:
    """Save stock price to the database

    Args:
        symbol (str): Stock symbol
        price (float): Price to be saved
    """
    with DatabaseCursor() as cursor:
        cursor.execute(get_sql("update_price"), (symbol, price))
