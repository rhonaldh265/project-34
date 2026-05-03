 PHP & MongoDB User Authentication SystemThis project is a simple web application that allows people to Sign Up and Log In. It is connected to a cloud database (MongoDB Atlas) and hosted live on the internet using Render.
 How it Works (The Big Picture)
When a user signs up, their information is sent to a secure cloud database. Instead of saving their real password, the system creates a "digital fingerprint" (a Hash) to keep their data safe
. What each file does:
File NamePurpose (In Simple Terms)
**db.php**
The Bridge. This is the most important file. It connects the website to the MongoDB cloud so they can talk to each other

**signup.php**
The Registration Desk. This is the form where new users type their username and password to create an account.

**login.php**

The Security Gate. This checks the username and password typed by a user against the records in the database to let them in.

**view_users.php**

The Admin List. A private page that shows a table of everyone who has registered (showing their usernames and secure "hashed" passwords)
**.Dockerfile**
The Instruction Manual. Tells the hosting service (Render) exactly how to set up the server and install PHP and MongoDB.

**composer.json**
The Toolkit. A list of extra "tools" (libraries) the project needs to talk to a MongoDB database.
 Security FeaturesPassword Hashing: We never store "real" passwords. We use industry-standard encryption so that even if the database is stolen, the passwords remain secret.
Cloud Hosting: The app is "Dockerized," meaning it can run on any server in the world without needing manual setup.🛠️ Built With:Language: PHP 8.2Database: MongoDB Atlas (NoSQL)Cloud Hosting: RenderContainerization: 
Docker How to run this yourselfClone this repository.Set up a MongoDB Atlas cluster.Update the connection string in db.php with your own database password.Push to Render and enjoy!
