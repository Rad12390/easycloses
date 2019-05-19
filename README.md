###### easycloses.com
Branch for Med App

##Installation

Step 1:
Clone repo to your directory

Step 2:
Use <code>$ composer update</code>, if you get error use <code>$ composer install</code> before it.


Step 3:
Create new user in your DB.

Step 4:
To create DB use <code>$ php app/console doctrine:database:create</code>

Step 5:
To create tables structure in DB use <code>$ php app/console doctrine:schema:create</code>

Step 6:
Create new roles in DB 

    {
        name: "Admin",
        role: "ROLE_ADMIN"
    },
    {
        name: "Manager",
        role: "ROLE_MANAGER"
    }

Step 7:
Create new status in DB 

    {
        status: "new",
        color: "green"
    }

Step 8:
Now you can register new user using Application

    But when you try to register new user you will get an error. 
    To fix it you should open table "users" in your DB and update new row: set "role_id" to ID of Admin, and field "createdBy_id" cannot be null (I set it equal to user ID)
     
Application work fine
