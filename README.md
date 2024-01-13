Test Project.

1. Clone project from github by using "git clone https://github.com/gridiron9/kaufland-task".
2. Once it is finished open it in Phpstorm, VSCode or etc. Open terminal and go to Projects directory. Then run "docker compose up". There are 3 containers needs to be run. Wait for them and if it`s finished you are good to go.

Links:

-For mysql management type http://localhost:8001/. 

Process:

-If you dont want to use docker and use your own database, go to "db.php" file and change credentials with your own one.

-To run the program from cmd you need to go inside docker container of php-apache, which name should be "kaufland-task-www-1". Get corresponding name from your command line with docker ps command. Then run "docker exec -it <container_name> bash". In this case it should be "docker exec -it kaufland-task-www-1 bash".

-After container is open you are free to go. To execute, run "php index.php table=products file=feed.xml". Argument order does not matter, so you can also run "php index.php file=feed.xml table=products".

-If you are running it first time it will automatically create products table. It is hard coded because in case you would like to create table error. Otherwise each case wrong table is input, new database will be created. Namely, wrong table name error will never happend.

-Any kind of error will appear in log.txt, it contains time when it arised and text to understand it.

-If code runs without error, it will give total number of seconds required to insert into table and success message.


If on first run, you face "Call to undefined function mysqli_connect()", it is because php library is not installed. To solve it.
1. Get names of docker ps. The container used for PHP service should install the dependencies. If there is no change in the name of the folder and docker-compose.yml file, name should probably by "kaufland-task-www-1".
2. Open internal command prompt of container with "docker exec -it <container_name> bash". In this case it should be
"docker exec -it kaufland-task-www-1 bash".
3. Run following command "docker-php-ext-install mysqli && docker-php-ext-enable mysqli && apachectl restart". Problem should be solved.
