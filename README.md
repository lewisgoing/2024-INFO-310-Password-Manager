# Week 10 | Deploying

Now that we have hardened our web application from common vulnerabilities, we can move forward with deploying our password manager to production! Cloud deployment offers many benefits including scalability, flexibility, and efficiency! It also removes much of the headache and complexity of managing our own infrastructure. One of the most popular cloud platforms is Azure. Today we will be pushing our password manager to Azure for the public to use!

## Part 1: Pushing our images to Docker Hub

Now that we have configured our PHP and Nginx docker images to our liking, we can push them to Docker Hub! Doing this will allow us to pull the images directly into Azure when we deploy the password manager, and we can easily track and update different versions of our PHP and Nginx configurations using Docker hub!

To do this we will need to make a Docker Hub account and log in within our Docker Desktop application.

1. In Docker Desktop, click on the "Sign in" in the top right corner.
    ![Sign in Button](/lab-writeup-imgs/sign_in_button.png)

2. Your browser will open and prompt you to login, from here you can click the "Sign Up" link in the top right corner.
    ![Docker Sign up](/lab-writeup-imgs/sign_up_docker.png)

3. Once you have created your account, go back to Docker Desktop and click the "Sign in" button again. You should already be logged in with your new Docker account in your browser, so you will get signed in to Docker Desktop automatically.

4. Redeploy your Password Manager.
    ![Redeploy the Password Manager](/lab-writeup-imgs/redeply.png)

5. Run the following command to view a list of all your images.
    ```
    docker images
    ```

    you should see something similar to the following output:

    ![Docker Images](/lab-writeup-imgs/docker_images.png)

    This shows us a list of all the images we have built and deployed into containers to get our password manager up and running. As you can see, we have three images, our PHP server, nginx server, and MySQL server.

6. Before we can push our images to Docker hub, we need to tag them. This will allow us to track the version of our images as we continue to make future changes. To do this, copy the image ID of the image we want to tag and run the following command:

    ```
    docker tag <image_id> <your_username>/<image_name>:latest
    ```

    Your username should match the username used in your Docker account. The image name can be whatever you'd like, however I would recommend using `nginx` and `php` for their respective images.

7. Once you have both the images tagged, you can push them to Docker hub individually by running the following command:
    ```
    docker push <your_username>/<image_name>:latest
    ```

8. Once you have them pushed, they should appear under "Repositories" in you Docker Hub account:

    ![Docker images in Docker Hub](/lab-writeup-imgs/docker_hub_images.png)

You have now successfully pushed your Docker Images to Docker Hub! We can now use them to deploy our password manager in Azure!

## Part 2: Setting up Azure SQL Database

1. Let us start by logging into Azure. If you don't already have an account, go ahead and make one now using your UW NetID.

2. Once logged in, you should see the Azure home page. From here, click "Create a resource".
    ![Azure Home Page](/lab-writeup-imgs/azure_home.png)

    In Azure, we can utilize a variety of different *resources*. A resource is a manageable item in Azure. Web apps, virtual machines, databases, and virtual networks are some examples of resources available in Azure. It is best practice to group these resources that we will use together into a resource group so that we can easily manage the different resources that we will use for any given project. 

3. From the Create a resource page, search "resource group", and create a new resource group.
    ![Search for resource group](/lab-writeup-imgs/search_resource_group.png)
    
    Once you search for "resource group", you will be redirected the the Marketplace where you can create a new resource group

    ![Select Resource Group](/lab-writeup-imgs/select_resouce_group.png)

4. We will be prompted to enter some information about our new resource group. Feel free to name it whatever you'd like. I put it's location as West US 2, although this shouldn't make a big difference.

    ![Name our resource group](/lab-writeup-imgs/name_resouce_group.png)

    Once you have your resource group's name entered, click "Review + Create" in the bottom left corner. Once the validation has passed, click "Create".

    ![Create Resource Gorup](/lab-writeup-imgs/create_resouce_group.png)

5. Next, we will create our SQL database in Azure. On your Azure homepage, search for MySQL at the top.
    ![Searching MySQL](/lab-writeup-imgs/search_mysql.png)


6. You will see a list of all your MySQL databases in Azure (this will probably be empty). Click the "Create" button in the top left corner.
    ![MySQL Databases List](/lab-writeup-imgs/mysql_database_page.png)

7. You will then be asked to select a deployment option. Choose "Flexible Server".
    ![Flexible Server](/lab-writeup-imgs/flexible_server.png)

8. Next, you will be asked to enter some information regarding the SQL Database:
    - For Server Name, call it whatever you'd like.
    - For Region, make sure it matches what you used for the resource group.
    - Ensure that Workload Type is set "For development or hobby projects".
    - For Authentication, select "MySQL authentication".
    - For the Admin username and password, feel free to choose whatever you'd like. Make sure to write it down as we will need it for later.

    ![Database basics](/lab-writeup-imgs/database_basics_config.png)

9. Click the "Networking" tab at the top and make sure to select "Allow public access from any Azure service within Azure to this server".
    ![Allow Azure Access](/lab-writeup-imgs/allow_azure_access_db.png)

10. Click Review + Create, and then Create! You should get redirected to a page stating that Deployment is in progress. Once your deployment succeeds, click "Go to resource".
    ![Db deployed](/lab-writeup-imgs/db_deployed.png)

11. You will get taken to the Overview page for your database. First, take note of the "Server Name" on the right hand side, this will be important for later. Next, in the side panel on the left, select "Connect".
    ![Select Connect db](/lab-writeup-imgs/db_select_connect.png)

