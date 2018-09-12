CSRF Class
===================
This is a simple yet effective class to enable you to protect your forms from CSRF attacks.

**What is CSRF?**
A CSRF attack or Cross Site Request Forgery as it's known is where a malicious user takes advantage of your logged in state. For example, say you used a get request to logout a user, like so:

    http://mysite.com/user/logout
This is a perfectly valid URL, however if this user was also logged in on another site, they could take advantage of this by placing this in an image tag:

    <img src="http://mysite.com/user/logout" />
This would cause the user to be logged out on your site. This is an annoying, but not too serious example. 

To protect from this we use what's called a **security token** which ensures that the request did come from our site. You could use it in a get request like so:

    http://mysite.com/user/logout?hash=6f792794d27d157fda64bc51f296e4f3
This would prevent that image tag logging the user out as the security token wouldn't be present.
However, there can be problems doing it this way, so your better off logging a user out via a post request in a form.

## Usage ##
If you look in the **form_demo.php** file, you will see an example of how the class can be used.

**Step 1:** Echo `Token::display(); ` in your form.

**Step 2:** Check for a post request and whether the security token is valid. If you want you can also check if it's recent:
 ```php
 if (!Token::isValid() OR !Token::isRecent())
 { 
	$errors[] = 'Invalid Security Token'; 
	// Stop further processing.
 }
 ```

**Step 3:** That's it!

It's literally as easy as 1..2..3!


