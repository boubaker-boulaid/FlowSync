# Simulated external systems run as a separate Sandbox app

The demo's external systems (store, courier, CRM, help desk, mailer) live in their own small Laravel app, `sandbox/`, deployed as a separate container. They are not mock routes inside FlowSync. FlowSync must reach them over real HTTP so that failures, timeouts and idempotency behaviour are real network effects. The cost is that the Sandbox host has to be explicitly allowlisted in FlowSync's SSRF protection, because it sits on a private network address that would otherwise be blocked.
