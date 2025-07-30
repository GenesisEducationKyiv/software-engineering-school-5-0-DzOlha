FROM otel/opentelemetry-collector-contrib:latest

# Copy config from build context to container
COPY ./infra/dev/otel/config.yaml /etc/otel/config.yaml

# Use the provided entrypoint with custom config
CMD [ "--config", "/etc/otel/config.yaml" ]