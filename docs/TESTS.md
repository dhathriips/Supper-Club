# Verification and evaluator test cases

## Executed before packaging

A clean import of the bundled SQL ran on **MariaDB 11.4.5** with **PHP 8.2.35** and the PDO MySQL driver. The application ran through PHP's local HTTP server. All PHP files passed syntax checks and the JavaScript passed Node's syntax check. The HTTP journey passed against the live database. An independent headless Chrome session exercised rendered pages and saved the screenshots. The Apache/XAMPP setup instructions are provided for classroom use; this test run used PHP's development server.

| Executed check | Observed result |
| --- | --- |
| Home, dish-name search, literal wildcard query | Three featured cards. Margherita found Slice Society. Searching for a literal percent sign returned no kitchens. |
| Guest adds dosa, attempts second kitchen, updates count | Cross-kitchen add rejected. Two dosas totalled ₹190. |
| Guest starts checkout, registers, signs in | Invalid demo email rejected server-side. Session resumed the existing bag after successful sign-in. |
| Tampered quote, valid checkout | Incorrect total rejected. Correct total created a receipt and cleared the bag. |
| Access controls, invalid token and logout | Guest protected pages redirected to login. Customer admin access and invalid CSRF token returned 403. Logout removed access. A new session displayed the previous visit. |
| File feedback CRUD and ownership | Escaped note text rendered correctly, edit persisted, another user could not delete it, owner delete succeeded. |
| Order ownership | Second user saw an empty order history. |
| Restaurant and menu CRUD | Created and edited a kitchen, created a dish, toggled availability, deleted dish and kitchen. A sold-out dish was rejected at checkout. |
| Historical receipt | Previously ordered dish remained visible after catalogue deletion. |
| Browser events | Dish filter narrowed results, invalid student code failed validity, help opened and Escape closed it, focused ticket came forward. |
| Responsive home | At 390 × 844 the homepage fit the viewport without horizontal scrolling. Desktop screenshots used 1366 × 940. |

## Repeat the HTTP journey

Use a **fresh disposable project copy and database**, including an empty `storage/feedback.txt` containing `[]`. The test creates demo users, sample orders and temporary catalogue entries. Use the original seeded IDs and prices. Start the app using the README, then run these commands from the project folder in a second PowerShell window. Python 3 is required only for the repeatable test.

```powershell
$env:SUPPER_BASE = 'http://127.0.0.1:8000/'
$env:SUPPER_PHP = 'C:\xampp\php\php.exe'
python tests\journey.py
```

For Apache, set `SUPPER_BASE` to `http://localhost/supper-club/public/`. If the server uses database environment overrides, supply the same values to the test terminal so the local promotion command reaches the same database. A successful run prints PASS lines and exits with code 0. Do not run with Python's `-O` option. Create a new disposable copy for a clean repeat.

## Additional hands-on assessment cases

| Case | Steps | Expected result |
| --- | --- | --- |
| V1 · Four registration rules | Try a one-letter name, an external email, a lowercase-only password and SC-12. Correct each value. | Associated hint highlights and invalid submission is blocked. Valid fictional details are accepted. |
| V2 · Unique constraints | Reuse an existing classroom email or student code on a new registration. | Friendly duplicate message. No extra account. |
| D1 · Filters and sorting | Pick Italian, then clear. Sort all kitchens by fastest first and then highest rating. | Italian returns Slice Society. Little Kulfi Co. is fastest at 15 min. Ghee & Grain is highest at 4.8. |
| C1 · Quantity boundaries | Enter 10, then try 11, then 0. | Ten portions accepted. Above-limit input rejected. Zero removes the item. |
| C2 · Price change | Open a signed-in cart. In a separate admin session change the dish price. Submit the original cart. | Checkout asks for review and displays the current total. Only a fresh confirmation places the order. |
| C3 · Refresh after checkout | Confirm once, refresh My orders twice. | A single receipt remains. |
| F1 · File evidence | Save a fictional note, inspect the private text file locally, update and delete through the web form. | Record appears, changes and disappears in the file. |
| E1 · Screen and client coordinates | Move the browser window, then move across the ticket board. Hover each visible ticket edge. | Both coordinate pairs update. The hovered ticket is raised. |
| E2 · Keyboard-only | Tab through tickets and to Help. Press Enter, then Escape. | Ticket focus is visible, help opens and closes, and focus returns to Help. |
| R1 · Small viewport | Use a 390px browser viewport and visit Home, Discover, a menu, Bag and Admin. | Cards and panels stack. Controls remain reachable. |
| S1 · Direct access | While signed out open Orders. As a customer open the admin URL. Try requesting a storage file by HTTP. | Login required, admin denied, and private files inaccessible. |

Change one requirement during the viva, repeat its test and record the observed result alongside the expected result.

