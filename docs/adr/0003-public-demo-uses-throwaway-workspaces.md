# Public demo uses throwaway workspaces, not open sign-up

Status: accepted, conditional on a public host existing (see ADR-0004).

If FlowSync is deployed publicly, the demo environment disables registration. Instead, each visitor gets a Demo Workspace cloned from a seed template, owned by a temporary user and hard-deleted after 24 hours. Outbound HTTP is allowlisted to the Sandbox host only. With open sign-up, a public HTTP Request node could be turned into a tool for sending traffic at third parties, and visitors would break each other's seeded workflows. The 24-hour deletion is the only place where Execution history is hard-deleted. If there is no public host, Demo Workspaces are not built.
