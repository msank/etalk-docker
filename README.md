# HOW TO CREATE AN ETALK USING THE CONTAINER 

_by Martial Sankar & Claire Clivaz_

This etalk setup is suitable for eduction purpose. It permits to use and test the etalk application on the user's own plateform. It uses the docker-compose tool to set-up the application services (etalk php/apache, mysql, phpmyadmin). 


## REQUIREMENTS

- MAC : Requires Apple macOS Yosemite 10.10.3 or above

- WINDOWS : Requires Microsoft Windows 10 Professional or Enterprise 64-bit

- Browsers: Google Chrome; Safari (version ≥7); Internet Explorer (version 11).



## FIRST STEP : INSTALL DOCKER and Run the etalk virtual machine (VM)

1. INSTALL docker (free) for your desktop (windows, osx or linux) https://www.docker.com/products/docker#

2. Download or clone this repository.

3. Open a terminal in linux or open the "docker quick start terminal" in MacOSX and Windows (that comes along with the installation).

4. Go Inside the directory (use `cd /path/to/directory` more info here [:link:]( https://fr.wikipedia.org/wiki/Cd_(commande)) )

5. Secondly, build the image with : 

	```
	$ docker-compose build
	```

6. Then, run the etalk application with :

	```
	$ docker-compose up -d
	```

7. Open a browser

	On linux : 
	go to the url http://localhost:88 for the  __viewer interface__ or http://localhost:88/edit for the __edit interface__

	On MacOSX or Windows :
	go to the url http://192.168.99.100:88 for the __viewer interface__ or http://192.168.99.100:88/edit for the __edit interface__


## SECOND STEP : MAKE YOU OWN ETALK

To make your own etalk, you can follow the how-to from the etalk _"Make your own etalk"_ accessing http://192.168.99.100:88
 
1. Create and name a folder that will contain the mp3 files inside `etalkapp/etalk-master/data/`

2. go to the __edit interface__  and start editing your etalk.

