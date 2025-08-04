FROM grafana/loki:3.5.3

COPY ./infra/test/loki/local-config.yaml /etc/loki/local-config.yaml

CMD [ "-config.file=/etc/loki/local-config.yaml" ]