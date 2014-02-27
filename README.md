PHP Home Test
As a developer it's always good to have coffee. We want to write a simple php application that consists of 3 parts.
Guidelines:

• use php (obviously)
• use a database that you feel comfortable working with
• use a memory based storage that you feel comfortable working with
• use an API that you feel comfortable with
• use MVC
• use a framework if you like (symfony, zend, codeigniter)
• document your code (phpdoc comments)
• use Jquery (for fancy effects) or plain javascript
• include some kind of unit test to test your application

Step 1 - Setup
Use the “Google Custom Search” API (or whichever image search you like, as long as it has an API) to get a list of images of coffee beans.

• Implement a search box
• display the images that you found below that search box
• let the user click-select some of the images found
• save these image urls into a db (choose whatever you're comfortable with: mysql, nosql, etc)
￼
Step 2 – Coffee time
Now we want to put some beans into our virtual coffee machine (we'll do a blend of the finest coffee beans we found).

• display the images we got from Step 1 with a quantity box (default 1) and an “Add to Grinder” button next to that
• if we click on the button, we will
• add that number of these beans into our grinder (via Ajax)
• store this values in a memory-based cache (you can use memcache, redis etc)
• update the right pane with the bean counts from memcache and their images from the
db (via Ajax)
• if we hit “Grind!” we'll clear the db and our memory storage and display the last screen
Step 3 – Thanks for brewing coffee, come again!
