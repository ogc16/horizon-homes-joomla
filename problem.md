# Challenge: High-Performance Asynchronous Real Estate Listing Ingestion Service

## Overview

Build a production-grade, asynchronous listing ingestion service that ingests real
estate property listings from multiple external sources (MLS feeds, partner APIs,
CSV drops, and webhooks), normalises them into the Horizon Homes schema, and makes
them queryable with minimal end-to-end latency.

The service must handle **burst traffic** (e.g. 10 000 listings arriving in under
60 seconds) without losing data or exceeding acceptable write latency on the
primary database.

---

## Functional Requirements

1. **Multi-source ingestion**
   - Accept listings from at least three source types: a JSON REST feed, a
     newline-delimited CSV file upload, and a webhook endpoint.
   - Each source may use a different field naming convention, coordinate format
     (decimal degrees vs. DMS), and currency code. The service must normalise all
     of them into the canonical `#__estate_listings` schema.

2. **Idempotency**
   - Re-submitting the same listing (identified by source + external ID) must
     update the existing record rather than creating a duplicate.
   - The service must guarantee exactly-once effective writes even if a source
     retries delivery.

3. **Validation & quarantine**
   - Invalid records (missing title, malformed coordinates, unparseable price)
     must be routed to a quarantine store with the rejection reason, not silently
     dropped or allowed to poison the batch.
   - A listing with `latitude` outside `[-90, 90]` or `longitude` outside
     `[-180, 180]` is invalid.

4. **Publishing rules**
   - Ingested listings must land as `published = 0` until an operator approves
     them, OR be auto-published if the source is flagged trusted.
   - Off-plan listings must have a `completion_date` or a null value — never the
     sentinel `0000-00-00`.

5. **Queryability**
   - Within **2 seconds** of a listing being accepted, it must be visible to the
     front-end listings query (via `ListingsModel::getListings()`).

---

## Non-Functional Requirements

| Requirement | Target |
|---|---|
| Throughput | ≥ 5 000 listings/minute sustained |
| P99 end-to-end latency (webhook → searchable) | < 2 s |
| Data loss | Zero under normal operation; at-least-once under failure |
| Duplicates | Zero under normal operation (idempotent upserts) |
| Poison messages | Isolated in quarantine, never block the pipeline |
| Horizontal scaling | Workers can be added/removed without code changes |
| Observability | Per-source ingestion rate, error rate, and lag exposed as metrics |

---

## Architecture Constraints

- **Language**: PHP 8.3+ (the rest of the stack is Joomla 6 / PHP 8.3) — OR —
  a standalone Go/Node worker if you justify the polyglot boundary. Your choice;
  document the trade-off.
- **Queue**: Redis Streams, RabbitMQ, or Azure Service Bus (the site runs on
  Azure App Service). No third-party SaaS queues.
- **Database**: MySQL 8.4 (existing Horizon Homes schema — see
  `components/com_estate/admin/sql/install.mysql.utf8.sql`).
- **No ORM required** — the existing codebase uses raw query builders; follow
  the same pattern or justify a deviation.
- **The service must not block the Joomla web worker.** All heavy processing
  happens off the request thread.

---

## Deliverables

1. **Design document** (architecture diagram — ASCII or Mermaid) showing the
   data flow from source → queue → worker → DB, including the quarantine path.
2. **Producer component** — an endpoint or CLI command that accepts raw payloads
   and enqueues them.
3. **Worker process** — reads from the queue, validates, normalises, upserts,
   and handles failures with retry + dead-letter logic.
4. **Schema migrations** — any new tables needed (e.g. `#__estate_ingest_log`,
   `#__estate_quarantine`).
5. **Tests** — at minimum: unit tests for the normaliser (DMS → decimal,
   currency conversion, field mapping) and an integration test proving
   idempotent upsert.
6. **Runbook** — how to deploy, monitor, scale, and drain the queue.

---

## Evaluation Criteria

| Criterion | Weight |
|---|---|
| Correctness of normalisation logic | 25 % |
| Idempotency and failure handling (retries, DLQ, quarantine) | 25 % |
| Latency and throughput design choices (batching, back-pressure) | 20 % |
| Code quality, test coverage, and adherence to existing codebase conventions | 15 % |
| Observability and operational readiness (metrics, logging, runbook) | 15 % |

---

## Out of Scope

- Front-end changes to the listings UI.
- Authentication/authorisation for the webhook (assume a shared secret header).
- Full-text search indexing (Elasticsearch etc.) — MySQL queries are sufficient.
- Multi-region replication.
