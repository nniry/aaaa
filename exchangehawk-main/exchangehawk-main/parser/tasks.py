from celery import Celery
from os import getenv
from db import get_stocks
from parse import get_stock_prices
import logging


celery_logger = logging.getLogger("celery")

app = Celery(
    "tasks",
    broker=f"amqp://{getenv('RABBITMQ_USER')}:{getenv('RABBITMQ_PASSWORD')}@rabbitmq:5672/vhost",
)


@app.task
def parse_prices():
    """Parse prices and update the values in the database"""
    print("Parsing...")
    for ticker in get_stocks():
        celery_logger.info(f"Processing {ticker}")
        prices = get_stock_prices(ticker)
        celery_logger.error(prices)


@app.on_after_configure.connect
def setup_periodic_tasks(sender, **kwargs):
    # Run parser immediately
    parse_prices.delay()

    # Run every 3 minutes
    sender.parse_prices(
        crontab(minute="*/3"),
        check_table.s(),
    )