12. Expand the section title "Connect from browser or locally" and copy the command.
    ![Copy db command](/lab-writeup-imgs/local_browser_cmd.png)

13. In the top right corner, click the terminal icon. This will open a cloud terminal in Azure.
    ![Terminal icon](/lab-writeup-imgs/cloud_terminal_icon.png)

    You should see a terminal similar to the following:
    ![Cloud Terminal](/lab-writeup-imgs/cloud_terminal.png)

14. In the Cloud Terminal, paste in the command to connect to you SQL Database. Make sure the username matches what you put for the Admin Username in Step 8.
    ![Cloud Terminal Connected to db](/lab-writeup-imgs/cloud_terminal_db_connection.png)

15. We will want to populate the database with all of the information we have in our password manager. To do this, we can copy and paste all of the content from the `init.sql` file into our Azure cloud terminal.

    We can then run the command `use password_manager` to select our new database followed by `show tables;` to confirm that all of our tables are present

    ![Populated db](/lab-writeup-imgs/database_populated.png)

16. We will now need to allow insecure connections to our database. This is not recommended, however our nginx container will be running on port 80 since our certificates will not work. To do this click on "Server parameters" on the side bar.
    ![Server parameters](/lab-writeup-imgs/server_parameters.png)

17. Next, find the parameter titled 'require_secure_transport' and set it's value to OFF.
    ![Require secure transport](/lab-writeup-imgs/require_secure_transport.png)

We have successfully set up our MySQL database in Azure! Let's now configure our App Service so we can start using our Password Manager in the cloud :D

## Part 3: Setting up our App Service

App Services in Azure is a Platform-as-a-Service, or PaaS, which is a cloud computing model that allows for us as developers to quickly deploy web applications without needing to configure and manage all of the infrastructure needed to actually deploy the web app. 

1. In the Azure home page, search for "App Services".
    ![Search app services](/lab-writeup-imgs/search_app_services.png)

2. You should be taken to an overview of all your App Services, which should be an empty list. From here, select Create > Web App.
    ![Create Web App](/lab-writeup-imgs/create_webapp.png)

3. From here, you will be asked to enter some configuration settings for your App Service.
    - For Resource Group, ensure you are using the same one you created for your SQL Database.
    - For Publish, select "Docker Container".
    - For Operating System, select "Linux".
    - For Region, select "West US".
    ![App Service Basic Config](/lab-writeup-imgs/web_app_basic_config.png)

4. Click the Docker tab at the top to configure the Docker settings.
    - For Options, select "Docker Compose".
    - For Image Source, select Docker Hub.
    - For Access Type, select Public.

5. We will need to add our docker-compose file, but we first need to make some modifications to it so that it can run properly in our cloud environment. 

    - Let's start by making a copy of our docker-compose.yaml file, so that we can always keep a copy of our original that we know is functional locally. Make sure to rename the copy to `azure-docker-compose.yml`

    - For the nginx configuration, we can delete the `build` section and replace it with the image we pushed to docker hub.
    ```
    image: <your_username>/nginx:latest
    ```

    - For the PHP configuration, we can delete the `build` section and replace it with an `image` line that will pull our custom PHP docker image.

    - Docker environment variables do not appear to function properly in Azure. I'm sure there is a way to get them to work, however I cannot find a simple way to do so. That being said, we will need to hard code our SQL credentials and loggly token. **THIS IS VERY BAD AND SHOULD NEVER BE DONE IN A PRODUCTION ENVIRONMENT**, however for the sake of time, we will do so in our deployment.

    - Enter your Loggly Token value for LOGGLY_TOKEN.
    - Enter the Server Name we saved from earlier for the MYSQL_HOST variable.
    - Enter teh Admin username and password for MYSQL_ROOT_PASSWORD, MYSQL_USER, and MYSQL_PASSWORD. 
    - Use the password_manager database we created in our SQL database for the MYSQL_DATABASE variable.

    - We will not need any of the mysql stuff in the compose file, as we already have our MySQL database up and running. Your final `azure-docker-compose.yml` file should look like the following:

    ```
    services:
        # nginx    
        router:
            image: zkonras/nginx:latest                 
            ports:
                - "80:80"
        # php
        php-server:
            image: zkonras/php:latest
            environment:
                LOGGLY_TOKEN: <Loggly Token>
                MYSQL_HOST: <SQL Server Name>
                MYSQL_ROOT_PASSWORD: <Admin Password>
                MYSQL_USER: <Admin Username>
                MYSQL_PASSWORD: <Admin Password>
                MYSQL_DATABASE: password_manager
            extra_hosts:
                - host.docker.internal:host-gateway
    ```

6. Select your newly created `azure-docker-compose.yml` file for the Configuration File.
    ![Azure Docker Compose](/lab-writeup-imgs/azure_docker_compose.png)

7. Next click Review + Create, and then Create. You will be taken to a screen to track your deployment progress.

8. Once the deployment is done, go to the App Service resource and view your password manager in your browser by clicking on "Default Domain"
    ![Default Domain](/lab-writeup-imgs/default_domain.png)

## For Credit
Congratulations on finishing your final lab in INFO 310! We hope this has been an insightful quarter into the world of cybersecurity and encourage you to keep exploring the subject! Much of security can be self taught, and there are many amazing resources online to continue your journey into security :D

For this weeks lab, just submit a link to your password manager hosted in Azure to the canvas assignment.