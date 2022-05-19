from fastapi import FastAPI
from parse import get_stock_prices
from fastapi.middleware.cors import CORSMiddleware

app = FastAPI()
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


@app.get("/get-price/{ticker}")
def stock_price(ticker: str):
    """Get current stock price for a given ticker"""
    return get_stock_prices(ticker)
