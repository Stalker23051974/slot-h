<small>Previous articles:

- ➡️ [Section 21. Controller Types](/docs/21-controllers)
- ➡️ [Section 22. Frontend Structure](/docs/22-layouts)
- ➡️ [Section 23. JS Library Choice](/docs/23-js)
</small>

# SLOT-H: Dynamic Signature

The system implements a dynamic signature mechanism to protect requests from CSRF attacks and data tampering. The signature is generated on the server and sent to the frontend, then returned with each request for verification.

---

## How It Works

With each request, the server generates a unique signature and stores it in the session. The signature is sent to the frontend as part of the response. When sending the next request, the frontend must pass this signature back. The server compares the received signature with the one stored in the session.

If the signatures match - the request is considered valid. If the signature is missing or does not match - the request is rejected.

The signature is updated with each request, making it impossible to reuse an old signature.

---

## Storage

The signature is stored in the user's session. The `Request` object is used to retrieve and update it:

```php
self::_VERIFY_ = 'verifyHash';
$_SESSION[self::_VERIFY_] = md5(time() . rand(1000, 9999));
```
The signature is sent to the frontend in the sign field of the response. For API requests, the signature is automatically added to the response body.

## Sending the Signature to the Frontend
The signature is sent in the server response in the sign field. For web pages, the signature is automatically added to the session and accessible through frontend mechanisms. For API requests, the signature is included in the JSON response.

Example API response:

```json
{
    "status": "data",
    "data": {...},
    "message": false,
    "sign": "f8cbc97de21289267b415a035419897b"
}
```
The frontend extracts the signature from the response and stores it for the next request.

## Signature Verification
Verification is performed in the Request class when processing an incoming request. For POST requests, the presence of the _sign_ field and its match with the session value is checked.

```php
if (!isset($_REQUEST['_sign_']) || $_SESSION[self::_VERIFY_] != $_REQUEST['_sign_']) {
    // signature is invalid - request rejected
}
```
If the signature is invalid, all request parameters are cleared, and the request is considered invalid.

## Frontend Signature Update
When the page loads, the signature is passed as the value of the alfaFooter JavaScript variable. Any API request (including scheduled pings) receives the current signature in the response and updates alfaFooter.

Thus, the maximum signature lifetime is limited to 30 seconds - the period of the scheduled ping. However, the signature can be updated more frequently if user actions trigger other API requests.

## Disabling Verification
Signature verification can be disabled via configuration:

```php
define('IGNORE_SIGN', true);
```
This flag is used in development mode when testing needs to be simplified. In production mode, IGNORE_SIGN must be false.

**Important:** Disabling signature verification reduces system security and is not recommended for production environments.

##Implementation Features
The signature mechanism protects the system from CSRF attacks without requiring additional libraries or complicating the code. It is built into the system kernel and works automatically for all POST requests.

Signature verification is not performed for GET requests, as they do not change the system state.

The signature is not directly tied to the user - it is bound to the session, making it resistant to forgery. When the session changes (re-login), the signature is automatically recreated.

##What's next?
- ➡️ [Section 25. Access Protection](/docs/25-security-access)
- ➡️ [Section 26. Localization Principles](/docs/26-localization-principle)
- ➡️ [Section 27. Queue Server Startup](/docs/27-queue-start)
