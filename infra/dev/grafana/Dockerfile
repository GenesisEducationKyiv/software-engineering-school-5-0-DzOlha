FROM grafana/grafana:12.0.2

COPY ./infra/dev/grafana/provisioning/ /etc/grafana/provisioning/
COPY ./infra/dev/grafana/dashboards/ /var/lib/grafana/dashboards/
COPY ./infra/dev/grafana/grafana.ini /etc/grafana/grafana.ini
