<h3>About</h3>
Client for consuming q symfony skeleton api. It will expose data about books and authors, fetched using endpoints 
from mentioned api. There is no data storage layer, only memcached used for temporally caching results of api calls.
<h3>Installation</h3>
<b>First method</b>
<br>
This method uses docket to run the project. Clone this repository and after changing directory to project directory run
<br><br>
<pre><code>composer update</code></pre>
To install all dependencies needed for project to rune (it runs on symfony 5). After dependencies are installed run docker containers
<br>
<pre><code>docker-compose up</code></pre>
And you should be all set up. Run it locally at localhost:8080
<br><br>
<b>Second method</b>
<br>
As a alternative to first one, you dont have to use docker to run the project. Since its built on symfony, it is possible to use symfony build-in server to run it.
For doing so, make sure you have symfony binaries installed on you system. You can found out how to do so
<a href="https://symfony.com/download">here</a>. After that is done, you will again need to update composer dependencies
<br>
<pre><code>composer update</code></pre>
Now, only thing left to do is start symfony server
<br>
<pre><code>symfony server:start -d</code></pre>
That's it, project now should be available at localhost:8000
<br><br>
<b>Third method</b>
<br>
And last ist the simplest one. But it suitable only for production environments. It utilizes composer, instructions how to do so
can be found on <a href="https://github.com/zus1/qss_client-compose">qss client composer</a> repository.
<br><br>
<h3>Running commands</h3>
Project uses symfony cli command to create new author. To run this command first bash into qss_client container
<br><br>
<pre><code>docker container exec -it qss_client bash</code></pre>
And there run command by using following line(you should be in /var/www/html directory)
<br>
<pre><code>php bin/console app:create-author</code></pre> 
And after that follow on screen instructions to create a new author
<br><br>
Note: Running this command requires memcached php extension to be installed and enabled. If you running this command from you local system, not docker container,
it is possible to check that memcached is installed and enabled by adding --memcached=true option to command
<br>
<pre><code>php bin/console app:create-author --memcached=true</code></pre>
<br>
<h3>Running tests</h3>
Qss client comes with included unit and functional tests. To run them, first bash into qss_client container and then run
<br><br>
<pre><code>php bin/phpunit --testdox</code></pre>
That command will first install php unit, and then run tests. Flag --testdox is just there to output each test result in console, when test is finished.
<br><br>
That's all she wroth, enjoy using this qss client!