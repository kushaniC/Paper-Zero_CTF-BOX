FROM debian:bookworm-slim AS evidence
RUN apt-get update \
    && apt-get install -y --no-install-recommends steghide \
    && rm -rf /var/lib/apt/lists/*
WORKDIR /evidence
COPY base.jpeg secret.txt ./
# Rebuild the hidden evidence at image build time so its next-step URL stays portable.
RUN steghide embed -cf base.jpeg -ef secret.txt -p "" -f

FROM nginx:alpine
COPY index.html /usr/share/nginx/html/index.html
COPY --from=evidence /evidence/base.jpeg /usr/share/nginx/html/suspicious_photo.jpg
