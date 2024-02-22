# Week 8 | Injection and XSS

In the realm of application security, two prevalent vulnerabilities that pose significant threats to web applications are SQL Injection (SQLi) and Cross-Site Scripting (XSS). SQL Injection attacks exploit vulnerabilities in an application's database layer, allowing attackers to execute unauthorized SQL commands, leading to data breaches, unauthorized access, and even the takeover of database servers. On the other hand, Cross-Site Scripting vulnerabilities enable attackers to inject malicious scripts into web pages viewed by users, potentially leading to data theft, session hijacking, and the compromise of user credentials. Both attack vectors exploit weaknesses in how user input is handled and validated, emphasizing the critical need for robust input validation and sanitization measures. This week's lab is designed to delve into the mechanics of these vulnerabilities, offering hands-on experience in both identifying and mitigating these threats, thereby fortifying our web applications against such insidious attacks.

## ZAP!

### Part 1: What is ZAP?

Zed Attack Proxy (ZAP) is a powerful penetration testing tool. It can scan web applications to identify vulnerabilities, and is currently the most popular web application security tool. It is maintained by OWASP. ZAP includes automatic scanning of common vulnerabilities which can allow you to identify security weaknesses you weren't even aware of!

To get started with ZAP, download it [here](https://www.zaproxy.org/download/).

### Part 2: Using ZAP

Now that we have ZAP installed, let us begin scanning our web application!

1. When you first open ZAP, you may see the following window pop up:

    ![persist session](/lab-writeup-imgs/persist_session.png)

    The default setting is fine, feel free to click "Remember my choice and do not ask me again.", to avoid the pop up in the future.

2. After, you should see the Welcome to ZAP page. Select "Automated Scan".

    ![Automated Scan](/lab-writeup-imgs/automated_scan.png)

    Shockingly, this will automatically scan the web application for vulnerabilities.

3. We can enter the URL to attack, which will be [https://localhost](https://localhost)

    ![Attack URL](/lab-writeup-imgs/attack%20url.png)

    Be sure to enter HTTP**S**, instead of HTTP.
    
4. Now, we can click "Attack" and let the scan begin! We can click on alerts to see the vulnerabilities identified by the scan.

    ![Alerts](/lab-writeup-imgs/alerts.png)

    We should be able to see two vulnerabilities of interest, SQL Injections and Cross Site Scripting!

    ![Specific Alerts](/lab-writeup-imgs/specific_alerts.png)


## SQL Injections

### Part 1: Getting started

When we do not sanitize user input, adversaries can modify the queries our web applications run by utilizing special words and characters to access information that they otherwise wouldn't be able to. When crafting our SQL Injections, they will always start with a single quote, `'`. A web application vulnerable to SQL Injections will take in user input and directly place it in the query as a parameter without sanitizing the input. These parameters are enclosed by single quotes, so by starting our injection with one we are escaping that parameter and can begin adding new conditions to the query! When constructing a query, it is important to remember some important key words in SQL:

- **SELECT**: Used to select data from a database.

- **FROM**: Specifies the table from which to select or delete data.

- **WHERE**: Adds conditions to the selection or deletion.

- **INSERT INTO**: Adds new data into a database.

- **UPDATE**: Modifies existing data within a table.

- **DELETE**: Removes data from a database.

- **AND**: Combines conditions for our query.

- **UNION**: Combines the result set of two or more SELECT statements (only distinct values).

- **ALTER TABLE**: Modifies an existing table structure (e.g., adding or deleting columns).

- **DROP TABLE**: Deletes a table and its data.

When we have crafted our SQL Injection, it is important to remember that we will want to end with `; -- `. The semicolon indicates that we have finished the SQL query, and the two dashes will comment out the rest of the original query as to not interfere with our injection. It is important to remember that we must follow the two dashes with a space, so that everything after it is successfully commented out.

### Part 2: Checking if we're vulnerable

Let us see if the SQL Injection identified in ZAP can be used by us! We can click on "SQL Injection" and see the specific attack used by ZAP to identify the vulnerability.

![injection](/lab-writeup-imgs/injection.png)

We can see that ZAP was able to use the SQL Injection `' AND 1=1;-- `, on the login page in the "username" field. Let us breakdown what this injection is doing:

- `'`: The single quote allows us to escape the `username` parameter.

- `AND`: Combines the original query with the injection we are about to add.

- `1=1`: This is a statement that will always be true, as 1 will always equal 1. 

- `;`: The semicolon indicates that our query is over. 

- `--`: The double dashes will comment out the rest of our query, which in this case most likely checks for the password input

We are able to craft this injection without even seeing the full query in the code! We can assume the query is something like the following:

![login_injection](/lab-writeup-imgs/login_query.png)

By supplying our injection as the input for the username, we can see the query is altered to the following:

![login_injection](/lab-writeup-imgs/login_injection.png)

We can see that we can successfully escape the username parameter and check for `1=1`, which will just be true. Again, by including the semicolon we can end the query, and by including the double dashes, we comment out the rest of the query. This should allow us to login without providing any valid user input! Make sure to include some random text in the password field, as the web app will check if this field is populated before running the query.

Now that we know that our web application is truly vulnerable, lets see what other information we can get by crafting new injections! Logging in as `1=1`, while successful, isn't the most helpful as our (very basic) access control will eventually prevent us from accessing things like vaults and passwords as `1=1` is not a real user. See if you can modify the query to log in as a real user!

## XSS

### Part 1: Getting started

Now that we can successfully bypass authentication using SQL injection, let us take a look at the other application-level vulnerability identified in ZAP, cross site scripting! Cross Site Scripting, or XSS, is a vulnerability which also revolves around unsanitized user input. Instead of using this to modify SQL queries, and adversary can use it to execute our own Javascript directly in the web application.

There are three types of XSS attacks:

- **Reflected XSS***: This type of XSS refers to when our payload is not stored in the web application, but rather it is being reflected in the browser somewhere. 

- **Stored XSS**: This is when the XSS payload is stored in the web application, such as a database. This means that whenever that payload is retrieved from the database the payload will execute in the victims browser.

- **DOM XSS**: This type of XSS attack occurs when the payload alters the DOM environment in the users browser.

In this lab, we are only going to be looking at Reflected and Stored XSS. Let us get started!

### Part 2: Crafting our payload

First, lets take a look at the specific payload that ZAp was able to use to execute XSS.

![alert](/lab-writeup-imgs/alert_xss.png)

We can see that the following javascript was used in the `first_name` field in the "Create Account" page.

```
<script>alert(1);</script>
```

This simple javascript payload, when executed, will create a pop up window in the victim's browser displaying whatever is inside the brackets!

Since we have bypassed authentication using SQL Injection, try to use that same payload to execute a reflected XSS attack somewhere in the password manager. Be sure to pick a location that your victim will be able to view, and alter the alert message so that it is a bit more convincing.

For our next XSS attack, see if you can alter the URL of some webpage so that when your victim clicks on the link, the javascript will execute in their browser. Remember you can execute *any* javascript code, so get creative!

## For Credit

Choose any of the following activities in order to earn 5 points to complete for your lab! Provide a screenshot for each in your write up along with the steps to reproduce.

- .5 pt Log in as Admin without using their password.

- .5 pt Find a way to retrieve all VAULT passwords from the database.

- 1 pt Craft a link to your vault that when opened will do something... nefarious >:)

- 2 pt Create a false pop-up asking your victim for confidential information whenever they access a vault.

- 2 pt Patch the vulnerability that allows logging in without credentials!

- 3 pt Create a payload to return all the USER passwords from the system (HINT: SEE SQL Keywords Above)

- 5 pt AND NO WRITEUP REQUIRED if you can obtain one of your lab partners session id (It's likely you will need to use either advanced XSS or a combination of XSS and SQLi)
