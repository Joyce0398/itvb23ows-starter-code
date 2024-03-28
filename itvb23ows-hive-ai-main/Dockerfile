FROM python:3.12

WORKDIR /usr/src/hive-ai

COPY . /usr/src/hive-ai

RUN pip install flask

EXPOSE 5000

CMD ["flask", "--app", "app", "run", "--host=0.0.0.0"]