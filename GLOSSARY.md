# FlowSync

A multi-tenant workflow automation product. Workspaces build Workflows on a visual canvas, publish them as immutable Versions, and FlowSync runs them when Trigger events arrive.

## Language

### Tenancy

**Workspace**:
The tenant: an isolated space that owns Workflows, Credentials, Webhook Endpoints and Executions.
_Avoid_: Tenant (in UI), organization, team, account

**Member**:
A user's membership in one Workspace, carrying exactly one Role.
_Avoid_: Seat, collaborator

**Role**:
A Member's fixed permission level: owner, admin, builder, operator or viewer.
_Avoid_: Permission set, grant

**Credential**:
A named, encrypted secret owned by a Workspace that HTTP Request Nodes use to authenticate to an external system. It can optionally be restricted to a base URL.
_Avoid_: Integration, connection, secret, API key (as the entity name)

**Audit Event**:
A permanent record of a Member performing a privileged or recovery action in a Workspace.
_Avoid_: Activity log entry, history, Execution log (that belongs to one Execution)

### Authoring

**Workflow**:
A named automation owned by a Workspace. It holds one editable Draft and a history of Workflow Versions.
_Avoid_: Automation, flow, zap

**Draft**:
The single editable definition of a Workflow, saved from the editor. Never executed directly.
_Avoid_: Working copy, unsaved version

**Workflow Version**:
An immutable snapshot of a Workflow's definition. It is either published or a test snapshot, and every Execution runs exactly one.
_Avoid_: Revision, release

**Published Version**:
The Workflow Version that live Trigger events run against.
_Avoid_: Active version, live workflow

**Node**:
One configured operation in a Workflow definition, of a fixed node type (Webhook Trigger, HTTP Request, Condition, Data Transform, Delay).
_Avoid_: Step (reserved for runtime), block, action

**Trigger**:
The single root Node of a Workflow that starts an Execution.
_Avoid_: Entry point, start node

**Condition**:
A Node that evaluates a list of rules joined by either "all" or "any", and continues down its true or false Branch.
_Avoid_: If node, filter, router

**Branch**:
The path that follows one outcome (true or false) of a Condition. Branches never merge.
_Avoid_: Route, lane

**Paused**:
The state of a Workflow that has a Published Version but refuses new Trigger Events. Executions already running continue.
_Avoid_: Disabled, stopped, inactive

**Archived**:
The state of a retired Workflow: hidden, read-only and unreachable from outside, with its Execution history kept.
_Avoid_: Deleted, removed

**Expression**:
A `{{…}}` reference inside Node configuration that reads data from the Trigger Event or earlier Steps, optionally piped through named filters.
_Avoid_: Template, formula, script

### Running

**Execution**:
One run of one Workflow Version, started by a Trigger, with its own status and step history.
_Avoid_: Run (in code/UI labels), job, instance

**Test Run**:
An Execution started from the editor against a test snapshot of the current Draft. It makes real calls but is excluded from dashboard metrics.
_Avoid_: Dry run, preview, simulation

**Step**:
The runtime record of one Node within one Execution, holding its status, input, output and attempts.
_Avoid_: Task, node run

**Attempt**:
One try at performing a Step. A Step has one or more Attempts.
_Avoid_: Try, execution (for this meaning)

**Outcome Unknown**:
The state of a Step whose side effect may or may not have happened remotely. A person must settle it.
_Avoid_: Uncertain, ambiguous failure

**Needs Review**:
The state of an Execution that is blocked on an Outcome Unknown Step until an Operator settles it.
_Avoid_: Stuck, paused, on hold

**Retry**:
Re-attempting the failed or Outcome Unknown Step of an Execution, on the same Workflow Version with the same inputs. The Execution then continues.
_Avoid_: Rerun, resume

**Replay**:
A new Execution that re-runs a previous Execution's trigger payload from the start, usually against the current Published Version.
_Avoid_: Rerun, re-execute, clone

### Intake

**Webhook Endpoint**:
The stable public address through which external systems send Trigger events to one Workflow.
_Avoid_: Hook URL, listener, callback

**Trigger Event**:
One inbound request received at a Webhook Endpoint, identified by its Event Key and recorded even when it does not start an Execution.
_Avoid_: Webhook (for the payload), request, message

**Event Key**:
The identifier that makes a Trigger Event unique within its Webhook Endpoint and is used to discard duplicates.
_Avoid_: Event ID, idempotency key (reserved for outbound calls)

**Idempotency Key**:
A value fixed per Step and sent with outbound calls so that the remote system can discard repeated Attempts of the same Step.
_Avoid_: Operation key, request ID, Event Key (that is for inbound events)

### Demo

**Sandbox**:
A separate application that imitates the external systems the demo Workflows talk to (store, courier, CRM, help desk, mailer). It has controls for injecting faults and sending Trigger Events.
_Avoid_: Mock server, fake API, simulator (singular, for the whole thing)

**Demo Workspace**:
A throwaway Workspace cloned from a seed template for one public demo visitor and deleted after 24 hours.
_Avoid_: Guest account, trial, sandbox workspace
