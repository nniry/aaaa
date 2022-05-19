from time import time
import requests
import os


def get_stock_prices(ticker: str):
    """Get current stock price

    Args:
        ticker (str): Ticker
    """
    r = requests.get(
        f'https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&symbol={ticker}&interval=5min&apikey={os.getenv("API_KEY")}'
    ).json()
    if "Error Message" in r:
        return []
    entries = r["Time Series (5min)"]
    result = {}
    for timestamp, data in entries.items():
        result[timestamp] = (float(data["1. open"]) / float(data["4. close"])) / 2
    return result
