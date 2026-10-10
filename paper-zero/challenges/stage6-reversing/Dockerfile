FROM gcc:12 AS build
COPY exam_lock.c /src/exam_lock.c
RUN gcc -O0 /src/exam_lock.c -o /exam_lock

FROM nginx:alpine
COPY index.html /usr/share/nginx/html/index.html
COPY --from=build /exam_lock /usr/share/nginx/html/exam_lock
