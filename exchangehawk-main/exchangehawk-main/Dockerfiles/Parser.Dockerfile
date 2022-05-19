FROM python:3.10

COPY . ./usr/src/parser
RUN pip install -r /usr/src/parser/requirements.txt

WORKDIR /usr/src/parser/
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "88", "--reload"]
