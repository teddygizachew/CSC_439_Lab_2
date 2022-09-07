================================
== TO BUILD
================================

docker build -t lab2 .

- This command means "build a docker image from the current directory, ".", and tag it as "lab2".

================================
== TO RUN
================================

powershell:
docker run -it --rm --name lab2_container -v ${pwd}:/usr/src/myapp lab2

bash:
docker run -it --rm --name lab2_container -v $(pwd):/usr/src/myapp lab2

- This command means "Run the image lab2 with the following options":
  -it : run interactively 
  --rm : remove container when it exits
  --name : name it lab2_container, for when we use "docker ps" to see what containers are running.
  -v : mount our current directory to the container, so we don't have to rebuild every code change.

================================
== TO TEST
================================

powershell:
docker run -it --rm --name lab2_container -v ${pwd}:/usr/src/myapp lab2 phpunit .

bash:
docker run -it --rm --name lab2_container -v $(pwd):/usr/src/myapp lab2 phpunit .

- This command is similar to run, but after the image name (lab2) we override the default CMD with our phpunit command "phpunit ." .

  
================================
== NOTES
================================
- Remember, you can use the up and down arrow keys to cycle through previous commands on the terminal
- The -it option will not work with Git Bash on Windows; use PowerShell, Cygwin, or WSL.
- The difference between Powershell and Bash variants is the {} or () in the pwd.
- These docker commands can have issues with spaces in paths with bash; try to put your code in a path with no spaces.
