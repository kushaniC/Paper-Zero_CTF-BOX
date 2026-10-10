FROM python:3.10-slim
RUN apt-get update && apt-get install -y iputils-ping \
    && rm -rf /var/lib/apt/lists/* && pip install --no-cache-dir flask \
    && useradd -m appuser
RUN echo 'LST{pyth0n_c0mm4nd_1nj3ct10n}' > /flag.txt \
    && echo 'Encrypted exam vault: open /stage6/exam_lock on the same host/IP used for Paper Zero.' > /srv_note.txt \
    && chmod 644 /flag.txt /srv_note.txt
COPY app.py /app/app.py
USER appuser
WORKDIR /app
CMD ["python", "app.py"]
