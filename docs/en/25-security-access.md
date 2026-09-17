<small>Previous articles:

- ➡️ [Section 22. Frontend Structure](/docs/22-layouts)
- ➡️ [Section 23. JS Library Choice](/docs/23-js)
- ➡️ [Section 24. Dynamic Signature](/docs/24-security-sign)
</small>

# SLOT-H: Access Protection

The system implements access protection at the routing level. This is the primary filter that validates the request before it reaches the controller.

---

## Request Validation

When a request arrives, the system analyzes the URI and determines whether it is a direct call to a module/controller/action or a link from the `aliases` table.

**Direct call** - the URI has a `module/controller/action` structure. The system checks the existence of each component.

**Short link** - the system looks for a match in the `aliases` table. If the link is found, it is replaced with the corresponding internal path. If the link is not found, the request is considered invalid.

If the module is not found - the request is considered invalid. If the controller or action is missing - the system redirects to the default page.

This prevents calls to non-existent entry points and protects against routing bypass attempts.

---

## Brute Force Protection

If a request tries to access a non-existent file or script (e.g., directly accessing a PHP file bypassing routing), the system records this in the `brutus` table. The URL and attempt count are saved for each such request.

This allows tracking system scanning and taking action on suspicious activity.

---

## Redirect to IP

For suspicious requests, the system redirects the user to their own IP address. This effectively disrupts automated scanners and bots that expect a standard server response, but instead receive a redirect to themselves.

---

## Handling Non-existent Modules

If the requested module is not found, the system redirects to the project's main page specified in the ini file. If the previous action used the `$this->setViewPath()` method, the system redirects to the last successfully loaded page.

This ensures a smooth transition for erroneous requests and prevents the user from seeing an error.

---

## Protection Against Direct Calls

Any attempt to directly access system files (bypassing `index.php`) is blocked via `.htaccess`. All requests are redirected to the single entry point, where validation is performed.

This prevents scripts from running outside the routing context and protects against unauthorized access to system files.

---

## Implementation Features

- Validation is performed before the controller loads, making the protection independent of application logic.
- The system does not provide detailed error messages for invalid requests, to avoid giving attackers information about the system structure.
- All suspicious requests are logged for subsequent analysis.

---

## What's next?

- ➡️ [Section 26. Localization Principles](/docs/26-localization-principle)
- ➡️ [Section 27. Queue Server Startup](/docs/27-queue-start)
- ➡️ [Section 28. Queue Server Tasks](/docs/28-queue-tasks)