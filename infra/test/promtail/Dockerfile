FROM grafana/promtail:2.8.2

COPY ./infra/test/promtail/config.yaml /etc/promtail/config.yaml

CMD ["-config.file=/etc/promtail/config.yaml"]