# Observability Alerts and Log Retention Policy

## 1. Alerts

### RED Metrics
- High HTTP Error Rate:
  Trigger: error_rate > 5% of http_requests (5min window)
  Reason: Indicates application or integration failures.

- External Service Errors:
  Trigger: external_service_errors > threshold (10min)
  Reason: Critical for third-party API reliability.

- Slow HTTP Requests:
  Trigger: p95(http_request_duration) > 2s
  Reason: Signals performance degradation.

- Application Errors Spike:
  Trigger: application_errors > threshold
  Reason: Unhandled exceptions or module failures.

### USE Metrics
- High DB Connections:
  Trigger: db_connections > 90% pool size
  Reason: Can lead to failed queries.

- Queue Processing Errors:
  Trigger: queue_errors > 5 per 10min
  Reason: Delayed jobs, broken background tasks.

- Queue Latency:
  Trigger: queue_backlog_seconds > 30s
  Reason: Potential job bottlenecks.

- Memory Usage:
  Trigger: memory_usage > 80% of total
  Reason: Prevents OOM crashes.

- Cache Errors:
  Trigger: cache_errors > 0
  Reason: Affects app speed and logic.

### Business Alerts
- Weather API failures:
  Trigger: weather_fetches{success="false"} > 50%
  Reason: Broken data pipeline or provider outage.

- Low Email Subscriptions:
  Trigger: email_subscriptions delta < threshold
  Reason: UX or system issue.

## 2. Log Retention Policy

| Log Level | Retention | After Retention       | Reason                            |
|-----------|-----------|------------------------|-----------------------------------|
| error     | 90 days   | Archive to S3/Glacier | Needed for audit & postmortems   |
| warn      | 30 days   | Delete                 | Useful for diagnosing patterns   |
| info      | 14 days   | Delete                 | Operational transparency         |
| debug     | 3 days    | Delete (local only)    | Dev-only, large volume           |

Cleanup handled by Loki retention or cron jobs. Archived logs follow S3 lifecycle policies.

