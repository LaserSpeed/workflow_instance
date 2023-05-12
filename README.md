# workflow_instance
Creating the workflow instances by using the workflow model

# Functionality
1. Create an Instance

2. Update an Instance

3. Create an Instance Controller:
Whenever an instance is created then it will create an instance controller, which help to give the visibily to a particular user according to the workflow used by the instance. The handler user either be a single person or a group of person depending upon the steps. 

4. Update Instance Controller
Initially the intance controller is 0, means it is not visited by the handler. It is in pending state. So, according the handler action it should be updated.

5. Go to next step

6. Go to previous step

7. Go to a particular step